<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bonus')->insert([
            ['sum' => 10.00, 'status' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['sum' => 1.00, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sum' => 0.01, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sum' => 0.02, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sum' => 0.03, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sum' => 0.04, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sum' => 0.05, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['sum' => 0.06, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],

        ]);
    }
}
