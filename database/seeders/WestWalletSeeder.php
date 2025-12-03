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

        // Список криптовалют для добавления с учетом сетей
        $cryptoCurrencies = [
            // Bitcoin
            [
                'name' => 'BTC (Bitcoin)',
                'currency' => 'BTC',
                'network' => null,
                'min_deposit' => 0.0001,
                'min_withdrawal' => 0.001,
            ],
            // Ethereum
            [
                'name' => 'ETH (Ethereum)',
                'currency' => 'ETH',
                'network' => null,
                'min_deposit' => 0.001,
                'min_withdrawal' => 0.01,
            ],
            // USDT - разные сети
            [
                'name' => 'USDT (TRC20)',
                'currency' => 'USDT',
                'network' => 'TRC20',
                'min_deposit' => 1,
                'min_withdrawal' => 5,
            ],
            [
                'name' => 'USDT (ERC20)',
                'currency' => 'USDT',
                'network' => 'ERC20',
                'min_deposit' => 10,
                'min_withdrawal' => 20,
            ],
            [
                'name' => 'USDT (BEP20)',
                'currency' => 'USDT',
                'network' => 'BEP20',
                'min_deposit' => 1,
                'min_withdrawal' => 5,
            ],
            [
                'name' => 'USDT (TON)',
                'currency' => 'USDT',
                'network' => 'TON',
                'min_deposit' => 1,
                'min_withdrawal' => 5,
            ],
            // TRON
            [
                'name' => 'TRX (Tron)',
                'currency' => 'TRX',
                'network' => null,
                'min_deposit' => 10,
                'min_withdrawal' => 50,
            ],
            // Litecoin
            [
                'name' => 'LTC (Litecoin)',
                'currency' => 'LTC',
                'network' => null,
                'min_deposit' => 0.01,
                'min_withdrawal' => 0.05,
            ],
            // Ripple
            [
                'name' => 'XRP (Ripple)',
                'currency' => 'XRP',
                'network' => null,
                'min_deposit' => 1,
                'min_withdrawal' => 10,
            ],
            // TON
            [
                'name' => 'TON (Toncoin)',
                'currency' => 'TON',
                'network' => null,
                'min_deposit' => 1,
                'min_withdrawal' => 5,
            ],
        ];

        foreach ($cryptoCurrencies as $crypto) {
            PaymentHandler::updateOrCreate(
                [
                    'payment_system_id' => $westWallet->id,
                    'currency' => $crypto['currency'],
                    'network' => $crypto['network'],
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
                    'withdrawal_mode' => 'manual',
                    'auto_withdrawal_enabled' => false,
                    'require_admin_approval' => true,
                ]
            );
        }

        $this->command->info('WestWallet payment handlers created successfully!');
    }
}
