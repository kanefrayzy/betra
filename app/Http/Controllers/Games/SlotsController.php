<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\SlotegratorGame;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserGameHistory;
use App\Services\ExchangeService;
use App\Services\Slotegrator\SlotegratorClient;
use App\Traits\Hashable;
use App\Enums\TransactionType;
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

class SlotsController extends Controller
{
    use Hashable;

    protected SlotegratorClient $client;
    protected $transactionTimeout = 2;
    protected $logger;

    public function __construct(SlotegratorClient $client)
    {
        $this->client = $client;
        $this->logger = Log::channel('slots');
    }

    public function limits()
    {
        $response = $this->client->get('/games/limits');
        return response()->json($response);
    }

    public function initGame(array $data)
    {
        return $this->client->post('/games/init', $data);
    }

    protected function initDemoGame(array $data)
    {
        return $this->client->post('/games/init-demo', $data);
    }

    public function launchGame($game)
    {
        try {
            $locale = App::getLocale();
            $game = Cache::remember("game:{$game}", 3600, function () use ($game) {
                return SlotegratorGame::where('name', $game)->firstOrFail();
            });

            $user = Auth::user();
            $sessionToken = Str::uuid()->toString();

            $user->gamesHistory()->create([
                'slotegrator_game_id' => $game->id,
                'session_token' => $sessionToken,
                'ip' => request()->ip(),
                'device' => (new Agent())->device(),
            ]);

            $locale = $locale === 'az' ? 'tr' : $locale;

            try {
                $response = $this->initGame([
                    'game_uuid' => $game->uuid,
                    'player_id' => $user->id,
                    'player_name' => $user->username,
                    'currency' => $user->currency->symbol,
                    'session_id' => $sessionToken,
                    'language' => $locale,
                ]);

            $user->gameSession()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'token' => $sessionToken,
                    'game_uuid' => $game->uuid,
                    'currency' => $user->currency->symbol,
                ]
            );                $gameUrl = $response['url'] ?? null;

                if (!$gameUrl) {
                    return redirect()->back()->with('error', __('errors.game_unavailable'));
                }

                $view = (new Agent())->isMobile() ? 'games.mobile' : 'games.play';
                return view($view, ['game' => $game, 'gameUrl' => $gameUrl]);

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return redirect()->back()->with('error', __('Недоступно'));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    public function launchDemoGame($game)
    {
        $locale = App::getLocale();
        $game = SlotegratorGame::where('name', $game)->firstOrFail();
        $locale = $locale === 'az' ? 'tr' : $locale;
        $return_url = route('home');

        $response = $this->initDemoGame([
            'game_uuid' => $game->uuid,
            'return_url' => $return_url,
            'language' => $locale,
        ]);

        $gameUrl = is_array($response) ? $response['url'] : null;

        $agent = new Agent();
        $isMobile = $agent->isMobile();
        return view($isMobile ? 'games.mobile' : 'games.play', ['game' => $game, 'gameUrl' => $gameUrl]);
    }

    /**
     * Прямой запуск игры (принимает объект SlotegratorGame)
     */
    public function launchGameDirect(SlotegratorGame $game)
    {
        try {
            $locale = App::getLocale();
            $user = Auth::user();
            $sessionToken = Str::uuid()->toString();

            $user->gamesHistory()->create([
                'slotegrator_game_id' => $game->id,
                'session_token' => $sessionToken,
                'ip' => request()->ip(),
                'device' => (new Agent())->device(),
            ]);

            $locale = $locale === 'az' ? 'tr' : $locale;

            try {
                $response = $this->initGame([
                    'game_uuid' => $game->uuid,
                    'player_id' => $user->id,
                    'player_name' => $user->username,
                    'currency' => $user->currency->symbol,
                    'session_id' => $sessionToken,
                    'return_url' => route('home'),
                    'language' => $locale,
                ]);

                $user->gameSession()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'token' => $sessionToken,
                        'game_uuid' => $game->uuid,
                        'currency' => $user->currency->symbol,
                    ]
                );

                $gameUrl = $response['url'] ?? null;

                if (!$gameUrl) {
                    return redirect()->back()->with('error', __('Недоступно'));
                }

                $view = (new Agent())->isMobile() ? 'games.mobile' : 'games.play';
                return view($view, ['game' => $game, 'gameUrl' => $gameUrl]);

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return redirect()->back()->with('error', __('Недоступно'));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    /**
     * Прямой запуск демо игры (принимает объект SlotegratorGame)
     */
    public function launchDemoGameDirect(SlotegratorGame $game)
    {
        $locale = App::getLocale();
        $locale = $locale === 'az' ? 'tr' : $locale;
        $return_url = route('home');

        $response = $this->initDemoGame([
            'game_uuid' => $game->uuid,
            'return_url' => $return_url,
            'language' => $locale,
        ]);

        $gameUrl = is_array($response) ? $response['url'] : null;

        $agent = new Agent();
        $isMobile = $agent->isMobile();
        return view($isMobile ? 'games.mobile' : 'games.play', ['game' => $game, 'gameUrl' => $gameUrl]);
    }

    public function callback(Request $request)
    {
        $startTime = microtime(true);
        $action = $request->input('action');
        $data = $request->all();

        // Проверка существования пользователя
        $userExists = Cache::remember("user_exists:{$data['player_id']}", 3600, function () use ($data) {
            return User::where('id', $data['player_id'])->exists();
        });

        if (!$userExists) {
            return $this->errorResponse('Player not found');
        }

        try {
            // Верификация подписи
            $this->client->verifySignature($request->headers->all(), $data);
            
            // Проверяем/создаём сессию при первом callback
            if (isset($data['session_id'])) {
                $user = User::find($data['player_id']);
                
                $session = DB::table('game_sessions')
                    ->where('user_id', $data['player_id'])
                    ->where('token', $data['session_id'])
                    ->first();
                    
                if (!$session) {
                    // Создаём сессию автоматически при первом callback от Slotegrator
                    $this->logger->info('Auto-creating session from callback', [
                        'player_id' => $data['player_id'],
                        'session_id' => $data['session_id'],
                        'game_uuid' => $data['game_uuid'] ?? null,
                        'currency' => $data['currency'],
                        'action' => $action
                    ]);
                    
                    $user->gameSession()->updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'token' => $data['session_id'],
                            'game_uuid' => $data['game_uuid'] ?? null,
                            'currency' => $data['currency'],
                        ]
                    );
                    
                    $session = (object)[
                        'currency' => $data['currency'],
                        'game_uuid' => $data['game_uuid'] ?? null
                    ];
                }
                
                // Проверка валюты сессии (защита от смены валюты во время игры)
                if ($session->currency && $data['currency'] !== $session->currency) {
                    $this->logger->error('Currency mismatch with session', [
                        'session_currency' => $session->currency,
                        'transaction_currency' => $data['currency'],
                        'player_id' => $data['player_id'],
                        'session_id' => $data['session_id']
                    ]);
                    return $this->errorResponse('Currency mismatch');
                }
            }
            
            $result = $this->processCallbackWithRetry($action, $data);
            $endTime = microtime(true);
            
            return $result;
        } catch (Exception $e) {
            $this->logger->error('Error processing callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Internal server error');
        }
    }

