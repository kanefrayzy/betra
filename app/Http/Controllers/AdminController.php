<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Promocode;
use App\Models\Settings;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Rate;
use App\Models\Transaction;
use App\Models\ForbiddenWord;
use App\Models\SlotegratorGame;
use App\Models\AdminTask;
use App\Models\Expense;
use App\Notifications\Notify;
use App\Models\PaymentHandler;
use App\Enums\TransactionType;
use App\Enums\PaymentStatus;
use App\Services\ExchangeService;
use App\Services\BetaTransferService;
use App\Models\UserVerification;
use App\Models\PaymentSystem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;
use App\Services\NotifyService;

class AdminController extends Controller
{
    const CHAT_CHANNEL = 'chat.message';
    const NEW_MSG_CHANNEL = 'new.msg';
    const CLEAR = 'chat.clear';
    const DELETE_MSG_CHANNEL = 'del.msg';


    protected $notifyService;

    public function __construct(NotifyService $notifyService)
    {
        $this->notifyService = $notifyService;
    }

    public function getParam()
    {
        return [
            'fake' => Settings::get()->fake,
        ];
    }


    public function userDelete($id)
    {
        $case = User::where('id', $id)->first();
        User::where('id', $id)->delete();

        return Redirect::route('adminBots')->with('success', 'Пользователь удален!');
    }

    public function index()
    {
      $currentUser = Auth::user();
      if ($currentUser->is_moder){
          return redirect()->route('admin.detailed_statistics');
        }else if ($currentUser->is_withdraw_moder){
          return redirect()->route('adminWithdraw');
        }else {
        return redirect()->route('adminUsers');
      }
    }

    public function stats()
    {

        $currentUser = Auth::user();

        if ($currentUser->id == 34) {
            return redirect()->route('adminUsers');
        }

        $currencies = Currency::all();
        $exchangeService = app(ExchangeService::class);
        $currencyRates = $this->getCurrencyRates($currencies, $exchangeService);


        $transactions = $this->getTransactions($currencies);

        // Кэшируем только обработку данных и расчеты
        $cachedData = Cache::remember('admin_statistics_processed', 60, function () use ($transactions, $currencies, $currencyRates) {
            $statistics = $this->processTransactions($transactions, $currencies, $currencyRates);
            $totals = $this->calculateTotals($transactions, $currencyRates);
            return compact('statistics', 'totals');
        });


        $additionalStats = $this->getAdditionalStats();

        return view('admin.index', [
            'statistics' => $cachedData['statistics'],
            'with_req' => 1,
            'odobr' => 1,
            'additionalStats' => $additionalStats
        ] + $cachedData['totals']);
    }

    private function getAdditionalStats()
    {
        $today = now()->startOfDay();
        $yesterday = now()->yesterday();
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newUsersYesterday = User::whereDate('created_at', $yesterday)->count();
        $telegramConfirmed = User::whereNotNull('telegram_id')->count();

        $dailyBonusStats = Transaction::where('type', 'dailybonus')
            ->whereDate('created_at', $today)
            ->get();

            $dailyBonusStatsYesterday = Transaction::where('type', 'dailybonus')
                ->whereDate('created_at', $yesterday)
                ->get();

        $dailyBonusCount = $dailyBonusStats->count();
        $dailyBonusCountYesterday = $dailyBonusStatsYesterday->count();
        $dailyBonusTotal = 0;
        $dailyBonusTotalYesterday = 0;

        foreach ($dailyBonusStats as $bonus) {
            $currency = Currency::find($bonus->currency_id);
            if ($currency) {
                $usdAmount = $this->convertToUSD($bonus->amount, $currency->symbol);
                $dailyBonusTotal += $usdAmount;
            }
        }

        foreach ($dailyBonusStatsYesterday as $bonus) {
            $currency = Currency::find($bonus->currency_id);
            if ($currency) {
                $usdAmount = $this->convertToUSD($bonus->amount, $currency->symbol);
                $dailyBonusTotalYesterday += $usdAmount;
            }
        }

      return [
          'totalUsers' => $totalUsers,
          'newUsersToday' => $newUsersToday,
          'newUsersYesterday' => $newUsersYesterday,
          'telegramConfirmed' => $telegramConfirmed,
          'dailyBonusCount' => $dailyBonusCount,
          'dailyBonusCountYesterday' => $dailyBonusCountYesterday,
          'dailyBonusTotal' => number_format($dailyBonusTotal, 2),
          'dailyBonusTotalYesterday' => number_format($dailyBonusTotalYesterday, 2)
      ];
  }

  private function convertToUSD($amount, $fromCurrency)
  {
      if ($fromCurrency === 'USD') {
          return $amount;
      }

      $exchangeService = app(ExchangeService::class);
      return $exchangeService->convert($amount, $fromCurrency, 'USD');
  }


