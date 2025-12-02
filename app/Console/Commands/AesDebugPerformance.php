<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class AesDebugPerformance extends Command
{
    protected $signature = 'aes:debug';
    protected $description = 'Debug AES performance bottlenecks';

    public function handle()
    {
        $this->info('🔍 AES Performance Debug');
        $this->newLine();

        // 1. Database latency
        $this->info('1️⃣ Testing Database...');
        $dbStart = microtime(true);
        DB::connection()->getPdo();
        $dbConnect = (microtime(true) - $dbStart) * 1000;

        $queryStart = microtime(true);
        $result = DB::selectOne('SELECT 1');
        $queryTime = (microtime(true) - $queryStart) * 1000;

        $this->table(['Test', 'Time', 'Status'], [
            ['DB Connection', round($dbConnect, 2) . ' ms', $dbConnect < 5 ? '✅' : '❌'],
            ['Simple Query', round($queryTime, 2) . ' ms', $queryTime < 2 ? '✅' : '❌'],
        ]);

        // 2. User query performance
        $this->info('2️⃣ Testing User Query...');
        $userId = DB::selectOne('SELECT id FROM users LIMIT 1')->id ?? 1;

        $start = microtime(true);
        $user = DB::selectOne('SELECT id, balance, currency_id FROM users WHERE id = ? LIMIT 1 FOR UPDATE', [$userId]);
        $userQueryTime = (microtime(true) - $start) * 1000;

        $this->line('User Query: ' . round($userQueryTime, 2) . ' ms ' . ($userQueryTime < 3 ? '✅' : '❌ SLOW!'));

        // 3. Transaction insert performance
        $this->info('3️⃣ Testing Transaction Insert...');
        $start = microtime(true);
        DB::insert(
            'INSERT INTO transactions (user_id, amount, currency_id, type, status, hash, context, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [$userId, 1.0, 1, 'test', 'success', 'test_' . time(), '{}']
        );
        $insertTime = (microtime(true) - $start) * 1000;
        DB::delete('DELETE FROM transactions WHERE hash LIKE "test_%"');

        $this->line('Transaction Insert: ' . round($insertTime, 2) . ' ms ' . ($insertTime < 2 ? '✅' : '❌ SLOW!'));

        // 4. Cache performance
        $this->info('4️⃣ Testing Cache...');
        $start = microtime(true);
        Cache::put('test_key', 'test_value', 60);
        Cache::get('test_key');
        Cache::forget('test_key');
        $cacheTime = (microtime(true) - $start) * 1000;

        $this->line('Cache Operations: ' . round($cacheTime, 2) . ' ms ' . ($cacheTime < 1 ? '✅' : '❌ SLOW!'));

        // 5. Redis performance
        $this->info('5️⃣ Testing Redis...');
        try {
            $start = microtime(true);
            Redis::connection()->client()->ping();
            $redisTime = (microtime(true) - $start) * 1000;
            $this->line('Redis Ping: ' . round($redisTime, 2) . ' ms ' . ($redisTime < 1 ? '✅' : '❌ SLOW!'));
        } catch (\Exception $e) {
            $this->error('Redis Error: ' . $e->getMessage());
        }

        // 6. Full transaction simulation
        $this->newLine();
        $this->info('6️⃣ Simulating Full BET Transaction...');

        $totalStart = microtime(true);

        DB::transaction(function () use ($userId, &$times) {
            $times = [];

            // Step 1: Lock user
            $start = microtime(true);
            $user = DB::selectOne('SELECT id, balance, currency_id FROM users WHERE id = ? LIMIT 1 FOR UPDATE', [$userId]);
            $times['lock'] = (microtime(true) - $start) * 1000;

            // Step 2: Update balance
            $start = microtime(true);
            DB::update('UPDATE users SET balance = balance - ? WHERE id = ?', [1.0, $userId]);
            $times['update'] = (microtime(true) - $start) * 1000;

            // Step 3: Insert transaction
            $start = microtime(true);
            DB::insert(
                'INSERT INTO transactions (user_id, amount, currency_id, type, status, hash, context, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
                [$userId, 1.0, $user->currency_id, 'bet', 'success', 'sim_' . time(), '{}']
            );
            $times['insert'] = (microtime(true) - $start) * 1000;

            // Cleanup
            DB::delete('DELETE FROM transactions WHERE hash LIKE "sim_%"');
            DB::update('UPDATE users SET balance = balance + ? WHERE id = ?', [1.0, $userId]);
        });

        $totalTime = (microtime(true) - $totalStart) * 1000;

        $this->newLine();
        $this->table(['Step', 'Time'], [
            ['1. Lock User (FOR UPDATE)', round($times['lock'], 2) . ' ms'],
            ['2. Update Balance', round($times['update'], 2) . ' ms'],
            ['3. Insert Transaction', round($times['insert'], 2) . ' ms'],
            ['🔥 TOTAL', round($totalTime, 2) . ' ms'],
        ]);

        // 7. Check indexes
        $this->newLine();
        $this->info('7️⃣ Checking Indexes...');

        $indexes = [
            'users.aes_user_code' => DB::select("SHOW INDEX FROM users WHERE Column_name = 'aes_user_code'"),
            'users.id (PRIMARY)' => DB::select("SHOW INDEX FROM users WHERE Key_name = 'PRIMARY'"),
            'transactions.hash' => DB::select("SHOW INDEX FROM transactions WHERE Column_name = 'hash'"),
        ];

        foreach ($indexes as $name => $result) {
            $status = !empty($result) ? '✅ EXISTS' : '❌ MISSING';
            $this->line("{$name}: {$status}");
        }

        // 8. Recommendations
        $this->newLine();
        $this->info('📋 Analysis:');

        if ($totalTime > 10) {
            $this->warn('⚠️  Transaction time is HIGH (' . round($totalTime, 2) . ' ms)');
            $this->newLine();

            if ($dbConnect > 5) {
                $this->error('❌ DB Connection is SLOW! Check network/DB server');
            }
            if ($times['lock'] > 5) {
                $this->error('❌ User lock is SLOW! Check users table size/indexes');
                $this->line('   Run: ANALYZE TABLE users;');
            }
            if ($times['insert'] > 3) {
                $this->error('❌ Transaction insert is SLOW! Check transactions table');
                $this->line('   Run: ANALYZE TABLE transactions;');
                $this->line('   Check: disk I/O, innodb_buffer_pool_size');
            }
        } else {
            $this->info('✅ Performance looks good! (' . round($totalTime, 2) . ' ms)');
            $this->line('   The slowdown might be from:');
            $this->line('   - Network latency to AES servers');
            $this->line('   - PHP processing overhead');
            $this->line('   - Laravel framework overhead');
        }

        $this->newLine();
        $this->info('💡 Quick Fixes:');
        $this->line('1. Optimize MySQL: innodb_buffer_pool_size = 1G');
        $this->line('2. Enable query cache (MySQL < 8.0)');
        $this->line('3. Check slow query log');
        $this->line('4. Run: OPTIMIZE TABLE transactions;');
        $this->line('5. Consider persistent DB connections');
    }
}
