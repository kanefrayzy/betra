<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CurrencySeeder::class);
        // $this->call(RanksSeeder::class);
        $this->call(SettingsSeeder::class);
        // $this->call(UsersSeeder::class);
        // $this->call(WalletsSeeder::class);
        $this->call(DepositBonusSeeder::class);
    }
}
