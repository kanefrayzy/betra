<?php

namespace App\Http\Controllers;

use App\Models\UserCryptoWallet;
use App\Models\Transaction;
use App\Models\User;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Notifications\Notify;
use App\Services\ExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WestWalletCallbackController extends Controller
{
    /**
     * Обработка IPN уведомлений от WestWallet
     * Документация: https://api.westwallet.io/
     */
    public function handle(Request $request)
    {
        // Проверка IP адреса
        if (!$this->isTrustedIP($request->ip())) {
            Log::error('WestWallet IPN: Unauthorized IP', ['ip' => $request->ip()]);
            return response('Unauthorized IP address', 403);
        }

        Log::info('WestWallet IPN Request', $request->all());

        try {
            // Получаем данные из запроса
            $data = $request->all();

            // Валидация обязательных полей
            if (!isset($data['label'], $data['status'], $data['amount'], $data['currency'])) {
                Log::error('WestWallet IPN: Missing required fields', $data);
                return response('Missing required fields', 400);
            }

            // Находим кошелек по label
            $wallet = UserCryptoWallet::where('label', $data['label'])->first();

            if (!$wallet) {
                Log::error('WestWallet IPN: Wallet not found', ['label' => $data['label']]);
                return response('Wallet not found', 404);
            }

            $user = $wallet->user;

            // Проверяем статус
            if ($data['status'] !== 'completed') {
                Log::info('WestWallet IPN: Transaction not completed', [
                    'label' => $data['label'],
                    'status' => $data['status']
                ]);
                return response('Transaction not completed yet', 200);
            }

            // Проверяем, не обработана ли уже эта транзакция
            $txId = $data['transaction'] ?? $data['id'] ?? null;
            if ($txId) {
                $existingTransaction = Transaction::where('context->westwallet_tx_id', $txId)
                    ->where('status', TransactionStatus::Success)
                    ->first();

                if ($existingTransaction) {
                    Log::info('WestWallet IPN: Transaction already processed', ['tx_id' => $txId]);
                    return response('Transaction already processed', 200);
                }
            }

            // Обрабатываем пополнение
            $this->processDeposit($user, $wallet, $data);

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('WestWallet IPN Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Internal Server Error', 500);
        }
    }

    /**
     * Обработка депозита
     */
    private function processDeposit(User $user, UserCryptoWallet $wallet, array $data): void
    {
        DB::transaction(function () use ($user, $wallet, $data) {
            $amount = (float)$data['amount'];
            $currency = strtoupper($data['currency']);
            
            // Получаем валюту пользователя
            $userCurrency = $user->currency;
            
            // Конвертируем сумму если нужно
            $amountToCredit = $amount;
            if ($currency !== $userCurrency->symbol) {
                $exchangeService = new ExchangeService();
                $amountToCredit = $exchangeService->convert(
                    $amount,
                    $currency,
                    $userCurrency->symbol
                );
            }

            // Создаем транзакцию
            $transaction = $user->transactions()->create([
                'amount' => $amountToCredit,
                'currency_id' => $user->currency_id,
                'type' => TransactionType::Deposit,
                'status' => TransactionStatus::Success,
                'hash' => $data['txid'] ?? $data['hash'] ?? \Illuminate\Support\Str::uuid()->toString(),
                'context' => json_encode([
                    'payment_system' => 'WestWallet',
                    'payment_handler' => 'WestWallet Crypto',
                    'balance_before' => $user->balance,
                    'crypto_currency' => $currency,
                    'crypto_amount' => $amount,
                    'wallet_address' => $wallet->address,
                    'westwallet_tx_id' => $data['transaction'] ?? $data['id'] ?? null,
                    'txid' => $data['txid'] ?? null,
                    'confirmations' => $data['confirmations'] ?? null,
                    'explorer_link' => $data['explorer_transaction_link'] ?? null,
                    'address_from' => $data['address_from'] ?? null,
                ]),
            ]);

            // Создаем запись о платеже
            $user->payments()->create([
                'amount' => $amountToCredit,
                'details' => $wallet->address,
                'currency_id' => $user->currency_id,
                'transaction_id' => $transaction->id,
                'external_id' => $data['transaction'] ?? $data['id'] ?? \Illuminate\Support\Str::uuid()->toString(),
                'status' => \App\Enums\PaymentStatus::Completed,
                'processed_at' => now(),
                'comment' => "Crypto deposit: {$amount} {$currency}",
            ]);

            // Зачисляем средства пользователю
            $user->balance += $amountToCredit;
            $user->save();

            // Обновляем статистику кошелька
            $wallet->incrementStats($amount);

            // Отправляем уведомление пользователю
            $messageNotify = __('Депозит на сумму :amount :currency успешно зачислен', [
                'amount' => moneyFormat($amountToCredit),
                'currency' => $userCurrency->symbol
            ]);

            $user->notify(Notify::send('deposit', ['message' => $messageNotify]));

            Log::info('WestWallet Deposit Processed', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'crypto_amount' => $amount,
                'crypto_currency' => $currency,
                'credited_amount' => $amountToCredit,
                'credited_currency' => $userCurrency->symbol,
            ]);
        });
    }

    /**
     * Проверка доверенного IP адреса
     */
    private function isTrustedIP(string $ip): bool
    {
        $trustedIPs = config('payment.westwallet.trusted_ips', ['5.188.51.47']);
        
        // В режиме разработки можно отключить проверку
        if (config('app.debug') && config('payment.westwallet.skip_ip_check', false)) {
            return true;
        }

        return in_array($ip, $trustedIPs);
    }
}
