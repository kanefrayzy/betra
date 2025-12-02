<?php

namespace Database\Seeders;

use App\Models\PaymentSystem;
use App\Models\PaymentHandler;
use Illuminate\Database\Seeder;

class BetaTransferSeeder extends Seeder
{
    public function run(): void
    {
        $betaTransfer = PaymentSystem::where('name', 'BetaTransfer')->first();

        if (!$betaTransfer) {
            echo "❌ BetaTransfer не найден в payment_systems!\n";
            return;
        }

        $handlers = [
            // RU: P2P SBP by phone number
            [
                'name' => 'RU SBP (по номеру телефона)',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'RUB',
                'withdrawal_fee' => '5.50', // 5.5% + 60 RUB фиксированно добавится
                'deposit_fee' => '13.50',
                'min_withdrawal_limit' => '1000.00',
                'max_withdrawal_limit' => '100000.00',
                'min_deposit_limit' => '1000.00',
                'max_deposit_limit' => '100000.00',
                'active' => false,
            ],
            
            // RU: P2P by card number
            [
                'name' => 'RU Card P2P (Visa/MC/МИР)',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'RUB',
                'withdrawal_fee' => '5.50', // 5.5% + 60 RUB
                'deposit_fee' => '13.50',
                'min_withdrawal_limit' => '1000.00',
                'max_withdrawal_limit' => '100000.00',
                'min_deposit_limit' => '1000.00',
                'max_deposit_limit' => '100000.00',
                'active' => false,
            ],
            
            // KZ: P2P by card
            [
                'name' => 'KZ Card P2P (Visa/MC)',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'KZT',
                'withdrawal_fee' => '4.00',
                'deposit_fee' => '8.50',
                'min_withdrawal_limit' => '4000.00',
                'max_withdrawal_limit' => '200000.00',
                'min_deposit_limit' => '4000.00',
                'max_deposit_limit' => '200000.00',
                'active' => false,
            ],
            
            // UZ: UZcard P2P (30 000-5 700 000 UZS ~ 3-570 USD)
            [
                'name' => 'UZ UZcard P2P',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'UZS',
                'withdrawal_fee' => '3.00',
                'deposit_fee' => '8.00',
                'min_withdrawal_limit' => '30000.00',
                'max_withdrawal_limit' => '999999.99', // Максимум для decimal(8,2)
                'min_deposit_limit' => '30000.00',
                'max_deposit_limit' => '999999.99',
                'active' => false,
            ],
            
            // UZ: HUMO P2P
            [
                'name' => 'UZ HUMO P2P',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'UZS',
                'withdrawal_fee' => '3.00',
                'deposit_fee' => '8.00',
                'min_withdrawal_limit' => '30000.00',
                'max_withdrawal_limit' => '999999.99',
                'min_deposit_limit' => '30000.00',
                'max_deposit_limit' => '999999.99',
                'active' => false,
            ],
            
            // UZ: Payme
            [
                'name' => 'UZ Payme',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'UZS',
                'withdrawal_fee' => '3.00',
                'deposit_fee' => '8.00',
                'min_withdrawal_limit' => '30000.00',
                'max_withdrawal_limit' => '999999.99',
                'min_deposit_limit' => '30000.00',
                'max_deposit_limit' => '999999.99',
                'active' => false,
            ],
            
            // AZ: P2P (Visa/MC, M10)
            [
                'name' => 'AZ Card P2P (Visa/MC)',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'AZN',
                'withdrawal_fee' => '4.00',
                'deposit_fee' => '8.00',
                'min_withdrawal_limit' => '20.00',
                'max_withdrawal_limit' => '3000.00',
                'min_deposit_limit' => '20.00',
                'max_deposit_limit' => '3000.00',
                'active' => false,
            ],
            
            [
                'name' => 'AZ M10',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'AZN',
                'withdrawal_fee' => '4.00',
                'deposit_fee' => '8.00',
                'min_withdrawal_limit' => '20.00',
                'max_withdrawal_limit' => '3000.00',
                'min_deposit_limit' => '20.00',
                'max_deposit_limit' => '3000.00',
                'active' => false,
            ],
            
            // AZ: Acquiring (квази ecom)
            [
                'name' => 'AZ Acquiring (Visa/MC)',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'AZN',
                'withdrawal_fee' => '3.00',
                'deposit_fee' => '9.50',
                'min_withdrawal_limit' => '100.00',
                'max_withdrawal_limit' => '5000.00',
                'min_deposit_limit' => '100.00',
                'max_deposit_limit' => '5000.00',
                'active' => false,
            ],
            
            // Binance Pay
            [
                'name' => 'Binance Pay',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'USD',
                'withdrawal_fee' => '0.00', // Выплата на Binance account
                'deposit_fee' => '2.50',
                'min_withdrawal_limit' => '1.00',
                'max_withdrawal_limit' => '5000.00',
                'min_deposit_limit' => '1.00',
                'max_deposit_limit' => '5000.00',
                'active' => false,
            ],
            
            // Crypto: USDT/TUSD
            [
                'name' => 'USDT/TUSD',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'USDT',
                'withdrawal_fee' => '0.00', // $10 фиксированно
                'deposit_fee' => '2.00',
                'min_withdrawal_limit' => '5.00',
                'max_withdrawal_limit' => '50000.00',
                'min_deposit_limit' => '5.00',
                'max_deposit_limit' => '50000.00',
                'active' => false,
            ],
            
            // Crypto: BTC
            [
                'name' => 'Bitcoin',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'BTC',
                'withdrawal_fee' => '0.00', // $10 фиксированно
                'deposit_fee' => '0.00',
                'min_withdrawal_limit' => '5.00',
                'max_withdrawal_limit' => '50000.00',
                'min_deposit_limit' => '5.00',
                'max_deposit_limit' => '50000.00',
                'active' => false,
            ],
            
            // Crypto: ETH
            [
                'name' => 'Ethereum',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'ETH',
                'withdrawal_fee' => '0.00', // $10 фиксированно
                'deposit_fee' => '0.00',
                'min_withdrawal_limit' => '5.00',
                'max_withdrawal_limit' => '50000.00',
                'min_deposit_limit' => '5.00',
                'max_deposit_limit' => '50000.00',
                'active' => false,
            ],
            
            // Crypto: TRX
            [
                'name' => 'TRON',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'TRX',
                'withdrawal_fee' => '0.00', // $10 фиксированно
                'deposit_fee' => '0.00',
                'min_withdrawal_limit' => '5.00',
                'max_withdrawal_limit' => '50000.00',
                'min_deposit_limit' => '5.00',
                'max_deposit_limit' => '50000.00',
                'active' => false,
            ],
            
            // Crypto: LTC
            [
                'name' => 'Litecoin',
                'payment_system_id' => $betaTransfer->id,
                'currency' => 'LTC',
                'withdrawal_fee' => '0.00', // $10 фиксированно
                'deposit_fee' => '0.00',
                'min_withdrawal_limit' => '5.00',
                'max_withdrawal_limit' => '50000.00',
                'min_deposit_limit' => '5.00',
                'max_deposit_limit' => '50000.00',
                'active' => false,
            ],
        ];

        foreach ($handlers as $handler) {
            PaymentHandler::create($handler);
            echo "✅ Создан: {$handler['name']} ({$handler['currency']})\n";
        }

        echo "\n✅ Все обработчики BetaTransfer созданы!\n";
        echo "📝 Не забудьте обновить маппинг в BetaTransferService::getPaymentSystemCode()\n";
    }
}
