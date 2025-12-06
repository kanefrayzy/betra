<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\SlotegratorGame;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Slotegrator\SlotegratorClient;
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
                );

                $gameUrl = $response['url'] ?? null;

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
        $action = $request->input('action');
        $data = $request->all();

        $userExists = Cache::remember("user_exists:{$data['player_id']}", 3600, function () use ($data) {
            return User::where('id', $data['player_id'])->exists();
        });

        if (!$userExists) {
            return $this->errorResponse('Player not found');
        }

        try {
            $this->client->verifySignature($request->headers->all(), $data);
            
            if (isset($data['session_id'])) {
                $user = User::find($data['player_id']);
                
                $session = DB::table('game_sessions')
                    ->where('user_id', $data['player_id'])
                    ->where('token', $data['session_id'])
                    ->first();
                    
                if (!$session) {
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
                
                if ($session->currency && $data['currency'] !== $session->currency) {
                    return $this->errorResponse('Currency mismatch');
                }
            }
            
            $result = match ($action) {
                'balance' => $this->getBalance($data),
                'bet', 'win' => $this->handleTransaction($data, $action),
                'refund' => $this->refund($data),
                'rollback' => $this->rollback($data),
                default => $this->errorResponse('Unknown action')
            };

            return $result;
            
        } catch (Exception $e) {
            return $this->errorResponse('Internal server error');
        }
    }

    /**
     * Возвращает текущий баланс игрока
     */
    protected function getBalance($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::select('id', 'balance')->lockForUpdate()->findOrFail($data['player_id']);

            return response()->json([
                'balance' => round($user->balance, 2),
            ]);
        });
    }

    /**
     * Обрабатывает BET и WIN транзакции
     */
    protected function handleTransaction($data, $type)
    {
        return DB::transaction(function () use ($data, $type) {
            $amount = $data['amount'];
            $user = User::lockForUpdate()->findOrFail($data['player_id']);

            if ($data['currency'] !== $user->currency->symbol) {
                return $this->errorResponse('Currency mismatch');
            }

            if ($this->isExistingTransaction($data['transaction_id'])) {
                return $this->getDuplicateTransactionResponse($user, $data['transaction_id']);
            }

            if ($type === 'bet' && $user->balance < $amount) {
                return $this->insufficientFundsResponse();
            }

            $balanceBefore = $user->balance;

            if ($type === 'bet') {
                $user->balance -= $amount;
            } else {
                $user->balance += $amount;
            }
            
            $user->save();

            $transaction = $this->createTransaction(
                $user, 
                $data, 
                $type, 
                $amount, 
                $balanceBefore,
                $user->balance
            );

            $this->storeTransactionInRedis($transaction, $user, $data);

            return response()->json([
                'balance' => round($user->balance, 2),
                'transaction_id' => $this->hash($transaction->id)
            ]);
            
        }, $this->transactionTimeout);
    }

    /**
     * Обрабатывает REFUND транзакции
     */
    protected function refund($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::lockForUpdate()->findOrFail($data['player_id']);
            
            if ($this->isExistingTransaction($data['transaction_id'])) {
                return $this->getDuplicateTransactionResponse($user, $data['transaction_id']);
            }

            $refundKey = "refund:{$data['player_id']}:{$data['bet_transaction_id']}";
            $isFirstRefund = Redis::setnx($refundKey, $data['transaction_id']);
            
            if (!$isFirstRefund) {
                $existingRefundHash = Redis::get($refundKey);
                return $this->getDuplicateTransactionResponse($user, $existingRefundHash);
            }
            
            Redis::expire($refundKey, 3600);

            $originalTransaction = Transaction::where('hash', $data['bet_transaction_id'])->first();

            if (!$originalTransaction) {
                $transaction = $this->createRefundWithoutOriginal($user, $data);
                return response()->json([
                    'balance' => round($user->balance, 2),
                    'transaction_id' => $this->hash($transaction->id)
                ]);
            }

            $amount = $data['amount'];
            $balanceBefore = $user->balance;
            
            if ($originalTransaction->type->value === 'bet') {
                $user->balance += $amount;
                $user->save();
            }

            $transaction = $this->createTransaction(
                $user,
                $data,
                'refund',
                $amount,
                $balanceBefore,
                $user->balance
            );
            
            $this->storeTransactionInRedis($transaction, $user, $data);

            return response()->json([
                'balance' => round($user->balance, 2),
                'transaction_id' => $this->hash($transaction->id)
            ]);
        });
    }

    /**
     * Создаёт refund транзакцию когда оригинальная транзакция не найдена
     */
    private function createRefundWithoutOriginal($user, $data)
    {
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
                'note' => 'No balance change - original transaction not found'
            ])
        ]);
    }

    /**
     * Возвращает ответ для дублирующего запроса
     */
    private function getDuplicateTransactionResponse($user, $transactionId)
    {
        $transaction = Transaction::where('hash', $transactionId)->first();
        
        if (!$transaction) {
            return $this->errorResponse('Transaction not found');
        }
        
        return response()->json([
            'balance' => round($user->balance, 2),
            'transaction_id' => $this->hash($transaction->id)
        ]);
    }

    /**
     * Обрабатывает ROLLBACK транзакции
     */
    protected function rollback($data)
    {
        $rollbackTimeout = 5;
        
        return DB::transaction(function () use ($data) {
            $rollbackTransactions = $data['rollback_transactions'];
            $user = User::lockForUpdate()->findOrFail($data['player_id']);
            $balanceBefore = $user->balance;

            $existingRollbackTransaction = Transaction::where('hash', $data['transaction_id'])->first();
            if ($existingRollbackTransaction) {
                $context = json_decode($existingRollbackTransaction->context, true);
                $rollbackTxIds = $context['rollback_transactions'] ?? [];

                return response()->json([
                    'balance' => round($user->balance, 2),
                    'transaction_id' => $this->hash($existingRollbackTransaction->id),
                    'rollback_transactions' => $rollbackTxIds,
                ]);
            }

            $rollbackTxIds = [];
            
            foreach ($rollbackTransactions as $transactionData) {
                $rollbackTxIds[] = $transactionData['transaction_id'];
                
                $transaction = Transaction::where('hash', $transactionData['transaction_id'])->first();

                if ($transaction && $transaction->amount != 0) {
                    $this->processRollbackForTransaction($user, $transaction);
                }
            }

            $rollbackTransaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => 0,
                'currency_id' => $user->currency_id,
                'type' => 'rollback',
                'status' => 'success',
                'hash' => $data['transaction_id'],
                'context' => json_encode([
                    'description' => 'Rollback transaction',
                    'balance_before' => $balanceBefore,
                    'balance_after' => $user->balance,
                    'rollback_transactions' => $rollbackTxIds,
                ])
            ]);

            return response()->json([
                'balance' => round($user->balance, 2),
                'transaction_id' => $this->hash($rollbackTransaction->id),
                'rollback_transactions' => $rollbackTxIds,
            ]);
        }, $rollbackTimeout);
    }

    /**
     * Создаёт пустую rollback транзакцию для не найденной транзакции
     */
    protected function createEmptyRollbackTransaction($user, $transactionData, $playerId)
    {
        Transaction::create([
            'user_id' => $playerId,
            'amount' => 0,
            'currency_id' => $user->currency_id,
            'type' => 'rollback',
            'status' => 'success',
            'hash' => $transactionData['transaction_id'],
            'context' => json_encode([
                'description' => 'Rollback for non-existent transaction',
                'amount' => 0,
            ]),
        ]);
    }

    /**
     * Откатывает конкретную транзакцию
     */
    protected function processRollbackForTransaction($user, $transaction)
    {
        $amount = $transaction->amount;
        
        if ($transaction->type->value === 'bet') {
            $user->balance += $amount;
        } elseif ($transaction->type->value === 'win') {
            $user->balance -= $amount;
        } elseif ($transaction->type->value === 'refund') {
            $user->balance -= $amount;
        }
        
        $user->save();

        $context = json_decode($transaction->context, true) ?? [];
        $context['rollback'] = true;
        $context['rollback_at'] = now()->toDateTimeString();
        
        $transaction->update([
            'amount' => 0,
            'status' => 'rollback',
            'context' => json_encode($context)
        ]);
    }

    /**
     * Создаёт финальную rollback транзакцию
     */
    protected function createFinalRollbackTransaction($user, $data, $balanceBefore, $answerArrRollbackTransactions)
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
                'balance_before' => $balanceBefore,
                'balance_after' => round($user->balance, 2),
                'rollback_transactions' => $answerArrRollbackTransactions,
            ])
        ]);
    }

    /**
     * Проверяет существование транзакции по hash
     */
    protected function isExistingTransaction($transactionId)
    {
        return Transaction::where('hash', $transactionId)->exists();
    }

    /**
     * Ответ при недостаточности средств
     */
    private function insufficientFundsResponse()
    {
        return response()->json([
            'error_code' => 'INSUFFICIENT_FUNDS',
            'error_description' => 'Not enough money to continue playing'
        ]);
    }

    /**
     * Создаёт транзакцию в БД
     */
    protected function createTransaction($user, $data, $type, $amount, $balanceBefore, $balanceAfter)
    {
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
                'session_token' => $data['session_id'] ?? null,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'bet_transaction_id' => $data['bet_transaction_id'] ?? null,
            ])
        ]);
    }

    /**
     * Сохраняет транзакцию в Redis для быстрого доступа
     */
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
        $transactionDataGames['round_id'] = $data['round_id'] ?? null;
        $transactionDataGames['finished'] = $data['finished'] ?? null;

        $transactionJsonGames = json_encode($transactionDataGames);

        Redis::pipeline(function ($pipe) use ($transactionJsonGames) {
            $pipe->lpush("game_history:all", $transactionJsonGames);
            $pipe->ltrim("game_history:all", 0, 500);
        });
    }

    /**
     * Возвращает ошибку в формате Slotegrator
     */
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

            $gamesCount = \App\Models\SlotegratorGame::count();
            
            if ($gamesCount === 0) {
                return response()->json([
                    'error' => 'No games found in database',
                    'hint' => 'Please import games first',
                    'action_required' => 'Run: php artisan slotegrator:import'
                ], 400);
            }

            try {
                $response = $this->client->get('/games', ['page' => 1, 'per-page' => 10]);
                
                if (!isset($response['items']) || empty($response['items'])) {
                    return response()->json([
                        'error' => 'No games available from Slotegrator API',
                        'hint' => 'No providers are enabled in your contract',
                        'action_required' => 'Contact Slotegrator support to enable providers'
                    ], 400);
                }
                
                $apiGame = $response['items'][0];
                $game = \App\Models\SlotegratorGame::where('uuid', $apiGame['uuid'])->first();
                
                if (!$game) {
                    $game = new \App\Models\SlotegratorGame();
                    $game->uuid = $apiGame['uuid'];
                    $game->name = $apiGame['name'];
                    $game->provider = $apiGame['provider'] ?? 'Unknown';
                }
                
                $sessionToken = Str::uuid()->toString();
                
                $initResponse = $this->initGame([
                    'game_uuid' => $game->uuid,
                    'player_id' => $user->id,
                    'player_name' => $user->username,
                    'currency' => $user->currency->symbol,
                    'session_id' => $sessionToken,
                    'return_url' => route('home'),
                    'language' => 'en',
                ]);
                
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
                
                $user->gameSession()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'token' => $sessionToken,
                        'game_uuid' => $game->uuid,
                        'currency' => $user->currency->symbol,
                    ]
                );

                sleep(1);
                
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

                Log::info('Waiting for Slotegrator to register session...');
                sleep(5);

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

            return $this->launchDemoGame($game->name);

        } catch (\Exception $e) {
            Log::error('Slotegrator demo game launch error', [
                'game_code' => $gameCode,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }
}