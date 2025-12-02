<?php

namespace App\Http\Controllers;

use App\Notifications\Notify;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\DB;

class PayteezCallbackController extends Controller
{
    const SUCCESS_STATUS = 'success';

    public function handle(Request $request)
    {
        Log::info('Payteez Callback: Received data', $request->all());

        $requiredFields = ['status', 'operation_id', 'operation_pay_system', 'operation_date', 'operation_pay_date', 'shop', 'order', 'amount', 'amount_azn', 'currency', 'signature'];

        foreach ($requiredFields as $field) {
            if (!$request->has($field)) {
                Log::error('Payteez Callback: Missing required field', ['field' => $field]);
                return response()->json(['status' => 'error', 'message' => 'Missing required field: ' . $field], 400);
            }
        }

        $secretKey = Config::get('payment.payteez.secret_key');
        $calculatedSignature = $this->generateSignature($request->all(), $secretKey);

        if ($request->signature !== $calculatedSignature) {
            Log::error('Payteez Callback: Invalid signature', [
                'received' => $request->signature,
                'calculated' => $calculatedSignature
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        if ($request->status === self::SUCCESS_STATUS) {
            return $this->processPayment($request);
        }

        return response()->json(['status' => 'received']);
    }

    private function processPayment(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $transaction = Transaction::lockForUpdate()->find($request->order);

            if (!$transaction) {
                Log::error('Payteez Callback: Transaction not found', ['order_id' => $request->order]);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            if ($transaction->status === TransactionStatus::Success) {
                Log::info('Payteez Callback: Transaction already processed', ['transaction_id' => $transaction->id]);
                return response()->json(['status' => 'success', 'message' => 'Transaction already processed']);
            }

            $exchangeService = new ExchangeService();
            $convertedCallbackAmount = $exchangeService->convert(
              $request->amount,
              $request->currency,
              $transaction->currency->symbol
          );

          // Проверка на соответствие суммы с допуском в 1%
          $tolerance = $transaction->amount * 0.01; // 1% допуск
          if (abs($transaction->amount - $convertedCallbackAmount) > $tolerance) {
              Log::error('Payteez Callback: Amount mismatch', [
                  'transaction_amount' => $transaction->amount,
                  'callback_amount' => $request->amount,
                  'callback_currency' => $request->currency,
                  'converted_callback_amount' => $convertedCallbackAmount,
                  'transaction_currency' => $transaction->currency->symbol
              ]);
              return response()->json(['status' => 'error', 'message' => 'Amount mismatch'], 400);
          }


            $context = array_merge(json_decode($transaction->context, true) ?? [], [
                'payteez_operation_id' => $request->operation_id,
                'payteez_operation_pay_system' => $request->operation_pay_system,
                'payteez_operation_date' => $request->operation_date,
                'payteez_operation_pay_date' => $request->operation_pay_date,
                'payteez_amount' => $request->amount,
                'payteez_amount_azn' => $request->amount_azn,
                'payteez_currency' => $request->currency,
            ]);

            $transaction->update([
                'status' => TransactionStatus::Success,
                'context' => json_encode($context),
            ]);

            $user = $transaction->user;
            $userCurrency = $user->currency;
            $transactionCurrency = $transaction->currency;

            $amountToCredit = $transaction->amount;

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

            $crns = $transactionCurrency->symbol;

            $messageNotify = __('Deposit with amount :amount :currency successful', [
                'amount' => moneyFormat($transaction->amount),
                'currency' => $transactionCurrency->symbol
            ]);

            $user->notify(Notify::send('deposit', ['message' => $messageNotify]));

            Log::info('Payteez Callback: Payment processed successfully', ['transaction_id' => $transaction->id]);

            return response()->json(['status' => 'success']);
        });
    }

    private function generateSignature($data, $key): string
    {
        $hashData = [
            $data['status'],
            $data['operation_id'],
            $data['operation_pay_system'],
            $data['operation_date'],
            $data['operation_pay_date'],
            $data['shop'],
            $data['order'],
            $data['amount'],
            $data['amount_azn'],
            $data['currency'],
            $key
        ];

        $hashString = implode(':', $hashData);
        $hashedValue = hash('sha256', $hashString);

        return strtoupper($hashedValue);
    }
}
