<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Requests\AccountUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\UploadAvatarRequest;
use App\Models\Rank;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ExchangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Models\Currency;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    protected ExchangeService $exchangeService;

    public function __construct()
    {
        $this->exchangeService = new ExchangeService();

    }

    public function account()
    {
        $user = Auth::user();
        $verification = $user->verification; 

        return view('user.index', compact('verification'));
    }


    public function update(AccountUpdateRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();
        $user->update($validated);

        return $user;
    }

    public function password(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (!Hash::check($request->curr_password, $user->password)) {
            return Redirect::back()->with('error', __('errors.netot'));
        }

        $user->update(['password' => Hash::make($request->password)]);

        return Redirect::back()->with('success', __('errors.sucpas'));
    }

    public function updateEmail(Request $request)
        {
            $user = Auth::user();

            if ($user->email_changed) {
                return redirect()->back()->with('error', __('Вы уже изменяли свой email. Повторное изменение невозможно.'));
            }

            $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id,
            ], [
                'email.required' => __('Поле email обязательно для заполнения.'),
                'email.email' => __('Введите корректный email адрес.'),
                'email.unique' => __('Этот email уже используется другим пользователем.'),
            ]);

            $user->email = $request->input('email');
            $user->email_changed = true;
            $user->save();

            return redirect()->back()->with('success', __('Email успешно обновлен. Вы больше не сможете изменить его.'));
        }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $img = $this->storeImage($request->file('avatar'), $user->id);
        $img = str_replace(" ", "", $img);

        if (!$img) {
            return Redirect::back()->with('error', __('errors.errav'));
        }

        $user->update(['avatar' => $img]);

        if ($request->expectsJson()) {
            return Response::json(['avatar' => $user->avatar], HttpStatus::CREATED);
        }

        return Redirect::back()->with('success', __('errors.sucav'));
    }

    public function referrals(Request $request)
    {
        $user = Auth::user();
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Получаем ID рефералов
        $referralIds = $user->referrals()->pluck('id')->toArray();


        // Подсчитываем общее количество депозитов
        $totalCount = \DB::table('transactions')
            ->whereIn('user_id', $referralIds)
            ->where('type', 'payment')
            ->where('status', 'success')
            ->where('amount', '>', 0)
            ->count();

        $totalDepositsUsd = 0;
        $depositsUsdByUser = [];
        $processedCount = 0;

        // Обрабатываем депозиты чанками с подробным логированием
        \DB::table('transactions as t')
            ->join('currencies as c', 't.currency_id', '=', 'c.id')
            ->select(
                't.id',
                't.user_id',
                't.amount',
                't.created_at',
                'c.symbol as currency_symbol'
            )
            ->whereIn('t.user_id', $referralIds)
            ->where('t.type', 'payment')
            ->where('t.status', 'success')
            ->where('t.amount', '>', 0)
            ->orderBy('t.id')
            ->chunk(500, function ($deposits) use (&$totalDepositsUsd, &$depositsUsdByUser, &$processedCount) {
                $chunkStart = $processedCount;

                foreach ($deposits as $deposit) {
                    try {
                        $amountUsd = $this->exchangeService->convert(
                            $deposit->amount,
                            $deposit->currency_symbol,
                            'USD'
                        );

                        $totalDepositsUsd += $amountUsd;

                        if (!isset($depositsUsdByUser[$deposit->user_id])) {
                            $depositsUsdByUser[$deposit->user_id] = 0;
                        }
                        $depositsUsdByUser[$deposit->user_id] += $amountUsd;

                        $processedCount++;
                    } catch (\Exception $e) {
                        \Log::error('Conversion error', [
                            'id' => $deposit->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

            });


        // Получаем данные рефералов чанками для отображения
        $allReferrals = collect([]);
        $totalFromRefUsd = 0;
        $debugInfo = [];

        $user->referrals()
            ->with(['currency'])
            ->withSum(['transactions as total_deposits' => function ($query) {
                $query->where('type', 'payment')
                      ->where('status', 'success');
            }], 'amount')
            ->when($sortBy === 'total_deposits', function ($query) use ($sortOrder) {
                $query->orderBy('total_deposits', $sortOrder);
            })
            ->when($sortBy === 'from_ref', function ($query) use ($sortOrder) {
                $query->orderBy('from_ref', $sortOrder);
            })
            ->when($sortBy === 'created_at', function ($query) use ($sortOrder) {
                $query->orderBy('created_at', $sortOrder);
            })
            ->chunk(100, function ($referrals) use (&$allReferrals, &$totalFromRefUsd, &$debugInfo, $depositsUsdByUser, $user) {
                foreach ($referrals as $referral) {
                    $referral->total_deposits_usd = $depositsUsdByUser[$referral->id] ?? 0;

                    try {
                        $fromRefUsd = $this->exchangeService->convert(
                            $referral->from_ref ?? 0,
                            $user->currency->symbol,
                            'USD'
                        );
                        $referral->from_ref_usd = $fromRefUsd;
                        $totalFromRefUsd += $fromRefUsd;
                    } catch (\Exception $e) {
                        $referral->from_ref_usd = 0;
                    }

                    $debugInfo[] = [
                        'referral_id' => $referral->id,
                        'raw_deposits' => $referral->total_deposits,
                        'original_currency' => $referral->currency->symbol,
                        'converted_deposits_usd' => $referral->total_deposits_usd,
                        'raw_from_ref' => $referral->from_ref,
                        'converted_from_ref_usd' => $referral->from_ref_usd
                    ];

                    $allReferrals->push($referral);
                }
            });



        try {
            $totalDepositsUserCurrency = $this->exchangeService->convert(
                $totalDepositsUsd,
                'USD',
                $user->currency->symbol
            );

            $totalFromRefUserCurrency = $this->exchangeService->convert(
                $totalFromRefUsd,
                'USD',
                $user->currency->symbol
            );

        } catch (\Exception $e) {

            $totalDepositsUserCurrency = 0;
            $totalFromRefUserCurrency = 0;
        }

        $convertedRefBalance = $this->exchangeService->fromDefaultCurrency(
            $user->ref_balance,
            $user->currency->symbol
        );

        $page = $request->get('page', 1);
        $perPage = 10;
        $referrals = new \Illuminate\Pagination\LengthAwarePaginator(
            $allReferrals->forPage($page, $perPage),
            $allReferrals->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('user.referrals', [
            'refLink' => url('/r/' . $user->affiliate_id),
            'referrals' => $referrals,
            'referralsCount' => $allReferrals->count(),
            'refProfit' => $totalFromRefUserCurrency,
            'refBalance' => $convertedRefBalance,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'totalDeposits' => $totalDepositsUserCurrency,
            'userCurrency' => $user->currency->symbol,
            'debugInfo' => $debugInfo
        ]);
    }

    public function activateReferral($affiliate_id = ''): RedirectResponse
    {
        if (Auth::check()) {
            return Redirect::to('/');
        }

        if ($this->isValidAffiliateId($affiliate_id)) {
            $this->setAffiliateCookie($affiliate_id);
        }


        return Redirect::to('/');
    }

    public function showBalance()
    {
        $user = Auth::user();
        $currency = Currency::find($user->currency_id);
        $currencies = Currency::all();

        return view('balance', [
            'user' => $user,
            'currency' => $currency,
            'currencies' => $currencies,
        ]);
    }

    public function changeCurrency($currencyCode): RedirectResponse
    {
        $user = Auth::user();
        $currency = Currency::where('symbol', $currencyCode)->first();

        if ($currency) {

            $newBalance = $this->exchangeService->convert($user->balance, $user->currency->symbol, $currency->symbol);


            $user->currency_id = $currency->id;
            $user->balance = $newBalance;
            $user->save();
        }

        return Redirect::back();
    }


    public function takeBonus()
    {
        $defaultCurrency = Currency::where('symbol', config('app.currency'))->firstOrFail();
        $user = Auth::user();
        $currentBonus = $user->ref_balance;

        if ($user->currency->id !== $defaultCurrency->id) {
            $currentBonus = $this->exchangeService->fromDefaultCurrency($user->ref_balance, $user->currency->symbol);
        }

        $beforeBalance = $user->balance;
        $user->balance += $currentBonus;
        $afterBalance = $user->balance;
        $user->ref_balance = 0;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $currentBonus,
            'currency_id' => $user->currency_id,
            'type' => TransactionType::Bonus->value,
            'status' => TransactionStatus::Success->value,
            'hash' => Str::uuid()->toString(),
            'context' => json_encode([
                'description' => "Referral bonus",
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
            ]),
        ]);

        return Redirect::back();
    }

    protected function storeImage($image, $userId)
    {
        $directory = public_path('/assets/images/avatars/');

        // Проверяем, существует ли директория, и создаем ее, если нет
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = $userId . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = $image->move($directory, $filename);

        return $path ? '/assets/images/avatars/' . $filename : false;
    }

    private function isValidAffiliateId(string $affiliate_id): bool
    {
        return !empty($affiliate_id) && User::where('affiliate_id', $affiliate_id)->exists();
    }


    private function setAffiliateCookie(string $affiliate_id): void
    {
        Cookie::queue('affiliate_id', $affiliate_id, 60 * 24 * 30);
    }

    public function collectRakeback()
    {
        $defaultCurrency = Currency::where('symbol', config('app.currency'))->firstOrFail();
        $user = Auth::user();
        $rakebackAmount = $user->rakeback;
        if ($user->currency->id !== $defaultCurrency->id) {
            $rakebackAmount = $this->exchangeService->fromDefaultCurrency($user->rakeback, $user->currency->symbol);
        }
        if ($rakebackAmount > 0) {
            // Обнуляем рейкбек баланс
            $user->rakeback = 0;
            // Увеличиваем основной баланс
            $user->balance += $rakebackAmount;
            $user->save();

            session()->flash('success', __('Вы успешно собрали рейкбейк'));

            return response()->json([
                'success' => true,
                'message' => __('Вы успешно собрали рейкбейк'),
                'new_balance' => $user->balance
            ]);
        }

        session()->flash('error', __('У вас нет рейкбейка'));

        return response()->json([
            'success' => false,
            'message' => __('У вас нет рейкбейка')
        ]);
    }

}
