<?php

namespace App\Services;

use App\Models\Jackpot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ProvablyFairService
{
    /**
     * Генерирует серверный сид и хеш для новой игры
     */
    public function generateGameHash(): array
    {
        $serverSeed = Str::random(32);
        $hash = hash('sha256', $serverSeed);

        return [
            'server_seed' => $serverSeed,
            'hash' => $hash
        ];
    }

    /**
     * Вычисляет выигрышный билет на основе серверного сида и данных игры
     */
    public function calculateWinningTicket(Jackpot $game): int
    {
        $serverSeed = Cache::get("game_seed:{$game->id}");
        if (!$serverSeed) {
            throw new \Exception('Server seed not found');
        }

        // Собираем данные для генерации
        $gameData = [
            'game_id' => $game->game_id,
            'room' => $game->room,
            'price' => $game->price,
            'bets_count' => $game->bets()->count(),
            'created_at' => $game->created_at->timestamp
        ];

        // Создаем строку для хеширования
        $dataString = json_encode($gameData) . $serverSeed;

        // Генерируем хеш
        $hash = hash('sha256', $dataString);

        // Преобразуем первые 8 символов хеша в число
        $decimal = hexdec(substr($hash, 0, 8));

        // Получаем максимальный номер билета
        $maxTicket = $game->bets()->max('to');

        // Вычисляем выигрышный билет
        return ($decimal % $maxTicket) + 1;
    }

    /**
     * Проверяет валидность игры
     */
    public function verifyGame(Jackpot $game, string $serverSeed): bool
    {
        // Проверяем соответствие хеша
        if (hash('sha256', $serverSeed) !== $game->hash) {
            return false;
        }

        // Получаем выигрышный билет
        $ticket = $this->calculateWinningTicketWithSeed($game, $serverSeed);

        // Находим победителя по билету
        $winningBet = $game->bets()
            ->where('from', '<=', $ticket)
            ->where('to', '>=', $ticket)
            ->first();

        // Проверяем, что победитель совпадает
        return $winningBet && $winningBet->user_id === $game->winner_id;
    }

    /**
     * Вычисляет выигрышный билет с предоставленным сидом
     */
    protected function calculateWinningTicketWithSeed(Jackpot $game, string $serverSeed): int
    {
        $gameData = [
            'game_id' => $game->game_id,
            'room' => $game->room,
            'price' => $game->price,
            'bets_count' => $game->bets()->count(),
            'created_at' => $game->created_at->timestamp
        ];

        $dataString = json_encode($gameData) . $serverSeed;
        $hash = hash('sha256', $dataString);
        $decimal = hexdec(substr($hash, 0, 8));
        $maxTicket = $game->bets()->max('to');

        return ($decimal % $maxTicket) + 1;
    }

    /**
     * Сохраняет серверный сид для игры
     */
    public function storeGameSeed(int $gameId, string $serverSeed): void
    {
        Cache::put("game_seed:{$gameId}", $serverSeed, now()->addDays(30));
    }

    /**
     * Получает серверный сид игры
     */
    public function getGameSeed(int $gameId): ?string
    {
        return Cache::get("game_seed:{$gameId}");
    }
}
