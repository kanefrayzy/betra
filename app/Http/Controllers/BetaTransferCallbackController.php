<?php

namespace App\Http\Controllers;

use App\Notifications\Notify;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Models\UserDepositBonus;
use App\Services\BetaTransferService;
use App\Services\ExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class BetaTransferCallbackController extends Controller
{
    private $betaTransferService;

    public function __construct(BetaTransferService $betaTransferService)
    {
        $this->betaTransferService = $betaTransferService;
    }

    public function handle(Request $request)
    {
        try {
            // Логируем входящий callback
            Log::info('BetaTransfer Callback received', $request->all());

            // Проверяем обязательные поля
            $requiredFields = ['id', 'orderId', 'amount', 'status', 'sign'];
            foreach ($requiredFields as $field) {
                if (!$request->has($field)) {
                    Log::error('BetaTransfer Callback: Missing required field', ['field' => $field]);
                    return response()->json(['status' => 'error', 'message' => 'Missing required field: ' . $field], 400);
                }
            }

            // Проверяем подпись
            if (!$this->betaTransferService->verifyCallbackSignature(
                $request->amount,
                $request->orderId,
                $request->sign
            )) {
                Log::error('BetaTransfer Callback: Invalid signature', [
                    'orderId' => $request->orderId,
                    'amount' => $request->amount,
                    'sign' => $request->sign
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            return $this->processCallback($request);

        } catch (Exception $e) {
            Log::error('BetaTransfer Callback: Error processing callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    private function processCallback(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $transaction = Transaction::lockForUpdate()->find($request->orderId);

            if (!$transaction) {
                Log::error('BetaTransfer Callback: Transaction not found', ['orderId' => $request->orderId]);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            $status = $request->status;
            $currentStatus = $transaction->status;

            Log::info('BetaTransfer Callback: Processing transaction', [
                'transaction_id' => $transaction->id,
                'current_status' => $currentStatus->value,
                'new_status' => $status
            ]);

            // Если транзакция уже успешна, разрешаем только refund или partial_payment
            if ($currentStatus === TransactionStatus::Success) {
                if (in_array($status, ['refund', 'partial_payment'])) {
                    return $this->handleSpecialStatus($transaction, $request, $status);
                }
                
                Log::info('BetaTransfer Callback: Transaction already successful, ignoring');
                return response()->json(['status' => 'received']);
            }

            // Если транзакция отменена, разрешаем только success
            if ($currentStatus === TransactionStatus::Failed) {
                if ($status !== 'success') {
                    return response()->json(['status' => 'received']);
                }
            }

            // Обрабатываем статусы
            switch ($status) {
                case 'success':
                    return $this->handleSuccess($transaction, $request);
                
                case 'cancel':
                case 'error':
                case 'not_paid':
                case 'not_paid_timeout':
                case 'not_paid_unavailable_country':
                    return $this->handleFailure($transaction, $request, $status);
                
                case 'partial_payment':
                    return $this->handlePartialPayment($transaction, $request);
                
                case 'processing':
                case 'pending':
                case 'checkPayment':
                    // Промежуточные статусы - просто логируем
                    Log::info('BetaTransfer Callback: Intermediate status', [
                        'transaction_id' => $transaction->id,
                        'status' => $status
                    ]);
                    return response()->json(['status' => 'received']);
                
                default:
                    Log::warning('BetaTransfer Callback: Unknown status', ['status' => $status]);
                    return response()->json(['status' => 'received']);
            }
        });
    }

    private function handleSuccess(Transaction $transaction, Request $request)
    {
        Log::info('BetaTransfer Callback: Payment successful', ['transaction_id' => $transaction->id]);

        $user = $transaction->user;
        $exchangeService = new ExchangeService();

        // Конвертация суммы если необходимо
        $amountToCredit = $this->calculateAmountToCredit($request, $user, $exchangeService);

        $context = json_decode($transaction->context, true) ?? [];
        $hasBonus = $context['has_bonus'] ?? false;
        $bonusId = $context['bonus_id'] ?? null;
        $bonusAmount = $context['bonus_amount'] ?? 0;

        // Обработка бонуса
        if ($hasBonus && $bonusId) {
            UserDepositBonus::create([
                'user_id' => $user->id,
                'deposit_bonus_id' => $bonusId,
                'deposit_amount' => $transaction->amount,
                'bonus_amount' => $bonusAmount,
                'wagering_requirement' => $transaction->amount,
                'wagered_amount' => 0,
            ]);

            $amountToCredit += $bonusAmount;
            $user->increment('wagering_requirement', $transaction->amount);
        }

        // Зачисляем средства
        $user->balance += $amountToCredit;
        $user->save();

        // Обновляем транзакцию
        $transaction->update([
            'status' => TransactionStatus::Success,
            'context' => json_encode(array_merge($context, [
                'betatransfer_transaction_id' => $request->id,
                'betatransfer_payment_system' => $request->paymentSystem ?? null,
                'betatransfer_amount' => $request->amount,
                'betatransfer_paid_amount' => $request->paidAmount ?? $request->amount,
                'betatransfer_currency' => $request->currency,
                'betatransfer_commission' => $request->commission ?? 0,
                'payment_card' => $request->paymentCard ?? null,
                'payee_card' => $request->payeeCard ?? null,
                'bonus_credited' => $hasBonus,
                'bonus_amount_credited' => $bonusAmount
            ]))
        ]);

        // Отправляем уведомление
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

    private function handleFailure(Transaction $transaction, Request $request, $status)
    {
        Log::info('BetaTransfer Callback: Payment failed', [
            'transaction_id' => $transaction->id,
            'status' => $status
        ]);

        $context = json_decode($transaction->context, true) ?? [];

        $transaction->update([
            'status' => TransactionStatus::Failed,
            'context' => json_encode(array_merge($context, [
                'betatransfer_transaction_id' => $request->id,
                'betatransfer_status' => $status,
                'betatransfer_amount' => $request->amount,
                'betatransfer_currency' => $request->currency,
                'failure_reason' => $this->getFailureReason($status)
            ]))
        ]);

        return response()->json(['status' => 'received']);
    }

    private function handlePartialPayment(Transaction $transaction, Request $request)
    {
        Log::info('BetaTransfer Callback: Partial payment', [
            'transaction_id' => $transaction->id,
            'paid_amount' => $request->paidAmount,
            'expected_amount' => $request->amount
        ]);

        $user = $transaction->user;
        $exchangeService = new ExchangeService();

        // Используем фактически оплаченную сумму
        $paidAmount = $request->paidAmount;
        $amountToCredit = $this->calculateAmountToCredit($request, $user, $exchangeService, true);

        $user->balance += $amountToCredit;
        $user->save();

        $context = json_decode($transaction->context, true) ?? [];

        $transaction->update([
            'status' => TransactionStatus::Success,
            'context' => json_encode(array_merge($context, [
                'betatransfer_transaction_id' => $request->id,
                'betatransfer_amount' => $request->amount,
                'betatransfer_paid_amount' => $paidAmount,
                'betatransfer_currency' => $request->currency,
                'is_partial_payment' => true
            ]))
        ]);

        $user->notify(Notify::send('deposit', [
            'message' => __('Частичная оплата :amount :currency зачислена', [
                'amount' => moneyFormat($amountToCredit),
                'currency' => $user->currency->symbol
            ])
        ]));

        return response()->json(['status' => 'success']);
    }

    private function handleSpecialStatus(Transaction $transaction, Request $request, $status)
    {
        if ($status === 'refund') {
            return $this->handleRefund($transaction, $request);
        }

        if ($status === 'partial_payment') {
            return $this->handlePartialPayment($transaction, $request);
        }

        return response()->json(['status' => 'received']);
    }

    private function handleRefund(Transaction $transaction, Request $request)
    {
        Log::info('BetaTransfer Callback: Processing refund', ['transaction_id' => $transaction->id]);

        $user = $transaction->user;
        $context = json_decode($transaction->context, true) ?? [];

        // Возвращаем средства пользователю если они были списаны
        if ($transaction->status === TransactionStatus::Success) {
            $user->balance -= $transaction->amount;
            
            // Если был бонус, убираем и его
            $bonusAmount = $context['bonus_amount_credited'] ?? 0;
            if ($bonusAmount > 0) {
                $user->balance -= $bonusAmount;
                $user->decrement('wagering_requirement', $transaction->amount);
            }
            
            $user->save();
        }

        $transaction->update([
            'status' => TransactionStatus::Refund,
            'context' => json_encode(array_merge($context, [
                'refund_transaction_id' => $request->id,
                'refund_amount' => $request->amount,
                'refund_date' => now()
            ]))
        ]);

        $user->notify(Notify::send('refund', [
            'message' => __('Возврат платежа :amount :currency', [
                'amount' => moneyFormat($transaction->amount),
                'currency' => $user->currency->symbol
            ])
        ]));

        return response()->json(['status' => 'success', 'message' => 'Refund processed']);
    }

    private function calculateAmountToCredit($request, $user, $exchangeService, $usesPaidAmount = false)
    {
        $amount = $usesPaidAmount ? ($request->paidAmount ?? $request->amount) : $request->amount;
        $currency = $request->currency;
        $userCurrency = $user->currency->symbol;

        if ($currency === $userCurrency) {
            return $amount;
        }

        return $exchangeService->convert($amount, $currency, $userCurrency);
    }

    private function getFailureReason($status)
    {
        $reasons = [
            'cancel' => 'Платеж отменен',
            'error' => 'Ошибка платежа',
            'not_paid' => 'Не оплачено',
            'not_paid_timeout' => 'Не оплачено (истекло время)',
            'not_paid_unavailable_country' => 'Не оплачено (страна карты не поддерживается)',
        ];

        return $reasons[$status] ?? 'Неизвестная ошибка';
    }
}