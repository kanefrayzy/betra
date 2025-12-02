<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use App\Notifications\Notify;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Models\DepositBonus;
use App\Models\UserDepositBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\DB;
use Exception;

class StreamPayCallbackController extends Controller
{
    public function handle(Request $request)
    {

        try {
            // Проверяем подпись
            // if (!$this->verifySignature($request)) {
            //     Log::error('StreamPay Callback: Invalid signature');
            //     return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            // }

              // Получаем IP-адрес клиента
              $clientIp = $request->ip();

              // Проверяем, соответствует ли IP-адрес ожидаемому
              if ($clientIp !== '54.36.117.153') {
                  // Логируем, что запрос был отклонен
                  Log::warning('Request denied from IP: ' . $clientIp);
                  return response()->json(['message' => 'Forbidden'], 403);
              }


            $requiredFields = [
                'external_id',    // ID транзакции на стороне мерчанта
                'invoice',        // ID инвойса StreamPay
                'amount',         // Сумма в валюте инвойса
                'amount_with_commission', // Сумма инвойса с учетом комиссии
                'payed_amount',   // Сумма оплаты в валюте платежа
                'merchant_total', // Сумма в системной валюте зачисляемая на баланс мерчанта
                'currency',       // Валюта платежа
                'system_currency', // Системная валюта (валюта кошелька мерчанта)
                'status'         // Статус инвойса
            ];

            foreach ($requiredFields as $field) {
                if (!$request->has($field)) {
                    Log::error('StreamPay Callback: Missing required field', ['field' => $field]);
                    return response()->json(['status' => 'error', 'message' => 'Missing required field: ' . $field], 400);
                }
            }

            return $this->processPayment($request);
        } catch (Exception $e) {
            Log::error('StreamPay Callback: Error processing callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    private function processPayment(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $transaction = Transaction::lockForUpdate()->find($request->external_id);

            if (!$transaction) {
                Log::error('StreamPay Callback: Transaction not found', ['external_id' => $request->external_id]);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            // Проверяем статус транзакции
            if ($transaction->status === TransactionStatus::Success) {
                // Если статус Success, разрешаем только Refund
                if ($request->status === 'refund') {
                    $this->handleRefund($transaction, $request);  // Логика возврата средств
                    return response()->json(['status' => 'success', 'message' => 'Transaction refunded']);
                }

                return response()->json(['status' => 'error', 'message' => 'Invalid status change after success'], 400);
            }

            // Если статус Cancel, разрешаем только Success
            if ($transaction->status === TransactionStatus::Failed) {
                if ($request->status !== 'success') {
                    return response()->json(['status' => 'received']);
                }
            }

            // Обрабатываем обновление статуса
            if ($request->status === 'success') {
                Log::info('StreamPay Callback: Payment successful', ['transaction_id' => $transaction->id]);

                $exchangeService = new ExchangeService();
                $user = $transaction->user;

                // Конвертация суммы если нужно
                $amountToCredit = $this->calculateAmountToCredit($request, $user, $exchangeService);

                $context = json_decode($transaction->context, true);
                $hasBonus = $context['has_bonus'] ?? false;
                $bonusId = $context['bonus_id'] ?? null;
                $bonusAmount = $context['bonus_amount'] ?? 0;

                if ($hasBonus && $bonusId) {
                    // Создаем запись о бонусе только после успешной оплаты
                    UserDepositBonus::create([
                        'user_id' => $user->id,
                        'deposit_bonus_id' => $bonusId,
                        'deposit_amount' => $transaction->amount,
                        'bonus_amount' => $bonusAmount,
                        'wagering_requirement' => $transaction->amount,
                        'wagered_amount' => 0,
                    ]);

                    // Добавляем бонус к сумме зачисления
                    $amountToCredit += $bonusAmount;

                    // Обновляем wagering_requirement пользователя
                    $user->increment('wagering_requirement', $transaction->amount);
                }

                // Зачисляем общую сумму (депозит + бонус если есть)
                $user->balance += $amountToCredit;
                $user->save();

                // Обновляем транзакцию
                $transaction->update([
                    'status' => TransactionStatus::Success,
                    'context' => json_encode(array_merge($context, [
                        'streampay_invoice_id' => $request->invoice,
                        'streampay_amount' => $request->amount,
                        'streampay_amount_with_commission' => $request->amount_with_commission,
                        'streampay_payed_amount' => $request->payed_amount,
                        'streampay_merchant_total' => $request->merchant_total,
                        'streampay_currency' => $request->currency,
                        'streampay_system_currency' => $request->system_currency,
                        'bonus_credited' => $hasBonus,
                        'bonus_amount_credited' => $bonusAmount
                    ]))
                ]);

                // Отправляем уведомление пользователю
                $messageNotify = $hasBonus
                    ? __('Депозит :amount :currency + бонус :bonus :currency зачислен', [
                        'amount' => moneyFormat($transaction->amount),
                        'bonus' => moneyFormat($bonusAmount),
                        'currency' => $user->currency->symbol
                    ])
                    : __('Депозит :amount :currency зачислен', [
                        'amount' => moneyFormat($transaction->amount),
                        'currency' => $user->currency->symbol
                    ]);

                $user->notify(Notify::send('deposit', ['message' => $messageNotify]));

                return response()->json(['status' => 'success']);
            }

            // Если статус не Cancel, не позволяем возвращаться к Cancel
            if ($request->status === 'cancel') {
                // Log::error('StreamPay Callback: Cancel not allowed after success', ['transaction_id' => $transaction->id]);
                return response()->json(['status' => 'error', 'message' => 'Cannot cancel after success'], 400);
            }

            return response()->json(['status' => 'error', 'message' => 'Unknown status'], 400);
        });
    }

    private function calculateAmountToCredit($request, $user, $exchangeService)
    {
        $amount = $request->amount;

        if($request->currency == 'AZN' && $user->currency->symbol !== 'AZN') {
            return $exchangeService->convert($amount, 'AZN', $user->currency->symbol);
        }

        if($request->currency == 'RUB' && $user->currency->symbol !== 'RUB') {
            return $exchangeService->convert($amount, 'RUB', $user->currency->symbol);
        }

        if($request->currency == 'KZT' && $user->currency->symbol !== 'KZT') {
            return $exchangeService->convert($amount, 'KZT', $user->currency->symbol);
        }

        return $amount;
    }

    private function handleRefund(Transaction $transaction, Request $request)
    {
        // Логика обработки возврата средств (refund)
        // Обновляем статус транзакции на Refund
        $transaction->update([
            'status' => TransactionStatus::Refund,
            'context' => json_encode(array_merge(json_decode($transaction->context, true), [
                'refund_invoice_id' => $request->invoice,
                'refund_amount' => $request->amount
            ]))
        ]);

        Log::info('StreamPay Callback: Refund processed successfully', ['transaction_id' => $transaction->id]);
    }


    private function verifySignature(Request $request): bool
    {
        $publicKey = hex2bin(Config::get('payment.streampay.public_key'));
        $signature = $request->headers->get('signature');

        if (!$signature) {
            Log::error('No signature found in request');
            return false;
        }

        $signature = hex2bin($signature);

        $reqContent = $request->getContent();
        Log::info('Request Content for signature verification', ['content' => $reqContent]);

        $utcNow = new DateTime("now", new DateTimeZone("UTC"));

        for ($i = 0; $i < 2; $i++) {
            $toSign = $reqContent . $utcNow->format('Ymd:Hi');
            Log::info('StreamPay Callback: Verifying signature', [
                'signed_data' => $toSign,
                'signature' => bin2hex($signature)
            ]);

            // Проверка подписи
            if (sodium_crypto_sign_verify_detached($signature, $toSign, $publicKey) !== false) {
                return true;
            }

            $utcNow->modify('-1 minutes');
        }

        Log::error('StreamPay Callback: Invalid signature after retries');
        return false; // Подпись неверна
    }



}
