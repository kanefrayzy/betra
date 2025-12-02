<?php

namespace App\Http\Controllers;

use App\Models\ManualPayment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentHandler;
use App\Enums\TransactionStatus;
use App\Enums\PaymentStatus;
use App\Notifications\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManualDepositController extends Controller
{
    /**
     * Показываем страницу ручного пополнения
     */
    public function show($id)
    {
        $user = Auth::user();

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['currency'])
            ->firstOrFail();

        $manualPayment = ManualPayment::where('transaction_id', $transaction->id)
            ->with(['paymentHandler'])
            ->firstOrFail();

        // Проверяем, что это действительно ручной платеж
        if (!$manualPayment->paymentHandler->is_manual) {
            abort(404);
        }

        return view('payment.manual-payment', compact('transaction', 'manualPayment'));
    }

    /**
     * Сохранение чека и комментария
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'user_comment' => 'nullable|string|max:500'
        ], [
            'receipt.required' => __('Пожалуйста, загрузите чек об оплате'),
            'receipt.image' => __('Файл должен быть изображением'),
            'receipt.mimes' => __('Поддерживаемые форматы: JPEG, PNG, JPG'),
            'receipt.max' => __('Размер файла не должен превышать 5MB'),
        ]);

        $user = Auth::user();

        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $manualPayment = ManualPayment::where('transaction_id', $transaction->id)
            ->firstOrFail();

        // Проверяем, что платеж еще в статусе pending
        if ($manualPayment->status !== 'pending') {
            return redirect()->back()->with('error', __('Этот платеж уже обработан'));
        }

        try {
            // Сохраняем чек
            $receiptPath = $request->file('receipt')->store('manual-deposits', 'public');

            // Обновляем данные платежа
            $manualPayment->update([
                'receipt_path' => $receiptPath,
                'user_comment' => $request->user_comment,
            ]);

            return redirect()->route('manual-deposit.my-deposits')
                ->with('success', __('Чек успешно загружен! Ваш платеж отправлен на модерацию.'));

        } catch (\Exception $e) {
            \Log::error(__('Ошибка при сохранении чека:') . ' ' . $e->getMessage());
            return redirect()->back()->with('error', __('Произошла ошибка при загрузке чека. Попробуйте еще раз.'));
        }
    }

    /**
     * Мои ручные депозиты
     */
    public function myDeposits()
    {
        $user = Auth::user();

        $manualPayments = ManualPayment::where('user_id', $user->id)
            ->with(['transaction', 'paymentHandler', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('manual-deposit.my-deposits', compact('manualPayments'));
    }

    /**
     * Админка: список всех ручных платежей
     */
    public function adminIndex(Request $request)
    {
        $query = ManualPayment::with(['user', 'transaction', 'paymentHandler', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        // Фильтрация по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Поиск по пользователю
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        $manualPayments = $query->paginate(20);

        // Статистика
        $stats = [
            'pending' => ManualPayment::pending()->count(),
            'approved' => ManualPayment::approved()->count(),
            'rejected' => ManualPayment::rejected()->count(),
            'total' => ManualPayment::count(),
        ];

        return view('admin.manual-deposit', compact('manualPayments', 'stats'));
    }

    /**
     * Админка: обработка платежа (одобрение/отклонение)
     */
    public function adminProcess(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_comment' => 'nullable|string|max:500'
        ]);

        $manualPayment = ManualPayment::with(['transaction', 'user'])
            ->findOrFail($id);

        // Проверяем, что платеж еще не обработан
        if ($manualPayment->status !== 'pending') {
            return redirect()->back()->with('error', __('Этот платеж уже обработан'));
        }

        try {
            DB::transaction(function () use ($request, $manualPayment) {
                $admin = Auth::user();
                $action = $request->action;

                if ($action === 'approve') {
                    // Одобряем платеж
                    $this->approvePayment($manualPayment, $admin, $request->admin_comment);
                } else {
                    // Отклоняем платеж
                    $this->rejectPayment($manualPayment, $admin, $request->admin_comment);
                }
            });

            $statusText = $request->action === 'approve' ? __('одобрен') : __('отклонен');
            return redirect()->back()->with('success', __("Платеж успешно {$statusText}"));

        } catch (\Exception $e) {
            \Log::error(__('Ошибка при обработке ручного платежа:') . ' ' . $e->getMessage());
            return redirect()->back()->with('error', __('Произошла ошибка при обработке платежа'));
        }
    }

    /**
     * Одобрение платежа
     */
private function approvePayment(ManualPayment $manualPayment, User $admin, $comment = null)
{
    // Обновляем статус ручного платежа
    $manualPayment->update([
        'status' => 'approved',
        'admin_comment' => $comment,
        'approved_at' => now(),
        'approved_by' => $admin->id
    ]);

    // Обновляем транзакцию
    $transaction = $manualPayment->transaction;
    $transaction->update([
        'status' => TransactionStatus::Success
    ]);

    // ИСПРАВЛЯЕМ КОНВЕРТАЦИЮ ВАЛЮТ
    $user = $manualPayment->user;

    // Получаем сумму в валюте транзакции
    $amountToAdd = $manualPayment->amount;
    $transactionCurrency = $transaction->currency->symbol;
    $userCurrentCurrency = $user->currency->symbol;

    // Если валюты разные - конвертируем
    if ($transactionCurrency !== $userCurrentCurrency) {
        $exchangeService = new \App\Services\ExchangeService();
        $amountToAdd = $exchangeService->convert(
            $manualPayment->amount,
            $transactionCurrency,
            $userCurrentCurrency
        );
    }

    // Пополняем баланс в текущей валюте пользователя
    $user->increment('balance', $amountToAdd);

    // Обрабатываем бонус, если есть
    $context = $transaction->context;
    if (isset($context['has_bonus']) && $context['has_bonus'] && isset($context['bonus_id'])) {
        $this->processBonusForManualPayment($user, $context, $amountToAdd);
    }

    // Отправляем уведомление пользователю
    $user->notify(Notify::send('manual_deposit_approved', [
        'message' => __('Ваш платеж на сумму :amount :currency одобрен и зачислен на баланс!', [
                'amount' => number_format($manualPayment->amount, 2),
                'currency' => $manualPayment->currency
            ])
        ]));
    }

    /**
     * Отклонение платежа
     */
    private function rejectPayment(ManualPayment $manualPayment, User $admin, $comment = null)
    {
        // Обновляем статус ручного платежа
        $manualPayment->update([
            'status' => 'rejected',
            'admin_comment' => $comment,
            'approved_at' => now(),
            'approved_by' => $admin->id
        ]);

        // Обновляем транзакцию
        $transaction = $manualPayment->transaction;
        $transaction->update([
            'status' => TransactionStatus::Failed
        ]);

        // Отправляем уведомление пользователю
        $user = $manualPayment->user;
        $user->notify(Notify::send('manual_deposit_rejected', [
            'message' => __('Ваш платеж на сумму :amount :currency отклонен. Причина: :reason', [
                'amount' => number_format($manualPayment->amount, 2),
                'currency' => $manualPayment->currency,
                'reason' => $comment ?: __('Не указана')
            ])
        ]));
    }

    /**
     * Обработка бонуса для ручного платежа
     */
    private function processBonusForManualPayment(User $user, array $context, float $amount)
    {
        if (!isset($context['bonus_id']) || !isset($context['bonus_amount'])) {
            return;
        }

        // Здесь должна быть логика обработки бонуса
        // Аналогично тому, как это делается для автоматических платежей
        // Это зависит от вашей системы бонусов
    }
}
