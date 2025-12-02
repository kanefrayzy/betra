<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Services\ExchangeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DetailedStatisticsController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();

        if ($currentUser->id == 34) {
            return redirect()->route('adminUsers');
        }

        return view('admin.detailed_statistics');
    }

    public function getStatistics(Request $request)
    {
        $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
        $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

        $cacheKey = "detailed_stats_{$start_date->timestamp}_{$end_date->timestamp}";

        $data = Cache::remember($cacheKey, 60, function () use ($start_date, $end_date) {
            $rates = $this->getCurrencyRates();

            $transactions = DB::table('transactions')
                ->select(
                    'currency_id',
                    DB::raw('DATE(updated_at) as date'),
                    DB::raw('SUM(CASE WHEN type = "payment" AND status = "success" THEN amount ELSE 0 END) as deposits'),
                    DB::raw('SUM(CASE WHEN type = "withdrawal" AND status = "success" THEN amount ELSE 0 END) as withdrawals')
                )
                ->whereBetween('updated_at', [$start_date, $end_date])
                ->whereIn('type', ['payment', 'withdrawal'])
                ->where('status', 'success')
                ->groupBy('currency_id', DB::raw('DATE(updated_at)'))
                ->get();

            $totalDeposits = 0;
            $totalWithdrawals = 0;
            $dailyStats = [];

            foreach ($transactions as $transaction) {
                $rate = $rates[$transaction->currency_id] ?? 1;
                $depositsUSD = $transaction->deposits * $rate;
                $withdrawalsUSD = $transaction->withdrawals * $rate;

                $totalDeposits += $depositsUSD - ($depositsUSD *0.06);
                $totalWithdrawals += $withdrawalsUSD;

                if (!isset($dailyStats[$transaction->date])) {
                    $dailyStats[$transaction->date] = ['deposits' => 0, 'withdrawals' => 0];
                }
                $dailyStats[$transaction->date]['deposits'] += $depositsUSD;
                $dailyStats[$transaction->date]['withdrawals'] += $withdrawalsUSD;
            }

            return [
                'statistics' => [
                    'total_deposits' => $totalDeposits,
                    'total_withdrawals' => $totalWithdrawals,
                ],
                'chart_data' => collect($dailyStats)->map(function ($item, $date) {
                    return [
                        'date' => $date,
                        'deposits' => round($item['deposits'], 2),
                        'withdrawals' => round($item['withdrawals'], 2),
                    ];
                })->values()->all(),
            ];
        });

        return response()->json($data);
    }

    private function getCurrencyRates()
    {
        return Cache::remember('currency_rates', 3600, function () {
            $currencies = Currency::all();
            $exchangeService = app(ExchangeService::class);

            return $currencies->mapWithKeys(function ($currency) use ($exchangeService) {
                $rate = $currency->code === 'USD' ? 1 : $exchangeService->convert(1, $currency->code, 'USD');
                return [$currency->id => $rate];
            })->all();
        });
    }
}
