<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentSystem;
use App\Models\PaymentHandler;
use Illuminate\Support\Facades\DB;

class WestWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем PaymentSystem для WestWallet, если не существует
        $westWallet = PaymentSystem::firstOrCreate(
            ['name' => 'WestWallet'],
            [
                'url' => 'https://westwallet.io',
                'merchant_id' => config('payment.westwallet.public_key', ''),
                'merchant_secret_1' => config('payment.westwallet.private_key', ''),
                'merchant_secret_2' => null,
                'active' => true,
                'logo' => null,
            ]
        );

        // Список криптовалют для добавления
        $cryptoCurrencies = [
            [
                'name' => 'Bitcoin (BTC)',
                'currency' => 'BTC',
                'min_deposit' => 0.0001,
                'min_withdrawal' => 0.001,
            ],
            [
                'name' => 'Ethereum (ETH)',
                'currency' => 'ETH',
                'min_deposit' => 0.001,
                'min_withdrawal' => 0.01,
            ],
            [
                'name' => 'Tether USDT (TRC20)',
                'currency' => 'USDT',
                'min_deposit' => 1,
                'min_withdrawal' => 5,
            ],
            [
                'name' => 'TRON (TRX)',
                'currency' => 'TRX',
                'min_deposit' => 10,
                'min_withdrawal' => 50,
            ],
            [
                'name' => 'Litecoin (LTC)',
                'currency' => 'LTC',
                'min_deposit' => 0.01,
                'min_withdrawal' => 0.05,
            ],
            [
                'name' => 'Ripple (XRP)',
                'currency' => 'XRP',
                'min_deposit' => 1,
                'min_withdrawal' => 10,
            ],
        ];

        foreach ($cryptoCurrencies as $crypto) {
            PaymentHandler::updateOrCreate(
                [
                    'payment_system_id' => $westWallet->id,
                    'currency' => $crypto['currency'],
                ],
                [
                    'name' => $crypto['name'],
                    'withdrawal_fee' => 0.00,
                    'deposit_fee' => 0.00,
                    'min_withdrawal_limit' => $crypto['min_withdrawal'],
                    'max_withdrawal_limit' => 50000.00,
                    'min_deposit_limit' => $crypto['min_deposit'],
                    'max_deposit_limit' => 50000.00,
                    'active' => true,
                    'withdrawal_mode' => 'manual', // Пока выводы вручную
                    'auto_withdrawal_enabled' => false,
                    'require_admin_approval' => true,
                ]
            );
        }

        $this->command->info('WestWallet payment handlers created successfully!');
    }
}
