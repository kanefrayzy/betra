<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wallets')->insert([
            [
                'id' => 1,
                'system' => '2',
                'wallet' => '+994 50 516 71 98',
                'status' => null,
                'created_at' => '2023-11-01 12:31:19',
                'updated_at' => '2023-11-01 09:31:19'
            ],
            [
                'id' => 2,
                'system' => '1',
                'wallet' => '4098 5844 9777 7324',
                'status' => null,
                'created_at' => '2023-11-01 12:31:48',
                'updated_at' => '2023-11-01 09:31:48'
            ],
            [
                'id' => 3,
                'system' => '1',
                'wallet' => '5243 7544 4765 7659',
                'status' => null,
                'created_at' => '2023-11-01 12:35:04',
                'updated_at' => '2023-11-01 09:35:04'
            ],
            [
                'id' => 4,
                'system' => '0',
                'wallet' => '50 516 39 44',
                'status' => null,
                'created_at' => '2023-11-08 04:43:52',
                'updated_at' => '2023-11-08 01:43:52'
            ]
        ]);
    }
}
