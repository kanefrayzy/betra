<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'symbol' => 'USD', 'active' => true],
            ['name' => 'Russian Ruble', 'symbol' => 'RUB', 'active' => true],
            ['name' => 'Kazakhstani Tenge', 'symbol' => 'KZT', 'active' => true],
            ['name' => 'Turkish Lira', 'symbol' => 'TRY', 'active' => true],
            ['name' => 'Azerbaijani Manat', 'symbol' => 'AZN', 'active' => true],
            ['name' => 'Uzbekistani Som', 'symbol' => 'UZS', 'active' => true],
            ['name' => 'Euro', 'symbol' => 'EUR', 'active' => true],
            ['name' => 'Polish Zloty', 'symbol' => 'PLN', 'active' => true]
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['symbol' => $currency['symbol']],
                $currency
            );
        }
    }
}