    protected function processCallbackWithRetry($action, $data, $attempts = 3)
    {
        for ($i = 0; $i < $attempts; $i++) {
            try {
                $result = match ($action) {
                    'balance' => $this->getBalance($data),
                    'bet', 'win' => $this->handleTransaction($data, $action),
                    'refund' => $this->refund($data),
                    'rollback' => $this->rollback($data),
                    default => $this->errorResponse('Unknown action')
                };

                if ($result instanceof \Illuminate\Http\JsonResponse && $result->getStatusCode() == 200) {
                    return $result;
                }
            } catch (Exception $e) {

                if ($i == $attempts - 1) {
                    throw $e;
                }
                sleep(1);
            }
        }
        return $this->errorResponse("Failed to process after {$attempts} attempts");
    }

    protected function getBalance($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::select('id', 'balance')->lockForUpdate()->findOrFail($data['player_id']);

            return response()->json([
                'balance' => round($user->balance, 2),
            ]);
        });
    }

    protected function handleTransaction($data, $type)
    {
        $startTime = microtime(true);
        
        // Логируем новые опциональные параметры
        if (isset($data['transaction_datetime'])) {
            $this->logger->debug('Transaction datetime provided', [
                'transaction_datetime' => $data['transaction_datetime'],
                'type' => $type
            ]);
        }
        
        if (isset($data['casino_request_retry_count'])) {
            $this->logger->info('Retry attempt detected', [
                'retry_count' => $data['casino_request_retry_count'],
                'transaction_id' => $data['transaction_id'] ?? 'unknown'
            ]);
        }
        
        return DB::transaction(function () use ($data, $type, $startTime) {
            $amount = round($data['amount'], 2);
            $user = User::lockForUpdate()->findOrFail($data['player_id']);

            if ($data['currency'] !== $user->currency->symbol) {
                $this->logger->error('Currency mismatch', [
                    'user_currency' => $user->currency->symbol,
                    'transaction_currency' => $data['currency'],
                    'user_id' => $user->id
                ]);
                return $this->errorResponse('Currency mismatch');
            }

            if ($this->isExistingTransaction($data['transaction_id'])) {
                return $this->getExistingTransactionResponse($user, $data['transaction_id']);
            }

            if ($type === 'bet' && $user->balance < $amount) {
                return $this->insufficientFundsResponse();
            }

            $originalBalance = $user->balance;
            $this->updateUserBalance($user, $type, $amount);

            $transaction = $this->createTransaction($user, $data, $type, $amount, $originalBalance);

            $this->storeTransactionInRedis($transaction, $user, $data);

            $endTime = microtime(true);

            return $this->getTransactionResponse($user, $transaction);
        }, $this->transactionTimeout);
    }

    protected function refund($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::lockForUpdate()->findOrFail($data['player_id']);
            
            // Проверка на дубликат refund
            if ($this->isExistingTransaction($data['transaction_id'])) {
                $this->logger->info('Duplicate refund detected', [
                    'refund_transaction_id' => $data['transaction_id']
                ]);
                return $this->getExistingTransactionResponse($user, $data['transaction_id']);
            }

            // Ищем ОРИГИНАЛЬНУЮ транзакцию
            $originalTransaction = Transaction::where('hash', $data['bet_transaction_id'])->first();

            if (!$originalTransaction) {
                $this->logger->warning('Original transaction not found for refund', [
                    'bet_transaction_id' => $data['bet_transaction_id'],
                    'refund_transaction_id' => $data['transaction_id']
                ]);
                $refundTransaction = $this->createRefundTransactionForNonExistentBet($user, $data);
                return $this->getTransactionResponse($user, $refundTransaction);
            }

            $amount = round($data['amount'], 2);
            $originalBalance = $user->balance;
            
            // КРИТИЧЕСКИ: Определяем направление refund на основе типа оригинальной транзакции
            if ($originalTransaction->type === TransactionType::Bet) {
                // Возврат ставки - возвращаем деньги
                $user->balance += $amount;
                $this->logger->info('Refund for BET', [
                    'original_tx' => $data['bet_transaction_id'],
                    'amount' => $amount,
                    'balance_before' => $originalBalance,
                    'balance_after' => $user->balance
                ]);
            } elseif ($originalTransaction->type === TransactionType::Win) {
                // Отмена выигрыша - забираем деньги
                $user->balance -= $amount;
                $this->logger->info('Refund for WIN', [
                    'original_tx' => $data['bet_transaction_id'],
                    'amount' => $amount,
                    'balance_before' => $originalBalance,
                    'balance_after' => $user->balance
                ]);
            } else {
                $this->logger->error('Refund for unknown type', [
                    'original_type' => $originalTransaction->type,
                    'original_tx' => $data['bet_transaction_id']
                ]);
            }
            
            $user->save();

            $transaction = $this->createTransaction($user, $data, 'refund', $amount, $originalBalance);
            $this->storeTransactionInRedis($transaction, $user, $data);

            return $this->getTransactionResponse($user, $transaction);
        });
    }


    private function createRefundTransactionForNonExistentBet($user, $data)
    {
        //  сохраняем refund без изменения баланса
        // (т.к. ставки не было, значит деньги не списывались)
        return Transaction::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'currency_id' => $user->currency_id,
            'type' => 'refund',
            'status' => 'success',
            'hash' => $data['transaction_id'],
            'context' => json_encode([
                'description' => 'Refund for non-existent bet',
                'bet_transaction_id' => $data['bet_transaction_id'],
                'amount' => $data['amount'],
                'balance_before' => $user->balance,
                'balance_after' => $user->balance,
                'note' => 'No balance change - original bet not found'
            ])
        ]);
    }

    protected function isExistingTransaction($transactionId)
    {
        // НЕ кешируем проверку существования - иначе повторные запросы создадут дубликаты
        return Transaction::where('hash', $transactionId)->exists();
    }

    private function getExistingTransactionResponse($user, $transactionId)
    {
        $transaction = Transaction::where('hash', $transactionId)->first();
        
        return response()->json([
            'balance' => round($user->balance, 2),
            'transaction_id' => $transaction ? $this->hash($transaction->id) : $this->hash($transactionId)
        ]);
    }

    private function insufficientFundsResponse()
    {
        return response()->json([
            'error_code' => 'INSUFFICIENT_FUNDS',
            'error_description' => 'Not enough money to continue playing'
        ]);
    }

    private function updateUserBalance($user, $type, $amount)
    {
        $balanceChange = ($type === 'bet') ? -$amount : $amount;
        $user->balance += $balanceChange;
        $user->save();

    }

    private function getTransactionResponse($user, $transaction)
    {
        return response()->json([
            'balance' => round($user->balance, 2),
            'transaction_id' => $this->hash($transaction->id)
        ]);
    }

    protected function createTransaction($user, $data, $type, $amount, $originalBalance)
    {
        // Валидация типов выигрышей
        $validWinTypes = [
            'win', 'jackpot', 'freespin', 'bonus',
            'pragmatic_prize_drop', 'pragmatic_tournament',
            'promo', 'prize_drop', 'tournament',
            'unaccounted_promo', 'loyalty_win'
        ];
        
        if ($type === 'win' && isset($data['type']) && !in_array($data['type'], $validWinTypes)) {
            $this->logger->warning('Unknown win type detected', [
                'win_type' => $data['type'],
                'game_uuid' => $data['game_uuid'] ?? 'unknown'
            ]);
        }
        
        $game = Cache::remember("game_uuid:{$data['game_uuid']}", 3600, function () use ($data) {
            return SlotegratorGame::where('uuid', $data['game_uuid'])->first();
        });
        $gameName = $game ? $game->name : $data['game_uuid'];
        return Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_id' => $user->currency_id,
            'type' => $type,
            'status' => 'success',
            'hash' => $data['transaction_id'],
            'context' => json_encode([
                'description' => ucfirst($type) . " in game {$gameName}",
                'amount' => $amount,
                'session_token' => $data['session_id'],
                'balance_before' => $originalBalance,
                'balance_after' => $user->balance,
                'bet_transaction_id' => $data['bet_transaction_id'] ?? null,
            ])
        ]);
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
                'username' => $user->username,
                'avatar' => $user->avatar ?? '/assets/images/avatar-placeholder.png',
            ],
            'currency' => [
                'symbol' => $user->currency->symbol,
            ],
            'game_name' => $data['game_uuid'],
            'created_at' => $transaction->created_at->toDateTimeString(),
        ];

        $transactionJson = json_encode($transactionData);

        Redis::pipeline(function ($pipe) use ($transaction, $user, $transactionJson) {
            $pipe->set("transaction:{$transaction->hash}", $transactionJson);
            $pipe->expire("transaction:{$transaction->hash}", 3600);
            $pipe->lpush("transactions:all", $transaction->hash);
            $pipe->ltrim("transactions:all", 0, 99);
            $pipe->lpush("transactions:user:{$user->id}", $transaction->hash);
            $pipe->ltrim("transactions:user:{$user->id}", 0, 99);
        });

        $transactionDataGames = $transactionData;
        $transactionDataGames['round_id'] = $data['round_id'] ?? null ;
        $transactionDataGames['finished'] = $data['finished'] ?? null ;

        $transactionJsonGames = json_encode($transactionDataGames);

        Redis::pipeline(function ($pipe) use ($transactionJsonGames) {
            $pipe->lpush("game_history:all", $transactionJsonGames);
            $pipe->ltrim("game_history:all", 0, 500);
        });

    }

    protected function rollback($data)
    {
        return DB::transaction(function () use ($data) {
            $rollbackTransactions = $data['rollback_transactions'];
            $answerArrRollbackTransactions = [];
            $user = User::lockForUpdate()->findOrFail($data['player_id']);
            $balance_before = round($user->balance, 2);

            $existingRollbackTransaction = Transaction::where('hash', $data['transaction_id'])->first();
            if ($existingRollbackTransaction) {
                $context = json_decode($existingRollbackTransaction->context, true);
                $rollbackTransactions = $context['rollback_transactions'] ?? [];

                return response()->json([
                    'balance' => round($user->balance, 2),
                    'transaction_id' => $this->hash($existingRollbackTransaction->id),
                    'rollback_transactions' => $rollbackTransactions,
                ]);
            }

            foreach ($rollbackTransactions as $transactionData) {
                $transaction = Transaction::where('hash', $transactionData['transaction_id'])->first();

                if (!$transaction) {
                    $this->createRollbackTransaction($user, $transactionData, $data['player_id']);
                    $answerArrRollbackTransactions[] = $transactionData['transaction_id'];
                    continue;
                }

                if ($transaction->amount != 0) {
                    $this->processRollbackForTransaction($user, $transaction);
                    $answerArrRollbackTransactions[] = $transactionData['transaction_id'];
                }
            }

            $rollbackTransaction = $this->createFinalRollbackTransaction($user, $data, $balance_before, $answerArrRollbackTransactions);

            return response()->json([
                'balance' => round($user->balance, 2),
                'transaction_id' => $this->hash($rollbackTransaction->id),
                'rollback_transactions' => $answerArrRollbackTransactions,
            ]);
        });
    }

    protected function createRollbackTransaction($user, $transactionData, $playerId)
    {
        Transaction::create([
            'user_id' => $playerId,
            'amount' => 0,
            'currency_id' => $user->currency_id,
            'type' => 'rollback',
            'status' => 'success',
            'hash' => $transactionData['transaction_id'],
            'context' => json_encode([
                'description' => 'Rollbacked transaction',
                'amount' => 0,
            ]),
        ]);
    }

    protected function processRollbackForTransaction($user, $transaction)
    {
        $amount = round($transaction->amount, 2);
        
        // Откатываем баланс в зависимости от ИСХОДНОГО типа транзакции
        if ($transaction->type === TransactionType::Bet) {
            $user->balance += $amount; // Возвращаем ставку
        } elseif ($transaction->type === TransactionType::Win) {
            $user->balance -= $amount; // Забираем выигрыш
        } elseif ($transaction->type === TransactionType::Refund) {
            $user->balance -= $amount; // Отменяем возврат
        }
        $user->save();

        // ВАЖНО: НЕ меняем type! Только помечаем в context
        $context = json_decode($transaction->context, true) ?? [];
        $context['rollback'] = true;
        $context['rollback_at'] = now()->toDateTimeString();
        $context['original_amount'] = $amount;
        
        $transaction->update([
            'amount' => 0, // Обнуляем сумму
            'status' => 'rollback', // Меняем статус
            'context' => json_encode($context)
        ]);
    }

    protected function createFinalRollbackTransaction($user, $data, $balance_before, $answerArrRollbackTransactions)
    {
        return Transaction::create([
            'user_id' => $user->id,
            'amount' => 0,
            'currency_id' => $user->currency_id,
            'type' => 'rollback',
            'status' => 'success',
            'hash' => $data['transaction_id'],
            'context' => json_encode([
                'description' => 'Rollback transaction',
                'balance_before' => $balance_before,
                'balance_after' => round($user->balance, 2),
                'rollback_transactions' => $answerArrRollbackTransactions,
            ])
        ]);
    }

    protected function errorResponse($message)
    {
        return response()->json([
            'error_code' => 'INTERNAL_ERROR',
            'error_description' => $message
        ]);
    }

    public function selfValidate()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Authentication required for self-validation',
                    'hint' => 'Please login first'
                ], 401);
            }

            // Проверяем, есть ли игры в базе
            $gamesCount = \App\Models\SlotegratorGame::count();
            
            if ($gamesCount === 0) {
                return response()->json([
                    'error' => 'No games found in database',
                    'hint' => 'Please import games first',
                    'action_required' => 'Run: php artisan slotegrator:import'
                ], 400);
            }

            // Получаем список доступных провайдеров напрямую из API
            try {
                $response = $this->client->get('/games', ['page' => 1, 'per-page' => 10]);
                
                if (!isset($response['items']) || empty($response['items'])) {
                    return response()->json([
                        'error' => 'No games available from Slotegrator API',
                        'hint' => 'No providers are enabled in your contract',
                        'action_required' => 'Contact Slotegrator support to enable providers'
                    ], 400);
                }
                
                // Берем первую игру из API (она точно доступна)
                $apiGame = $response['items'][0];
                
                // Ищем эту игру в нашей базе или используем её UUID
                $game = \App\Models\SlotegratorGame::where('uuid', $apiGame['uuid'])->first();
                
                if (!$game) {
                    // Если игры нет в базе, создаем временную запись
                    $game = new \App\Models\SlotegratorGame();
                    $game->uuid = $apiGame['uuid'];
                    $game->name = $apiGame['name'];
                    $game->provider = $apiGame['provider'] ?? 'Unknown';
                }
                
                $sessionToken = Str::uuid()->toString();
                
                // Инициализируем игру
                $initResponse = $this->initGame([
                    'game_uuid' => $game->uuid,
                    'player_id' => $user->id,
                    'player_name' => $user->username,
                    'currency' => $user->currency->symbol,
                    'session_id' => $sessionToken,
                    'return_url' => route('home'),
                    'language' => 'en',
                ]);
                
                // Проверяем что игра успешно инициализирована
                if (!isset($initResponse['url']) || empty($initResponse['url'])) {
                    return response()->json([
                        'error' => 'Game initialization failed',
                        'response' => $initResponse,
                        'hint' => 'Provider may not be enabled or game is unavailable'
                    ], 400);
                }
                
                Log::info('Slotegrator self-validate: Game initialized', [
                    'game_uuid' => $game->uuid,
                    'session_id' => $sessionToken,
                    'game_url_received' => true,
                    'player_id' => $user->id
                ]);
                
                // Обновляем сессию пользователя ПЕРЕД валидацией
                // ВАЖНО: Сохраняем UUID игры (не ID модели!) и валюту
                $user->gameSession()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'token' => $sessionToken,
                        'game_uuid' => $game->uuid, // UUID игры из API
                        'currency' => $user->currency->symbol, // Валюта сессии
                    ]
                );

                // КРИТИЧЕСКИ: Даём время на сохранение в БД
                sleep(1);
                
                // Проверяем что сессия сохранилась
                $savedSession = $user->gameSession()->first();
                if (!$savedSession) {
                    return response()->json([
                        'error' => 'Session was not saved to database',
                        'session_token' => $sessionToken,
                        'hint' => 'Check database connection and game_sessions table'
                    ], 500);
                }
                
                Log::info('Slotegrator self-validate: Session saved', [
                    'session_id' => $savedSession->token,
                    'user_id' => $user->id,
                    'game_uuid' => $savedSession->game_uuid
                ]);

                // КРИТИЧЕСКИ: Slotegrator нужно время чтобы зарегистрировать сессию в своей системе
                // После /games/init сессия еще НЕ активна на их стороне
                Log::info('Waiting for Slotegrator to register session...');
                sleep(5); // Увеличиваем паузу до 5 секунд

                // Запускаем self-validate
                Log::info('Starting self-validate process');
                $result = $this->client->selfValidate();
                
                return response()->json([
                    'validation' => $result,
                    'test_session' => [
                        'game' => $game->name,
                        'provider' => $game->provider,
                        'session_id' => $sessionToken,
                        'player_id' => $user->id,
                        'game_url_received' => true,
                        'session_saved' => true,
                    ]
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to initialize game for validation',
                    'message' => $e->getMessage(),
                    'hint' => 'This might mean no providers are enabled in your contract',
                    'action_required' => 'Contact Slotegrator support or check: php artisan slotegrator:check-providers'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Self-validate error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Self-validation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function infoSlot()
    {
        return view('games.info');
    }


    public function launchGameByCode($gameCode)
    {
        try {
            $game = Cache::remember("slotegrator_game_code:{$gameCode}", 3600, function () use ($gameCode) {
                return SlotegratorGame::where('game_code', $gameCode)
                    ->where('provider_type', 'slotegrator')
                    ->firstOrFail();
            });

            $user = Auth::user();

            if (!$user) {
                return redirect()->route('home')->with('error', 'Please log in to play');
            }

            return $this->launchGame($game->name);

        } catch (\Exception $e) {
            Log::error('Slotegrator game launch error', [
                'game_code' => $gameCode,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }


    public function launchDemoGameByCode($gameCode)
    {
        try {
            $game = Cache::remember("slotegrator_game_code:{$gameCode}", 3600, function () use ($gameCode) {
                return SlotegratorGame::where('game_code', $gameCode)
                    ->where('provider_type', 'slotegrator')
                    ->firstOrFail();
            });

            return $this->launchDemoGame($game->name); // Временное решение

        } catch (\Exception $e) {
            Log::error('Slotegrator demo game launch error', [
                'game_code' => $gameCode,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }
}
