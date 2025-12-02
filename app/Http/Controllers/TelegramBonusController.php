<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ExchangeService;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Support\Str;
use App\Services\TelegramService;

class TelegramBonusController extends Controller
{
    protected $telegramService;
    protected $exchangeService;

    public function __construct(TelegramService $telegramService, ExchangeService $exchangeService)
    {
        $this->telegramService = $telegramService;
        $this->exchangeService = $exchangeService;
    }

    public function show()
    {
        $user = Auth::user();
        $isSubscribed = false;
        $groupLink = env('TELEGRAM_GROUP_LINK', '');

        if ($user->telegram_id) {
            $isSubscribed = $this->telegramService->isUserMember($user->telegram_id);
        }

        return view('user.bonus', compact('user', 'isSubscribed', 'groupLink'));
    }

    public function claim(Request $request)
    {
        $user = Auth::user();

        if (!$user->telegram_id) {
            return redirect()->back()->with('error', __('Для получения бонуса необходимо привязать Telegram аккаунт в настройках'));
        }

        if ($this->hasClaimedBonus($user->id)) {
            return redirect()->back()->with('error', __('Вы уже получили бонус за подписку на группу'));
        }

        if (!$this->telegramService->isUserMember($user->telegram_id)) {
            return redirect()->back()->with('error', __('Для получения бонуса необходимо быть участником нашей группы в Telegram'));
        }

        $bonusAmount = $this->exchangeService->convert(1, 'AZN', $user->currency->symbol);
        $bonusAmount = moneyFormat($bonusAmount);

        $beforeBalance = $user->balance;
        $user->balance += $bonusAmount;
        $user->save();
        $afterBalance = $user->balance;

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $bonusAmount,
            'currency_id' => $user->currency_id,
            'type' => TransactionType::TelegramBonus->value,
            'status' => TransactionStatus::Success->value,
            'hash' => Str::uuid()->toString(),
            'context' => json_encode([
                'description' => "Telegram Subscription Bonus",
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
            ]),
        ]);

        return redirect()->back()->with('success', __('Вам начислен бонус за подписку: :amount :currency', ['amount' => $bonusAmount, 'currency' => $user->currency->symbol]));
    }

    private function hasClaimedBonus($userId)
    {
        return Transaction::where('user_id', $userId)
            ->where('type', TransactionType::TelegramBonus->value)
            ->exists();
    }
}
