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
    protected $transactionTimeout = 10;
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
                    ['token' => $sessionToken]
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

    public function callback(Request $request)
    {
        $startTime = microtime(true);
        $action = $request->input('action');
        $data = $request->all();

        // $this->logger->info('Received callback', ['action' => $action, 'data' => $data]);

        $userExists = Cache::remember("user_exists:{$data['player_id']}", 3600, function () use ($data) {
            return User::where('id', $data['player_id'])->exists();
        });

        if (!$userExists) {
            // $this->logger->warning('Player not found', ['player_id' => $data['player_id']]);
            return $this->errorResponse('Player not found - ' . $data['player_id']);
        }

        try {
            $this->client->verifySignature($request->headers->all(), $data);
            $result = $this->processCallbackWithRetry($action, $data);
            $endTime = microtime(true);
            // $this->logger->info('Callback processed', [
            //     'action' => $action,
            //     'execution_time' => $endTime - $startTime,
            //     'result' => $result->getContent()
            // ]);
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
                    // $this->logger->info("Action processed successfully", [
                    //     'action' => $action,
                    //     'attempt' => $i + 1,
                    //     'result' => $result->getContent()
                    // ]);
                    return $result;
                }


                // $this->logger->warning("Unexpected result", [
                //     'action' => $action,
                //     'attempt' => $i + 1,
                //     'result' => $result instanceof \Illuminate\Http\JsonResponse ? $result->getContent() : 'Not a JSON response'
                // ]);
            } catch (Exception $e) {
                $this->logger->warning("Attempt failed", [
                    'action' => $action,
                    'attempt' => $i + 1,
                    'error' => $e->getMessage()
                ]);

                if ($i == $attempts - 1) {
                    $this->logger->error("All attempts failed", [
                        'action' => $action,
                        'attempts' => $attempts,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
                sleep(1);
            }
        }

        $this->logger->error("Failed to process after multiple attempts", [
            'action' => $action,
            'attempts' => $attempts
        ]);
        return $this->errorResponse("Failed to process after {$attempts} attempts");
    }

    protected function getBalance($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::select('id', 'balance')->lockForUpdate()->findOrFail($data['player_id']);

            if ($errorDescription = $this->checkTokenValidity($user, $data)) {
                return $this->errorResponse("Token fail: {$errorDescription}");
            }

            return response()->json([
                'balance' => round($user->balance, 2),
            ]);
        });
    }

    protected function handleTransaction($data, $type)
    {
        $startTime = microtime(true);
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
            // Проверка минимальной ставки для TRY
            if ($type === 'bet' && $user->currency_id === 4 && $amount < 1) {
                $this->logger->warning('Bet amount below minimum for TRY', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'currency_id' => $user->currency_id
                ]);
                return response()->json([
                    'error_code' => 'INVALID_BET_AMOUNT',
                    'error_description' => 'Minimum bet amount is 10 TRY'
                ]);
            }
            // $this->logger->info('Processing transaction', [
            //     'type' => $type,
            //     'amount' => $amount,
            //     'user_id' => $user->id,
            //     'transaction_id' => $data['transaction_id']
            // ]);

            if ($this->isExistingTransaction($data['transaction_id'])) {
                // $this->logger->info('Existing transaction found', ['transaction_id' => $data['transaction_id']]);
                return $this->getExistingTransactionResponse($user, $data['transaction_id']);
            }

            if ($errorDescription = $this->checkTokenValidity($user, $data)) {
                // $this->logger->warning('Invalid token', ['user_id' => $user->id, 'error' => $errorDescription]);
                return $this->errorResponse("Token fail: {$errorDescription}");
            }

            if ($type === 'bet' && $user->balance < $amount) {
                return $this->insufficientFundsResponse();
            }

            $originalBalance = $user->balance;
            $this->updateUserBalance($user, $type, $amount);

            $transaction = $this->createTransaction($user, $data, $type, $amount, $originalBalance);


            $this->storeTransactionInRedis($transaction, $user, $data);

            $endTime = microtime(true);
            // $this->logger->info('Transaction processed successfully', [
            //     'type' => $type,
            //     'amount' => $amount,
            //     'user_id' => $user->id,
            //     'balance_before' => $originalBalance,
            //     'balance_after' => $user->balance,
            //     'execution_time' => $endTime - $startTime
            // ]);

            if ($type === 'bet') {
                $betAmount = round($data['amount'], 2);

                // Если у пользователя есть активный отыгрыш
                if ($user->hasActiveWagering()) {
                  // $this->logger->info('Transaction processed successfully', [
                  //     'type' => $type,
                  //     'amount' => $amount,
                  //     'user_id' => $user->id,
                  //     'balance_before' => $originalBalance,
                  //     'balance_after' => $user->balance,
                  //     ]);
                    $exchangeService = new ExchangeService();

                    // Конвертируем сумму ставки в AZN если нужно
                    if ($user->currency->symbol !== 'AZN') {
                        $betAmount = $exchangeService->convert(
                            $betAmount,
                            $user->currency->symbol,
                            'AZN'
                        );
                    }

                    $user->addToWageringAmount($betAmount);
                }
            }

            return $this->getTransactionResponse($user, $transaction);
        }, $this->transactionTimeout);
    }

    protected function refund($data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::lockForUpdate()->findOrFail($data['player_id']);
            $betTransaction = Transaction::where('hash', $data['bet_transaction_id'])->first();

            // Проверка на существующую транзакцию возврата
            $existingRefundTransaction = Transaction::where('hash', $data['transaction_id'])->first();
            if ($existingRefundTransaction && $existingRefundTransaction->type === 'refund') {
                // Возврат уже обработан, возвращаем информацию о транзакции
                return $this->getTransactionResponse($existingRefundTransaction->user, $existingRefundTransaction);
            }

            // Если транзакция ставки не найдена
            if (!$betTransaction) {
                // Возвращаем ошибку или регистрируем событие
                // $this->logger->warning('Refund requested for non-existent bet', [
                //     'user_id' => $user->id,
                //     'bet_transaction_id' => $data['bet_transaction_id']
                // ]);

                return $this->errorResponse('Bet transaction not found for refund');
            }

            // Если ставка существует, выполняем стандартную обработку возврата
            return $this->handleTransaction($data, 'refund');
        });
    }


    private function createRefundTransactionForNonExistentBet($user, $data)
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
                'balance_after' => $user->balance + $data['amount']
            ])
        ]);
    }

    protected function isExistingTransaction($transactionId)
    {
        $cacheKey = "transaction_exists:{$transactionId}";
        return Cache::remember($cacheKey, 3600, function () use ($transactionId) {
            return Transaction::where('hash', $transactionId)->exists();
        });
    }

    private function getExistingTransactionResponse($user, $transactionId)
    {
        return response()->json([
            'balance' => round($user->balance, 2),
            'transaction_id' => $this->hash($transactionId)
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

        // $this->logger->info('User balance updated', [
        //     'user_id' => $user->id,
        //     'type' => $type,
        //     'amount' => $amount,
        //     'new_balance' => $user->balance
        // ]);
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
            $user = User::with('gameSession')->lockForUpdate()->findOrFail($data['player_id']);
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
        // $this->logger->info('Rollback transaction created for non-existent bet', [
        //     'user_id' => $playerId,
        //     'transaction_id' => $transactionData['transaction_id']
        // ]);
    }

    protected function processRollbackForTransaction($user, $transaction)
    {
        $amount = round($transaction->amount, 2);
        if ($transaction->type === 'bet') {
            $user->balance += $amount;
        } elseif ($transaction->type === 'win') {
            $user->balance -= $amount;
        }
        $user->save();

        $transaction->update([
            'amount' => 0,
            'type' => 'rollback',
            'context' => json_encode(array_merge(
                json_decode($transaction->context, true),
                ['log' => 'rollback']
            )),
        ]);
        // $this->logger->info('Transaction rolled back', [
        //     'user_id' => $user->id,
        //     'transaction_id' => $transaction->id,
        //     'type' => $transaction->type,
        //     'amount' => $amount
        // ]);
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

    protected function checkTokenValidity($user, $data): ?string
    {
        $gameSession = $user->gameSession()->select('token')->first();
        return (!$gameSession || $gameSession->token !== $data['session_id']) ? 'Invalid game session' : null;
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
        $result = $this->client->selfValidate();
        if ($result) {
            return response()->json($result);
        } else {
            return response()->json(['error' => 'No response from server'], 500);
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
