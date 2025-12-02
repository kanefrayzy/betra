<?php
namespace Database\Seeders;

use App\Models\DepositBonus;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class DepositBonusSeeder extends Seeder
{
    public function run()
    {
        $azn = Currency::where('symbol', 'AZN')->first();

        if (!$azn) {
            return;
        }

        $bonuses = [
            ['required' => 5, 'bonus' => 1],
            ['required' => 20, 'bonus' => 2.30],
            ['required' => 100, 'bonus' => 7.50],
            ['required' => 250, 'bonus' => 45],
            ['required' => 500, 'bonus' => 85],
            ['required' => 1000, 'bonus' => 120],
        ];

        foreach ($bonuses as $bonus) {
            DepositBonus::create([
                'required_amount' => $bonus['required'],
                'bonus_amount' => $bonus['bonus'],
                'currency_id' => $azn->id
            ]);
        }
    }
}
