<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\SlotegratorGame;

class TransactionsTable extends Component
{
    // Константы для настроек
    const TRANSACTIONS_LIMIT = 500;
    const DISPLAY_LIMIT = 10;
    const CACHE_TTL = 30; // секунд

    public array $transactions = [];
    public array $mytransactions = [];
    public string $type = 'all';

    protected $listeners = ['refreshTransactions'];

    public function mount(): void
    {
        $this->loadTransactions();
    }

    public function loadTransactions(): void
    {
        try {
            $userId = Auth::id();
            $this->transactions = $this->getTransactions();
            $this->mytransactions = $this->getTransactions($userId);
        } catch (\Exception $e) {
            Log::error('Error loading transactions', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            $this->transactions = [];
            $this->mytransactions = [];
        }
    }

    private function getTransactions(?int $userId = null): array
    {
        $cacheKey = $userId ? "transactions:user:{$userId}" : "transactions:all";

        // Пытаемся получить из кеша
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $rawTransactions = $this->getRawTransactionsFromRedis();
            $processedTransactions = $this->processTransactions($rawTransactions, $userId);

            // Кешируем результат
            Cache::put($cacheKey, $processedTransactions, self::CACHE_TTL);

            return $processedTransactions;

        } catch (\Exception $e) {
            Log::error('Error processing transactions', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return [];
        }
    }

    private function getRawTransactionsFromRedis(): array
    {
        try {
            return Redis::lrange('game_history:all', 0, self::TRANSACTIONS_LIMIT);
        } catch (\Exception $e) {
            Log::error('Redis error when fetching transactions', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function processTransactions(array $transactionHashes, ?int $userId = null): array
    {
        $transactions = collect($transactionHashes)
            ->map([$this, 'parseTransaction'])
            ->filter()
            ->groupBy([$this, 'getGroupKey'])
            ->map([$this, 'processTransactionGroup'])
            ->filter()
            ->sortByDesc('created_at')
            ->values();

        // Фильтруем по пользователю ПОСЛЕ группировки, если указан userId
        if ($userId) {
            $transactions = $transactions->filter(function ($transaction) use ($userId) {
                $transactionUserId = $transaction['user']['id'] ?? null;

                // Сравниваем как строки и как числа
                return $transactionUserId == $userId || $transactionUserId === (string)$userId;
            });
        }

        return $transactions->take(self::DISPLAY_LIMIT)->toArray();
    }

    public function parseTransaction(?string $transactionJson): ?array
    {
        if (empty($transactionJson)) {
            return null;
        }

        $data = json_decode($transactionJson, true);

        if (!$this->isValidTransactionData($data)) {
            return null;
        }

        return $this->enrichTransactionData($data);
    }

    private function isValidTransactionData($data): bool
    {
        return is_array($data)
            && isset($data['round_id'])
            && isset($data['user'])
            && is_array($data['user'])
            && isset($data['user']['username'])
            && isset($data['type'])
            && in_array($data['type'], ['bet', 'win']);
    }

    private function enrichTransactionData(array $data): array
    {
        // Приоритет: сначала пытаемся взять game_name напрямую
        $data['game_name'] = $this->extractGameName($data);

        return $data;
    }

    private function extractGameName(array $data): string
    {
        // 1. Сначала пытаемся взять напрямую из game_name (приоритет!)
        if (!empty($data['game_name'])) {
            return $data['game_name'];
        }

        // 2. Проверяем gameId в основных данных
        if (!empty($data['gameId'])) {
            return $data['gameId'];
        }

        // 3. Если нет, пытаемся извлечь из контекста
        $context = $this->parseContext($data['context'] ?? '{}');

        // 4. Проверяем есть ли game_name в контексте
        if (!empty($context['game_name'])) {
            return $context['game_name'];
        }

        // 5. Проверяем gameId в контексте
        if (!empty($context['gameId'])) {
            return $context['gameId'];
        }

        // 6. Fallback: пытаемся извлечь из description (старый способ)
        $description = $context['description'] ?? '';
        if (preg_match('/in game (.+)/', $description, $matches)) {
            return trim($matches[1]);
        }

        // 7. Проверяем другие возможные поля
        if (!empty($context['game'])) {
            return $context['game'];
        }

        // 8. Последний fallback
        return 'Unknown Game';
    }

    private function parseContext($context): array
    {
        if (is_array($context)) {
            return $context;
        }

        return json_decode($context, true) ?: [];
    }

    public function getGroupKey(array $item): string
    {
        return $item['round_id'] ?? 'unknown';
    }

    public function processTransactionGroup($group): ?array
    {
        $bet = $group->firstWhere('type', 'bet');
        $wins = $group->where('type', 'win');

        if (!$bet || $wins->isEmpty()) {
            return null;
        }

        // Проверяем, что раунд завершен
        $lastWin = $wins->last();
        if (!($lastWin['finished'] ?? false)) {
            return null;
        }

        $betAmount = (float) ($bet['amount'] ?? 0);
        $totalWinAmount = (float) $wins->sum('amount');

        $gameName = $bet['game_name'] ?? 'Unknown Game';
        $gameSlug = $this->getGameSlug($gameName);

        return [
            'game_name' => $gameName,
            'game_slug' => $gameSlug,
            'user' => $this->formatUserData($bet['user'] ?? []),
            'bet_amount' => $betAmount,
            'win_amount' => $totalWinAmount,
            'coefficient' => $this->calculateCoefficient($betAmount, $totalWinAmount),
            'currency' => $bet['currency'] ?? ['symbol' => '$'],
            'round_id' => $bet['round_id'] ?? 'unknown',
            'finished' => $lastWin['finished'] ?? false,
            'created_at' => $lastWin['created_at'] ?? now()->toISOString(),
        ];
    }

    private function formatUserData(array $userData): array
    {
        // Приводим user_id к строке для консистентности
        $userId = $userData['id'] ?? ($userData['user_id'] ?? 'unknown');

        return [
            'id' => (string)$userId, // Приводим к строке
            'username' => $userData['username'] ?? 'Unknown User',
            'avatar' => $userData['avatar'] ?? '/assets/images/avatar-placeholder.png'
        ];
    }

    private function calculateCoefficient(float $betAmount, float $winAmount): float
    {
        if ($betAmount <= 0) {
            return 0;
        }

        return round($winAmount / $betAmount, 2);
    }

    private function getGameSlug(string $gameName): ?string
    {
        // Кешируем результаты поиска slug на 1 час
        $cacheKey = "game:slug:" . md5($gameName);
        
        return Cache::remember($cacheKey, 3600, function () use ($gameName) {
            // Ищем игру по имени в базе
            $game = SlotegratorGame::where('name', $gameName)->first();
            
            return $game ? $game->slug : null;
        });
    }

    public function refreshTransactions(): void
    {
        // Очищаем кеш перед обновлением
        Cache::forget("transactions:all");
        if (Auth::id()) {
            Cache::forget("transactions:user:" . Auth::id());
        }

        $this->loadTransactions();

    }

    public function switchType(string $newType): void
    {
        $allowedTypes = ['all', 'my'];

        if (in_array($newType, $allowedTypes)) {
            $this->type = $newType;
        }
    }

    public function render()
    {
        return view('livewire.transactions-table');
    }

    // Метод для очистки кеша (можно вызывать из других частей приложения)
    public static function clearCache(): void
    {
        Cache::forget("transactions:all");
        // Очищаем кеш для всех пользователей (если нужно)
        // Cache::flush(); // Осторожно! Это очистит весь кеш
    }

    // Отладочный метод для проверки структуры данных
    public function debugTransactionStructure(): void
    {
        try {
            $rawTransactions = Redis::lrange('game_history:all', 0, 5);

            foreach ($rawTransactions as $index => $transactionJson) {
                if (!empty($transactionJson)) {
                    $data = json_decode($transactionJson, true);
                    Log::info("Transaction structure #{$index}", [
                        'has_game_name' => isset($data['game_name']),
                        'game_name_value' => $data['game_name'] ?? 'not set',
                        'has_gameId' => isset($data['gameId']),
                        'gameId_value' => $data['gameId'] ?? 'not set',
                        'context_keys' => array_keys($data['context'] ?? []),
                        'type' => $data['type'] ?? 'not set',
                        'round_id' => $data['round_id'] ?? 'not set',
                        'user_structure' => array_keys($data['user'] ?? []),
                    ]);
                    break; // Показываем только первую транзакцию
                }
            }
        } catch (\Exception $e) {
            Log::error('Debug transaction structure error', ['error' => $e->getMessage()]);
        }
    }
}