    private function getTransactions($currencies)
    {
        $now = now();
        return DB::table('transactions')
            ->select('currency_id')
            ->selectRaw('
                SUM(CASE WHEN type = "payment" AND status = "success" THEN amount ELSE 0 END) as total_deposits,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" THEN amount ELSE 0 END) as total_withdrawals,
                SUM(CASE WHEN type = "payment" AND status = "success" AND DATE(created_at) = ? THEN amount ELSE 0 END) as pay_today,
                SUM(CASE WHEN type = "payment" AND status = "success" AND DATE(created_at) = ? THEN amount ELSE 0 END) as pay_yesterday,
                SUM(CASE WHEN type = "payment" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as pay_week,
                SUM(CASE WHEN type = "payment" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as pay_month,
                SUM(CASE WHEN type = "payment" AND status = "success" THEN amount ELSE 0 END) as pay_all,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" AND DATE(updated_at) = ? THEN amount ELSE 0 END) as with_today,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" AND DATE(updated_at) = ? THEN amount ELSE 0 END) as with_yesterday,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" THEN amount ELSE 0 END) as with_all
            ')
            ->whereIn('currency_id', $currencies->pluck('id'))
            ->whereIn('type', ['payment', 'withdrawal'])
            ->where('status', 'success')
            ->groupBy('currency_id')
            ->addBinding([
                $now->toDateString(),
                $now->copy()->subDay()->toDateString(),
                $now->copy()->startOfWeek()->toDateTimeString(),
                $now->copy()->startOfMonth()->toDateTimeString(),
                $now->toDateString(),
                $now->copy()->subDay()->toDateString()
            ], 'select')
            ->get();
    }


    private function getCurrencyRates($currencies, $exchangeService)
    {
        return Cache::remember('currency_rates', 3600, function () use ($currencies, $exchangeService) {
            return $currencies->mapWithKeys(function ($currency) use ($exchangeService) {
                $rate = $currency->symbol === 'USD' ? 1 : $exchangeService->convert(1, $currency->symbol, 'USD');
                return [$currency->id => $rate];
            });
        });
    }

    private function processTransactions($transactions, $currencies, $currencyRates)
    {
        return $transactions->map(function ($transaction) use ($currencies, $currencyRates) {
            $currency = $currencies->find($transaction->currency_id);
            return [
                'currency_code' => $currency->symbol,
                'currency_name' => $currency->name,
                'total_deposits' => $transaction->total_deposits,
                'total_withdrawals' => $transaction->total_withdrawals,
                'pay_today' => $transaction->pay_today,
                'pay_yesterday' => $transaction->pay_yesterday,
                'pay_week' => $transaction->pay_week,
                'pay_month' => $transaction->pay_month,
                'pay_all' => $transaction->pay_all,
                'with_today' => $transaction->with_today,
                'with_all' => $transaction->with_all,
            ];
        });
    }

    private function calculateTotals($transactions, $currencyRates)
    {
        $totals = [
            'totalDepositsInUSD' => 0,
            'totalWithdrawalsInUSD' => 0,
            'payTodayInUSD' => 0,
            'payYesterdayInUSD' => 0,
            'payWeekInUSD' => 0,
            'payMonthInUSD' => 0,
            'payAllInUSD' => 0,
            'withTodayInUSD' => 0,
            'withYesterdayInUSD' => 0,
            'withAllInUSD' => 0,
        ];

        foreach ($transactions as $transaction) {
            $rate = $currencyRates[$transaction->currency_id];
            $totals['totalDepositsInUSD'] += $transaction->total_deposits * $rate;
            $totals['totalWithdrawalsInUSD'] += $transaction->total_withdrawals * $rate;
            $totals['payTodayInUSD'] += $transaction->pay_today * $rate;
            $totals['payYesterdayInUSD'] += $transaction->pay_yesterday * $rate;
            $totals['payWeekInUSD'] += $transaction->pay_week * $rate;
            $totals['payMonthInUSD'] += $transaction->pay_month * $rate;
            $totals['payAllInUSD'] += $transaction->pay_all * $rate;
            $totals['withTodayInUSD'] += $transaction->with_today * $rate;
            $totals['withYesterdayInUSD'] += $transaction->with_yesterday * $rate;
            $totals['withAllInUSD'] += $transaction->with_all * $rate;
        }

        return $totals;
    }

    public function statsFromDate()
    {
        $currentUser = Auth::user();

        $currencies = Currency::all();
        $exchangeService = app(ExchangeService::class);
        $currencyRates = $this->getCurrencyRates($currencies, $exchangeService);
        $transactions = $this->getTransactionsFromDate($currencies);

        // Получаем статистику модераторов за все время
        $moderatorStats = Cache::remember('moderator_withdrawal_stats', 60, function () {
            return DB::table('withdrawals')
                ->join('users', 'withdrawals.accepted_by', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.username',
                    DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
                    DB::raw('COUNT(CASE WHEN status = "refunded" THEN 1 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as completed_amount'),
                    DB::raw('SUM(CASE WHEN status = "refunded" THEN amount ELSE 0 END) as rejected_amount')
                )
                ->whereNotNull('accepted_by')
                ->groupBy('users.id', 'users.username')
                ->get();
        });

        // Статистика модераторов за сегодня
        $moderatorStatsToday = Cache::remember('moderator_withdrawal_stats_today', 60, function () {
            return DB::table('withdrawals')
                ->join('users', 'withdrawals.accepted_by', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.username',
                    DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
                    DB::raw('COUNT(CASE WHEN status = "refunded" THEN 1 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as completed_amount'),
                    DB::raw('SUM(CASE WHEN status = "refunded" THEN amount ELSE 0 END) as rejected_amount')
                )
                ->whereNotNull('accepted_by')
                ->whereDate('withdrawals.updated_at', now()->toDateString())
                ->groupBy('users.id', 'users.username')
                ->get();
        });

        $moderatorStatsYesterday = Cache::remember('moderator_withdrawal_stats_yesterday', 60, function () {
            return DB::table('withdrawals')
                ->join('users', 'withdrawals.accepted_by', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.username',
                    DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
                    DB::raw('COUNT(CASE WHEN status = "refunded" THEN 1 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as completed_amount'),
                    DB::raw('SUM(CASE WHEN status = "refunded" THEN amount ELSE 0 END) as rejected_amount')
                )
                ->whereNotNull('accepted_by')
                ->whereDate('withdrawals.updated_at', now()->subDay()->toDateString())
                ->groupBy('users.id', 'users.username')
                ->get();
        });

        $statistics = $this->processTransactions($transactions, $currencies, $currencyRates);
        $totals = $this->calculateTotals($transactions, $currencyRates);

        return view('admin.newstats', [
            'statistics' => $statistics,
            'with_req' => 1,
            'odobr' => 1,
            'additionalStats' => $this->getAdditionalStatsFromDate(),
            'moderatorStats' => $moderatorStats,
            'moderatorStatsToday' => $moderatorStatsToday,
            'moderatorStatsYesterday' => $moderatorStatsYesterday
        ] + $totals);
    }

    private function getTransactionsFromDate($currencies)
    {
        $startDate = Carbon::create(2024, 12, 12)->startOfDay();
        $now = now();

        return DB::table('transactions')
            ->select('currency_id')
            ->selectRaw('
                SUM(CASE WHEN type = "payment" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as total_deposits,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as total_withdrawals,
                SUM(CASE WHEN type = "payment" AND status = "success" AND DATE(created_at) = ? THEN amount ELSE 0 END) as pay_today,
                SUM(CASE WHEN type = "payment" AND status = "success" AND DATE(created_at) = ? THEN amount ELSE 0 END) as pay_yesterday,
                SUM(CASE WHEN type = "payment" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as pay_week,
                SUM(CASE WHEN type = "payment" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as pay_month,
                SUM(CASE WHEN type = "payment" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as pay_all,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" AND DATE(updated_at) = ? THEN amount ELSE 0 END) as with_today,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" AND DATE(updated_at) = ? THEN amount ELSE 0 END) as with_yesterday,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" AND created_at >= ? THEN amount ELSE 0 END) as with_all
            ')
            ->whereIn('currency_id', $currencies->pluck('id'))
            ->whereIn('type', ['payment', 'withdrawal'])
            ->where('status', 'success')
            ->where('created_at', '>=', $startDate)
            ->groupBy('currency_id')
            ->addBinding([
                $startDate->toDateTimeString(),
                $startDate->toDateTimeString(),
                $now->toDateString(),
                $now->copy()->subDay()->toDateString(),
                $now->copy()->startOfWeek()->toDateTimeString(),
                $now->copy()->startOfMonth()->toDateTimeString(),
                $startDate->toDateTimeString(),
                $now->toDateString(),
                $now->copy()->subDay()->toDateString(),
                $startDate->toDateTimeString()
            ], 'select')
            ->get();
    }

    private function getAdditionalStatsFromDate()
    {
        $startDate = Carbon::create(2024, 12, 12)->startOfDay();
        $today = now()->startOfDay();
        $yesterday = now()->yesterday();

        $totalUsers = User::where('created_at', '>=', $startDate)->count();
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newUsersYesterday = User::whereDate('created_at', $yesterday)->count();
        $telegramConfirmed = User::where('created_at', '>=', $startDate)
            ->whereNotNull('telegram_id')
            ->count();

        $dailyBonusStats = Transaction::where('type', 'dailybonus')
            ->whereDate('created_at', $today)
            ->get();

        $dailyBonusStatsYesterday = Transaction::where('type', 'dailybonus')
            ->whereDate('created_at', $yesterday)
            ->get();

        $dailyBonusCount = $dailyBonusStats->count();
        $dailyBonusCountYesterday = $dailyBonusStatsYesterday->count();
        $dailyBonusTotal = 0;
        $dailyBonusTotalYesterday = 0;

        foreach ($dailyBonusStats as $bonus) {
            $currency = Currency::find($bonus->currency_id);
            if ($currency) {
                $usdAmount = $this->convertToUSD($bonus->amount, $currency->symbol);
                $dailyBonusTotal += $usdAmount;
            }
        }

        foreach ($dailyBonusStatsYesterday as $bonus) {
            $currency = Currency::find($bonus->currency_id);
            if ($currency) {
                $usdAmount = $this->convertToUSD($bonus->amount, $currency->symbol);
                $dailyBonusTotalYesterday += $usdAmount;
            }
        }

        return [
            'totalUsers' => $totalUsers,
            'newUsersToday' => $newUsersToday,
            'newUsersYesterday' => $newUsersYesterday,
            'telegramConfirmed' => $telegramConfirmed,
            'dailyBonusCount' => $dailyBonusCount,
            'dailyBonusCountYesterday' => $dailyBonusCountYesterday,
            'dailyBonusTotal' => number_format($dailyBonusTotal, 2),
            'dailyBonusTotalYesterday' => number_format($dailyBonusTotalYesterday, 2)
        ];
    }

    public function statsCategory()
    {

        $currentUser = Auth::user();

        if ($currentUser->id == 34) {
            return redirect()->route('adminUsers');
        }

        $exchangeService = app(ExchangeService::class);
        $currencies = Currency::all();

        $stats = $this->calculateStats($exchangeService, $currencies);

        return view('admin.statscategory', $stats);
    }

    private function calculateStats($exchangeService, $currencies)
    {
        $periods = [
            'Today' => [Carbon::today(), Carbon::tomorrow()],
            'Yesterday' => [Carbon::yesterday(), Carbon::today()],
            'Total' => [null, null]
        ];

        $stats = [];

        foreach ($periods as $periodName => $dateRange) {
            $transactions = $this->getTransactionsCat($dateRange[0], $dateRange[1], 'payment');
            $transactionsWithdrawals = $this->getTransactionsCat($dateRange[0], $dateRange[1], 'withdrawal');

            $stats["m10Payments{$periodName}"] = $this->calculateAmount($transactions, 'payment_handler', 'M10', $exchangeService, $currencies);
            $stats["m10Withdrawals{$periodName}"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', '11', $exchangeService, $currencies);
            $stats["mPayPayments{$periodName}"] = $this->calculateAmount($transactions, 'payment_handler', 'MPAY', $exchangeService, $currencies);
            $stats["mPayWithdrawals{$periodName}"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', '13', $exchangeService, $currencies);
            $stats["cardPayments{$periodName}"] = $this->calculateAmount($transactions, 'payment_handler', 'Card AZ', $exchangeService, $currencies);
            $stats["cardWithdrawals{$periodName}"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', '12', $exchangeService, $currencies);
            $stats["paykassaPayments{$periodName}"] = $this->calculateAmount($transactions, 'payment_system', 'paykassa', $exchangeService, $currencies);
            $stats["paykassaWithdrawals{$periodName}"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', 'paykassa', $exchangeService, $currencies);
            $stats["freekassaPayments{$periodName}"] = $this->calculateAmount($transactions, 'payment_system', 'FreeKassa', $exchangeService, $currencies);
            $stats["freekassaWithdrawals{$periodName}"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', 'FreeKassa', $exchangeService, $currencies);
        }

        return $stats;
    }

    private function getTransactionsCat($start, $end, $type)
    {
        $query = Transaction::where('type', $type)
            ->where('status', 'success');

        if ($start && $end) {
            $query->whereBetween('updated_at', [$start, $end]);
        }

        return $query->get();
    }

    private function calculateAmount($transactions, $contextKey, $contextValue, $exchangeService, $currencies)
    {
        return $transactions
            ->filter(function ($transaction) use ($contextKey, $contextValue) {
                $context = json_decode($transaction->context, true);
                return isset($context[$contextKey]) && $context[$contextKey] === $contextValue;
            })
            ->sum(function ($transaction) use ($exchangeService, $currencies) {
                $currency = $currencies->find($transaction->currency_id);
                $amount = $transaction->amount;
                if ($currency->symbol !== 'USD') {
                    $amount = $exchangeService->convert($amount, $currency->symbol, 'USD');
                }
                return $amount;
            });
    }
    public function statistics()
    {
        try {
            $currencies = Currency::all();
            $exchangeRates = $this->getCachedExchangeRates($currencies);

            // Используем временную таблицу для агрегации данных
            DB::statement('CREATE TEMPORARY TABLE temp_stats AS
                SELECT
                    currency_id,
                    SUM(CASE WHEN type = "bet" THEN amount WHEN type = "refund" THEN -amount ELSE 0 END) as total_bets,
                    SUM(CASE WHEN type = "win" THEN amount ELSE 0 END) as total_wins,
                    SUM(CASE WHEN (type = "bet" OR type = "refund") AND created_at >= CURDATE() THEN
                        CASE WHEN type = "bet" THEN amount ELSE -amount END
                    ELSE 0 END) as bets_today,
                    SUM(CASE WHEN type = "win" AND created_at >= CURDATE() THEN amount ELSE 0 END) as wins_today,
                    SUM(CASE WHEN (type = "bet" OR type = "refund") AND created_at >= CURDATE() - INTERVAL 1 DAY AND created_at < CURDATE() THEN
                        CASE WHEN type = "bet" THEN amount ELSE -amount END
                    ELSE 0 END) as bets_yesterday,
                    SUM(CASE WHEN type = "win" AND created_at >= CURDATE() - INTERVAL 1 DAY AND created_at < CURDATE() THEN amount ELSE 0 END) as wins_yesterday,
                    SUM(CASE WHEN (type = "bet" OR type = "refund") AND created_at >= DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) THEN
                        CASE WHEN type = "bet" THEN amount ELSE -amount END
                    ELSE 0 END) as bets_week,
                    SUM(CASE WHEN type = "win" AND created_at >= DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) THEN amount ELSE 0 END) as wins_week,
                    SUM(CASE WHEN (type = "bet" OR type = "refund") AND created_at >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) THEN
                        CASE WHEN type = "bet" THEN amount ELSE -amount END
                    ELSE 0 END) as bets_month,
                    SUM(CASE WHEN type = "win" AND created_at >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) THEN amount ELSE 0 END) as wins_month
                FROM transactions
                WHERE currency_id IN (' . implode(',', $currencies->pluck('id')->toArray()) . ')
                GROUP BY currency_id'
            );

            $transactions = DB::table('temp_stats')->get();

            $stats = $this->calculateStatsSlot($transactions, $exchangeRates);

            DB::statement('DROP TEMPORARY TABLE IF EXISTS temp_stats');

            return view('admin.statistics', compact('stats'));
        } catch (\Exception $e) {
            Log::error("Error in statistics method: " . $e->getMessage());
            return view('admin.error', ['message' => 'An error occurred while generating statistics.']);
        }
    }

    private function getCachedExchangeRates($currencies)
    {
        return Cache::remember('exchange_rates', now()->addHours(1), function () use ($currencies) {
            $exchangeService = new ExchangeService();
            return $currencies->mapWithKeys(function ($currency) use ($exchangeService) {
                try {
                    $rate = $currency->symbol === 'USD' ? 1 : $exchangeService->convert(1, $currency->symbol, 'USD');
                    return [$currency->id => $rate];
                } catch (\Exception $e) {
                    Log::error("Error converting currency {$currency->symbol}: " . $e->getMessage());
                    return [$currency->id => null];
                }
            })->filter()->all();
        });
    }

    private function calculateStatsSlot($transactions, $exchangeRates)
    {
        $stats = [
            'total' => ['bets' => 0, 'wins' => 0, 'profit' => 0, 'provider_debt' => 0],
            'today' => ['bets' => 0, 'wins' => 0, 'profit' => 0, 'provider_debt' => 0],
            'yesterday' => ['bets' => 0, 'wins' => 0, 'profit' => 0, 'provider_debt' => 0],
            'week' => ['bets' => 0, 'wins' => 0, 'profit' => 0, 'provider_debt' => 0],
            'month' => ['bets' => 0, 'wins' => 0, 'profit' => 0, 'provider_debt' => 0],
        ];

        foreach ($transactions as $transaction) {
            if (!isset($exchangeRates[$transaction->currency_id])) {
                Log::warning("Missing conversion rate for currency ID: " . $transaction->currency_id);
                continue;
            }

            $conversionRate = $exchangeRates[$transaction->currency_id];

            $stats['total']['bets'] += $transaction->total_bets * $conversionRate;
            $stats['total']['wins'] += $transaction->total_wins * $conversionRate;
            $stats['today']['bets'] += $transaction->bets_today * $conversionRate;
            $stats['today']['wins'] += $transaction->wins_today * $conversionRate;
            $stats['yesterday']['bets'] += $transaction->bets_yesterday * $conversionRate;
            $stats['yesterday']['wins'] += $transaction->wins_yesterday * $conversionRate;
            $stats['week']['bets'] += $transaction->bets_week * $conversionRate;
            $stats['week']['wins'] += $transaction->wins_week * $conversionRate;
            $stats['month']['bets'] += $transaction->bets_month * $conversionRate;
            $stats['month']['wins'] += $transaction->wins_month * $conversionRate;
        }

        foreach ($stats as &$period) {
            $period['profit'] = $period['bets'] - $period['wins'];
            $period['provider_debt'] = $period['profit'] * 0.15; // 15% от профита
        }

        return $stats;
    }

    public function payHistory($id)
    {
        $us = User::where('id', $id)->first();
        $pays = Transaction::where('user_id', $id)
            ->where('amount', '>', 0)
            ->where('type', 'payment')
            ->where('status', 'success')
            ->orderBy('updated_at', 'desc')
            ->paginate(25);

            foreach ($pays as $pay) {
                if (is_string($pay->context)) {
                    $pay->context = json_decode($pay->context, true);
                }
            }
        $withdraws = Transaction::where('user_id', $id)
            ->where('type', 'withdrawal')
            ->where('amount', '>', 0)
            ->where('status', 'success')
            ->orderBy('updated_at', 'desc')
            ->paginate(25);


            foreach ($withdraws as $withs) {
                if (is_string($withs->context)) {
                    $withs->context = json_decode($withs->context, true);
                }
            }
        return view('admin.payHistory', compact('pays', 'withdraws'));
    }

    public function gameHistory($id)
    {
        $bets = Transaction::where('user_id', $id)
            ->where('amount', '>', 0)
            ->whereIn('type', ['bet', 'win'])
            ->orderBy('updated_at', 'desc')
            ->paginate(25); // Указываем количество элементов на странице

        foreach ($bets as $bet) {
            if (is_string($bet->context)) {
                $bet->context = json_decode($bet->context, true);
            }
        }

        $win = Transaction::where('user_id', $id)
            ->where('type', 'win')
            ->where('amount', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->paginate(25); // Указываем количество элементов на странице

        foreach ($win as $wins) {
            if (is_string($wins->context)) {
                $wins->context = json_decode($wins->context, true);
            }
        }

        return view('admin.gameHistory', compact('bets', 'win'));
    }

    public function transferHistory($id)
    {
        $transfers = Transaction::where('user_id', $id)
            ->where('amount', '>', 0)
            ->where('type', 'transfer')
            ->orderBy('updated_at', 'desc')
            ->paginate(25); // Указываем количество элементов на странице

        foreach ($transfers as $transfer) {
            if (is_string($transfer->context)) {
                $transfer->context = json_decode($transfer->context, true);
            }
        }


        return view('admin.transferHistory', compact('transfers'));
    }


    public function users()
    {
        return view('admin.users');
    }


    public function usersAjax()
    {
        $users = User::query()
            ->select('users.*')
            ->selectRaw('(users.rank_id) as adjusted_rank_id');

        return datatables($users)
            ->addColumn('rank', function ($user) {
                return $user->adjusted_rank_id;
            })
            ->orderColumn('rank', function ($query, $order) {
                $query->orderBy('adjusted_rank_id', $order);
            })
            ->addColumn('status', function ($user) {
                return true;
            })
            ->orderColumn('status', function ($query, $order) {
                $query->orderBy('is_admin', $order)
                ->orderBy('is_moder', $order)
                ->orderBy('is_youtuber', $order);

            })
            ->toJson();
    }

    public function user($id)
    {
        $user = User::find($id);
        
        // Теперь показываем статистику только для валюты пользователя
        $userCurrency = $user->currency;
        $statistics = [];
        
        // Получаем только платёжные транзакции (депозиты/выводы) - они не удаляются
        $paymentTransactions = Transaction::where('user_id', $id)
            ->where('currency_id', $userCurrency->id)
            ->selectRaw('currency_id,
                SUM(CASE WHEN type = "payment" AND status = "success" THEN amount ELSE 0 END) as total_deposits,
                COUNT(CASE WHEN type = "payment" AND status = "success" THEN 1 ELSE NULL END) as deposit_count,
                SUM(CASE WHEN type = "withdrawal" AND status = "success" THEN amount ELSE 0 END) as total_withdrawals,
                COUNT(CASE WHEN type = "withdrawal" AND status = "success" THEN 1 ELSE NULL END) as withdrawal_count')
            ->groupBy('currency_id')
            ->first();
        
        // Конвертируем игровую статистику из USD в валюту пользователя для отображения
        $exchangeService = app(\App\Services\ExchangeService::class);
        $totalBetsInUserCurrency = $exchangeService->convert($user->total_bets_amount ?? 0, 'USD', $userCurrency->symbol);
        $totalWinsInUserCurrency = $exchangeService->convert($user->total_wins_amount ?? 0, 'USD', $userCurrency->symbol);
        
        // Добавляем статистику для валюты пользователя
        $statistics[] = [
            'currency_code' => $userCurrency->symbol,
            'currency_name' => $userCurrency->name,
            // Платёжные операции (из транзакций - не удаляются)
            'total_deposits' => $paymentTransactions ? $paymentTransactions->total_deposits : 0,
            'deposit_count' => $paymentTransactions ? $paymentTransactions->deposit_count : 0,
            'total_withdrawals' => $paymentTransactions ? $paymentTransactions->total_withdrawals : 0,
            'withdrawal_count' => $paymentTransactions ? $paymentTransactions->withdrawal_count : 0,
            // Игровая статистика (из полей users таблицы - конвертированная в валюту пользователя)
            'total_bets' => $totalBetsInUserCurrency,
            'total_wins' => $totalWinsInUserCurrency,
            'net_bets' => $totalBetsInUserCurrency,
            // Статистика из БД (обновляются командой oborot:update каждую минуту)
            'total_games' => $user->total_games ?? 0,
            'total_wins_count' => $user->total_wins ?? 0,
            'total_bets_amount' => $user->total_bets_amount ?? 0, // В USD для отображения в карточках
            'total_wins_amount' => $user->total_wins_amount ?? 0, // В USD для отображения в карточках
        ];
        
        $refcount = User::where('referred_by', $user->user_id)->count();
        
        // Считаем общую сумму заработанную от рефералов из таблицы referral_bonuses
        $ref = \DB::table('referral_bonuses')
            ->where('referrer_user_id', $user->id)
            ->sum('amount');
        
        // Считаем сколько данный пользователь принес своему рефереру
        $broughtToReferrer = \DB::table('referral_bonuses')
            ->where('referral_user_id', $user->id)
            ->sum('amount');
        
        $referrer = $user->referred_by ? User::where('user_id', $user->referred_by)->first() : null;
        $rain_ud = $user->rain_money;
        return view('admin.user', compact('user', 'statistics', 'ref', 'refcount', 'referrer', 'broughtToReferrer', 'rain_ud'));
    }

    public function userSave(Request $r)
    {
        $admin = 0;
        $moder = 0;
        $chat_moder = 0;
        if ($r->get('id') == null) {
            return redirect()->route('adminUsers')->with('error', 'Не удалось найти пользователя с таким ID!');
        }
        if ($r->get('balance') == null) {
            return redirect()->route('adminUsers')->with('error', 'Поле "Баланс" не может быть пустым!');
        }
        if ($r->get('priv') == 'Admin') {
            $admin = 1;
        }
        if ($r->get('priv') == 'moder') {
            $moder = 1;
        }
        if ($r->get('priv') == 'chat_moder') {
            $chat_moder = 1;
        }

        $paymentBanTime = $r->get('payment_ban');
        $paymentBanAt = null;
        if ($paymentBanTime) {
            $paymentBanAt = now()->addHours($paymentBanTime);
        }

        $currentUser = Auth::user();
        $user = User::find($r->get('id'));

        // Массив полей для обновления
        $updatedFields = [
            'payment_ban_at' => $paymentBanAt,
        ];

        // Если пользователь - администратор, добавляем остальные поля
        if ($currentUser->is_admin) {
            $updatedFields['is_admin'] = $admin;
            $updatedFields['is_moder'] = $moder;
            $updatedFields['is_chat_moder'] = $chat_moder;
            $updatedFields['username'] = $r->get('username');
            $updatedFields['balance'] = $r->get('balance');

            // Обработка реферального процента
            $refPercentage = $r->get('ref_percentage');
            if ($refPercentage !== null) {
                // Проверяем и ограничиваем значение от 0 до 100
                $refPercentage = max(0, min(100, floatval($refPercentage)));
                // Если значение 5 (стандартное), записываем null
                $updatedFields['ref_percentage'] = $refPercentage == 20 ? null : $refPercentage;
            }
        }

        // Если пользователя банят
        if ($currentUser->is_admin) {
            if ($r->get('user_ban') == 1) {
                $updatedFields['last_login_at'] = null;
                $updatedFields['ban'] = 1;
            }
            if ($r->get('user_ban') == 2) {
                $updatedFields['ban'] = 0;
            }
        }

        // Обновление пользователя
        User::where('id', $r->get('id'))->update($updatedFields);

        $message = 'Пользователь ID ' . $r->get('id') . ' был изменен пользователем ' .
            $currentUser->username . '. Измененные поля: ' . json_encode($updatedFields, JSON_UNESCAPED_UNICODE);

        return redirect()->route('adminUsers')->with('success', 'Пользователь сохранен!');
    }

    public function settings()
    {

        return view('admin.settings');
    }

    public function settingsSave(Request $r)
    {
        Settings::where('id', 1)->update([
            'domain' => $r->get('domain'),
            'sitename' => $r->get('sitename'),
            'title' => $r->get('title'),
            'desc' => $r->get('desc'),
            'keys' => $r->get('keys'),
            'min_rain_amount' => $r->get('min_rain_amount'),
            'max_rain_amount' => $r->get('max_rain_amount'),
            'min_rain_count' => $r->get('min_rain_count'),
            'max_rain_count' => $r->get('max_rain_count'),
            'min_rain_user_oborot' => $r->get('min_rain_user_oborot'),
            'chat_mess_support' => $r->get('chat_mess_support'),
            'chat_status' => $r->get('chat_status'),
            'ip_maintenance' => str_replace(' ', '', $r->get('ip_maintenance')),
            'text_maintenance' => $r->get('text_maintenance'),
            'withdrawal_commission' => $r->get('withdrawal_commission', 0),
            'support_tg' => $r->get('support_tg'),
            'welcome_bonus_enabled' => $r->has('welcome_bonus_enabled') ? 1 : 0,
            'welcome_bonus_amount' => $r->get('welcome_bonus_amount', 0),
        ]);

        $maintenance_mode = App::isDownForMaintenance();
        if ($maintenance_mode) {
            if ($r->get('status_maintenance') == 1) {
                Artisan::call('up');
            }
        } else {
            if ($r->get('status_maintenance') == 0) {
                Artisan::call('down');
            }
        }

        return redirect()->route('adminSettings')->with('success', 'Настройки сохранен!');
    }

    public function promo()
    {
        $codes = Promocode::get();

        return view('admin.promo', compact('codes'));
    }

    public function promoNew(Request $r)
    {
        $code = $r->get('code');
        $limit = $r->get('limit');
        $amount = $r->get('amount');
        $count_use = $r->get('count_use');
        $have = Promocode::where('code', $code)->first();
        if (!$code) return redirect()->route('adminPromo')->with('error', 'Вы заполнили не все поля!');
        if (!$amount) return redirect()->route('adminPromo')->with('error', 'Вы заполнили не все поля!');
        if (!$count_use) return redirect()->route('adminPromo')->with('error', 'Вы заполнили не все поля!');
        if ($have) return redirect()->route('adminPromo')->with('error', 'Такой код уже существует');

        Promocode::create([
            'code' => $code,
            'limit' => $limit,
            'amount' => $amount,
            'count_use' => $count_use
        ]);

        return redirect()->route('adminPromo')->with('success', 'Промокод создан!');
    }

    public function promoSave(Request $r)
    {
        $id = $r->get('id');
        $code = $r->get('code');
        $limit = $r->get('limit');
        $amount = $r->get('amount');
        $count_use = $r->get('count_use');
        $have = Promocode::where('code', $code)->where('id', '!=', $id)->first();
        if (!$id) return redirect()->route('adminPromo')->with('error', 'Не удалось найти данный ID!');
        if (!$code) return redirect()->route('adminPromo')->with('error', 'Вы заполнили не все поля!');
        if (!$amount) return redirect()->route('adminPromo')->with('error', 'Вы заполнили не все поля!');
        if (!$count_use) $count_use = 0;
        if ($have) return redirect()->route('adminPromo')->with('error', 'Такой код уже существует');

        Promocode::where('id', $id)->update([
            'code' => $code,
            'limit' => $limit,
            'amount' => $amount,
            'count_use' => $count_use
        ]);

        return redirect()->route('adminPromo')->with('success', 'Промокод обновлен!');
    }

    public function promoDelete($id)
    {
        if (!$id) return redirect()->route('adminPromo')->with('error', 'Нет такого промокода!');
        Promocode::where('id', $id)->delete();

        return redirect()->route('adminPromo')->with('success', 'Промокод удален!');
    }


    public function inserts(Request $request)
    {
        $query = Transaction::select(
            'transactions.id',
            'transactions.user_id',
            'transactions.amount',
            'transactions.created_at',
            'transactions.context',
            'users.avatar',
            'users.username',
            'currencies.symbol as currency_symbol'
        )
        ->join('users', 'users.id', '=', 'transactions.user_id')
        ->join('currencies', 'currencies.id', '=', 'transactions.currency_id')
        ->where('transactions.type', 'payment')
        ->where('transactions.status', 'success');

        $currentUser = Auth::user();
        $sort = $request->input('sort', 'transactions.id');
        $direction = $request->input('direction', 'desc');

        // Поиск по логину
        if ($request->has('search')) {
            $query->where('users.username', 'like', '%' . $request->input('search') . '%');
        }

        // Сортировка
        $query->orderBy($sort, $direction);

        //
        if ($currentUser->is_moder) {
            $payments = $query->latest('transactions.id')->take(30)->get();
        } else {
            $payments = $query->paginate(30)->appends($request->all());
        }

        // Decode JSON context
        foreach ($payments as $payment) {
            $context = json_decode($payment->context, true);
            $payment->payment_handler = $context['payment_handler'] ?? null;
        }

        return view('admin.inserts', compact('payments', 'sort', 'direction'));
    }



    public function withdraw(Request $request)
    {
        $exchangeService = new ExchangeService();
        // Получение параметров запроса
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at'); // По умолчанию сортируем по дате
        $direction = $request->input('direction', 'desc'); // По умолчанию по убыванию
        $amountFrom = $request->input('amount_from');
        $amountTo = $request->input('amount_to');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $paymentSystem = $request->input('payment_system');
        $perPage = 15;
        $activeTab = $request->input('tab', 'pending');

        // Базовый запрос
        $query = Withdrawal::query()
            ->with(['user', 'transaction.currency']);

        // Фильтрация по статусу
        switch ($activeTab) {
            case 'pending':
                $query->where('status', PaymentStatus::Pending);
                break;
            case 'completed':
                $query->where('status', PaymentStatus::Completed);
                break;
            case 'refunded':
                $query->where('status', PaymentStatus::Refunded);
                break;
        }

        // Поиск по логину
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            });
        }

        // Фильтрация по сумме
        if ($amountFrom) {
            $query->where('amount', '>=', $amountFrom);
        }
        if ($amountTo) {
            $query->where('amount', '<=', $amountTo);
        }

        // Фильтрация по дате
        if ($dateFrom) {
            $query->whereDate('updated_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('updated_at', '<=', $dateTo);
        }

        // Фильтрация по платежной системе
        if ($paymentSystem) {
            $query->whereHas('transaction', function ($q) use ($paymentSystem) {
                $q->whereJsonContains('context->payment_system', $paymentSystem);
            });
        }

        // Применяем сортировку в зависимости от выбранного столбца
        switch ($sort) {
            case 'amount':
                $query->orderBy('amount', $direction);
                break;
            case 'created_at':
                $query->orderBy('updated_at', $direction);
                break;
            case 'status':
                $query->orderBy('status', $direction);
                break;
            case 'id':
                $query->orderBy('id', $direction);
                break;
            case 'duplicates':
            $query->addSelect(['duplicate_count' => function($query) {
                $query->selectRaw('COUNT(DISTINCT w2.user_id)')
                      ->from('withdrawals as w2')
                      ->whereColumn('w2.details', 'withdrawals.details')
                      ->whereRaw('w2.user_id != withdrawals.user_id');
            }])
            ->orderBy('duplicate_count', $direction);
            break;

            default:
                $query->orderBy('updated_at', 'desc');
        }

        // Получаем список платежных систем для фильтра
        $paymentSystems = PaymentHandler::all();

        // Пагинация с сохранением параметров сортировки
        $withdrawals = $query->paginate($perPage)->appends($request->query());

        // Добавляем подсчет общего количества ожидающих выплат
        $totalPendingCount = Withdrawal::where('status', PaymentStatus::Pending)->count();

        $totalPendingSum = 0;
        $pendingWithdrawals = Withdrawal::where('status', PaymentStatus::Pending)
            ->with(['transaction.currency'])
            ->get();

        foreach ($pendingWithdrawals as $withdraw) {
          $transactionCurrency = $withdraw->transaction->currency;
              $totalPendingSum += $exchangeService->convert(
                  $withdraw->amount,
                  $transactionCurrency->symbol,
                  'AZN'
              );
          }

        // Трансформация данных
        $withdrawals->getCollection()->transform(function ($withdrawal) {
            $context = json_decode($withdrawal->transaction->context, true);
            $paymentSystemId = $context['payment_system'] ?? null;
            $paymentSystemName = null;
            $paymentHandler = null; // Инициализируем переменную заранее
            $color = "#000";
            $details = $withdrawal->details;

            if ($paymentSystemId) {
                $paymentHandler = \App\Models\PaymentHandler::where('id', $paymentSystemId)->first();
                $paymentSystemName = $paymentHandler ? $paymentHandler->name : null;
            }

            // Получаем количество ожидающих выплат для пользователя
            $pendingCount = Withdrawal::where('user_id', $withdrawal->user_id)
                ->where('status', PaymentStatus::Pending)
                ->count();

            switch ($paymentSystemName) {
                case 'M10':
                    $color = "#1c6c00";
                    if (str_starts_with($details, '0')) {
                        $details = substr($details, 0, 3) . ' ' . substr($details, 3, 3) . ' ' . substr($details, 6, 2) . ' ' . substr($details, 8, 2);
                    } elseif (str_starts_with($details, '+')) {
                        $details = preg_replace('/^(\+\d{1,4})(\d{2})(\d{3})(\d{2})(\d{2})$/', '$1 $2 $3 $4 $5', $details);
                    } elseif (str_starts_with($details, '9')) {
                        $details = preg_replace('/^(\d{1,3})(\d{2})(\d{3})(\d{2})(\d{2})$/', '$1 $2 $3 $4 $5', $details);
                    } else {
                        $details = preg_replace('/^(\d{1,2})(\d{3})(\d{2})(\d{2})$/', '$1 $2 $3 $4 $5', $details);
                    }
                    break;

                case 'Card AZ':
                    $details = implode(' ', str_split($withdrawal->details, 4));
                    $color = "#cd00aa";
                    break;
            }

            // Добавляем информацию о верификации
              $isVerified = $withdrawal->user->profile_verified;

              $verificationDocs = null;
              if ($withdrawal->user->profile_verified) {
                  $verification = UserVerification::where('user_id', $withdrawal->user_id)
                      ->where('status', 'approved')
                      ->latest()
                      ->first();

                  if ($verification) {
                      $verificationDocs = [
                          'selfie' => $verification->selfie,
                          'document_front' => $verification->document_front,
                          'document_back' => $verification->document_back
                      ];
                  }
              }

              //  реквизитами
              $duplicateUsers = Withdrawal::where('details', $withdrawal->details)
                  ->where('user_id', '!=', $withdrawal->user_id)
                  ->with('user')
                  ->get()
                  ->unique('user_id')
                  ->values()
                  ->map(function($w) {
                      return [
                          'username' => $w->user->username,
                          'user_id' => $w->user->id,
                          'avatar' => $w->user->avatar ?? null
                      ];
                  })
                  ->toArray();

            // Получаем информацию о payment handler и режиме автовыплат
            // Используем тот же $paymentSystemId который уже получен выше
            $withdrawalMode = 'manual';
            $autoEnabled = false;
            
            if ($paymentSystemId && $paymentHandler) {
                $withdrawalMode = $paymentHandler->withdrawal_mode;
                $autoEnabled = $paymentHandler->auto_withdrawal_enabled;
            }

            return [
                'id' => $withdrawal->id,
                'user_id' => $withdrawal->user->id,
                'username' => $withdrawal->user->username,
                'avatar' => $withdrawal->user->avatar,
                'currency' => $withdrawal->transaction->currency->symbol ?? null,
                'system' => $paymentSystemName,
                'details' => $details,
                'color' => $color,
                'value' => $withdrawal->amount,
                'status' => $withdrawal->status,
                'created_at' => $withdrawal->created_at,
                'pending_count' => $pendingCount,
                'verification_docs' => $verificationDocs,
                'is_verified' => $withdrawal->user->profile_verified,
                'duplicate_users' => $duplicateUsers,
                'has_duplicates' => count($duplicateUsers) > 0,
                'withdrawal_mode' => $withdrawalMode,
                'auto_enabled' => $autoEnabled,
                'auto_processed' => $withdrawal->auto_processed ?? false,
                'betatransfer_status' => $withdrawal->betatransfer_status ?? null
            ];
        });

        return view('admin.withdraw', compact('withdrawals', 'sort', 'direction', 'activeTab', 'paymentSystems', 'totalPendingCount', 'totalPendingSum'));
    }

    public function withdrawSend($id)
    {
        $currentUser = Auth::user();
        $withdraw = Withdrawal::with(['transaction.currency', 'user'])->findOrFail($id);
        $transaction = $withdraw->transaction;
        $user = $withdraw->user;

        // Проверяем что выплата в статусе pending
        if ($withdraw->status != PaymentStatus::Pending) {
            return redirect()->back()->with('error', 'Выплата уже обработана');
        }

        try {
            // Получаем payment_handler_id из контекста транзакции
            $paymentHandlerId = $transaction->context['payment_handler_id'] ?? null;
            
            if (!$paymentHandlerId) {
                // Нет handler - делаем ручную обработку как раньше
                return $this->processManualWithdrawal($withdraw, $transaction, $currentUser);
            }

            // Загружаем handler с системой
            $paymentHandler = PaymentHandler::with('system')->find($paymentHandlerId);
            
            if (!$paymentHandler) {
                return $this->processManualWithdrawal($withdraw, $transaction, $currentUser);
            }

            // Проверяем режим выплаты
            if ($paymentHandler->withdrawal_mode === 'manual' || !$paymentHandler->auto_withdrawal_enabled) {
                // Ручной режим - просто меняем статус
                return $this->processManualWithdrawal($withdraw, $transaction, $currentUser);
            }

            // Проверяем что это BetaTransfer (ID = 8)
            if ($paymentHandler->payment_system_id != 8) {
                // Другая система - пока только ручная обработка
                return $this->processManualWithdrawal($withdraw, $transaction, $currentUser);
            }

            // BetaTransfer автовыплата - проверяем режим
            if ($paymentHandler->withdrawal_mode === 'semi_auto') {
                // Полуавтомат - требует одобрения админа перед отправкой
                return $this->processSemiAutoWithdrawal($withdraw, $transaction, $currentUser, $paymentHandler);
            }

            if ($paymentHandler->withdrawal_mode === 'instant') {
                // Мгновенная выплата - сразу отправляем в BetaTransfer
                return $this->processInstantWithdrawal($withdraw, $transaction, $currentUser, $paymentHandler);
            }

            // По умолчанию ручная обработка
            return $this->processManualWithdrawal($withdraw, $transaction, $currentUser);

        } catch (\Exception $e) {
            Log::error('Withdrawal processing error', [
                'withdrawal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Ошибка обработки: ' . $e->getMessage());
        }
    }

    /**
     * Ручная обработка выплаты (как раньше)
     */
    private function processManualWithdrawal(Withdrawal $withdraw, Transaction $transaction, User $currentUser)
    {
        $withdraw->accepted_by = $currentUser->id;
        $withdraw->status = PaymentStatus::Completed;
        $withdraw->auto_processed = false;
        $withdraw->save();

        $transaction->status = 'success';
        $transaction->save();

        return redirect()->back()->with('success', 'Выплата подтверждена вручную');
    }

    /**
     * Полуавтоматическая обработка - отправка в BetaTransfer после одобрения
     */
    private function processSemiAutoWithdrawal(Withdrawal $withdraw, Transaction $transaction, User $currentUser, PaymentHandler $paymentHandler)
    {
        try {
            // Инициализируем BetaTransfer сервис
            $betaTransferService = new BetaTransferService();
            
            // Получаем валюту из транзакции
            $currency = $transaction->currency->symbol ?? 'AZN';
            
            // Отправляем запрос на выплату в BetaTransfer
            $response = $betaTransferService->createWithdrawal(
                $paymentHandler->id,
                $withdraw->amount,
                $withdraw->details, // реквизиты
                $currency,
                "Withdrawal #{$withdraw->id}"
            );

            if (!$response['error']) {
                // Успешно отправлено в BetaTransfer
                $withdraw->accepted_by = $currentUser->id;
                $withdraw->admin_approved_by = $currentUser->id;
                $withdraw->admin_approved_at = now();
                $withdraw->status = PaymentStatus::Completed;
                $withdraw->auto_processed = true;
                $withdraw->betatransfer_transaction_id = $response['data']['transaction_id'] ?? null;
                $withdraw->betatransfer_status = 'sent';
                $withdraw->save();

                $transaction->status = 'success';
                $transaction->save();

                Log::info('Semi-auto withdrawal processed', [
                    'withdrawal_id' => $withdraw->id,
                    'betatransfer_id' => $response['data']['transaction_id'] ?? null,
                    'admin_id' => $currentUser->id
                ]);

                return redirect()->back()->with('success', 'Выплата отправлена через BetaTransfer (полуавтомат)');
            } else {
                // Ошибка от BetaTransfer
                Log::error('BetaTransfer withdrawal failed', [
                    'withdrawal_id' => $withdraw->id,
                    'error' => $response['message'] ?? 'Unknown error'
                ]);
                
                return redirect()->back()->with('error', 'Ошибка BetaTransfer: ' . ($response['message'] ?? 'Неизвестная ошибка'));
            }

        } catch (\Exception $e) {
            Log::error('Semi-auto withdrawal exception', [
                'withdrawal_id' => $withdraw->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Мгновенная обработка - сразу отправка в BetaTransfer
     */
    private function processInstantWithdrawal(Withdrawal $withdraw, Transaction $transaction, User $currentUser, PaymentHandler $paymentHandler)
    {
        try {
            // Дополнительные проверки безопасности для мгновенных выплат
            $user = $withdraw->user;
            
            // Проверка верификации (если требуется)
            if (!$user->is_verified) {
                return redirect()->back()->with('error', 'Пользователь не верифицирован. Требуется ручное одобрение.');
            }
            
            // Проверка дневного лимита
            if (!$paymentHandler->checkDailyLimit($withdraw->amount)) {
                return redirect()->back()->with('error', 'Превышен дневной лимит автовыплат. Обработайте вручную.');
            }

            // Инициализируем BetaTransfer сервис
            $betaTransferService = new BetaTransferService();
            
            // Получаем валюту из транзакции
            $currency = $transaction->currency->symbol ?? 'AZN';
            
            // Отправляем запрос на выплату в BetaTransfer
            $response = $betaTransferService->createWithdrawal(
                $paymentHandler->id,
                $withdraw->amount,
                $withdraw->details,
                $currency,
                "Withdrawal #{$withdraw->id}"
            );

            if (!$response['error']) {
                // Успешно отправлено
                $withdraw->accepted_by = $currentUser->id;
                $withdraw->status = PaymentStatus::Completed;
                $withdraw->auto_processed = true;
                $withdraw->betatransfer_transaction_id = $response['data']['transaction_id'] ?? null;
                $withdraw->betatransfer_status = 'sent';
                $withdraw->save();

                $transaction->status = 'success';
                $transaction->save();

                Log::info('Instant withdrawal processed', [
                    'withdrawal_id' => $withdraw->id,
                    'betatransfer_id' => $response['data']['transaction_id'] ?? null,
                    'admin_id' => $currentUser->id
                ]);

                return redirect()->back()->with('success', 'Выплата отправлена мгновенно через BetaTransfer');
            } else {
                // Ошибка от BetaTransfer - откатываемся к ручному режиму
                Log::error('BetaTransfer instant withdrawal failed', [
                    'withdrawal_id' => $withdraw->id,
                    'error' => $response['message'] ?? 'Unknown error'
                ]);
                
                return redirect()->back()->with('error', 'Ошибка BetaTransfer: ' . ($response['message'] ?? 'Неизвестная ошибка'));
            }

        } catch (\Exception $e) {
            Log::error('Instant withdrawal exception', [
                'withdrawal_id' => $withdraw->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    public function withdrawReturn($id)
    {
        $withdraw = Withdrawal::where('id', $id)->first();
        $user = User::where('id', $withdraw->user_id)->first();
        $currentUser = Auth::user();

        $perc = 0;
        $com = 0;
        $min = 1;

        // Получаем валюту транзакции
        $transaction = Transaction::where('id', $withdraw->transaction_id)->first();
        $transactionCurrency = $transaction->currency;

        // Проверяем валюту пользователя
        $userCurrency = $user->currency;

        // Если валюты различаются, конвертируем сумму
        if ($transactionCurrency->id != $userCurrency->id) {
            $exchangeService = new ExchangeService();
            $valwithcom = $exchangeService->convert(
                $withdraw->amount + ($min / 100 * $perc) + $com,
                $transactionCurrency->symbol,
                $userCurrency->symbol
            );
        } else {
            $valwithcom = $withdraw->amount + ($min / 100 * $perc) + $com;
        }

        $transaction->status = 'cancelled';
        $transaction->save();
        $withdraw->status = 'refunded';
        $withdraw->accepted_by = $currentUser->id;
        $withdraw->save();

        $user->balance += $valwithcom;
        $user->save();

        $usern = $user->username;

        return redirect()->back()->with('success', 'Вы вернули ' . $valwithcom . ' ' . $userCurrency->symbol . ' на баланс ' . $usern);
    }

    public function wbanUser($id)
    {
        try {
            // Начинаем транзакцию БД
            DB::beginTransaction();

            // Получаем данные о выводе средств
            $withdraw = Withdrawal::where('id', $id)->first();
            if (!$withdraw) {
                throw new \Exception('Вывод средств не найден');
            }

            // Получаем данные пользователя
            $user = User::where('id', $withdraw->user_id)->first();
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }

            // Получаем все активные выводы пользователя
            $pendingWithdrawals = Withdrawal::where('user_id', $user->id)
                ->whereNotIn('status', ['refunded', 'cancelled', 'completed'])
                ->get();

            $currentUser = Auth::user();
            $totalRefunded = 0;

            // Обрабатываем каждый вывод средств
            foreach ($pendingWithdrawals as $withdrawal) {
                // Получаем транзакцию для вывода
                $transaction = Transaction::where('id', $withdrawal->transaction_id)
                ->where('status', 'pending')
                ->first();

                if ($transaction) {
                    // Получаем валюты
                    $transactionCurrency = $transaction->currency;
                    $userCurrency = $user->currency;

                    // Рассчитываем сумму возврата с комиссией
                    $perc = 0;
                    $com = 0;
                    $min = 1;

                    // Если валюты разные - конвертируем
                    if ($transactionCurrency->id != $userCurrency->id) {
                        $exchangeService = new ExchangeService();
                        $valwithcom = $exchangeService->convert(
                            $withdrawal->amount + ($min / 100 * $perc) + $com,
                            $transactionCurrency->symbol,
                            $userCurrency->symbol
                        );
                    } else {
                        $valwithcom = $withdrawal->amount + ($min / 100 * $perc) + $com;
                    }

                    // Обновляем статус транзакции
                    $transaction->status = 'cancelled';
                    $transaction->save();

                    // Обновляем статус вывода
                    $withdrawal->status = 'refunded';
                    $withdrawal->accepted_by = $currentUser->id;
                    $withdrawal->save();

                    // Добавляем сумму на баланс пользователя
                    // $user->balance += $valwithcom;
                    $totalRefunded += $valwithcom;
                }
            }

            // Баним пользователя
            $user->last_login_at = null;
            $user->ban = 1;
            $user->save();

            // Подтверждаем транзакцию БД
            DB::commit();

            return redirect()->back()->with('success',
                'Пользователь ' . $user->username . ' забанен. Возвращено ' .
                $totalRefunded . ' ' . $userCurrency->symbol . ' на баланс'
            );

        } catch (\Exception $e) {
            // В случае ошибки откатываем все изменения
            DB::rollback();
            return redirect()->back()->with('error', 'Произошла ошибка: ' . $e->getMessage());
        }
    }

    public function verifyUser($id)
    {
        try {
            // Начинаем транзакцию БД
            DB::beginTransaction();

            // Получаем данные о выводе средств
            $withdraw = Withdrawal::where('id', $id)->first();
            if (!$withdraw) {
                throw new \Exception('Вывод средств не найден');
            }

            // Получаем данные пользователя
            $user = User::where('id', $withdraw->user_id)->first();
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }

            $user->need_verify = 1;
            $user->save();

            DB::commit();

            return redirect()->back()->with('success',
                'Пользователь ' . $user->username . ' отправлен на верификацию.'
            );

        } catch (\Exception $e) {
            // В случае ошибки откатываем все изменения
            DB::rollback();
            return redirect()->back()->with('error', 'Произошла ошибка: ' . $e->getMessage());
        }
    }

    // для чата
    public function showForbiddenWords()
    {
        $forbiddenWords = ForbiddenWord::all();
        return view('admin.words', compact('forbiddenWords'));
    }

    public function addForbiddenWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string',
            'type' => 'required|string|in:word,link',
        ]);

        $forbiddenWord = new ForbiddenWord();
        $forbiddenWord->word = $request->input('word');
        $forbiddenWord->type = $request->input('type');
        $forbiddenWord->save();
        if ($forbiddenWord) {
            return redirect()->route('adminWords')->with('success', 'Вы успешно добавили слово');
        } else {
            return redirect()->route('adminWords')->with('error', 'Error');
        }
    }

    public function deleteForbiddenWord(Request $request)
    {
        $request->validate([
            'word_id' => 'required|integer',
        ]);

        $forbiddenWord = ForbiddenWord::find($request->input('word_id'));
        if ($forbiddenWord) {
            $forbiddenWord->delete();
            return redirect()->route('adminWords')->with('success', 'Вы успешно удалили слово');
        } else {
            return redirect()->route('adminWords')->with('error', 'Error');
        }
    }


    // слоты
    public function slots(Request $request)
    {
        // Создаем базовый запрос
        $query = SlotegratorGame::query();

        // Фильтруем по провайдеру, если он указан
        if ($request->has('provider') && $request->input('provider') !== null) {
            $query->where('provider', $request->input('provider'));
        }

        // Фильтруем по поисковому запросу, если он указан
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Пагинация результатов и добавление параметров запроса в пагинацию
        $games = $query->paginate(10)->appends($request->all());

        // Получаем список уникальных провайдеров
        $providers = SlotegratorGame::select('provider')->distinct()->get();

        // Возвращаем представление с результатами и провайдерами
        return view('admin.slots', compact('games', 'providers'));
    }

    public function editSlot($id)
    {
        $game = SlotegratorGame::findOrFail($id);
        return view('admin.editSlot', compact('game'));
    }

    public function updateSlot(Request $request, $id)
     {
         // Валидация входящих данных
         $request->validate([
             'name' => 'required|string|max:255',
             'provider' => 'required|string|max:255',
             'is_active' => 'required|boolean',
             'is_live' => 'required|boolean',
             'is_higher' => 'required|boolean',
             'is_new' => 'required|boolean',
             'is_popular' => 'required|boolean',
             'is_table' => 'required|boolean',
             'is_roulette' => 'required|boolean',
             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         ]);

         // Находим игру по ID
         $game = SlotegratorGame::findOrFail($id);
         $game->is_active = $request->input('is_active');
         $game->is_live = $request->input('is_live');
         $game->is_higher = $request->input('is_higher');
         $game->is_new = $request->input('is_new');
         $game->is_popular = $request->input('is_popular');
         $game->is_table = $request->input('is_table');
         $game->is_roulette = $request->input('is_roulette');

         // Обновляем данные игры, кроме изображения
         $game->update($request->except('image'));

        // Проверяем, загружен ли файл изображения
        if ($request->hasFile('image')) {
            // Удаляем старое изображение, если оно существует и это локальный файл
            if ($game->image && !str_starts_with($game->image, 'http')) {
                $oldImagePath = public_path(ltrim($game->image, '/'));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Сохраняем новое изображение
            $file = $request->file('image');
            $destinationPath = public_path('/assets/images/slots');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Формируем имя файла на основе имени слота
            $slug = Str::slug($game->name);
            $fileName = $slug . '.' . $file->getClientOriginalExtension();

            $file->move($destinationPath, $fileName);

            // Обновляем путь к изображению в базе данных
            $game->image = '/assets/images/slots/' . $fileName;
        }         $game->save();

         // Перенаправляем обратно с сообщением об успехе
         return redirect()->route('slotegrator_games.slots')->with('success', 'Игра успешно обновлена');
     }


     // admin_tasks
     public function tasks()
      {
          $tasks = AdminTask::with(['creator', 'assignee'])->orderBy('created_at', 'desc')->get();
          $admins = User::where('is_admin', 1)->orWhere('is_moder', 1)->get();
          return view('admin.tasks', compact('tasks', 'admins'));
      }

      public function createTask(Request $request)
      {
          $request->validate([
              'assignee_id' => 'required|exists:users,id',
              'description' => 'required|string',
              'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
          ]);

          $taskData = [
              'creator_id' => auth()->id(),
              'assignee_id' => $request->assignee_id,
              'description' => $request->description,
          ];

          if ($request->hasFile('screenshot')) {
              $file = $request->file('screenshot');
              $filename = time() . '_' . $file->getClientOriginalName();
              $file->storeAs('public/task_screenshots', $filename);
              $taskData['screenshot'] = $filename;
          }

          AdminTask::create($taskData);

          // Получаем информацию о создателе и исполнителе задания
          $creator = User::find(auth()->id());
          $assignee = User::find($request->assignee_id);

          return redirect()->route('adminTasks')->with('success', 'Задание создано успешно');
      }

      public function completeTask($id)
      {
          $task = AdminTask::findOrFail($id);
          $task->update(['completed' => true]);

          return redirect()->route('adminTasks')->with('success', 'Задание отмечено как выполненное');
      }

      public function showBannedUsers()
      {
          $currentDate = now();
          $bannedUsers = User::where('banned_until', '>', $currentDate)->get();
          return view('admin.bannedUsers', compact('bannedUsers'));
      }

      public function unbanUser($id)
      {
          $user = User::findOrFail($id);
          $user->banned_until = null;
          $user->save();

          return redirect()->route('admin.bannedUsers')->with('success', 'Пользователь успешно разблокирован');
      }

      public function addSmile(Request $request)
      {

          $request->validate([
              'name' => 'required|string',
              'smile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
          ]);

          $directory = public_path('/assets/images/emoj/');

          if (!File::exists($directory)) {
              File::makeDirectory($directory, 0755, true);
          }


          $name = str_replace(':', '', $request->name);

          if ($name == '') {
              return redirect()->route('adminSettings')->with('error', 'Введите название');
          }

          if ($request->hasFile('smile')) {
              $file = $request->file('smile');
              $filename = $name.'.'. time() . '.' . $file->getClientOriginalExtension();
              $file->move($directory, $filename);
          }

          return redirect()->route('adminSettings')->with('success', 'Новый смайл добавлен');

      }

      public function deleteSmile(Request $request)
      {
          $directory = public_path('/assets/images/emoj/');
          $img = $request->get('img');

          File::delete($directory.$img);

          return redirect()->route('adminSettings')->with('success', 'Вы успешно удалили смайл '.$img);

      }

      public function userIdAuth($id)
      {

          Auth::logout();
          Auth::login(User::find($id));

          return redirect()->route('account');

      }


      public function notifyPage()
      {
          $userCount = User::count();
          return view('admin.notify', compact('userCount'));
      }

      public function sendMassNotification(Request $request)
      {
          $request->validate([
              'message' => 'required|string',
              'user_id' => 'nullable|integer'
          ]);

          try {
              // Проверка на наличие конкретного пользователя
              $userId = $request->input('user_id');

              dispatch(new \App\Jobs\SendMassNotificationJob($request->message, $userId));

              return redirect()
                  ->back()
                  ->with('success', 'Уведомление успешно добавлено в очередь для отправки. Вы получите обновления после завершения.');
          } catch (\Exception $e) {
              \Log::error('Ошибка при отправке уведомлений: ' . $e->getMessage());
              return redirect()
                  ->back()
                  ->with('error', 'Произошла ошибка при отправке уведомлений');
          }
      }

      public function getUserInfo(Request $request)
      {

          try {
              $userId = $request->input('user_id');

              // Проверяем, является ли ID числом
              if (!is_numeric($userId)) {
                  return response()->json([
                      'success' => false,
                      'message' => 'ID пользователя должен быть числом'
                  ]);
              }

              // Поиск пользователя по ID
              $user = User::find($userId);

              if ($user) {
                  return response()->json([
                      'success' => true,
                      'user' => [
                          'username' => $user->username,
                          'email' => $user->email
                      ]
                  ]);
              }

              return response()->json([
                  'success' => false,
                  'message' => 'Пользователь не найден'
              ]);

          } catch (\Exception $e) {
              \Log::error('Error in getUserInfo: ' . $e->getMessage());
              return response()->json([
                  'success' => false,
                  'message' => 'Произошла ошибка при поиске пользователя'
              ]);
          }
      }


      //
      public function expenses()
      {
          // Получаем даты
          $today = now()->startOfDay();
          $startOfMonth = now()->startOfMonth();

          $expenses = Expense::with('user')
              ->orderBy('created_at', 'desc')
              ->get();

          // Подсчитываем статистику с учетом статусов и дат
          $totalByStatus = [
              // Общие суммы по статусам
              'pending' => Expense::where('status', 'pending')->sum('amount'),
              'approved' => Expense::where('status', 'approved')->sum('amount'),
              'total' => Expense::where('status', 'approved')->sum('amount'),

              // Расходы за сегодня (только одобренные)
              'today' => Expense::where('status', 'approved')
                  ->whereDate('created_at', $today)
                  ->sum('amount'),

              // Расходы за текущий месяц (только одобренные)
              'month' => Expense::where('status', 'approved')
                  ->whereBetween('created_at', [$startOfMonth, now()])
                  ->sum('amount'),

              // Дополнительная статистика по месяцам
              'months' => Expense::where('status', 'approved')
                  ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
                  ->groupBy('month')
                  ->orderBy('month', 'desc')
                  ->get()
                  ->pluck('total', 'month')
                  ->toArray(),

              // Статистика по категориям
              'by_category' => Expense::where('status', 'approved')
                  ->selectRaw('category, SUM(amount) as total')
                  ->groupBy('category')
                  ->get()
                  ->pluck('total', 'category')
                  ->toArray()
          ];

          $categories = [
              'Реклама',
              'Разработка',
              'Сервер',
              'Зарплаты',
              'Другие'
          ];

          $currencies = Currency::all();

          // Добавляем дополнительную информацию для отображения трендов
          $previousMonth = now()->subMonth()->startOfMonth();
          $totalByStatus['month_trend'] = [
              'current' => $totalByStatus['month'],
              'previous' => Expense::where('status', 'approved')
                  ->whereBetween('created_at', [$previousMonth, $previousMonth->endOfMonth()])
                  ->sum('amount'),
          ];

          $yesterday = now()->subDay()->startOfDay();
          $totalByStatus['day_trend'] = [
              'current' => $totalByStatus['today'],
              'previous' => Expense::where('status', 'approved')
                  ->whereDate('created_at', $yesterday)
                  ->sum('amount'),
          ];

          return view('admin.expenses', compact('expenses', 'totalByStatus', 'categories', 'currencies'));
      }

      public function getExpenseStats()
      {
          // Этот метод можно использовать для AJAX запросов статистики
          $stats = [
              'daily' => $this->getDailyStats(),
              'monthly' => $this->getMonthlyStats(),
              'category' => $this->getCategoryStats()
          ];

          return response()->json($stats);
      }

      private function getDailyStats()
      {
          $startDate = now()->subDays(30);

          return Expense::where('status', 'approved')
              ->where('created_at', '>=', $startDate)
              ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
              ->groupBy('date')
              ->get();
      }

      private function getMonthlyStats()
      {
          $startDate = now()->subMonths(12);

          return Expense::where('status', 'approved')
              ->where('created_at', '>=', $startDate)
              ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
              ->groupBy('month')
              ->get();
      }

      private function getCategoryStats()
      {
          return Expense::where('status', 'approved')
              ->selectRaw('category, SUM(amount) as total')
              ->groupBy('category')
              ->get();
      }
      public function storeExpense(Request $request)
      {
          $request->validate([
              'category' => 'required|string',
              'amount' => 'required|numeric|min:0',
              'currency' => 'required|string',
              'description' => 'required|string',
              'expense_date' => 'required|date',
              'receipt' => 'nullable|file|mimes:jpeg,png,pdf|max:2048'
          ]);

          $expense = new Expense($request->all());
          $expense->user_id = auth()->id();

          if ($request->hasFile('receipt')) {
              $file = $request->file('receipt');
              $filename = time() . '_' . $file->getClientOriginalName();
              $file->storeAs('public/receipts', $filename);
              $expense->receipt_file = $filename;
          }

          $expense->save();

          return redirect()->route('adminExpenses')->with('success', 'Расход успешно добавлен');
      }

      public function updateExpenseStatus(Request $request, $id)
      {
          $request->validate([
              'status' => 'required|in:approved,rejected',
              'rejection_reason' => 'required_if:status,rejected'
          ]);

          $expense = Expense::findOrFail($id);
          $expense->status = $request->status;
          $expense->rejection_reason = $request->rejection_reason;
          $expense->save();

          return redirect()->route('adminExpenses')->with('success', 'Статус расхода обновлен');
      }

      public function verifications(Request $request)
      {
          // Получение параметров запроса (оставляем как есть)
          $search = $request->input('search');
          $sort = $request->input('sort', 'created_at');
          $direction = $request->input('direction', 'desc');
          $dateFrom = $request->input('date_from');
          $dateTo = $request->input('date_to');
          $status = $request->input('status');
          $perPage = 15;

          // Изменяем базовый запрос, добавляя нужные отношения
          $query = UserVerification::query()
              ->with(['user.depositBonuses', 'user.currency']);

          // Поиск и фильтры оставляем как есть
          if ($search) {
              $query->whereHas('user', function ($q) use ($search) {
                  $q->where('username', 'like', "%{$search}%");
              });
          }

          if ($dateFrom) {
              $query->whereDate('created_at', '>=', $dateFrom);
          }
          if ($dateTo) {
              $query->whereDate('created_at', '<=', $dateTo);
          }

          if ($status) {
              $query->where('status', $status);
          }

          // Сортировка остается как есть
          switch ($sort) {
              case 'username':
                  $query->join('users', 'user_verifications.user_id', '=', 'users.id')
                      ->orderBy('users.username', $direction);
                  break;
              case 'status':
                  $query->orderBy('status', $direction);
                  break;
              case 'created_at':
                  $query->orderBy('created_at', $direction);
                  break;
              default:
                  $query->orderBy('created_at', 'desc');
          }

          $verifications = $query->paginate($perPage)->appends($request->query());

          // Расширяем трансформацию данных, добавляя все необходимые поля
          $verifications->getCollection()->transform(function ($verification) {
              $bonusInfo = $this->getUserBonusInfo($verification->user);

              return [
                  'id' => $verification->id,
                  'user_id' => $verification->user->id,
                  'username' => $verification->user->username,
                  'avatar' => $verification->user->avatar,
                  'document_type' => $verification->document_type,
                  'status' => $verification->status,
                  'created_at' => $verification->created_at,
                  'is_verified' => $verification->user->profile_verified,
                  'bonus_info' => $bonusInfo,
                  // Добавляем новые поля
                  'first_name' => $verification->first_name,
                  'last_name' => $verification->last_name,
                  'birth_date' => $verification->birth_date ? $verification->birth_date->format('d.m.Y') : null,
                  'document_front' => $verification->document_front,
                  'document_back' => $verification->document_back,
                  'selfie' => $verification->selfie,
                  'reject_reason' => $verification->reject_reason
              ];
          });

          return view('admin.verifications', compact('verifications', 'sort', 'direction', 'status'));
      }

      // Модифицируем метод обновления статуса для поддержки AJAX
      public function updateVerificationStatus(Request $request, $id)
      {
          $request->validate([
              'status' => 'required|in:approved,rejected',
              'reject_reason' => 'required_if:status,rejected'
          ]);

          $verification = UserVerification::findOrFail($id);
          $user = $verification->user;

          try {
              DB::transaction(function () use ($verification, $user, $request) {
                  $verification->status = $request->status;
                  $verification->reject_reason = $request->reject_reason;
                  $verification->verified_at = $request->status === 'approved' ? now() : null;
                  $verification->save();

                  $user->profile_verified = $request->status === 'approved';
                  $user->save();

                  if ($request->status === 'approved') {
                      $user->notify(Notify::send('verification', [
                          'message' => __('Ваша верификация была успешно подтверждена')
                      ]));
                  } else {
                      $user->notify(Notify::send('verification', [
                          'message' => __('Ваша верификация была отклонена. Причина: :reason', [
                              'reason' => $request->reject_reason
                          ])
                      ]));
                  }
              });

              if ($request->ajax()) {
                  return response()->json([
                      'success' => true,
                      'message' => __('Статус верификации успешно обновлен')
                  ]);
              }

              return redirect()
                  ->route('admin.verifications')
                  ->with('success', __('Статус верификации успешно обновлен'));
          } catch (\Exception $e) {
              if ($request->ajax()) {
                  return response()->json([
                      'success' => false,
                      'message' => __('Произошла ошибка при обновлении статуса')
                  ], 422);
              }

              return back()->with('error', __('Произошла ошибка при обновлении статуса'));
          }
      }


      /**
       * Показать страницу управления провайдерами
       */
      public function providers(Request $request)
      {
          // Получаем уникальные типы провайдеров
          $providerTypes = SlotegratorGame::select('provider_type')
              ->distinct()
              ->whereNotNull('provider_type')
              ->orderBy('provider_type')
              ->get();

          // Получаем провайдеров с группировкой по типам
          $providersData = [];

          foreach ($providerTypes as $type) {
              $providers = SlotegratorGame::select('provider')
                  ->where('provider_type', $type->provider_type)
                  ->distinct()
                  ->get();

              $providersWithStats = [];
              foreach ($providers as $provider) {
                  $stats = SlotegratorGame::where('provider', $provider->provider)
                      ->selectRaw('
                          COUNT(*) as total_games,
                          SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_games,
                          SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_games,
                          SUM(CASE WHEN is_live = 1 THEN 1 ELSE 0 END) as live_games,
                          SUM(CASE WHEN is_new = 1 THEN 1 ELSE 0 END) as new_games,
                          SUM(CASE WHEN is_popular = 1 THEN 1 ELSE 0 END) as popular_games
                      ')
                      ->first();

                  $providersWithStats[] = [
                      'name' => $provider->provider,
                      'stats' => $stats
                  ];
              }

              $typeStats = SlotegratorGame::where('provider_type', $type->provider_type)
                  ->selectRaw('
                      COUNT(*) as total_games,
                      SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_games,
                      SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_games
                  ')
                  ->first();

              $providersData[] = [
                  'type' => $type->provider_type,
                  'providers' => $providersWithStats,
                  'type_stats' => $typeStats
              ];
          }

          return view('admin.select-provider', compact('providersData'));
      }

      /**
       * Управление активностью провайдера по типу
       */
      public function toggleProviderType(Request $request)
      {
          $request->validate([
              'provider_type' => 'required|string',
              'is_active' => 'required|boolean'
          ]);

          try {
              DB::beginTransaction();

              $affected = SlotegratorGame::where('provider_type', $request->provider_type)
                  ->update(['is_active' => $request->is_active]);

              DB::commit();

              // Очищаем кеш
              $this->clearGameCache();

              $status = $request->is_active ? 'активированы' : 'деактивированы';

              return response()->json([
                  'success' => true,
                  'message' => "Все игры провайдера типа '{$request->provider_type}' {$status}. Обновлено игр: {$affected}",
                  'affected_games' => $affected
              ]);

          } catch (\Exception $e) {
              DB::rollback();
              Log::error('Error toggling provider type: ' . $e->getMessage());

              return response()->json([
                  'success' => false,
                  'message' => 'Произошла ошибка при обновлении статуса провайдера'
              ], 500);
          }
      }

      /**
       * Управление активностью конкретного провайдера
       */
      public function toggleProvider(Request $request)
      {
          $request->validate([
              'provider' => 'required|string',
              'is_active' => 'required|boolean'
          ]);

          try {
              DB::beginTransaction();

              $affected = SlotegratorGame::where('provider', $request->provider)
                  ->update(['is_active' => $request->is_active]);

              DB::commit();

              // Очищаем кеш
              $this->clearGameCache();

              $status = $request->is_active ? 'активированы' : 'деактивированы';

              return response()->json([
                  'success' => true,
                  'message' => "Все игры провайдера '{$request->provider}' {$status}. Обновлено игр: {$affected}",
                  'affected_games' => $affected
              ]);

          } catch (\Exception $e) {
              DB::rollback();
              Log::error('Error toggling provider: ' . $e->getMessage());

              return response()->json([
                  'success' => false,
                  'message' => 'Произошла ошибка при обновлении статуса провайдера'
              ], 500);
          }
      }

      /**
       * Получить статистику провайдера
       */
      public function getProviderStats(Request $request)
      {
          $request->validate([
              'provider' => 'sometimes|string',
              'provider_type' => 'sometimes|string'
          ]);

          try {
              $query = SlotegratorGame::query();

              if ($request->has('provider')) {
                  $query->where('provider', $request->provider);
              }

              if ($request->has('provider_type')) {
                  $query->where('provider_type', $request->provider_type);
              }

              $stats = $query->selectRaw('
                  COUNT(*) as total_games,
                  SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_games,
                  SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_games,
                  SUM(CASE WHEN is_live = 1 THEN 1 ELSE 0 END) as live_games,
                  SUM(CASE WHEN is_new = 1 THEN 1 ELSE 0 END) as new_games,
                  SUM(CASE WHEN is_popular = 1 THEN 1 ELSE 0 END) as popular_games,
                  SUM(CASE WHEN is_table = 1 THEN 1 ELSE 0 END) as table_games,
                  SUM(CASE WHEN is_roulette = 1 THEN 1 ELSE 0 END) as roulette_games
              ')->first();

              return response()->json([
                  'success' => true,
                  'stats' => $stats
              ]);

          } catch (\Exception $e) {
              Log::error('Error getting provider stats: ' . $e->getMessage());

              return response()->json([
                  'success' => false,
                  'message' => 'Произошла ошибка при получении статистики'
              ], 500);
          }
      }

      /**
       * Очистка кеша игр
       */
      private function clearGameCache()
      {
          try {
              // Очищаем кеш Laravel
              Cache::flush();

              // Выполняем artisan команды
              Artisan::call('cache:clear');
              Artisan::call('config:clear');
              Artisan::call('route:clear');
              Artisan::call('view:clear');

              Log::info('Game cache cleared successfully');

          } catch (\Exception $e) {
              Log::error('Error clearing cache: ' . $e->getMessage());
          }
      }

      /**
       * Страница управления курсами валют
       */
      public function rates()
      {
          $currencies = Currency::with('rate')->get();

          // Получаем историю изменений курсов за последние 30 дней
          $rateHistory = DB::table('rate_history')
              ->select('currency_id', 'price', 'created_at')
              ->where('created_at', '>=', now()->subDays(30))
              ->orderBy('created_at', 'desc')
              ->get()
              ->groupBy('currency_id');

          // Статистика по курсам
          $stats = [
              'total_currencies' => $currencies->count(),
              'last_update' => Rate::max('updated_at'),
              'avg_change_24h' => $this->calculateAverageChange($currencies)
          ];

          return view('admin.rates', compact('currencies', 'rateHistory', 'stats'));
      }

      /**
       * Обновление курсов валют вручную
       */
      public function updateRates(Request $request)
      {
          $request->validate([
              'rates' => 'required|array',
              'rates.*.currency_id' => 'required|exists:currencies,id',
              'rates.*.price' => 'required|numeric|min:0'
          ]);

          try {
              DB::beginTransaction();

              foreach ($request->rates as $rateData) {
                  $currency = Currency::find($rateData['currency_id']);

                  // Сохраняем старый курс в историю
                  if ($currency->rate) {
                      DB::table('rate_history')->insert([
                          'currency_id' => $currency->id,
                          'price' => $currency->rate->price,
                          'created_at' => now(),
                          'updated_at' => now()
                      ]);
                  }

                  // Обновляем курс
                  Rate::updateOrCreate(
                      ['currency_id' => $currency->id],
                      ['price' => $rateData['price']]
                  );
              }

              // Очищаем кеш курсов
              Cache::forget('currency_rates');
              Cache::forget('exchange_rates');

              DB::commit();

              return redirect()->route('adminRates')
                  ->with('success', 'Курсы валют успешно обновлены');

          } catch (\Exception $e) {
              DB::rollback();
              Log::error('Error updating rates: ' . $e->getMessage());

              return redirect()->back()
                  ->with('error', 'Произошла ошибка при обновлении курсов');
          }
      }

      /**
       * Автоматическое обновление курсов через API
       */
      public function autoUpdateRates(Request $request)
      {
          try {
              $exchangeService = app(ExchangeService::class);
              $currencies = Currency::whereNotIn('symbol', ['USD'])->get();

              DB::beginTransaction();

              $updatedCount = 0;

              foreach ($currencies as $currency) {
                  try {
                      // Получаем актуальный курс
                      $rate = $exchangeService->convert(1, $currency->symbol, 'USD');

                      // Сохраняем в историю
                      if ($currency->rate) {
                          DB::table('rate_history')->insert([
                              'currency_id' => $currency->id,
                              'price' => $currency->rate->price,
                              'created_at' => now(),
                              'updated_at' => now()
                          ]);
                      }

                      // Обновляем курс
                      Rate::updateOrCreate(
                          ['currency_id' => $currency->id],
                          ['price' => $rate]
                      );

                      $updatedCount++;

                  } catch (\Exception $e) {
                      Log::warning("Failed to update rate for {$currency->symbol}: " . $e->getMessage());
                      continue;
                  }
              }

              // Очищаем кеш
              Cache::forget('currency_rates');
              Cache::forget('exchange_rates');

              DB::commit();

              return response()->json([
                  'success' => true,
                  'message' => "Автоматически обновлено курсов: {$updatedCount}",
                  'updated_count' => $updatedCount
              ]);

          } catch (\Exception $e) {
              DB::rollback();
              Log::error('Error in auto update rates: ' . $e->getMessage());

              return response()->json([
                  'success' => false,
                  'message' => 'Произошла ошибка при автоматическом обновлении курсов'
              ], 500);
          }
      }

      /**
       * Расчет средного изменения курсов за 24 часа
       */
      private function calculateAverageChange($currencies)
      {
          $totalChange = 0;
          $count = 0;

          foreach ($currencies as $currency) {
              if ($currency->rate) {
                  $oldRate = DB::table('rate_history')
                      ->where('currency_id', $currency->id)
                      ->where('created_at', '>=', now()->subDay())
                      ->orderBy('created_at', 'desc')
                      ->first();

                  if ($oldRate) {
                    
                  }
              }
          }

          return $count > 0 ? $totalChange / $count : 0;
      }

      /**
       * Создание новой валюты
       */
      public function createCurrency(Request $request)
      {
          $request->validate([
              'name' => 'required|string|max:255',
              'symbol' => 'required|string|max:10|unique:currencies,symbol',
              'initial_rate' => 'required|numeric|min:0'
          ]);

          try {
              DB::beginTransaction();

              // Создаем валюту
              $currency = Currency::create([
                  'name' => $request->name,
                  'symbol' => strtoupper($request->symbol),
                  'active' => true
              ]);

              // Создаем начальный курс
              Rate::create([
                  'currency_id' => $currency->id,
                  'price' => $request->initial_rate
              ]);

              // Сохраняем в историю
              DB::table('rate_history')->insert([
                  'currency_id' => $currency->id,
                  'price' => $request->initial_rate
              ]);

              // Очищаем кеш
              Cache::forget('currency_rates');
              Cache::forget('exchange_rates');

              DB::commit();

              return redirect()->route('adminRates')
                  ->with('success', "Валюта {$currency->name} ({$currency->symbol}) успешно создана");

          } catch (\Exception $e) {
              DB::rollback();
              Log::error('Error creating currency: ' . $e->getMessage());

              return redirect()->back()
                  ->withInput()
                  ->with('error', 'Произошла ошибка при создании валюты: ' . $e->getMessage());
          }
      }

      /**
       * Удаление валюты
       */
      public function deleteCurrency($id)
      {
          try {
              $currency = Currency::findOrFail($id);

              // Проверяем, используется ли валюта
              $usageCount = User::where('currency_id', $currency->id)->count();

              if ($usageCount > 0) {
                  return response()->json([
                      'success' => false,
                      'message' => "Невозможно удалить валюту. Она используется у {$usageCount} пользователей"
                  ], 400);
              }

              // Проверяем транзакции
              $transactionsCount = DB::table('transactions')
                  ->where('currency_id', $currency->id)
                  ->count();

              if ($transactionsCount > 0) {
                  return response()->json([
                      'success' => false,
                      'message' => "Невозможно удалить валюту. Существуют транзакции с этой валютой"
                  ], 400);
              }

              // Защита от удаления USD
              if ($currency->symbol === 'USD') {
                  return response()->json([
                      'success' => false,
                      'message' => 'Невозможно удалить базовую валюту USD'
                  ], 400);
              }

              DB::beginTransaction();

              // Удаляем связанные данные
              Rate::where('currency_id', $currency->id)->delete();
              DB::table('rate_history')->where('currency_id', $currency->id)->delete();

              // Удаляем валюту
              $currency->delete();

              // Очищаем кеш
              Cache::forget('currency_rates');
              Cache::forget('exchange_rates');

              DB::commit();

              return response()->json([
                  'success' => true,
                  'message' => "Валюта {$currency->name} успешно удалена"
              ]);

          } catch (\Exception $e) {
              DB::rollback();
              Log::error('Error deleting currency: ' . $e->getMessage());

              return response()->json([
                  'success' => false,
                  'message' => 'Произошла ошибка при удалении валюты'
              ], 500);
          }
      }

      /**
       * Переключение статуса активности валюты
       */
      public function toggleCurrency($id)
      {
          try {
              $currency = Currency::findOrFail($id);

              // Защита от деактивации USD
              if ($currency->symbol === 'USD' && $currency->active) {
                  return response()->json([
                      'success' => false,
                      'message' => 'Невозможно деактивировать базовую валюту USD'
                  ], 400);
              }

              $currency->active = !$currency->active;
              $currency->save();

              // Очищаем кеш
              Cache::forget('currency_rates');
              Cache::forget('exchange_rates');

              return response()->json([
                  'success' => true,
                  'message' => "Валюта {$currency->name} " . ($currency->active ? 'активирована' : 'деактивирована'),
                  'active' => $currency->active
              ]);

          } catch (\Exception $e) {
              Log::error('Error toggling currency: ' . $e->getMessage());

              return response()->json([
                  'success' => false,
                  'message' => 'Произошла ошибка при изменении статуса валюты'
              ], 500);
          }
      }
}
