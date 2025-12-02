<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\SlotegratorGame;
use Illuminate\Support\Facades\Cache;

class TransactionService
{
    public function isExistingTransaction($transactionId)
    {
        return Redis::exists("transaction:$transactionId");
    }

    public function createTransaction($user, $data, $type, $amount, $originalBalance)
    {
        $game = Cache::remember("game_uuid:{$data['game_uuid']}", 3600, function () use ($data) {
            return SlotegratorGame::where('uuid', $data['game_uuid'])->first();
        });

        $gameName = $game ? $game->name : $data['game_uuid'];

        $transaction = [
            'id' => uniqid(),
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_id' => $user->currency_id,
            'type' => $type,
            'status' => 'success',
            'hash' => $data['transaction_id'],
            'context' => json_encode([
                'description' => ucfirst($type) . " in game $gameName",
                'amount' => $amount,
                'session_token' => $data['session_id'],
                'balance_before' => $originalBalance,
                'balance_after' => $user->balance,
                'bet_transaction_id' => $data['bet_transaction_id'] ?? null,
            ]),
            'created_at' => now()->toDateTimeString(),
        ];

        $this->storeTransactionInRedis($transaction);

        return $transaction;
    }

    private function storeTransactionInRedis($transaction)
    {
        $transactionJson = json_encode($transaction);

        Redis::multi();
        Redis::set("transaction:{$transaction['hash']}", $transactionJson);
        Redis::expire("transaction:{$transaction['hash']}", 3600); // Хранить 1 час

        Redis::lpush("transactions:all", $transactionJson);
        Redis::ltrim("transactions:all", 0, 99); // Хранить только последние 100 транзакций

        Redis::lpush("transactions:user:{$transaction['user_id']}", $transactionJson);
        Redis::ltrim("transactions:user:{$transaction['user_id']}", 0, 99);
        Redis::exec();
    }

    public function getLatestTransactions($limit = 10)
    {
        $transactions = Redis::lrange("transactions:all", 0, $limit - 1);
        return array_map(function($transaction) {
            return json_decode($transaction, true);
        }, $transactions);
    }

    public function getLatestUserTransactions($userId, $limit = 10)
    {
        $transactions = Redis::lrange("transactions:user:$userId", 0, $limit - 1);
        return array_map(function($transaction) {
            return json_decode($transaction, true);
        }, $transactions);
    }
}
