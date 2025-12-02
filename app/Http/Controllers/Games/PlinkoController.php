<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PlinkoGame;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\Hashable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PlinkoController extends Controller
{
    use Hashable;

    protected $transactionTimeout = 10;
    protected $logger;
    protected $multipliers = [
        8 => [
            'low' => [5.6, 2.1, 1.1, 0.5, 1.0, 1.1, 2.1, 5.6],
            'medium' => [5.6, 2.1, 1.1, 0.5, 1.0, 1.1, 2.1, 5.6],
            'high' => [5.6, 2.1, 1.1, 0.5, 1.0, 1.1, 2.1, 5.6]
        ],
        12 => [
            'low' => [8.4, 3.2, 1.7, 1.1, 0.7, 0.4, 0.4, 0.7, 1.1, 1.7, 3.2, 8.4],
            'medium' => [8.4, 3.2, 1.7, 1.1, 0.7, 0.4, 0.4, 0.7, 1.1, 1.7, 3.2, 8.4],
            'high' => [8.4, 3.2, 1.7, 1.1, 0.7, 0.4, 0.4, 0.7, 1.1, 1.7, 3.2, 8.4]
        ],
        16 => [
            'low' => [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000],
            'medium' => [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000],
            'high' => [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000]
        ]
    ];

    public function __construct()
    {
        $this->logger = Log::class;
    }

    public function show()
    {
        $user = Auth::user();
        if(!$user->game_token) {
          $user->game_token = Str::uuid()->toString(); // Генерация токена
          $user->save();
        }
        return view('games.plinko');
    }

    public function init(Request $request)
    {
        $validated = $request->validate([
            'bet_amount' => 'required|numeric|min:0.01|max:1000',
            'risk_level' => 'required|string|in:low,medium,high',
            'rows' => 'required|integer|min:8|max:16',
            'client_seed' => 'nullable|string',
            'user_id' => 'required|uuid',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $user = User::lockForUpdate()->where('game_token', $validated['user_id'])->first();
                // if ($errorDescription = $this->checkTokenValidity($user)) {
                //   return $this->errorResponse("Token fail: {$errorDescription}");
                // }
                if ($user->balance < $validated['bet_amount']) {
                    return [
                        'success' => false,
                        'error' => 'Insufficient funds'
                    ];
                }

                // Генерация игровых данных
                $serverSeed = Str::random(16);
                $clientSeed = $validated['client_seed'] ?? Str::random(16);
                $hash = hash('sha256', $serverSeed . $clientSeed);

                // Расчет позиций (теперь на сервере)
                $positions = $this->calculatePositions(
                    $validated['rows'],
                    $validated['risk_level'],
                    $hash
                );

                // Расчет результата игры
                $finalPosition = $this->calculateFinalPosition($positions);
                $multiplier = $this->getMultiplier($validated['rows'], $validated['risk_level'], $finalPosition);
                Log::info('Server-calculated index:', ['index' => $finalPosition]);
                Log::info('Server multiplier:', ['multiplier' => $multiplier]);

                $winAmount = $validated['bet_amount'] * $multiplier;

                // Создаем транзакцию ставки
                $betTransaction = $this->createTransaction(
                    $user,
                    $validated['bet_amount'],
                    'bet',
                    Str::uuid()->toString(),
                    [
                        'game' => 'plinko',
                        'risk_level' => $validated['risk_level'],
                        'rows' => $validated['rows'],
                        'positions' => $positions,
                        'server_seed' => $serverSeed,
                        'client_seed' => $clientSeed,
                    ]
                );

                // Создаем транзакцию выигрыша
                $winTransaction = $this->createTransaction(
                    $user,
                    $winAmount,
                    'win',
                    Str::uuid()->toString(),
                    [
                        'game' => 'plinko',
                        'multiplier' => $multiplier,
                        'bet_transaction_id' => $betTransaction->hash
                    ]
                );

                // Обновляем баланс пользователя
                $user->balance = $user->balance - $validated['bet_amount'] + $winAmount;
                $user->save();

                // Создаем запись об игре
                $game = PlinkoGame::create([
                    'user_id' => $user->id,
                    'bet_amount' => $validated['bet_amount'],
                    'win_amount' => $winAmount,
                    'risk_level' => $validated['risk_level'],
                    'rows' => $validated['rows'],
                    'positions' => $positions,
                    'multiplier' => $multiplier,
                    'hash' => $hash,
                    'server_seed' => $serverSeed,
                    'client_seed' => $clientSeed,
                    'transaction_id' => $betTransaction->hash,
                    'win_transaction_id' => $winTransaction->hash,
                    'status' => 'completed'
                ]);

                // Сохраняем в Redis
                $this->storeTransactionInRedis($game, $user);


                return [
                    'success' => true,
                    'game_id' => $game->id,
                    'positions' => $positions,
                    'multiplier' => $multiplier,
                    'win_amount' => $winAmount,
                    'new_balance' => $user->balance,
                    'server_seed' => $serverSeed,
                    'client_seed' => $clientSeed,
                    'hash' => $hash
                ];



            }, $this->transactionTimeout);
        } catch (\Exception $e) {
            $this->logger->error('Game initialization error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Game initialization failed'
            ];
        }
    }

    protected function createTransaction($user, $amount, $type, $hash, $context = [])
    {
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_id' => $user->currency_id,
            'type' => $type,
            'status' => 'success',
            'hash' => $hash,
            'context' => json_encode(array_merge([
                'balance_before' => $user->balance,
                'balance_after' => $user->balance + ($type === 'bet' ? -$amount : $amount)
            ], $context))
        ]);

        // Сохраняем транзакцию в Redis для быстрого доступа
        $this->storeTransactionInRedis($transaction, $user);

        return $transaction;
    }

    protected function storeTransactionInRedis($transaction, $user)
    {
        $transactionData = [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'status' => $transaction->status,
            'hash' => $transaction->hash,
            'created_at' => $transaction->created_at->toDateTimeString(),
            'context' => json_decode($transaction->context, true)
        ];

        Redis::pipeline(function ($pipe) use ($transaction, $transactionData) {
            $pipe->set("transaction:{$transaction->hash}", json_encode($transactionData));
            $pipe->expire("transaction:{$transaction->hash}", 3600);
            $pipe->lpush('recent_transactions', json_encode($transactionData));
            $pipe->ltrim('recent_transactions', 0, 99);
        });
    }

    protected function calculateFinalPosition($positions) {
        return array_sum($positions);
    }

    protected function getMultiplier($rows, $riskLevel, $finalPosition) {
        $multipliers = $this->multipliers[$rows][$riskLevel];
        $middleIndex = floor(count($multipliers) / 2);
        $index = $middleIndex + $finalPosition;
        return $multipliers[max(0, min($index, count($multipliers) - 1))];
    }

    private function calculatePositions($rows, $riskLevel, $hash)
    {
        $positions = [];
        $hashChars = str_split($hash);

        $probability = match($riskLevel) {
            'low' => 0.45,
            'medium' => 0.5,
            'high' => 0.55,
            default => 0.5
        };

        for ($i = 0; $i < $rows; $i++) {
            $hashPart = hexdec($hashChars[$i] ?? 'f');
            $positions[] = ($hashPart / 15) < $probability ? 1 : -1;
        }

        return $positions;
    }

}
