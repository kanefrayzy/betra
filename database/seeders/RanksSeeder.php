<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ranks')->insert([
            [
                'name' => 'Rank 1',
                'oborot_min' => 0,
                'oborot_max' => 100,
                'rakeback' => 0.01,
                'daily' => 0.01,
            ],

        ]);
    }
}
