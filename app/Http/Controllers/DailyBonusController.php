<?php
namespace App\Http\Controllers;

use App\Notifications\Notify;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Rank;
use App\Services\ExchangeService;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\DailyBonusToken;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyBonusController extends Controller
{
    protected $telegramService;
    protected $exchangeService;

    public function __construct(TelegramService $telegramService, ExchangeService $exchangeService)
    {
        $this->telegramService = $telegramService;
        $this->exchangeService = $exchangeService;
    }

    public function generateBonusLink()
    {
        $token = $this->generateDailyToken();
        $link = "https://flashgame77.online/daily-bonus/{$token}";

        $this->telegramService->sendDailyBonusMessage($link);

        return response()->json(['link' => $link]);
    }

    public function showDailyBonus($token)
    {
        $user = Auth::user();


        if ($user->rank_id < 2) {
            return redirect('/')->with('error', __('Бонус доступен от 1-го уровня'));
        }

        $rank = Rank::find($user->rank_id);
        if (!$rank) {
            return redirect('/')->with('error', __('Ошибка при определении ранга'));
        }

        $expectedToken = $this->getDailyToken();
        if ($token !== $expectedToken) {
            return redirect('/')->with('error', __('Неверная или устаревшая ссылка на бонус'));
        }

        if ($this->hasClaimedBonusToday($user->id)) {
            return redirect('/')->with('error', __('Вы уже получили сегодняшний бонус'));
        }

        return view('user.daily-bonus', compact('user', 'rank', 'token'));
    }


    // Новый метод для подсчета оборота за 24 часа
    private function get24hTurnover($userId)
    {

      $tenDaysAgo = Carbon::now()->subDays(10);
      $defaultCurrency = config('app.currency', 'USD');

      // Получаем все транзакции ставок и возвратов за последние 10 дней
        $transactions = Transaction::where('user_id', $userId)
            ->whereIn('type', [TransactionType::Bet->value, TransactionType::Refund->value])
            ->where('status', TransactionStatus::Success->value)
            ->where('created_at', '>=', $tenDaysAgo)
            ->get();

        // Группируем транзакции по валюте
        $turnoverByCurrency = $transactions->groupBy('currency_id')->map(function ($currencyTransactions) {
            return $currencyTransactions->groupBy('type')->map(function ($typeTransactions) {
                return $typeTransactions->sum('amount');
            });
        });


        // Считаем общий оборот в USD
        $totalTurnoverUSD = 0;
        foreach ($turnoverByCurrency as $currencyId => $typeTotals) {
            $currency = Currency::find($currencyId);
            $betAmount = $typeTotals[TransactionType::Bet->value] ?? 0;
            $refundAmount = $typeTotals[TransactionType::Refund->value] ?? 0;
            $netAmount = $betAmount - $refundAmount;

            // Конвертируем в USD если нужно
            if ($currency->symbol !== $defaultCurrency) {
                $netAmount = $this->exchangeService->convert($netAmount, $currency->symbol, $defaultCurrency);
            }

            $totalTurnoverUSD += $netAmount;
        }

        return $totalTurnoverUSD;
    }

    public function claimDailyBonus(Request $request)
    {
        $user = Auth::user();

        $expectedToken = $this->generateDailyToken();
        if ($request->token !== $expectedToken) {
            return redirect('/')->with('error', __('Неверная или устаревшая ссылка на бонус'));
        }

        if ($this->hasClaimedBonusToday($user->id)) {
            return redirect('/')->with('error', __('Вы уже получили сегодняшний бонус'));
        }

        if ($user->rank_id < 2) {
            return redirect('/')->with('error', __('Бонус доступен от 1-го уровня'));
        }

        // Проверяем оборот за последние 24 часа вместо общего оборота
        $turnover24h = $this->get24hTurnover($user->id);
        if ($turnover24h < 100) {
            return redirect('/')->with('error', __('Для получения ежедневного бонуса ваш оборот за последние 10 дней должен быть более 100 USD'));
        }

        $rank = Rank::find($user->rank_id);
        if (!$rank) {
            return redirect('/')->with('error', __('Ошибка при определении ранга'));
        }

        $defaultCurrency = 'USD';
        $bonusAmount = mt_rand($rank->daily_min * 100, $rank->daily_max * 100) / 100;

        if ($user->currency->symbol !== $defaultCurrency) {
            $bonusAmount = $this->exchangeService->convert($bonusAmount, 'USD', $user->currency->symbol);
        }

        $beforeBalance = $user->balance;
        $user->balance += $bonusAmount;
        $user->save();
        $afterBalance = $user->balance;

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $bonusAmount,
            'currency_id' => $user->currency_id,
            'type' => TransactionType::DailyBonus->value,
            'status' => TransactionStatus::Success->value,
            'hash' => Str::uuid()->toString(),
            'context' => json_encode([
                'description' => "Daily Bonus",
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
                'turnover_10d' => $turnover24h, // Сохраняем информацию об обороте за 24 часа
            ]),
        ]);

        $messageNotify = __('Вы получили ежедневный бонус в размере :amount :currency', [
            'amount' => $bonusAmount,
            'currency' => $user->currency->symbol
        ]);

        $user->notify(Notify::send('rain', ['message' => $messageNotify]));

        return redirect('/')->with('success', __('Вам начислен ежедневный бонус: :amount :currency', ['amount' => $bonusAmount, 'currency' => $user->currency->symbol]));
    }

    private function generateDailyToken()
    {
        $today = Carbon::today();
        $tokenRecord = DailyBonusToken::firstOrCreate(
            ['date' => $today],
            ['token' => hash('sha256', $today->format('Y-m-d') . config('app.key') . Str::random(10))]
        );
        return $tokenRecord->token;
    }

    private function getDailyToken()
    {
        $today = Carbon::today();
        $tokenRecord = DailyBonusToken::where('date', $today)->first();
        if (!$tokenRecord) {
            return $this->generateDailyToken();
        }
        return $tokenRecord->token;
    }

    private function hasClaimedBonusToday($userId)
    {
        return Transaction::where('user_id', $userId)
            ->where('type', TransactionType::DailyBonus->value)
            ->whereDate('created_at', Carbon::today())
            ->exists();
    }

}
