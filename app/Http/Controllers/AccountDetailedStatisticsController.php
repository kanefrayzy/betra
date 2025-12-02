<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Transaction;
use App\Services\ExchangeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AccountDetailedStatisticsController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        if ($currentUser->is_admin != 1) {
            return redirect()->route('index'); 
        }
        return view('pages.day_statistics');
    }

    public function getStatistics(Request $request)
    {
        $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
        $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

        $cacheKey = "detailed_stats_{$start_date->timestamp}_{$end_date->timestamp}";

        $data = Cache::remember($cacheKey, 60, function () use ($start_date, $end_date) {
            $rates = $this->getCurrencyRates();
            
            $currencies = Currency::all();
            $exchangeService = app(ExchangeService::class);
            
            $transactionsPay = Transaction::where('type', 'payment')
            ->where('status', 'success')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->get();
            
            $transactionsWithdrawals = Transaction::where('type', 'withdrawal')
            ->where('status', 'success')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->get();
            
            $transactionsRain = Transaction::where('type', 'rain')
            ->where('status', 'success')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->get();
            
            $stats = [];
            $stats["m10Payments"] = $this->calculateAmount($transactionsPay, 'payment_handler', 'M10', $exchangeService, $currencies);
            $stats["mPayPayments"] = $this->calculateAmount($transactionsPay, 'payment_handler', 'MPAY', $exchangeService, $currencies);
            $stats["cardPayments"] = $this->calculateAmount($transactionsPay, 'payment_handler', 'Card AZ', $exchangeService, $currencies);
            $stats["paykassaPayments"] = $this->calculateAmount($transactionsPay, 'payment_system', 'paykassa', $exchangeService, $currencies);
            $stats["freekassaPayments"] = $this->calculateAmount($transactionsPay, 'payment_system', 'FreeKassa', $exchangeService, $currencies);
            
            $totalDeposits = $stats["m10Payments"] + $stats["mPayPayments"] + $stats["cardPayments"] + $stats["paykassaPayments"] + $stats["freekassaPayments"];
            
            $stats["m10Withdrawals"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', '11', $exchangeService, $currencies);
            $stats["mPayWithdrawals"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', '13', $exchangeService, $currencies);
            $stats["cardWithdrawals"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', '12', $exchangeService, $currencies);
            $stats["paykassaWithdrawals"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', 'paykassa', $exchangeService, $currencies);
            $stats["freekassaWithdrawals"] = $this->calculateAmount($transactionsWithdrawals, 'payment_system', 'FreeKassa', $exchangeService, $currencies);
            
            $totalWithdrawals = $stats["m10Withdrawals"] + $stats["mPayWithdrawals"] + $stats["cardWithdrawals"] + $stats["paykassaWithdrawals"] + $stats["freekassaWithdrawals"];
            
            $stats["rain"] = $this->calculateAmount($transactionsRain, 'description', 'Rain from system', $exchangeService, $currencies);
            
            $start_date_gr = Carbon::now()->subDays(7)->startOfWeek();
            $end_date_gr = Carbon::today()->endOfDay();
            
            $transactions = DB::table('transactions')
                ->select(
                    'currency_id',
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(CASE WHEN type = "payment" AND status = "success" THEN amount ELSE 0 END) as deposits'),
                    DB::raw('SUM(CASE WHEN type = "withdrawal" AND status = "success" THEN amount ELSE 0 END) as withdrawals')
                )
                ->whereBetween('created_at', [$start_date_gr, $end_date_gr])
                ->whereIn('type', ['payment', 'withdrawal'])
                ->where('status', 'success')
                ->groupBy('currency_id', DB::raw('DATE(created_at)'))
                ->get();

//            $totalDeposits = 0;
//            $totalWithdrawals = 0;
            $dailyStats = [];

            foreach ($transactions as $transaction) {
                $rate = $rates[$transaction->currency_id] ?? 1;
                $depositsUSD = $transaction->deposits * $rate;
                $withdrawalsUSD = $transaction->withdrawals * $rate;
                $profitUSD = $depositsUSD - $withdrawalsUSD;

//                $totalDeposits += $depositsUSD;
//                $totalWithdrawals += $withdrawalsUSD;

                if (!isset($dailyStats[$transaction->date])) {
                    $dailyStats[$transaction->date] = ['deposits' => 0, 'withdrawals' => 0, 'profit' => 0];
                }
                $dailyStats[$transaction->date]['deposits'] += $depositsUSD;
                $dailyStats[$transaction->date]['withdrawals'] += $withdrawalsUSD;
                $dailyStats[$transaction->date]['profit'] += $profitUSD;
                
            }

            return [
                'statistics' => [
                    'total_deposits' => $totalDeposits,
                    'total_withdrawals' => $totalWithdrawals,
                ],
                'pay' => $stats, 
                'chart_data' => collect($dailyStats)->map(function ($item, $date) {
                    return [
                        'date' => $date,
                        'deposits' => round($item['deposits'], 2),
                        'withdrawals' => round($item['withdrawals'], 2),
                        'profit' => round($item['profit'], 2),
                    ];
                })->values()->all(),
            ];
        });

        return response()->json($data);
    }
    
    private function calculateAmount($transactions, $contextKey, $contextValue, $exchangeService, $currencies)
    {
        return $transactions
            ->filter(function ($transaction) use ($contextKey, $contextValue) {
                
                if (is_array($transaction->context)) {
                    $context = $transaction->context;    
                } else {
                    $context = json_decode($transaction->context, true);
                }
                
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

    private function getCurrencyRates()
    {
        return Cache::remember('currency_rates', 3600, function () {
            $currencies = Currency::all();
            $exchangeService = app(ExchangeService::class);
            
            return $currencies->mapWithKeys(function ($currency) use ($exchangeService) {
                $rate = $currency->symbol === 'USD' ? 1 : $exchangeService->convert(1, $currency->symbol, 'USD');
                return [$currency->id => $rate];
            })->all();
        });
    }
}
