<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Traits\Hashable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AesCallbackController extends Controller
{
    use Hashable;

    protected $transactionTimeout = 2;

    public function handleCallback(Request $request)
    {
        $data = $request->all();
        $command = $data['command'] ?? null;

        try {
            $this->verifyCallbackToken($request);

            $result = match ($command) {
                'authenticate', 'status' => $this->handleStatus($data),
                'balance' => $this->handleBalance($data),
                'bet' => $this->handleBet($data),
                'win' => $this->handleWin($data),
                'cancel' => $this->handleCancel($data),
                'rollback' => $this->handleRollback($data),
                default => ['result' => 1, 'status' => 'Unknown command', 'data' => ['balance' => 0.0]]
            };

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('AES Error', [
                'command' => $command,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'result' => 1001,
                'status' => 'INTERNAL_SERVER_ERROR',
                'data' => ['balance' => 0.0]
            ]);
        }
    }

    protected function verifyCallbackToken(Request $request): void
    {
        $token = $request->header('Callback-Token');
        $accounts = config('services.aes.accounts', []);
        $validTokens = array_column($accounts, 'callback_token');

        if (empty($validTokens) || !in_array($token, $validTokens)) {
            throw new Exception('Invalid callback token');
        }
    }

    protected function handleBet(array $data): array
    {
        $account = $data['data']['account'] ?? null;
        $transId = $data['data']['trans_id'] ?? null;
        $amount = (float)($data['data']['amount'] ?? 0);

        if (!$account || !$transId || $amount < 0) {
            return ['result' => 1002, 'status' => 'VALIDATION_ERROR', 'data' => ['balance' => 0.0]];
        }

        return DB::transaction(function () use ($account, $transId, $amount, $data) {
            $user = $this->findUserWithLock($account);

            if (!$user) {
                return ['result' => 2002, 'status' => 'USER_NOT_FOUND', 'data' => ['balance' => 0.0]];
            }

            $balance = (float)$user->balance;

            if ($amount > 0 && $balance < $amount) {
                return ['result' => 2006, 'status' => 'BALANCE_NOT_ENOUGH', 'data' => ['balance' => $balance]];
            }

            $balanceAfter = $amount > 0 ? $balance - $amount : $balance;

            // Получаем название игры из БД
            $gameCode = $data['data']['game_code'] ?? null;
            $gameName = $gameCode;
            if ($gameCode) {
                $game = DB::selectOne(
                    'SELECT name FROM slotegrator_games WHERE game_code = ? AND provider_type = ? LIMIT 1',
                    [$gameCode, 'aes']
                );
                if ($game) {
                    $gameName = $game->name;
                }
            }

            $context = json_encode([
                'description' => "Bet in game {$gameName}",
                'provider' => 'aes',
                'amount' => $amount,
                'game_code' => $gameCode,
                'round_id' => $data['data']['round_id'] ?? null,
                'balance_before' => $balance,
                'balance_after' => $balanceAfter,
            ]);

            // Создаем уникальный hash с учетом валюты
            $uniqueHash = "aes_{$user->currency_id}_{$transId}";
            
            try {
                DB::insert(
                    'INSERT INTO transactions (user_id, amount, currency_id, type, status, hash, context, created_at, updated_at) 
                     VALUES (?, ?, ?, "bet", "success", ?, ?, NOW(), NOW())',
                    [$user->id, $amount, $user->currency_id, $uniqueHash, $context]
                );
                
                if ($amount > 0) {
                    DB::update('UPDATE users SET balance = ? WHERE id = ?', [$balanceAfter, $user->id]);
                }
                
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000) {
                    $currentBalance = DB::selectOne('SELECT balance FROM users WHERE id = ?', [$user->id]);
                    return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => (float)$currentBalance->balance]];
                }
                throw $e;
            }

            // Асинхронное сохранение в Redis
            $this->storeLiveBetAsync($user, $transId, $amount, 'bet', $data);

            // ОПТИМИЗАЦИЯ: Минимальный ответ (только баланс, без лишних полей)
            return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => $balanceAfter]];
        }, $this->transactionTimeout);
    }

    protected function handleWin(array $data): array
    {
        $account = $data['data']['account'] ?? null;
        $transId = $data['data']['trans_id'] ?? null;
        $amount = (float)($data['data']['amount'] ?? 0);

        if (!$account || !$transId || $amount < 0) {
            return ['result' => 1002, 'status' => 'VALIDATION_ERROR', 'data' => ['balance' => 0.0]];
        }

        return DB::transaction(function () use ($account, $transId, $amount, $data) {
            $user = $this->findUserWithLock($account);

            if (!$user) {
                return ['result' => 2002, 'status' => 'USER_NOT_FOUND', 'data' => ['balance' => 0.0]];
            }

            $balance = (float)$user->balance;
            $balanceAfter = $balance + $amount;

            // Получаем название игры из БД
            $gameCode = $data['data']['game_code'] ?? null;
            $gameName = $gameCode;
            if ($gameCode) {
                $game = DB::selectOne(
                    'SELECT name FROM slotegrator_games WHERE game_code = ? AND provider_type = ? LIMIT 1',
                    [$gameCode, 'aes']
                );
                if ($game) {
                    $gameName = $game->name;
                }
            }

            $context = json_encode([
                'description' => "Win in game {$gameName}",
                'provider' => 'aes',
                'amount' => $amount,
                'game_code' => $gameCode,
                'round_id' => $data['data']['round_id'] ?? null,
                'balance_before' => $balance,
                'balance_after' => $balanceAfter,
            ]);

            // Создаем уникальный hash с учетом валюты
            $uniqueHash = "aes_{$user->currency_id}_{$transId}";
            
            try {
                DB::insert(
                    'INSERT INTO transactions (user_id, amount, currency_id, type, status, hash, context, created_at, updated_at) 
                     VALUES (?, ?, ?, "win", "success", ?, ?, NOW(), NOW())',
                    [$user->id, $amount, $user->currency_id, $uniqueHash, $context]
                );
                
                DB::update('UPDATE users SET balance = ? WHERE id = ?', [$balanceAfter, $user->id]);
                
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000) {
                    $currentBalance = DB::selectOne('SELECT balance FROM users WHERE id = ?', [$user->id]);
                    return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => (float)$currentBalance->balance]];
                }
                throw $e;
            }

            // Асинхронное сохранение в Redis
            $this->storeLiveBetAsync($user, $transId, $amount, 'win', $data);

            // ОПТИМИЗАЦИЯ: Минимальный ответ
            return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => $balanceAfter]];
        }, $this->transactionTimeout);
    }

    protected function handleCancel(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $account = $data['data']['account'] ?? null;
            $transId = $data['data']['trans_id'] ?? null;
            $cancelTransId = $data['data']['cancel_trans_id'] ?? null;

            if (!$account || !$cancelTransId) {
                return ['result' => 1002, 'status' => 'VALIDATION_ERROR', 'data' => ['balance' => 0.0]];
            }

            // Сначала ищем пользователя
            $user = $this->findUserWithLock($account);
            
            if (!$user) {
                return ['result' => 2002, 'status' => 'USER_NOT_FOUND', 'data' => ['balance' => 0.0]];
            }

            // ОПТИМИЗАЦИЯ: JOIN с транзакцией с учетом валюты
            $cancelHash = "aes_{$user->currency_id}_{$cancelTransId}";
            $originalTrans = DB::selectOne('
                SELECT id, type, amount
                FROM transactions
                WHERE hash = ? AND user_id = ?
                LIMIT 1
            ', [$cancelHash, $user->id]);

            $balance = (float)$user->balance;

            if (!$originalTrans) {
                return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => $balance]];
            }

            $refundAmount = (float)$originalTrans->amount;
            $balanceAfter = $balance;

            if ($originalTrans->type === 'bet') {
                $balanceAfter = $balance + $refundAmount;
                DB::update('UPDATE users SET balance = ? WHERE id = ?', [$balanceAfter, $user->id]);
            } elseif ($originalTrans->type === 'win') {
                $balanceAfter = max(0, $balance - $refundAmount);
                DB::update('UPDATE users SET balance = ? WHERE id = ?', [$balanceAfter, $user->id]);
            }

            DB::update('UPDATE transactions SET type = "refund" WHERE id = ?', [$originalTrans->id]);

            if ($transId) {
                // Получаем название игры из БД
                $gameCode = $data['data']['game_code'] ?? null;
                $gameName = $gameCode;
                if ($gameCode) {
                    $game = DB::selectOne(
                        'SELECT name FROM slotegrator_games WHERE game_code = ? AND provider_type = ? LIMIT 1',
                        [$gameCode, 'aes']
                    );
                    if ($game) {
                        $gameName = $game->name;
                    }
                }
                
                $balanceAfter = (float)$user->balance + $refundAmount;
                
                $context = json_encode([
                    'description' => "Refund in game {$gameName}",
                    'provider' => 'aes',
                    'amount' => $refundAmount,
                    'cancel_transaction_id' => $cancelTransId,
                    'original_type' => $originalTrans->type,
                    'balance_before' => (float)$user->balance,
                    'balance_after' => $balanceAfter,
                ]);

                // Создаем уникальный hash с учетом валюты
                $refundHash = "aes_{$user->currency_id}_{$transId}";
                
                try {
                    DB::insert(
                        'INSERT INTO transactions (user_id, amount, currency_id, type, status, hash, context, created_at, updated_at) 
                         VALUES (?, ?, ?, "refund", "success", ?, ?, NOW(), NOW())',
                        [$user->id, $refundAmount, $user->currency_id, $refundHash, $context]
                    );
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() != 23000) throw $e;
                }

                $this->storeLiveBetAsync($user, $transId, $refundAmount, 'refund', $data);
            }

            return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => $balanceAfter]];
        }, $this->transactionTimeout);
    }

    protected function handleRollback(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $account = $data['data']['account'] ?? null;
            $rollbackTransId = $data['data']['rollback_trans_id'] ?? null;

            if (!$account || !$rollbackTransId) {
                return ['result' => 1002, 'status' => 'VALIDATION_ERROR', 'data' => ['balance' => 0.0]];
            }

            // Ищем пользователя
            $user = $this->findUserWithLock($account);
            
            if (!$user) {
                return ['result' => 2002, 'status' => 'USER_NOT_FOUND', 'data' => ['balance' => 0.0]];
            }

            // Ищем транзакцию с учетом валюты
            $rollbackHash = "aes_{$user->currency_id}_{$rollbackTransId}";
            $originalTrans = DB::selectOne('
                SELECT id, type, amount
                FROM transactions
                WHERE hash = ? AND user_id = ?
                LIMIT 1
            ', [$rollbackHash, $user->id]);

            $balance = (float)$user->balance;

            if (!$originalTrans) {
                return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => $balance]];
            }

            $amount = (float)$originalTrans->amount;
            $balanceAfter = $balance;

            if ($originalTrans->type === 'bet') {
                $balanceAfter = $balance + $amount;
                DB::update('UPDATE users SET balance = ? WHERE id = ?', [$balanceAfter, $user->id]);
            } elseif ($originalTrans->type === 'win') {
                $balanceAfter = max(0, $balance - $amount);
                DB::update('UPDATE users SET balance = ? WHERE id = ?', [$balanceAfter, $user->id]);
            }

            DB::update('UPDATE transactions SET type = "rollback" WHERE id = ?', [$originalTrans->id]);

            return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => $balanceAfter]];
        }, $this->transactionTimeout);
    }

    protected function handleBalance(array $data): array
    {
        $account = $data['data']['account'] ?? null;

        if (!$account) {
            return ['result' => 1002, 'status' => 'VALIDATION_ERROR', 'data' => ['balance' => 0.0]];
        }

        // Используем findUser с тройным fallback
        $user = $this->findUser($account);

        if (!$user) {
            return ['result' => 2002, 'status' => 'USER_NOT_FOUND', 'data' => ['balance' => 0.0]];
        }

        return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => (float)$user->balance]];
    }

    protected function handleStatus(array $data): array
    {
        $account = $data['data']['account'] ?? null;
        $transId = $data['data']['trans_id'] ?? null;
        $command = $data['command'] ?? '';

        if (!$account) {
            return ['result' => 1002, 'status' => 'VALIDATION_ERROR', 'data' => ['balance' => 0.0]];
        }

        // Ищем пользователя (без блокировки для status)
        $user = $this->findUser($account);

        if (!$user) {
            return ['result' => 2002, 'status' => 'USER_NOT_FOUND', 'data' => ['balance' => 0.0]];
        }

        $balance = (float)$user->balance;

        if ($command === 'status' && $transId) {
            // Проверяем транзакцию с учетом валюты
            $statusHash = "aes_{$user->currency_id}_{$transId}";
            $exists = DB::selectOne('SELECT 1 FROM transactions WHERE hash = ? LIMIT 1', [$statusHash]);

            if (!$exists) {
                return [
                    'result' => 2013,
                    'status' => 'TRANSACTION_NOT_FOUND',
                    'data' => ['balance' => $balance]
                ];
            }

            return [
                'result' => 0,
                'status' => 'OK',
                'data' => [
                    'account' => $account,
                    'trans_id' => $transId,
                    'trans_status' => 'OK'
                ]
            ];
        }

        if ($command === 'authenticate') {
            return [
                'result' => 0,
                'status' => 'OK',
                'data' => [
                    'account' => $account,
                    'balance' => $balance
                ]
            ];
        }

        return ['result' => 0, 'status' => 'OK', 'data' => ['balance' => $balance]];
    }

    /**
     * Асинхронное сохранение в Redis с pipeline оптимизацией
     */
    protected function storeLiveBetAsync($user, string $transId, float $amount, string $type, array $data): void
    {
        dispatch(function () use ($user, $transId, $amount, $type, $data) {
            try {
                // Получаем currency symbol
                $currency = DB::selectOne('SELECT symbol FROM currencies WHERE id = ? LIMIT 1', [$user->currency_id]);
                
                if (!$currency) {
                    return;
                }

                // Получаем название игры - используем LIKE для частичного совпадения
                $gameCode = $data['data']['game_code'] ?? null;
                $gameName = $gameCode;
                
                if ($gameCode) {
                    $game = DB::selectOne(
                        'SELECT name FROM slotegrator_games WHERE game_code LIKE ? AND provider_type = ? LIMIT 1',
                        ["%{$gameCode}%", 'aes']
                    );
                    if ($game) {
                        $gameName = $game->name;
                    }
                }

                // Создаем уникальный hash с учетом валюты
                $uniqueHash = "aes_{$user->currency_id}_{$transId}";
                
                $transactionData = [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => $type,
                    'hash' => $uniqueHash,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'avatar' => $user->avatar ?? '/assets/images/avatar-placeholder.png',
                    ],
                    'currency' => ['symbol' => $currency->symbol],
                    'game_name' => $gameName,
                    'created_at' => now()->toDateTimeString(),
                ];

                $gameData = array_merge($transactionData, [
                    'round_id' => $data['data']['round_id'] ?? null,
                    'finished' => true
                ]);

                //  Redis Pipeline для группировки всех команд
                $uniqueHash = "aes_{$user->currency_id}_{$transId}";
                Redis::pipeline(function($pipe) use ($uniqueHash, $transactionData, $user, $gameData) {
                    $pipe->setex("transaction:{$uniqueHash}", 3600, json_encode($transactionData));
                    $pipe->lpush("transactions:all", $uniqueHash);
                    $pipe->ltrim("transactions:all", 0, 99);
                    $pipe->lpush("transactions:user:{$user->id}", $uniqueHash);
                    $pipe->ltrim("transactions:user:{$user->id}", 0, 99);
                    $pipe->lpush("game_history:all", json_encode($gameData));
                    $pipe->ltrim("game_history:all", 0, 499);
                });

            } catch (\RedisException $e) {
                // Redis connection error - silent fail
            } catch (Exception $e) {
                // Redis storage error - silent fail
            }
        })->afterResponse();
    }

    /**
     * Поиск пользователя с fallback логикой
     */
    protected function findUser(string $account)
    {
        $user = DB::selectOne(
            'SELECT id, balance, currency_id, username, avatar FROM users WHERE aes_user_code = ? LIMIT 1',
            [$account]
        );

        if ($user) {
            return $user;
        }

        if (preg_match('/_(\d+)$/', $account, $matches)) {
            $userId = (int)$matches[1];
            
            $user = DB::selectOne(
                'SELECT id, balance, currency_id, username, avatar FROM users WHERE id = ? LIMIT 1',
                [$userId]
            );
            
            if ($user) {
                return $user;
            }
        }

        $username = preg_replace('/_\d+$/', '', str_replace('_', ' ', $account));
        
        $user = DB::selectOne(
            'SELECT id, balance, currency_id, username, avatar FROM users WHERE username = ? LIMIT 1',
            [$username]
        );

        return $user;
    }

    /**
     * Поиск пользователя с блокировкой FOR UPDATE и fallback логикой
     */
    protected function findUserWithLock(string $account)
    {
        // Попытка 1: Поиск по aes_user_code (числовой формат)
        $user = DB::selectOne(
            'SELECT id, balance, currency_id, username, avatar FROM users WHERE aes_user_code = ? LIMIT 1 FOR UPDATE',
            [$account]
        );

        if ($user) {
            return $user;
        }

        if (preg_match('/_(\d+)$/', $account, $matches)) {
            $userId = (int)$matches[1];
            
            $user = DB::selectOne(
                'SELECT id, balance, currency_id, username, avatar FROM users WHERE id = ? LIMIT 1 FOR UPDATE',
                [$userId]
            );
            
            if ($user) {
                return $user;
            }
        }

        $username = preg_replace('/_\d+$/', '', str_replace('_', ' ', $account));
        
        $user = DB::selectOne(
            'SELECT id, balance, currency_id, username, avatar FROM users WHERE username = ? LIMIT 1 FOR UPDATE',
            [$username]
        );

        return $user;
    }
}