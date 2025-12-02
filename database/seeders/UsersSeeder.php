<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'username' => 'Admin',
                'avatar' => 'a',
                'user_id' => '00735213',
                'network_id' => null,
                'network_type' => 'google',
                'email' => 'admin@gmail.com',
                'currency_id' => 1,
                'password' => Hash::make('123456'),
                'balance' => 10000,
                'rakeback' => 0,
                'ip' => '186.2.160.221',
                'is_admin' => 1,
                'is_moder' => 0,
                'is_youtuber' => 0,
                'banchat' => NULL,
                'fake' => 0,
                'ban' => 0,
                'affiliate_id' => 's4M9vQiTVO',
                'referred_by' => NULL,
                'ref_money' => 0.00,
                'ref_money_history' => 0,
                'from_ref' => 0,
                'persona_hash' => NULL,
                'remember_token' => 'pjr0YlN5mMVdDy4LECCSgtBvV9x6sJOtVqVvQAIfjjIpSsZygUzuxwxPy9Ib',
                'created_at' => '2023-10-07 20:42:01',
                'updated_at' => '2023-11-20 07:19:32',
                'auth_token' => '1aKCaeAvqNBNlbgJFJjeNjDHOFkvNvRI2oerdTqDPTQOEVv9G7KnZd7CWM6A',
                'oborot' => 0,
                'sessionId' => '186123923',
                'rain_money' => 0.00,
                'rank_id' => 1,
            ]
        ]);
    }
}
