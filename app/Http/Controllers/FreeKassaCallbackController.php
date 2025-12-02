<?php


namespace App\Http\Controllers;

use App\Notifications\Notify;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ExchangeService;

class FreeKassaCallbackController extends Controller
{
    public function handle(Request $request)
    {
        if (!$this->isTrustedIP()) {
            Log::error('FreeKassa: Unauthorized IP address', ['ip' => $this->getIP()]);
            return response('Unauthorized IP address', 403);
        }

        // Log::info('FreeKassa IPN Request', $request->all());

        $merchantId = Config::get('payment.freekassa.merchant_id');
        $secretKey = Config::get('payment.freekassa.secret_key');

        $sign = md5($merchantId . ':' . $request->AMOUNT . ':' . $secretKey . ':' . $request->MERCHANT_ORDER_ID);

        if ($sign != $request->SIGN) {
            // Log::error('FreeKassa: Invalid signature');
            return response('Invalid signature', 400);
        }

        $transaction = Transaction::find($request->MERCHANT_ORDER_ID);

        if (!$transaction) {
            // Log::error('FreeKassa: Transaction not found', ['order_id' => $request->MERCHANT_ORDER_ID]);
            return response('Transaction not found', 404);
        }

        // Проверим статус транзакции
        // Log::info('FreeKassa IPN: Found transaction', ['order_id' => $transaction->id, 'status' => $transaction->status->value]);
        if ($transaction->status->value == 'success') {
            // Log::info('FreeKassa IPN: Transaction already successful, ignoring.', ['order_id' => $transaction->id]);
            return response('Transaction already successful', 200);
        }

        // Обрабатываем успешную транзакцию
        DB::transaction(function () use ($transaction, $request) {
            $this->processSuccessfulPayment($transaction, $request->all());
        });

        return response('YES', 200);
    }

    private function processSuccessfulPayment($transaction, $data)
    {
        // Log::info('FreeKassa IPN: processSuccessfulPayment called', ['order_id' => $transaction->id]);

        DB::transaction(function () use ($transaction, $data) {
            $context = $this->decodeContext($transaction->context);
            $context['freekassa_transaction_id'] = $data['intid'];
            $context['freekassa_amount'] = $data['AMOUNT'];
            $context['freekassa_currency'] = $data['CURRENCY'] ?? 'UNKNOWN';

            // Log::info('FreeKassa IPN: Updating transaction context and status to success', ['order_id' => $transaction->id, 'context' => $context]);

            $transaction->update([
                'status' => 'success',
                'context' => json_encode($context),
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
            
        });

        // Log::info('FreeKassa: Payment processed successfully', ['order_id' => $transaction->id]);
    }

    private function decodeContext($context)
    {
        if (is_string($context)) {
            $decoded = json_decode($context, true);
            return is_array($decoded) ? $decoded : [];
        } elseif (is_array($context)) {
            return $context;
        } else {
            return [];
        }
    }

    private function getIP()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    private function isTrustedIP()
    {
        $trustedIPs = [
            '168.119.157.136',
            '168.119.60.227',
            '178.154.197.79',
            '51.250.54.238'
        ];
        return in_array($this->getIP(), $trustedIPs);
    }
}
