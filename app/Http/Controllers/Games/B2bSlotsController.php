<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\SlotegratorGame;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserGameHistory;
use App\Services\B2bSlots\B2bSlotsClient;
use App\Services\ExchangeService;
use App\Traits\Hashable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class B2bSlotsController extends Controller
{
    use Hashable;

    protected B2bSlotsClient $client;
    protected $transactionTimeout = 10;
    protected $logger;

    public function __construct(B2bSlotsClient $client)
    {
        $this->client = $client;
        $this->logger = Log::channel('single');
    }

    public function launchGame($game)
    {
        try {
            $locale = App::getLocale();
            $game = Cache::remember("b2b_game:{$game}", 3600, function () use ($game) {
                return SlotegratorGame::where('name', $game)
                    ->where('provider_type', 'b2b_slots')
                    ->firstOrFail();
            });

            $user = Auth::user();
            $authToken = Str::uuid()->toString();

            // Создаем игровую сессию
            $gameToken = Str::uuid()->toString();
            $user->gameSession()->updateOrCreate(
                ['user_id' => $user->id],
                ['token' => $gameToken]
            );

            // Записываем историю
            $user->gamesHistory()->create([
                'slotegrator_game_id' => $game->id,
                'session_token' => $gameToken,
                'ip' => request()->ip(),
                'device' => (new Agent())->device(),
            ]);

            // Генерируем URL игры
            $gameUrl = $this->client->generateGameUrl([
                'user_id' => $user->id,
                'auth_token' => $authToken,
                'currency' => $user->currency->symbol,
                'language' => $locale === 'az' ? 'tr' : $locale,
                'game_name' => $game->name,
                'game_code' => $game->game_code,
                'home_url' => route('home'),
            ]);

            // Сохраняем auth_token для проверки в callback
            Cache::put("b2b_auth:{$authToken}", [
                'user_id' => $user->id,
                'game_token' => $gameToken,
                'currency' => $user->currency->symbol,
            ], 3600);

            $view = (new Agent())->isMobile() ? 'games.mobile' : 'games.play';
            return view($view, ['game' => $game, 'gameUrl' => $gameUrl]);

        } catch (\Exception $e) {
            Log::error('B2B Slots game launch error', [
                'game' => $game,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    public function launchDemoGame($game)
    {
        $locale = App::getLocale();
        $game = SlotegratorGame::where('name', $game)
            ->where('provider_type', 'b2b_slots')
            ->firstOrFail();

        // Для демо режима используем фиктивные данные
        $gameUrl = $this->client->generateGameUrl([
            'user_id' => 'demo',
            'auth_token' => 'demo_token',
            'currency' => 'USD',
            'language' => $locale === 'az' ? 'tr' : $locale,
            'game_name' => $game->name,
            'game_code' => $game->game_code,
            'home_url' => route('home'),
        ]);

        $agent = new Agent();
        $isMobile = $agent->isMobile();
        return view($isMobile ? 'games.mobile' : 'games.play', ['game' => $game, 'gameUrl' => $gameUrl]);
    }

    public function callback(Request $request)
    {
        $startTime = microtime(true);
        $data = $request->json()->all();
        $api = $data['api'] ?? '';

        $this->logger->info('B2B Slots callback received', ['api' => $api, 'data' => $data]);

        try {
            $result = match ($api) {
                'do-auth-user-ingame' => $this->handleAuth($data['data']),
                'do-debit-user-ingame' => $this->handleDebit($data['data']),
                'do-credit-user-ingame' => $this->handleCredit($data['data']),
                'do-get-features-user-ingame' => $this->handleGetFeatures($data['data']),
                'do-activate-features-user-ingame' => $this->handleActivateFeatures($data['data']),
                'do-update-features-user-ingame' => $this->handleUpdateFeatures($data['data']),
                'do-end-features-user-ingame' => $this->handleEndFeatures($data['data']),
                default => $this->client->errorResponse($api, 1, 'Unknown API method')
            };

            $endTime = microtime(true);
            $this->logger->info('B2B Slots callback processed', [
                'api' => $api,
                'execution_time' => $endTime - $startTime,
                'result' => $result
            ]);

            return response()->json($result);

        } catch (Exception $e) {
            $this->logger->error('B2B Slots callback error', [
                'api' => $api,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json($this->client->errorResponse($api, 1, 'Internal error'));
        }
    }

    protected function handleAuth(array $data): array
    {
        // Проверяем auth_token
        $authData = Cache::get("b2b_auth:{$data['user_auth_token']}");
        if (!$authData) {
            return $this->client->errorResponse('do-auth-user-ingame', 4, 'Token not found');
        }

        $user = User::find($authData['user_id']);
        if (!$user) {
            return $this->client->errorResponse('do-auth-user-ingame', 5, 'User not found');
        }

        return $this->client->handleAuth([
            'user_id' => $user->id,
            'user_nickname' => $user->username,
            'balance' => number_format($user->balance, 2, '.', ''),
            'bonus_balance' => '0.00',
            'user_auth_token' => $data['user_auth_token'],
            'game_token' => $authData['game_token'],
            'currency' => $user->currency->symbol,
        ]);
    }

    protected function handleDebit(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::lockForUpdate()->find($data['user_id']);
            if (!$user) {
                return $this->client->errorResponse('do-debit-user-ingame', 5, 'User not found');
            }

            $amount = (float)$data['debit_amount'];

            // Проверяем, есть ли уже такая транзакция
            if ($this->isExistingTransaction($data['transaction_id'])) {
                return $this->getExistingB2bTransactionResponse($user, $data['transaction_id']);
            }

            // Проверяем game_token
            if (!$this->checkGameToken($user, $data)) {
                return $this->client->errorResponse('do-debit-user-ingame', 4, 'Invalid game token');
            }

            // Проверяем баланс
            if ($user->balance < $amount) {
                return $this->client->errorResponse('do-debit-user-ingame', 3, 'Insufficient funds');
            }

            $originalBalance = $user->balance;
            $user->balance -= $amount;
            $user->save();

            // Создаем транзакцию
            $transaction = $this->createB2bTransaction($user, $data, 'bet', $amount, $originalBalance);

            // Обработка отыгрыша если есть
            if ($user->hasActiveWagering()) {
                $exchangeService = new ExchangeService();
                $betAmount = $amount;

                if ($user->currency->symbol !== 'AZN') {
                    $betAmount = $exchangeService->convert($betAmount, $user->currency->symbol, 'AZN');
                }

                $user->addToWageringAmount($betAmount);
            }

            return $this->client->handleDebit([
                'transaction_id' => $data['transaction_id'],
                'user_id' => $user->id,
                'user_nickname' => $user->username,
                'balance' => number_format($user->balance, 2, '.', ''),
                'bonus_balance' => '0.00',
                'bonus_amount' => '0.00',
                'game_token' => $data['user_game_token'],
                'currency' => $user->currency->symbol,
            ]);
        });
    }

    protected function handleCredit(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::lockForUpdate()->find($data['user_id']);
            if (!$user) {
                return $this->client->errorResponse('do-credit-user-ingame', 5, 'User not found');
            }

            $amount = (float)$data['credit_amount'];

            // Проверяем, есть ли уже такая транзакция
            if ($this->isExistingTransaction($data['transaction_id'])) {
                return $this->getExistingB2bTransactionResponse($user, $data['transaction_id']);
            }

            // Проверяем game_token
            if (!$this->checkGameToken($user, $data)) {
                return $this->client->errorResponse('do-credit-user-ingame', 4, 'Invalid game token');
            }

            $originalBalance = $user->balance;
            $user->balance += $amount;
            $user->save();

            // Создаем транзакцию
            $transaction = $this->createB2bTransaction($user, $data, 'win', $amount, $originalBalance);

            return $this->client->handleCredit([
                'transaction_id' => $data['transaction_id'],
                'user_id' => $user->id,
                'user_nickname' => $user->username,
                'balance' => number_format($user->balance, 2, '.', ''),
                'bonus_balance' => '0.00',
                'bonus_amount' => '0.00',
                'game_token' => $data['user_game_token'],
                'currency' => $user->currency->symbol,
            ]);
        });
    }

    protected function handleGetFeatures(array $data): array
    {
        $user = User::find($data['user_id']);
        if (!$user) {
            return $this->client->errorResponse('do-get-features-user-ingame', 5, 'User not found');
        }

        // Здесь можно добавить логику для фриспинов
        // Пока возвращаем без бонусов
        return $this->client->handleGetFeatures([
            'user_id' => $user->id,
            'user_nickname' => $user->username,
            'balance' => number_format($user->balance, 2, '.', ''),
            'bonus_balance' => '0.00',
            'game_token' => $data['user_game_token'],
            'currency' => $user->currency->symbol,
            'free_rounds' => null, // Здесь можно добавить логику фриспинов
        ]);
    }

    protected function handleActivateFeatures(array $data): array
    {
        // Логика активации фриспинов
        $user = User::find($data['user_id']);
        return $this->client->handleGetFeatures([
            'user_id' => $user->id,
            'user_nickname' => $user->username,
            'balance' => number_format($user->balance, 2, '.', ''),
            'bonus_balance' => '0.00',
            'game_token' => $data['user_game_token'],
            'currency' => $user->currency->symbol,
        ]);
    }

    protected function handleUpdateFeatures(array $data): array
    {
        // Логика обновления фриспинов
        return $this->handleActivateFeatures($data);
    }

    protected function handleEndFeatures(array $data): array
    {
        // Логика завершения фриспинов
        return $this->handleActivateFeatures($data);
    }

    protected function isExistingTransaction($transactionId): bool
    {
        return Cache::remember("b2b_transaction_exists:{$transactionId}", 3600, function () use ($transactionId) {
            return Transaction::where('hash', $transactionId)->exists();
        });
    }

    protected function getExistingB2bTransactionResponse($user, $transactionId): array
    {
        return $this->client->handleDebit([
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'user_nickname' => $user->username,
            'balance' => number_format($user->balance, 2, '.', ''),
            'bonus_balance' => '0.00',
            'bonus_amount' => '0.00',
            'game_token' => $user->gameSession->token ?? '',
            'currency' => $user->currency->symbol,
        ]);
    }

    protected function checkGameToken($user, $data): bool
    {
        $gameSession = $user->gameSession;
        return $gameSession && $gameSession->token === $data['user_game_token'];
    }

    protected function createB2bTransaction($user, $data, $type, $amount, $originalBalance): Transaction
    {
        $game = Cache::remember("b2b_game_code:{$data['game_code']}", 3600, function () use ($data) {
            return SlotegratorGame::where('game_code', $data['game_code'])->first();
        });

        $gameName = $game ? $game->name : $data['game_name'];

        return Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_id' => $user->currency_id,
            'type' => $type,
            'status' => 'success',
            'hash' => $data['transaction_id'],
            'context' => json_encode([
                'description' => ucfirst($type) . " in B2B game {$gameName}",
                'amount' => $amount,
                'game_code' => $data['game_code'],
                'game_name' => $data['game_name'],
                'balance_before' => $originalBalance,
                'balance_after' => $user->balance,
                'turn_id' => $data['turn_id'] ?? null,
                'round_id' => $data['round_id'] ?? null,
            ])
        ]);
    }
}
