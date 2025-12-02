<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\SlotegratorGame;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserGameHistory;
use App\Models\GameSession;
use App\Services\Tbs2\Tbs2Client;
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
use Illuminate\Support\Facades\Redis;

class Tbs2Controller extends Controller
{
    use Hashable;

    protected Tbs2Client $client;
    protected $transactionTimeout = 10;
    protected $logger;

    public function __construct(Tbs2Client $client)
    {
        $this->client = $client;
        $this->logger = Log::channel('single');
    }

    public function handleAnyRequest(Request $request)
    {
        $data = $this->getRequestData($request);

        Log::info('TBS2 Universal API Request', [
            'url' => $request->fullUrl(),
            'data' => $data
        ]);

        $cmd = $data['cmd'] ?? null;

        switch ($cmd) {
            case 'getBalance':
                return $this->getBalance($request);
            case 'writeBet':
                return $this->writeBet($request);
            default:
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Unknown command: ' . $cmd
                ]);
        }
    }

    public function launchGame($game)
    {
        try {
            $locale = App::getLocale();
            $game = Cache::remember("tbs2_game:{$game}", 3600, function () use ($game) {
                return SlotegratorGame::where('name', $game)
                    ->where('provider_type', 'tbs2')
                    ->firstOrFail();
            });

            $user = Auth::user();
            $sessionToken = Str::uuid()->toString();

            $user->gamesHistory()->create([
                'slotegrator_game_id' => $game->id,
                'session_token' => $sessionToken,
                'ip' => request()->ip(),
                'device' => (new Agent())->device(),
            ]);

            try {
                $response = $this->client->openGame([
                    'login' => $user->id,
                    'gameId' => $game->game_code,
                    'language' => $locale === 'az' ? 'en' : $locale,
                    'demo' => '0'
                ]);

                if ($response['status'] !== 'success') {
                    throw new Exception($response['error'] ?? 'Game opening failed');
                }

                if (isset($response['content']['gameRes']['sessionId'])) {
                    $serverSessionId = $response['content']['gameRes']['sessionId'];

                    $user->gameSession()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['token' => $serverSessionId]
                    );

                    Log::info('TBS2 Game session created', [
                        'user_id' => $user->id,
                        'session_id' => $serverSessionId,
                        'token' => $serverSessionId,
                        'game_id' => $game->game_code
                    ]);
                }

                $gameUrl = $response['content']['game']['url'] ?? null;

                if (!$gameUrl) {
                    return redirect()->back()->with('error', __('errors.game_unavailable'));
                }

                $iframe = $response['content']['game']['iframe'] ?? '1';
                $withoutFrame = $response['content']['game']['withoutFrame'] ?? '0';

                if ($withoutFrame === '1') {
                    return redirect($gameUrl);
                }

                $view = (new Agent())->isMobile() ? 'games.mobile' : 'games.play';
                return view($view, [
                    'game' => $game,
                    'gameUrl' => $gameUrl,
                    'iframe' => $iframe === '1',
                    'exitButton' => $response['content']['game']['exitButton'] ?? '0',
                    'exitButton_mobile' => $response['content']['game']['exitButton_mobile'] ?? '0'
                ]);

            } catch (\Exception $e) {
                Log::error('TBS2 game launch error', [
                    'game' => $game->name,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', __('Недоступно'));
            }

        } catch (\Exception $e) {
            Log::error('TBS2 game launch general error', [
                'game' => $game,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    public function launchDemoGame($game)
    {
        try {
            $locale = App::getLocale();
            $game = SlotegratorGame::where('name', $game)
                ->where('provider_type', 'tbs2')
                ->firstOrFail();

            try {
                $response = $this->client->openGame([
                    'login' => 'demo_' . time(),
                    'gameId' => $game->game_code,
                    'language' => $locale === 'az' ? 'en' : $locale,
                    'demo' => '1'
                ]);

                if ($response['status'] !== 'success') {
                    throw new Exception($response['error'] ?? 'Demo game opening failed');
                }

                $gameUrl = $response['content']['game']['url'] ?? null;

                if (!$gameUrl) {
                    return redirect()->back()->with('error', __('errors.game_unavailable'));
                }

                $withoutFrame = $response['content']['game']['withoutFrame'] ?? '0';

                if ($withoutFrame === '1') {
                    return redirect($gameUrl);
                }

                $agent = new Agent();
                $isMobile = $agent->isMobile();
                return view($isMobile ? 'games.mobile' : 'games.play', [
                    'game' => $game,
                    'gameUrl' => $gameUrl,
                    'iframe' => true
                ]);

            } catch (\Exception $e) {
                Log::error('TBS2 demo game launch error', [
                    'game' => $game->name,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', __('Недоступно'));
            }

        } catch (\Exception $e) {
            Log::error('TBS2 demo game general error', [
                'game' => $game,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    public function getBalance(Request $request)
    {
        Log::info('🚀 TBS2 getBalance called', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'all_headers' => $request->headers->all(),
            'raw_content' => $request->getContent(),
            'request_all' => $request->all(),
        ]);

        $data = $this->getRequestData($request);
        Log::info('📊 TBS2 getBalance data', $data);

        try {
            if ($data['hall'] !== config('services.tbs2.hall_id') ||
                $data['key'] !== config('services.tbs2.hall_key')) {
                Log::error('❌ Invalid hall or key', [
                    'expected_hall' => config('services.tbs2.hall_id'),
                    'received_hall' => $data['hall'] ?? 'MISSING',
                    'expected_key' => config('services.tbs2.hall_key'),
                    'received_key' => $data['key'] ?? 'MISSING'
                ]);
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Invalid hall or key'
                ]);
            }

            // ИСПРАВЛЕНИЕ: Загружаем пользователя с валютой
            $user = User::with('currency')->find($data['login']);
            if (!$user) {
                Log::error('❌ User not found', ['login' => $data['login'] ?? 'MISSING']);
                return response()->json($this->client->balanceErrorResponse('user_not_found'));
            }

            // ИСПРАВЛЕНИЕ: Проверяем наличие валюты
            if (!$user->currency) {
                Log::error('❌ User currency not found', [
                    'user_id' => $user->id,
                    'currency_id' => $user->currency_id ?? 'NULL'
                ]);
                return response()->json([
                    'status' => 'fail',
                    'error' => 'User currency not configured'
                ]);
            }

            if (isset($data['sessionId'])) {
                if ($errorDescription = $this->checkTokenValidity($user, $data['sessionId'])) {
                    Log::warning('❌ Invalid token in TBS2 getBalance', [
                        'user_id' => $user->id,
                        'error' => $errorDescription
                    ]);
                    return response()->json([
                        'status' => 'fail',
                        'error_code' => 'INVALID_SESSION',
                        'error_description' => "Token fail: {$errorDescription}"
                    ]);
                }
            }

            $response = $this->client->handleGetBalance([
                'login' => $user->id,
                'balance' => number_format($user->balance, 2, '.', ''),
                'currency' => $user->currency->symbol
            ]);

            Log::info('✅ TBS2 getBalance success', $response);
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('💥 TBS2 getBalance error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            return response()->json($this->client->balanceErrorResponse('user_not_found'));
        }
    }

    public function writeBet(Request $request)
    {
        Log::info('🚀 TBS2 writeBet called', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'all_headers' => $request->headers->all(),
            'raw_content' => $request->getContent(),
            'request_all' => $request->all(),
        ]);

        $data = $this->getRequestData($request);
        Log::info('💰 TBS2 writeBet data', $data);

        return DB::transaction(function () use ($data) {
            try {
                if ($data['hall'] !== config('services.tbs2.hall_id') ||
                    $data['key'] !== config('services.tbs2.hall_key')) {
                    Log::error('❌ Invalid hall or key in writeBet');
                    return response()->json($this->client->betErrorResponse('invalid_credentials'));
                }

                // ИСПРАВЛЕНИЕ: Загружаем пользователя с валютой и блокируем
                $user = User::with('currency')->lockForUpdate()->find($data['login']);
                if (!$user) {
                    Log::error('❌ User not found in writeBet', ['login' => $data['login'] ?? 'MISSING']);
                    return response()->json($this->client->betErrorResponse('user_not_found'));
                }

                // ИСПРАВЛЕНИЕ: Проверяем наличие валюты
                if (!$user->currency) {
                    Log::error('❌ User currency not found in writeBet', [
                        'user_id' => $user->id,
                        'currency_id' => $user->currency_id ?? 'NULL'
                    ]);
                    return response()->json($this->client->betErrorResponse('currency_not_configured'));
                }

                if (isset($data['sessionId'])) {
                    if ($errorDescription = $this->checkTokenValidity($user, $data['sessionId'])) {
                        Log::warning('❌ Invalid token in TBS2 writeBet', [
                            'user_id' => $user->id,
                            'error' => $errorDescription,
                            'session_id' => $data['sessionId']
                        ]);
                        return response()->json($this->client->betErrorResponse('invalid_session'));
                    }
                }

                $bet = (float)$data['bet'];
                $win = (float)$data['win'];

                Log::info('💸 TBS2 writeBet amounts', [
                    'user_id' => $user->id,
                    'current_balance' => $user->balance,
                    'bet' => $bet,
                    'win' => $win,
                    'trade_id' => $data['tradeId'] ?? 'MISSING',
                    'currency' => $user->currency->symbol
                ]);

                if (isset($data['tradeId']) && $this->isExistingTbs2Transaction($data['tradeId'])) {
                    Log::info('🔄 Transaction already exists', ['trade_id' => $data['tradeId']]);
                    return response()->json($this->client->handleWriteBet([
                        'login' => $user->id,
                        'balance' => number_format($user->balance, 2, '.', ''),
                        'currency' => $user->currency->symbol
                    ]));
                }

                if ($user->currency_id === 4 && $bet < 1) {
                    Log::warning('TBS2 Bet amount below minimum for TRY', [
                        'user_id' => $user->id,
                        'amount' => $bet,
                        'currency_id' => $user->currency_id
                    ]);
                    return response()->json([
                        'status' => 'fail',
                        'error_code' => 'INVALID_BET_AMOUNT',
                        'error_description' => 'Minimum bet amount is 1 TRY'
                    ]);
                }

                if ($user->balance < $bet) {
                    Log::error('❌ Insufficient balance', [
                        'user_balance' => $user->balance,
                        'bet_amount' => $bet
                    ]);
                    return response()->json($this->client->betErrorResponse('fail_balance'));
                }

                $originalBalance = $user->balance;

                if ($bet > 0) {
                    $user->balance -= $bet;
                    $user->save();

                    $betTransaction = $this->createTbs2Transaction($user, $data, $bet, 'bet', $originalBalance);
                    $this->storeTransactionInRedis($betTransaction, $user, $data);

                    if (method_exists($user, 'hasActiveWagering') && $user->hasActiveWagering()) {
                        $exchangeService = new ExchangeService();
                        $betAmountInAzn = ($user->currency->symbol !== 'AZN')
                            ? $exchangeService->convert($bet, $user->currency->symbol, 'AZN')
                            : $bet;
                        $user->addToWageringAmount($betAmountInAzn);
                    }
                }

                if ($win > 0) {
                    $user->balance += $win;
                    $user->save();

                    $winTransaction = $this->createTbs2Transaction($user, $data, $win, 'win', $originalBalance - $bet);
                    $this->storeTransactionInRedis($winTransaction, $user, $data);
                }

                Log::info('💳 TBS2 Balance updated', [
                    'balance_before' => $originalBalance,
                    'balance_after' => $user->balance,
                    'bet' => $bet,
                    'win' => $win
                ]);

                $response = $this->client->handleWriteBet([
                    'login' => $user->id,
                    'balance' => number_format($user->balance, 2, '.', ''),
                    'currency' => $user->currency->symbol
                ]);

                Log::info('✅ TBS2 writeBet success', $response);
                return response()->json($response);

            } catch (\Exception $e) {
                Log::error('💥 TBS2 writeBet error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $data
                ]);
                return response()->json($this->client->betErrorResponse('fail_response'));
            }
        }, $this->transactionTimeout);
    }

    private function getRequestData(Request $request): array
    {
        $contentType = $request->header('Content-Type', '');

        if (str_contains($contentType, 'application/json')) {
            $data = $request->json()->all();
            if (!empty($data)) {
                $this->logger->info('TBS2 received JSON request', $data);
                return $data;
            }
        }

        $data = $request->all();
        if (!empty($data)) {
            $this->logger->info('TBS2 received form-data request', $data);
            return $data;
        }

        $rawContent = $request->getContent();
        if (!empty($rawContent)) {
            $decoded = json_decode($rawContent, true);
            if ($decoded && json_last_error() === JSON_ERROR_NONE) {
                $this->logger->info('TBS2 parsed from raw content', $decoded);
                return $decoded;
            }

            parse_str($rawContent, $parsed);
            if (!empty($parsed)) {
                $this->logger->info('TBS2 parsed form-encoded data', $parsed);
                return $parsed;
            }
        }

        $this->logger->error('TBS2 failed to parse request data', [
            'content_type' => $contentType,
            'has_json' => $request->json() !== null,
            'request_all' => $request->all(),
            'raw_length' => strlen($rawContent)
        ]);

        return [];
    }

    public function closeGame()
    {
        return view('games.close');
    }

    protected function isExistingTbs2Transaction($tradeId): bool
    {
        $cacheKey = "tbs2_transaction_exists:{$tradeId}";
        return Cache::remember($cacheKey, 3600, function () use ($tradeId) {
            return Transaction::where('hash', 'like', $tradeId . '%')->exists();
        });
    }

    protected function createTbs2Transaction($user, $data, $amount, $type, $balanceBefore): Transaction
    {
        $game = Cache::remember("tbs2_game_id:{$data['gameId']}", 3600, function () use ($data) {
            return SlotegratorGame::where('game_code', $data['gameId'])->first();
        });

        $gameName = $game ? $game->name : "Game ID: {$data['gameId']}";

        return Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_id' => $user->currency_id,
            'type' => $type,
            'status' => 'success',
            'hash' => ($data['tradeId'] ?? Str::uuid()) . '_' . $type,
            'context' => json_encode([
                'description' => ucfirst($type) . " in TBS2 game {$gameName}",
                'amount' => $amount,
                'action' => $data['action'] ?? 'spin',
                'game_id' => $data['gameId'] ?? null,
                'session_id' => $data['sessionId'] ?? null,
                'balance_before' => $balanceBefore,
                'balance_after' => $type === 'win' ? $balanceBefore + $amount : $balanceBefore - $amount,
                'bet_info' => $data['betInfo'] ?? null,
                'matrix' => $data['matrix'] ?? null,
                'win_lines' => $data['WinLines'] ?? null,
                'round_finished' => $data['round_finished'] ?? null,
                'trade_id' => $data['tradeId'] ?? null,
            ])
        ]);
    }

    public function callback(Request $request)
    {
        $data = $request->all();

        Log::info('🚀 TBS2 Callback received', [
            'cmd' => $data['cmd'] ?? 'MISSING',
            'data' => $data,
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);

        try {
            switch ($data['cmd']) {
                case 'getBalance':
                    return $this->getBalanceCallback($data);

                case 'writeBet':
                    return $this->writeBetCallback($data);

                default:
                    throw new Exception("Unknown cmd: " . ($data['cmd'] ?? 'MISSING'));
            }

        } catch (Exception $e) {
            Log::error('💥 TBS2 Callback error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return response()->json([
                'status' => 'fail',
                'error' => 'Internal server error'
            ], 500);
        }
    }

    private function getBalanceCallback($data)
    {
        return DB::transaction(function () use ($data) {
            // ИСПРАВЛЕНИЕ: Загружаем пользователя с валютой
            $user = User::with('currency')->select('id', 'balance', 'currency_id')->lockForUpdate()->find($data['login']);

            if (!$user) {
                Log::error('❌ User not found in callback', ['login' => $data['login'] ?? 'MISSING']);
                return response()->json([
                    'status' => 'fail',
                    'error' => 'User not found'
                ]);
            }

            // ИСПРАВЛЕНИЕ: Проверяем наличие валюты
            if (!$user->currency) {
                Log::error('❌ User currency not found in callback', [
                    'user_id' => $user->id,
                    'currency_id' => $user->currency_id ?? 'NULL'
                ]);
                return response()->json([
                    'status' => 'fail',
                    'error' => 'User currency not configured'
                ]);
            }

            if (isset($data['sessionId'])) {
                if ($errorDescription = $this->checkTokenValidity($user, $data['sessionId'])) {
                    Log::warning('❌ Invalid token in TBS2 getBalance callback', [
                        'user_id' => $user->id,
                        'error' => $errorDescription
                    ]);
                    return response()->json([
                        'status' => 'fail',
                        'error_code' => 'INVALID_SESSION',
                        'error_description' => "Token fail: {$errorDescription}"
                    ]);
                }
            }

            return response()->json([
                "status" => "success",
                "error" => "",
                "login" => $user->id,
                "balance" => number_format($user->balance, 2, '.', ''),
                "currency" => $user->currency->symbol
            ]);
        });
    }

    private function writeBetCallback($data)
    {
        if ($data['key'] !== config('services.tbs2.hall_key')) {
            return response()->json(['status' => 'fail', 'error' => 'Invalid key']);
        }

        return DB::transaction(function () use ($data) {
            $user = User::lockForUpdate()->find($data['login']);

            if (!$user) {
                Log::error('❌ User not found in TBS2 callback', ['login' => $data['login'] ?? 'MISSING']);
                return response()->json(['status' => 'fail', 'error' => 'User not found']);
            }

            if (isset($data['sessionId'])) {
                if ($errorDescription = $this->checkTokenValidity($user, $data['sessionId'])) {
                    Log::warning('❌ Invalid token in TBS2 writeBet callback', [
                        'user_id' => $user->id,
                        'error' => $errorDescription
                    ]);
                    return response()->json([
                        'status' => 'fail',
                        'error_code' => 'INVALID_SESSION',
                        'error_description' => "Token fail: {$errorDescription}"
                    ]);
                }
            }

            $bet = floatval($data['bet']);
            $win = floatval($data['win']);
            $tradeId = $data['tradeId'] ?? Str::uuid();

            if ($this->isExistingTbs2Transaction($tradeId)) {
                Log::info('🔄 TBS2 Callback: Transaction already exists', ['trade_id' => $tradeId]);
                return response()->json($this->getExistingTransactionResponse($user, $tradeId));
            }

            Log::info('💸 TBS2 Callback: Processing bet', [
                'user_id' => $user->id,
                'balance_before' => $user->balance,
                'bet' => $bet,
                'win' => $win,
                'trade_id' => $tradeId
            ]);

            if ($user->balance < $bet) {
                return response()->json(['status' => 'fail', 'error' => 'Insufficient balance']);
            }

            $originalBalance = $user->balance;

            if ($bet > 0) {
                $user->balance -= $bet;
                $user->save();

                $betTransaction = $this->createTbs2Transaction($user, $data, $bet, 'bet', $originalBalance);
                $this->storeTransactionInRedis($betTransaction, $user, $data);

                if ($user->hasActiveWagering()) {
                    $exchangeService = new ExchangeService();
                    $betAmountInAzn = ($user->currency->symbol !== 'AZN')
                        ? $exchangeService->convert($bet, $user->currency->symbol, 'AZN')
                        : $bet;
                    $user->addToWageringAmount($betAmountInAzn);
                }
            }

            if ($win > 0) {
                $user->balance += $win;
                $user->save();

                $winTransaction = $this->createTbs2Transaction($user, $data, $win, 'win', $originalBalance - $bet);
                $this->storeTransactionInRedis($winTransaction, $user, $data);
            }

            Log::info('💳 TBS2 Callback: Balance updated', [
                'balance_before' => $originalBalance,
                'balance_after' => $user->balance
            ]);

            return response()->json([
                "status" => "success",
                "error" => "",
                "login" => $user->id,
                "balance" => number_format($user->balance, 2, '.', ''),
                "currency" => $user->currency->symbol
            ]);
        }, $this->transactionTimeout);
    }

    private function getExistingTransactionResponse($user, $tradeId)
    {
        return [
            'status' => 'success',
            'login' => $user->id,
            'balance' => number_format($user->balance, 2, '.', ''),
            'currency' => $user->currency->symbol,
            'transaction_id' => $this->hash($tradeId)
        ];
    }

    protected function storeTransactionInRedis($transaction, $user, $data)
    {
        $transactionData = [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'amount' => $transaction->amount,
            'currency_id' => $user->currency_id,
            'type' => $transaction->type,
            'status' => $transaction->status,
            'hash' => $transaction->hash,
            'context' => json_decode($transaction->context, true),
            'user' => [
                'id' => $user->id,  // ← Добавь это поле!
                'username' => $user->username,
                'avatar' => $user->avatar ?? '/assets/images/avatar-placeholder.png',
            ],
            'currency' => [
                'symbol' => $user->currency->symbol,
            ],
            'game_name' => $data['gameId'] ?? 'TBS2 Game',
            'created_at' => $transaction->created_at->toDateTimeString(),
        ];

        $transactionJson = json_encode($transactionData);

        try {
            Redis::pipeline(function ($pipe) use ($transaction, $user, $transactionJson) {
                $pipe->set("transaction:{$transaction->hash}", $transactionJson);
                $pipe->expire("transaction:{$transaction->hash}", 3600);
                $pipe->lpush("transactions:all", $transaction->hash);
                $pipe->ltrim("transactions:all", 0, 99);
                $pipe->lpush("transactions:user:{$user->id}", $transaction->hash);
                $pipe->ltrim("transactions:user:{$user->id}", 0, 99);
            });

            $transactionDataGames = $transactionData;
            $transactionDataGames['round_id'] = $data['tradeId'] ?? null;
            $transactionDataGames['finished'] = $data['round_finished'] ?? null;
            // ← ПРОБЛЕМА ТУТ: $transactionDataGames уже содержит правильные user данные
            // но что-то их перезаписывает или теряет

            $transactionJsonGames = json_encode($transactionDataGames);
            Redis::pipeline(function ($pipe) use ($transactionJsonGames) {
                $pipe->lpush("game_history:all", $transactionJsonGames);
                $pipe->ltrim("game_history:all", 0, 500);
            });

            // Добавим отладку
            Log::info('✅ TBS2 Transaction stored in Redis', [
                'transaction_id' => $transaction->id,
                'hash' => $transaction->hash,
                'type' => $transaction->type,
                'user_id_in_data' => $transactionDataGames['user']['id'] ?? 'missing',
                'game_name' => $transactionDataGames['game_name'] ?? 'missing'
            ]);

        } catch (\Exception $e) {
            Log::error('💥 TBS2 Redis storage error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id
            ]);
        }
    }
    protected function checkTokenValidity($user, $sessionId): ?string
    {
        if (!$sessionId) {
            return 'Missing session ID';
        }

        $gameSession = $user->gameSession()->select('token')->first();
        return (!$gameSession || $gameSession->token !== $sessionId) ? 'Invalid game session' : null;
    }

    protected function errorResponse($message, $errorCode = 'INTERNAL_ERROR')
    {
        return [
            'status' => 'fail',
            'error_code' => $errorCode,
            'error_description' => $message
        ];
    }

    public function limits()
    {
        try {
            $response = $this->client->getLimits();
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('TBS2 limits error', ['error' => $e->getMessage()]);
            return response()->json($this->errorResponse('Failed to get limits'));
        }
    }

    public function gameInfo($gameCode)
    {
        try {
            $game = SlotegratorGame::where('game_code', $gameCode)
                ->where('provider_type', 'tbs2')
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'game' => [
                    'id' => $game->id,
                    'name' => $game->name,
                    'code' => $game->game_code,
                    'provider' => $game->provider,
                    'type' => $game->type,
                    'image' => $game->image,
                    'is_active' => $game->is_active,
                    'is_mobile' => $game->is_mobile ?? true,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Game not found', 'GAME_NOT_FOUND'));
        }
    }

    public function availableGames()
    {
        try {
            $games = SlotegratorGame::where('provider_type', 'tbs2')
                ->where('is_active', true)
                ->select('id', 'name', 'game_code', 'provider', 'image', 'type')
                ->get();

            return response()->json([
                'status' => 'success',
                'count' => $games->count(),
                'games' => $games
            ]);
        } catch (\Exception $e) {
            Log::error('TBS2 available games error', ['error' => $e->getMessage()]);
            return response()->json($this->errorResponse('Failed to get games list'));
        }
    }

    public function validateSession(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $userId = $request->input('user_id');

            if (!$sessionId || !$userId) {
                return response()->json($this->errorResponse('Missing session_id or user_id'));
            }

            $user = User::find($userId);
            if (!$user) {
                return response()->json($this->errorResponse('User not found', 'USER_NOT_FOUND'));
            }

            $errorDescription = $this->checkTokenValidity($user, $sessionId);
            if ($errorDescription) {
                return response()->json($this->errorResponse($errorDescription, 'INVALID_SESSION'));
            }

            return response()->json([
                'status' => 'success',
                'valid' => true,
                'user_id' => $user->id,
                'balance' => number_format($user->balance, 2, '.', ''),
                'currency' => $user->currency->symbol
            ]);

        } catch (\Exception $e) {
            Log::error('TBS2 session validation error', ['error' => $e->getMessage()]);
            return response()->json($this->errorResponse('Session validation failed'));
        }
    }

    public function selfValidate()
    {
        try {
            $result = $this->client->selfValidate();
            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'provider' => 'TBS2',
                    'result' => $result
                ]);
            } else {
                return response()->json($this->errorResponse('No response from TBS2 server'), 500);
            }
        } catch (\Exception $e) {
            Log::error('TBS2 self validation error', ['error' => $e->getMessage()]);
            return response()->json($this->errorResponse('Self validation failed'), 500);
        }
    }

    public function closeSession(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json($this->errorResponse('User not authenticated'));
            }

            $user->gameSession()->delete();

            Log::info('TBS2 session closed', ['user_id' => $user->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Session closed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('TBS2 close session error', ['error' => $e->getMessage()]);
            return response()->json($this->errorResponse('Failed to close session'));
        }
    }

    public function rollback(Request $request)
    {
        try {
            $data = $this->getRequestData($request);

            Log::info('TBS2 Rollback received', $data);

            return DB::transaction(function () use ($data) {
                $user = User::lockForUpdate()->find($data['login']);
                if (!$user) {
                    return response()->json($this->errorResponse('User not found', 'USER_NOT_FOUND'));
                }

                $tradeId = $data['tradeId'] ?? null;
                if (!$tradeId) {
                    return response()->json($this->errorResponse('Missing trade ID'));
                }

                $transactions = Transaction::where('hash', 'like', $tradeId . '%')->get();

                $balanceBefore = $user->balance;
                $rolledBackAmount = 0;

                foreach ($transactions as $transaction) {
                    if ($transaction->type === 'bet') {
                        $user->balance += $transaction->amount;
                        $rolledBackAmount += $transaction->amount;
                    } elseif ($transaction->type === 'win') {
                        $user->balance -= $transaction->amount;
                        $rolledBackAmount -= $transaction->amount;
                    }

                    $transaction->update([
                        'type' => 'rollback',
                        'context' => json_encode(array_merge(
                            json_decode($transaction->context, true),
                            ['rollback' => true, 'rollback_at' => now()]
                        ))
                    ]);
                }

                $user->save();

                Log::info('TBS2 Rollback completed', [
                    'user_id' => $user->id,
                    'trade_id' => $tradeId,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $user->balance,
                    'rolled_back_amount' => $rolledBackAmount
                ]);

                return response()->json([
                    'status' => 'success',
                    'login' => $user->id,
                    'balance' => number_format($user->balance, 2, '.', ''),
                    'currency' => $user->currency->symbol
                ]);
            });

        } catch (\Exception $e) {
            Log::error('TBS2 Rollback error', ['error' => $e->getMessage()]);
            return response()->json($this->errorResponse('Rollback failed'));
        }
    }

    public function gameStats()
    {
        try {
            $stats = [
                'total_games' => SlotegratorGame::where('provider_type', 'tbs2')->count(),
                'active_games' => SlotegratorGame::where('provider_type', 'tbs2')->where('is_active', true)->count(),
                'total_sessions' => UserGameHistory::whereHas('game', function($q) {
                    $q->where('provider_type', 'tbs2');
                })->count(),
                'today_sessions' => UserGameHistory::whereHas('game', function($q) {
                    $q->where('provider_type', 'tbs2');
                })->whereDate('created_at', now())->count(),
            ];

            return response()->json([
                'status' => 'success',
                'provider' => 'TBS2',
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('TBS2 stats error', ['error' => $e->getMessage()]);
            return response()->json($this->errorResponse('Failed to get stats'));
        }
    }

    public function launchGameByCode($gameCode)
    {
        try {
            $locale = App::getLocale();
            $game = Cache::remember("tbs2_game_code:{$gameCode}", 3600, function () use ($gameCode) {
                return SlotegratorGame::where('game_code', $gameCode)
                    ->where('provider_type', 'tbs2')
                    ->firstOrFail();
            });

            $user = Auth::user();
            $sessionToken = Str::uuid()->toString();

            $user->gamesHistory()->create([
                'slotegrator_game_id' => $game->id,
                'session_token' => $sessionToken,
                'ip' => request()->ip(),
                'device' => (new Agent())->device(),
            ]);

            try {
                $response = $this->client->openGame([
                    'login' => $user->id,
                    'gameId' => $game->game_code,
                    'language' => $locale === 'az' ? 'en' : $locale,
                    'demo' => '0'
                ]);

                if ($response['status'] !== 'success') {
                    throw new Exception($response['error'] ?? 'Game opening failed');
                }

                if (isset($response['content']['gameRes']['sessionId'])) {
                    $serverSessionId = $response['content']['gameRes']['sessionId'];

                    $user->gameSession()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['token' => $serverSessionId]
                    );

                    Log::info('TBS2 Game session created', [
                        'user_id' => $user->id,
                        'session_id' => $serverSessionId,
                        'token' => $serverSessionId,
                        'game_code' => $game->game_code
                    ]);
                }

                $gameUrl = $response['content']['game']['url'] ?? null;

                if (!$gameUrl) {
                    return redirect()->back()->with('error', __('errors.game_unavailable'));
                }

                $iframe = $response['content']['game']['iframe'] ?? '1';
                $withoutFrame = $response['content']['game']['withoutFrame'] ?? '0';

                if ($withoutFrame === '1') {
                    return redirect($gameUrl);
                }

                $view = (new Agent())->isMobile() ? 'games.mobile' : 'games.play';
                return view($view, [
                    'game' => $game,
                    'gameUrl' => $gameUrl,
                    'iframe' => $iframe === '1',
                    'exitButton' => $response['content']['game']['exitButton'] ?? '0',
                    'exitButton_mobile' => $response['content']['game']['exitButton_mobile'] ?? '0'
                ]);

            } catch (\Exception $e) {
                Log::error('TBS2 game launch error', [
                    'game_code' => $gameCode,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', __('Недоступно'));
            }

        } catch (\Exception $e) {
            Log::error('TBS2 game launch general error', [
                'game_code' => $gameCode,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    public function launchDemoGameByCode($gameCode)
    {
        try {
            $locale = App::getLocale();
            $game = Cache::remember("tbs2_game_code:{$gameCode}", 3600, function () use ($gameCode) {
                return SlotegratorGame::where('game_code', $gameCode)
                    ->where('provider_type', 'tbs2')
                    ->firstOrFail();
            });

            try {
                $response = $this->client->openGame([
                    'login' => 'demo_' . time(),
                    'gameId' => $game->game_code,
                    'language' => $locale === 'az' ? 'en' : $locale,
                    'demo' => '1'
                ]);

                if ($response['status'] !== 'success') {
                    throw new Exception($response['error'] ?? 'Demo game opening failed');
                }

                $gameUrl = $response['content']['game']['url'] ?? null;

                if (!$gameUrl) {
                    return redirect()->back()->with('error', __('errors.game_unavailable'));
                }

                $withoutFrame = $response['content']['game']['withoutFrame'] ?? '0';

                if ($withoutFrame === '1') {
                    return redirect($gameUrl);
                }

                $agent = new Agent();
                $isMobile = $agent->isMobile();
                return view($isMobile ? 'games.mobile' : 'games.play', [
                    'game' => $game,
                    'gameUrl' => $gameUrl,
                    'iframe' => true
                ]);

            } catch (\Exception $e) {
                Log::error('TBS2 demo game launch error', [
                    'game_code' => $gameCode,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', __('Недоступно'));
            }

        } catch (\Exception $e) {
            Log::error('TBS2 demo game general error', [
                'game_code' => $gameCode,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }
}
