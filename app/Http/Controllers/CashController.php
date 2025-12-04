<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ManualPayment;
use App\Notifications\Notify;
use App\Services\CommentGenerator;
use App\Traits\Hashable;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Factories\PaymentSystemFactory;
use App\Models\PaymentHandler;
use App\Models\PaymentSystem;
use App\Models\Settings;
use App\Services\FreeKassaService;
use App\Services\PayKassaService;
use App\Services\PayteezService;
use App\Services\StreamPayService;
use App\Services\StreamPayRubService;
use App\Services\StreamPayKztService;
use App\Services\BetaTransferService;
use Carbon\Carbon;
use App\Services\ExchangeService;
use App\Models\DepositBonus;
use App\Models\UserDepositBonus;
use App\Services\WestWalletService;
use App\Models\UserCryptoWallet;

class CashController extends Controller
{
    /**
     * Получение крипто-адреса для пополнения
     */
    public function getCryptoAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => ['required', 'string', 'in:BTC,ETH,USDT,TRX,LTC,XRP,DOGE, SOL, USDC, BNB'],
            'network' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Ошибка валидации'),
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $currency = $request->currency;
        $network = $request->network;

        try {
            $westWalletService = new WestWalletService();
            $result = $westWalletService->getOrCreateWallet($user, $currency, $network);

            if ($result['error']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? __('Ошибка при получении адреса')
                ], 400);
            }

            $data = $result['data'];
            
            // Генерируем QR код (base64)
            $qrCodeData = $data['address'];
            if ($data['dest_tag']) {
                $qrCodeData .= '?dt=' . $data['dest_tag'];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'address' => $data['address'],
                    'dest_tag' => $data['dest_tag'],
                    'currency' => $data['currency'],
                    'network' => $data['network'],
                    'qr_data' => $qrCodeData,
                    'existing' => $data['existing'] ?? false,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Get crypto address error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Произошла ошибка при получении адреса')
            ], 500);
        }
    }

    public function handler(Request $request, $operation)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1'],
            'system' => ['required', 'string'],
        ], [
            'amount.required' => __('Заполните сумму'),
            'amount.numeric' => __('Сумма должна быть числом'),
            'amount.min' => __('Сумма должна быть не менее :min', ['min' => 1]),
            'system.required' => __('Выберите систему'),
            'details.required' => __('Введите свои реквизиты')
        ]);

        $validator->sometimes('details', 'required|string', function ($input) use ($operation) {
            return $operation === 'withdrawal';
        });

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Ошибка валидации'),
                    'errors' => $validator->errors()
                ], 422);
            }
            return Redirect::back()->withErrors($validator);
        }

        $user = Auth::user();

        $data = [
            'amount' => $request->amount,
            'system' => $request->system,
            'details' => $request->details ?? null,
        ];

        if ($operation == 'deposit') {
            return $this->processDeposit($request);
        }

        if ($operation == 'withdrawal') {
            return $this->withdrawal($user, $data, $request);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => __('Неизвестная операция')
            ], 400);
        }
        return Redirect::back()->with('error', __('Неизвестная операция'));
    }

    protected function processDeposit(Request $request)
    {
        $user = Auth::user();

        // Проверка на наличие активного бана
        if ($user->payment_ban_at && Carbon::parse($user->payment_ban_at)->isFuture()) {
            $errorMessage = __('Вы можете пополнить баланс после :date', ['date' => Carbon::parse($user->payment_ban_at)->format('d.m H:i')]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 403);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        $amount = $request->amount;
        $selectedBonusId = $request->input('selected_bonus_id');

        // Если выбран бонус, проверяем его доступность
        $bonus = null;
        if ($selectedBonusId) {
            $bonus = DepositBonus::find($selectedBonusId);

            if (!$bonus) {
                $errorMessage = __('Выбранный бонус недоступен');
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 400);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            // Проверяем общее количество использованных бонусов
            $usedBonusesCount = UserDepositBonus::where('user_id', $user->id)->count();
            if ($usedBonusesCount >= 3) {
                $errorMessage = __('Вы уже использовали максимальное количество бонусов');
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 400);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            // Проверяем, не использовал ли пользователь этот бонус ранее
            $hasUsedBonus = UserDepositBonus::where('user_id', $user->id)
                ->where('deposit_bonus_id', $bonus->id)
                ->exists();

            if ($hasUsedBonus) {
                $errorMessage = __('Вы уже использовали этот бонус');
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 400);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            $bonusRequiredAmount = $bonus->required_amount;
            if ($user->currency->symbol !== 'AZN') {
                $exchangeService = new ExchangeService();
                $bonusRequiredAmount = $exchangeService->convert(
                    $bonus->required_amount,
                    'AZN',
                    $user->currency->symbol
                );
            }

            // Проверяем соответствие суммы депозита требованиям бонуса
            if ($amount < $bonusRequiredAmount) {
                $errorMessage = __('Сумма депозита не соответствует требованиям бонуса');
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 400);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
        }

        $amountCheck = $amount;
        $paymentHandler = PaymentHandler::findOrFail($request->system);
        $exchangeService = new ExchangeService();
        $minCheck = $paymentHandler->min_deposit_limit;

        // Существующая логика конвертации валюты
        $currencySymbol = $user->currency->symbol;
        if ($currencySymbol != $paymentHandler->currency && !in_array($paymentHandler->currency, ['BTC', 'USDT', 'LTC', 'XRP', 'TRX'])) {
            $amountCheck = $exchangeService->convert($amount, $currencySymbol, $paymentHandler->currency);
            $minCheck = $exchangeService->convert($minCheck, $paymentHandler->currency, $currencySymbol);
        }

        $minCheck = moneyFormat($minCheck);
        if ($amountCheck < $paymentHandler->min_deposit_limit) {
            $errorMessage = __('Минимальная сумма депозита составляет :min_amount :currency',
                ['min_amount' => $minCheck, 'currency' => $currencySymbol]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        // НОВАЯ ЛОГИКА: Проверяем, является ли платежный обработчик ручным
        if ($paymentHandler->is_manual) {
            return $this->processManualDeposit($request, $paymentHandler, $bonus);
        }

        // Существующая логика для автоматических платежей
        $sysId = $paymentHandler->id;
        $paymentSystem = PaymentSystem::where('id', $paymentHandler->payment_system_id)->first();

        try {
            $transaction = DB::transaction(function () use ($user, $amount, $paymentHandler, $paymentSystem, $bonus, $request) {
                // Создаем транзакцию депозита
                $transaction = $user->transactions()->create([
                    'amount' => $amount,
                    'currency_id' => $user->currency_id,
                    'type' => TransactionType::Deposit,
                    'status' => TransactionStatus::Pending,
                    'hash' => Str::uuid()->toString(),
                    'context' => json_encode([
                        'payment_system' => $paymentSystem->name,
                        'payment_handler' => $paymentHandler->name,
                        'balance_before' => $user->balance,
                        'has_bonus' => !is_null($bonus),
                        'bonus_id' => $bonus?->id,
                        'bonus_amount' => $bonus?->bonus_amount
                    ]),
                ]);

                // Создаем платеж
                $user->payments()->create([
                    'amount' => $amount,
                    'details' => null,
                    'currency_id' => $user->currency_id,
                    'transaction_id' => $transaction->id,
                    'external_id' => Str::uuid()->toString(),
                    'status' => PaymentStatus::Pending,
                    'comment' => CommentGenerator::payment(PaymentStatus::Pending, $amount, $user->currency->symbol),
                ]);

                return $transaction;
            });

            $paymentService = $this->getPaymentService($paymentSystem->name);

            $response = $paymentService->createOrder(
                $transaction->id,
                $amount,
                $paymentHandler->currency,
                $request->system
            );

            if (isset($response['error']) && $response['error']) {
                throw new Exception($response['message'] ?? __('Ошибка при создании заказа'));
            }

            // Обработка специфичного ответа для Payteez
            if ($paymentSystem->name === 'Payteez') {
                if ($response['data']['method'] === 'POST') {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => __('Переход к оплате...'),
                            'redirect' => route('payment.post-redirect', [
                                'url' => $response['data']['url'],
                                'fields' => $response['data']['fields']
                            ])
                        ]);
                    }
                    return view('payment.post-redirect', [
                        'url' => $response['data']['url'],
                        'fields' => $response['data']['fields'],
                    ]);
                }
            }

            $paymentUrl = $response['data']['url'] ?? $response['url'];
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Переход к оплате...'),
                    'url' => $paymentUrl
                ]);
            }

            return redirect($paymentUrl);

        } catch (Exception $e) {
            \Log::error(__('Ошибка при создании депозита:') . ' ' . $e->getMessage());
            $errorMessage = __('Произошла ошибка при создании депозита. Пожалуйста, попробуйте еще раз.');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * НОВЫЙ МЕТОД: Обработка ручного депозита
     */
    protected function processManualDeposit(Request $request, PaymentHandler $paymentHandler, $bonus = null)
    {
        $user = Auth::user();
        $amount = $request->amount;

        try {
            $transaction = DB::transaction(function () use ($user, $amount, $paymentHandler, $bonus) {
                // Создаем транзакцию депозита
                $transaction = $user->transactions()->create([
                    'amount' => $amount,
                    'currency_id' => $user->currency_id,
                    'type' => TransactionType::Deposit,
                    'status' => TransactionStatus::Pending,
                    'hash' => Str::uuid()->toString(),
                    'context' => json_encode([
                        'payment_system' => 'Manual',
                        'payment_handler' => $paymentHandler->name,
                        'balance_before' => $user->balance,
                        'is_manual' => true,
                        'has_bonus' => !is_null($bonus),
                        'bonus_id' => $bonus?->id,
                        'bonus_amount' => $bonus?->bonus_amount
                    ]),
                ]);

                // Создаем запись ручного платежа
                ManualPayment::create([
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'payment_handler_id' => $paymentHandler->id,
                    'amount' => $amount,
                    'currency' => $user->currency->symbol,
                    'status' => 'pending'
                ]);

                return $transaction;
            });

            // Перенаправляем на страницу ручного пополнения
            return redirect()->route('manual-deposit.show', ['id' => $transaction->id])
                ->with('success', __('Заявка на пополнение создана. Завершите платеж, загрузив чек.'));

        } catch (Exception $e) {
            \Log::error(__('Ошибка при создании ручного депозита:') . ' ' . $e->getMessage());
            return redirect()->back()->with('error', __('Произошла ошибка при создании депозита. Пожалуйста, попробуйте еще раз.'));
        }
    }

    private function getPaymentService($paymentSystemName)
    {
        $paymentSystemName = strtolower($paymentSystemName);

        $services = [
            'paykassa' => PayKassaService::class,
            'freekassa' => FreeKassaService::class,
            'payteez' => PayteezService::class,
            'streampay' => StreamPayService::class,
            'streampayrub' => StreamPayRubService::class,
            'streampaykzt' => StreamPayKztService::class,
            'betatransfer' => BetaTransferService::class,
        ];

        if (!isset($services[$paymentSystemName])) {
            throw new Exception(__('Неподдерживаемая платежная система:') . " $paymentSystemName");
        }

        $serviceClass = $services[$paymentSystemName];

        if (!class_exists($serviceClass)) {
            throw new Exception(__('Класс сервиса не найден:') . " $serviceClass");
        }

        return app($serviceClass);
    }

    protected function withdrawal(User $user, array $data, $request = null)
    {
        $amount = $data['amount'];
        $system = $data['system'];
        $details = $data['details'];
        $isAjax = $request && ($request->expectsJson() || $request->ajax());

        // Проверяем требования к верификации
        if ($user->need_verify && !$user->isVerified()) {
            $errorMessage = __('Для вывода средств необходима верификация профиля');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $errorMessage], 403);
            }
            return redirect()->route('account')->with('error', $errorMessage);
        }

        $commissionPercent = Settings::get()->withdrawal_commission;
        $commissionAmount = ($amount * $commissionPercent) / 100;

        $actualAmount = $amount - $commissionAmount;

        if ($user->balance < $amount) {
            $errorMessage = __('Недостаточно денег для вывода этой суммы');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return Redirect::back()->with('error', $errorMessage);
        }

        $amountCheck = $amount;
        $paymentHandler = PaymentHandler::where('id', $system)->first();
        $exchangeService = new ExchangeService();
        $minCheck = $paymentHandler->min_withdrawal_limit;

        // Конвертация валюты, если валюта пользователя не совпадает с валютой платежной системы
        $currencySymbol = $user->currency->symbol;

        if ($currencySymbol != $paymentHandler->currency && !in_array($paymentHandler->currency, ['BTC', 'USDT', 'LTC', 'XRP', 'TRX', 'ETH'])) {
            $amountCheck = $exchangeService->convert($amount, $currencySymbol, $paymentHandler->currency);
            $minCheck = $exchangeService->convert($minCheck, $paymentHandler->currency, $currencySymbol);
        }

        $minCheck = moneyFormat($minCheck);
        // Проверка минимального депозита
        if ($amountCheck < $paymentHandler->min_withdrawal_limit) {
            $errorMessage = __('Минимальная сумма вывода составляет :min_amount :currency', ['min_amount' => $minCheck, 'currency' => $currencySymbol]);
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        $userBeforeBalance = $user->balance;
        $user->balance -= $amount;
        $user->save();

        $withdrawal = null;
        $transaction = null;

        DB::transaction(function () use ($user, $amount, $actualAmount, $commissionAmount, $system, $details, &$withdrawal, &$transaction) {
            $transaction = $user->transactions()->create([
                'amount' => $actualAmount, // Сумма к получению после комиссии
                'currency_id' => $user->currency_id,
                'type' => TransactionType::Withdrawal,
                'status' => TransactionStatus::Pending,
                'hash' => Str::uuid()->toString(),
                'context' => json_encode([
                    'payment_system' => $system,
                    'balance_before' => $user->balance,
                    'commission_amount' => $commissionAmount,
                    'amount_before_commission' => $amount,
                    'comment' => CommentGenerator::transaction(TransactionType::Withdrawal, $actualAmount, $user->currency->symbol),
                ]),
            ]);

            $withdrawal = $user->withdrawals()->create([
                'amount' => $actualAmount,
                'details' => $details,
                'currency_id' => $user->currency_id,
                'transaction_id' => $transaction->id,
                'external_id' => Str::uuid()->toString(),
                'status' => PaymentStatus::Pending,
                'comment' => CommentGenerator::payment(PaymentStatus::Pending, $amount, $user->currency->symbol),
            ]);
        });

        // Проверяем режим автовыплат
        if ($paymentHandler && $paymentHandler->auto_withdrawal_enabled && $paymentHandler->withdrawal_mode === 'instant') {
            // Проверяем что это BetaTransfer (ID = 8)
            if ($paymentHandler->payment_system_id == 8) {
                try {
                    // Проверка верификации
                    if ($user->is_verified) {
                        // Проверка дневного лимита
                        if ($paymentHandler->checkDailyLimit($actualAmount)) {
                            // Инициализируем BetaTransfer сервис
                            $betaTransferService = new \App\Services\BetaTransferService();
                            
                            // Отправляем запрос на выплату в BetaTransfer
                            $response = $betaTransferService->createWithdrawal(
                                $paymentHandler->id,
                                $actualAmount,
                                $details,
                                $user->currency->symbol,
                                "Withdrawal #{$withdrawal->id}"
                            );

                            if (!$response['error']) {
                                // Успешно отправлено - обновляем статусы
                                $withdrawal->status = PaymentStatus::Completed;
                                $withdrawal->auto_processed = true;
                                $withdrawal->betatransfer_transaction_id = $response['data']['transaction_id'] ?? null;
                                $withdrawal->betatransfer_status = 'sent';
                                $withdrawal->save();

                                $transaction->status = 'success';
                                $transaction->save();

                                \Log::info('Instant auto-withdrawal processed', [
                                    'withdrawal_id' => $withdrawal->id,
                                    'user_id' => $user->id,
                                    'betatransfer_id' => $response['data']['transaction_id'] ?? null
                                ]);

                                $user->notify(Notify::send('withdrawal', ['message' => __('Ваша выплата обработана автоматически и отправлена!')]));
                                session()->flash('showWithdrawalModal', true);
                                
                                $successMessage = __('Выплата отправлена автоматически через BetaTransfer! 🚀');
                                if ($isAjax) {
                                    return response()->json([
                                        'success' => true,
                                        'message' => $successMessage,
                                        'balance' => moneyFormat($user->balance),
                                        'showWithdrawalModal' => true
                                    ]);
                                }
                                return Redirect::back()->with('success', $successMessage);
                            } else {
                                // Ошибка API - выплата останется в pending для ручной обработки
                                \Log::warning('Auto-withdrawal failed, pending manual processing', [
                                    'withdrawal_id' => $withdrawal->id,
                                    'error' => $response['message'] ?? 'Unknown error'
                                ]);
                            }
                        } else {
                            \Log::info('Daily limit exceeded for auto-withdrawal', [
                                'withdrawal_id' => $withdrawal->id,
                                'user_id' => $user->id
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Auto-withdrawal exception', [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage()
                    ]);
                    // При ошибке просто оставляем в pending для ручной обработки
                }
            }
        }

        $user->notify(Notify::send('withdrawal', ['message' => __('Ваш запрос на вывод средств отправлен на обработку.')]));

        session()->flash('showWithdrawalModal', true);
        
        $successMessage = __('Заявка отправлено на модерацию');
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'balance' => moneyFormat($user->balance)
            ]);
        }
        return Redirect::back()->with('success', $successMessage);
    }

    private function cardHandler(Transaction $transaction)
    {
        $url = 'https://test.oppwa.com/v1/payments/' . $transaction->hash;
        return Redirect::to($url);
    }
}
