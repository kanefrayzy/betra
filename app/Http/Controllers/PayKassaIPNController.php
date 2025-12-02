<?php

namespace App\Http\Controllers;

use App\Notifications\Notify;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Paykassa\PaykassaSCI;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ExchangeService;

class PayKassaIPNController extends Controller
{
    protected $paykassa;

    public function __construct()
    {
        $this->paykassa = new PaykassaSCI(
            config('payment.paykassa.merchant_id'),
            config('payment.paykassa.merchant_key'),
            config('payment.paykassa.test_mode', false)
        );
    }

    public function handle(Request $request)
    {
        Log::info('PayKassa IPN Request', $request->all());

        $privateHash = $request->input('private_hash');

        if (!$privateHash) {
            Log::error('PayKassa IPN Error: Missing private_hash');
            return response('Missing private_hash', 400);
        }

        $result = $this->paykassa->checkOrderIpn($privateHash);

        if ($result['error']) {
            Log::error('PayKassa IPN Error: ' . $result['message']);
            return response($result['message'], 400);
        }

        $data = $result['data'];

        if (!isset($data['status'])) {
            Log::warning('PayKassa IPN Warning: Status not set in response data', $data);
            $data['status'] = 'yes'; // или другое значение по умолчанию, если это применимо
        }


        DB::transaction(function () use ($data) {
            $transaction = Transaction::where('id', $data['order_id'])->first();

            if (!$transaction) {
                Log::error('PayKassa IPN: Transaction not found', ['order_id' => $data['order_id']]);
                throw new \Exception('Transaction not found');
            }

            // Проверим статус транзакции
          // Log::info('PayKassa IPN: Found transaction', ['order_id' => $data['order_id'], 'status' => $transaction->status]);
          if ($transaction->status->value == 'success') {
                Log::info('PayKassa IPN: Transaction already successful, ignoring.', ['order_id' => $data['order_id']]);
                return response('Transaction already successful', 200);
            }

            if ($data['status'] === 'yes') {
                $this->processSuccessfulPayment($transaction, $data);
            } else {
                $this->updateTransactionStatus($transaction, $data);
            }
        });

        return response($data['order_id'] . '|success', 200);
    }

    protected function processSuccessfulPayment($transaction, $data)
    {
        $sum = $transaction->amount;  // Adjust the amount as needed
        // $sum = $this->convertToUSD($amount, $data['currency']);

        $transaction->update([
            'status' => 'success',
            'context' => json_encode([
                'payment_system' => 'paykassa',
                'transaction_id' => $data['transaction'] ?? null,
                'txid' => $data['txid'] ?? null,
                'amount' => $data['amount'],
                'fee' => $data['fee'] ?? null,
                'currency' => $data['currency'],
                'system' => $data['system'],
                'address_from' => $data['address_from'] ?? null,
                'address' => $data['address'] ?? null,
                'confirmations' => $data['confirmations'] ?? null,
                'required_confirmations' => $data['required_confirmations'] ?? null,
                'explorer_transaction_link' => $data['explorer_transaction_link'] ?? null,
            ]),
        ]);

        $user = $transaction->user;
                  $userCurrency = $user->currency;  // Предполагается, что у пользователя есть активная валюта
                  $transactionCurrency = $transaction->currency;  // Валюта транзакции

                  $amountToCredit = $transaction->amount;

                  // Проверяем валюту транзакции и активную валюту пользователя
                  if ($transactionCurrency->id != $userCurrency->id) {
                      $exchangeService = new ExchangeService();
                      $amountToCredit = $exchangeService->convert(
                          $transaction->amount,
                          $transactionCurrency->symbol,
                          $userCurrency->symbol
                      );
                  }
                  $user->balance += $amountToCredit;
       $user->save();
        $crns = $transaction->currency->symbol;

        // Отправка уведомления
        $messageNotify = __('Deposit with amount :amount :currency successful', [
          'amount' => moneyFormat($transaction->amount),
          'currency' => $transactionCurrency->symbol
        ]);

        $user->notify(Notify::send('deposit', ['message' => $messageNotify]));
        
    }

    protected function updateTransactionStatus($transaction, $data)
    {
        $status = $data['status'] ?? 'failed';

        $transaction->update([
            'status' => $status === 'yes' ? 'success' : 'failed',
            'context' => json_encode([
                'payment_system' => 'paykassa',
                'transaction_id' => $data['transaction'],
                'txid' => $data['txid'],
                'amount' => $data['amount'],
                'fee' => $data['fee'],
                'currency' => $data['currency'],
                'system' => $data['system'],
                'address_from' => $data['address_from'],
                'address' => $data['address'],
                'confirmations' => $data['confirmations'],
                'required_confirmations' => $data['required_confirmations'],
                'explorer_transaction_link' => $data['explorer_transaction_link'],
                'status' => $status,
            ]),
        ]);

        Log::info('PayKassa: Payment status updated', ['order_id' => $data['order_id'], 'status' => $status]);
    }

    // protected function handleReferralBonus(User $user, $sum)
    // {
    //     if ($user->referrer_id) {
    //         $referralBonus = $sum * 0.1;
    //         $referrer = User::find($user->referrer_id);
    //
    //         if ($referrer) {
    //             $referrer->balance += $referralBonus;
    //             $referrer->ref_money_history += $referralBonus;
    //             $referrer->save();
    //         }
    //
    //         $user->from_ref += $referralBonus;
    //         $user->save();
    //     }
    // }

    // protected function convertToUSD($amount, $currency)
    // {
    //     $conversionRates = [
    //         "BTC" => 'BTCUSDT',
    //         "BNB" => 'BNBUSDT',
    //         "TRX" => 'TRXUSDT',
    //         "LTC" => 'LTCUSDT',
    //         "DOGE" => 'DOGEUSDT',
    //     ];
    //
    //     if (array_key_exists($currency, $conversionRates)) {
    //         $symbol = $conversionRates[$currency];
    //         $ch = curl_init("https://api.binance.com/api/v3/ticker/price?symbol={$symbol}");
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //         curl_setopt($ch, CURLOPT_HEADER, false);
    //         $html = curl_exec($ch);
    //         curl_close($ch);
    //         $data = json_decode($html, true);
    //
    //         if (isset($data['price'])) {
    //             return $amount * $data['price'];
    //         }
    //     }
    //
    //     return $amount; // Return the original amount if no conversion is available
    // }
}
