<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Services\Betvio\BetvioClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class BetvioCallbackController extends Controller
{
    protected $transactionTimeout = 2;
    
    // Статические кеши для данных, которые не меняются
    protected static $secretCache = [];
    protected static $currencyCache = [];
    protected static $gameNameCache = [];

    /**
     * Проверка баланса пользователя
     * POST /gold_api/user_balance
     */
    public function userBalance(Request $request)
    {
        try {
            $agentCode = $request->input('agent_code');
            $agentSecret = $request->input('agent_secret');
            $userCode = $request->input('user_code');

            if (!$this->verifyAgentSecretFast($agentCode, $agentSecret)) {
                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => 'INVALID_AGENT'
                ]);
            }

            $user = $this->findUserByCode($userCode);

            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => 'INVALID_USER'
                ]);
            }

            return response()->json([
                'status' => 1,
                'user_balance' => (float)$user->balance
            ]);

        } catch (Exception $e) {
            Log::error('Betvio user_balance error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 0,
                'user_balance' => 0,
                'msg' => 'INTERNAL_ERROR'
            ]);
        }
    }

    /**
     * Обработка игровых транзакций
     * POST /gold_api/game_callback
     */
    public function gameCallback(Request $request)
    {
        try {
            $agentCode = $request->input('agent_code');
            $agentSecret = $request->input('agent_secret');
            $userCode = $request->input('user_code');
            $slotData = $request->input('slot', []);

            // БЫСТРАЯ проверка secret
            if (!$this->verifyAgentSecretFast($agentCode, $agentSecret)) {
                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => 'INVALID_AGENT'
                ]);
            }

            $txnId = $slotData['txn_id'] ?? null;
            $txnType = $slotData['txn_type'] ?? null;
            $bet = max(0, (float)($slotData['bet'] ?? 0));
            $win = max(0, (float)($slotData['win'] ?? 0));

            if (!$txnId || !$txnType) {
                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => 'INVALID_PARAMETER'
                ]);
            }

            $hash = "betvio_{$txnId}_{$txnType}";

            // ПОЛУЧАЕМ gameName ДО транзакции (из кеша)
            $gameName = $this->getGameNameFast($slotData['provider_code'] ?? '', $slotData['game_code'] ?? '');

            return DB::transaction(function () use ($userCode, $txnId, $txnType, $bet, $win, $slotData, $hash, $gameName) {
                $user = $this->findUserWithLock($userCode);

                if (!$user) {
                    return response()->json([
                        'status' => 0,
                        'user_balance' => 0,
                        'msg' => 'INVALID_USER'
                    ]);
                }

                // БЫСТРАЯ проверка идемпотентности
                $exists = DB::selectOne('SELECT 1 FROM transactions WHERE hash = ? LIMIT 1', [$hash]);

                if ($exists) {
                    return response()->json([
                        'status' => 1,
                        'user_balance' => (float)$user->balance
                    ]);
                }

                $balance = (float)$user->balance;
                $balanceAfter = $balance;

                // Обработка по типу транзакции
                switch ($txnType) {
                    case 'debit':
                        if ($bet > $balance) {
                            return response()->json([
                                'status' => 0,
                                'user_balance' => $balance,
                                'msg' => 'INSUFFICIENT_USER_FUNDS'
                            ]);
                        }
                        $balanceAfter = $balance - $bet;
                        $this->saveTransaction($user, $hash, $bet, 'bet', $slotData);
                        break;

                    case 'credit':
                        $balanceAfter = $balance + $win;
                        $this->saveTransaction($user, $hash, $win, 'win', $slotData);
                        break;

                    case 'debit_credit':
                        if ($bet > $balance) {
                            return response()->json([
                                'status' => 0,
                                'user_balance' => $balance,
                                'msg' => 'INSUFFICIENT_USER_FUNDS'
                            ]);
                        }
                        $balanceAfter = $balance - $bet + $win;
                        
                        // ДВЕ записи: bet и win (для консистентности с Redis)
                        $this->saveTransaction($user, $hash, $bet, 'bet', $slotData);
                        if ($win > 0) {
                            $this->saveTransaction($user, "{$hash}_win", $win, 'win', $slotData);
                        }
                        break;

                    default:
                        return response()->json([
                            'status' => 0,
                            'user_balance' => $balance,
                            'msg' => 'INVALID_PARAMETER'
                        ]);
                }

                // ОДНА команда UPDATE
                DB::update('UPDATE users SET balance = ? WHERE id = ?', [$balanceAfter, $user->id]);

                // Redis ПОСЛЕ ответа клиенту
                $this->storeLiveBetAsync($user, $txnId, $bet, $win, $txnType, $slotData, $gameName);

                return response()->json([
                    'status' => 1,
                    'user_balance' => $balanceAfter
                ]);

            }, $this->transactionTimeout);

        } catch (Exception $e) {
            Log::error('Betvio game_callback error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 0,
                'user_balance' => 0,
                'msg' => 'INTERNAL_ERROR'
            ]);
        }
    }

    /**
     * Опциональный колбэк изменения баланса
     * POST /gold_api/money_callback
     */
    public function moneyCallback(Request $request)
    {
        try {
            $agentCode = $request->input('agent_code');
            $agentSecret = $request->input('agent_secret');
            $userCode = $request->input('user_code');
            $type = $request->input('type');
            $txnId = $request->input('txn_id');
            $amount = (float)$request->input('amount', 0);

            if (!$this->verifyAgentSecretFast($agentCode, $agentSecret)) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'INVALID_AGENT'
                ]);
            }

            // Если это транзакция игры - перенаправляем на game_callback
            if (in_array($type, ['debit', 'credit', 'debit_credit']) && $txnId) {
                $slotData = [
                    'provider_code' => $request->input('provider_code'),
                    'game_code' => $request->input('game_code'),
                    'round_id' => $txnId,
                    'txn_id' => $txnId,
                    'txn_type' => $type,
                    'bet' => 0,
                    'win' => 0,
                    'user_before_balance' => $request->input('user_before_balance'),
                    'user_after_balance' => $request->input('user_after_balance'),
                ];

                if ($type === 'debit' || $type === 'debit_credit') {
                    $slotData['bet'] = abs($amount);
                }
                
                if ($type === 'credit' || ($type === 'debit_credit' && $amount > 0)) {
                    $slotData['win'] = abs($amount);
                }

                $gameCallbackRequest = new Request([
                    'agent_code' => $agentCode,
                    'agent_secret' => $agentSecret,
                    'user_code' => $userCode,
                    'slot' => $slotData
                ]);

                return $this->gameCallback($gameCallbackRequest);
            }

            // Для остальных типов - просто логируем
            Log::info('Betvio money_callback (non-game)', $request->all());

            return response()->json([
                'status' => 1,
                'msg' => 'SUCCESS'
            ]);

        } catch (Exception $e) {
            Log::error('Betvio money_callback error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 0,
                'msg' => 'INTERNAL_ERROR'
            ]);
        }
    }

    /**
     * Универсальное сохранение транзакции
     */
    protected function saveTransaction($user, string $hash, float $amount, string $type, array $slotData): void
    {
        // МИНИМАЛЬНЫЙ context (без лишних полей)
        $context = json_encode([
            'provider_code' => $slotData['provider_code'] ?? null,
            'game_code' => $slotData['game_code'] ?? null,
            'round_id' => $slotData['round_id'] ?? null,
        ]);

        DB::insert(
            'INSERT INTO transactions (user_id, amount, currency_id, type, status, hash, context, created_at, updated_at) 
             VALUES (?, ?, ?, ?, "success", ?, ?, NOW(), NOW())',
            [$user->id, $amount, $user->currency_id, $type, $hash, $context]
        );
    }

    /**
     * БЫСТРОЕ получение названия игры (только из кеша)
     */
    protected function getGameNameFast(string $providerCode, string $gameCode): string
    {
        if (empty($providerCode) || empty($gameCode)) {
            return $gameCode ?: 'Unknown';
        }

        $cacheKey = "{$providerCode}:{$gameCode}";

        // Проверяем статический кеш (в памяти)
        if (isset(self::$gameNameCache[$cacheKey])) {
            return self::$gameNameCache[$cacheKey];
        }

        // Проверяем Laravel кеш
        $cached = cache()->get("betvio_game_{$cacheKey}");
        if ($cached) {
            self::$gameNameCache[$cacheKey] = $cached;
            return $cached;
        }

        // Запрос к БД только если нет в кеше
        $searchGameCode = json_encode([
            'provider_code' => $providerCode,
            'game_code' => $gameCode
        ]);

        $game = DB::selectOne(
            'SELECT name FROM slotegrator_games WHERE game_code = ? AND provider_type = ? LIMIT 1',
            [$searchGameCode, 'betvio']
        );

        $name = $game ? $game->name : $gameCode;

        // Сохраняем в оба кеша
        cache()->put("betvio_game_{$cacheKey}", $name, 604800); // 7 дней
        self::$gameNameCache[$cacheKey] = $name;

        return $name;
    }

    /**
     * Асинхронное сохранение в Redis
     */
    protected function storeLiveBetAsync($user, string $txnId, float $bet, float $win, string $txnType, array $slotData, string $gameName): void
    {
        $hash = "betvio_{$txnId}_{$txnType}";

        // Извлекаем данные пользователя ДО dispatch
        $userId = $user->id;
        $currencyId = $user->currency_id;
        $username = $user->username;
        $avatar = $user->avatar ?? '/assets/images/avatar-placeholder.png';

        // СТАТИЧЕСКИЙ кеш currency (валюта не меняется!)
        if (!isset(self::$currencyCache[$currencyId])) {
            $currency = DB::selectOne('SELECT symbol FROM currencies WHERE id = ? LIMIT 1', [$currencyId]);
            self::$currencyCache[$currencyId] = $currency ? $currency->symbol : 'USD';
        }
        $currencySymbol = self::$currencyCache[$currencyId];

        dispatch(function () use ($userId, $username, $avatar, $txnId, $bet, $win, $txnType, $gameName, $hash, $currencySymbol) {
            try {
                $timestamp = now()->toDateTimeString();

                if ($txnType === 'debit_credit') {
                    // ДВЕ транзакции для отображения коэффициента выигрыша
                    $betData = [
                        'user_id' => $userId,
                        'amount' => $bet,
                        'type' => 'bet',
                        'hash' => $hash,
                        'user' => [
                            'id' => $userId,
                            'username' => $username,
                            'avatar' => $avatar,
                        ],
                        'currency' => ['symbol' => $currencySymbol],
                        'game_name' => $gameName,
                        'created_at' => $timestamp,
                    ];

                    $winData = [
                        'user_id' => $userId,
                        'amount' => $win,
                        'type' => 'win',
                        'hash' => $hash . '_win',
                        'user' => [
                            'id' => $userId,
                            'username' => $username,
                            'avatar' => $avatar,
                        ],
                        'currency' => ['symbol' => $currencySymbol],
                        'game_name' => $gameName,
                        'created_at' => $timestamp,
                    ];

                    $betGameData = array_merge($betData, [
                        'round_id' => $txnId,
                        'finished' => true
                    ]);

                    $winGameData = array_merge($winData, [
                        'round_id' => $txnId,
                        'finished' => true
                    ]);

                    // JSON encoding перед pipeline
                    $betJson = json_encode($betData);
                    $winJson = json_encode($winData);
                    $betGameJson = json_encode($betGameData);
                    $winGameJson = json_encode($winGameData);

                    Redis::pipeline(function($pipe) use ($hash, $betJson, $winJson, $userId, $betGameJson, $winGameJson) {
                        $pipe->setex("transaction:{$hash}", 3600, $betJson);
                        $pipe->setex("transaction:{$hash}_win", 3600, $winJson);
                        $pipe->lpush("transactions:all", $hash, "{$hash}_win");
                        $pipe->ltrim("transactions:all", 0, 99);
                        $pipe->lpush("transactions:user:{$userId}", $hash, "{$hash}_win");
                        $pipe->ltrim("transactions:user:{$userId}", 0, 99);
                        $pipe->lpush("game_history:all", $betGameJson, $winGameJson);
                        $pipe->ltrim("game_history:all", 0, 499);
                    });

                    return;
                }

                // Для debit и credit - одна транзакция
                $displayType = $txnType === 'debit' ? 'bet' : 'win';
                $displayAmount = $txnType === 'debit' ? $bet : $win;

                $transactionData = [
                    'user_id' => $userId,
                    'amount' => $displayAmount,
                    'type' => $displayType,
                    'hash' => $hash,
                    'user' => [
                        'id' => $userId,
                        'username' => $username,
                        'avatar' => $avatar,
                    ],
                    'currency' => ['symbol' => $currencySymbol],
                    'game_name' => $gameName,
                    'created_at' => $timestamp,
                ];

                $gameData = array_merge($transactionData, [
                    'round_id' => $txnId,
                    'finished' => true
                ]);

                // JSON encoding перед pipeline
                $transactionJson = json_encode($transactionData);
                $gameJson = json_encode($gameData);

                Redis::pipeline(function($pipe) use ($hash, $transactionJson, $userId, $gameJson) {
                    $pipe->setex("transaction:{$hash}", 3600, $transactionJson);
                    $pipe->lpush("transactions:all", $hash);
                    $pipe->ltrim("transactions:all", 0, 99);
                    $pipe->lpush("transactions:user:{$userId}", $hash);
                    $pipe->ltrim("transactions:user:{$userId}", 0, 99);
                    $pipe->lpush("game_history:all", $gameJson);
                    $pipe->ltrim("game_history:all", 0, 499);
                });

            } catch (\RedisException $e) {
                Log::warning('Betvio Redis error', [
                    'hash' => $hash ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            } catch (\Exception $e) {
                Log::error('Betvio Redis critical error', [
                    'hash' => $hash ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        })->afterResponse();
    }

    /**
     * БЫСТРАЯ проверка agent_secret (статический кеш)
     */
    protected function verifyAgentSecretFast(string $agentCode, string $receivedSecret): bool
    {
        if (!isset(self::$secretCache[$agentCode])) {
            $account = BetvioClient::findAccountByAgentCode($agentCode);
            self::$secretCache[$agentCode] = $account['agent_secret'] ?? null;
        }

        return self::$secretCache[$agentCode] && hash_equals(self::$secretCache[$agentCode], $receivedSecret);
    }

    /**
     * Поиск пользователя по user_code
     */
    protected function findUserByCode(string $userCode)
    {
        return DB::selectOne(
            'SELECT id, balance, currency_id, username, avatar FROM users WHERE username = ? LIMIT 1',
            [$userCode]
        );
    }

    /**
     * Поиск пользователя с блокировкой FOR UPDATE
     */
    protected function findUserWithLock(string $userCode)
    {
        return DB::selectOne(
            'SELECT id, balance, currency_id, username, avatar FROM users WHERE username = ? LIMIT 1 FOR UPDATE',
            [$userCode]
        );
    }
}