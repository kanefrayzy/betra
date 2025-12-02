<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\DailyLeaderboard;
use App\Services\ExchangeService;

class LeaderboardController extends Controller
{
    protected $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function getDailyLeaderboard(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $perPage = 100;
        $leaderboard = DailyLeaderboard::with('user')
            ->where('date', $today)
            ->orderByDesc('daily_oborot')
            ->paginate($perPage);

        $yesterdayWinner = DailyLeaderboard::with('user')
            ->where('date', $yesterday)
            ->orderByDesc('daily_oborot')
            ->first();

        $currentUserId = auth()->id();
        $currentUserPosition = null;
        $currentUserOborot = null;
        $currency = 'AZN';

        if ($currentUserId) {
            $user = auth()->user();
            $currency = $user->currency->symbol ?? 'USD';

            $currentUserData = DB::table('daily_leaderboard')
                ->where('date', $today)
                ->where('user_id', $currentUserId)
                ->first();

            if ($currentUserData) {
                $currentUserPosition = DB::table('daily_leaderboard')
                    ->where('date', $today)
                    ->where('daily_oborot', '>', $currentUserData->daily_oborot)
                    ->count() + 1;
                $currentUserOborot = $currentUserData->daily_oborot;
            }
        }

        $prizes = [100, 50, 30, 20, 10, 5, 5, 5, 5, 5];

        return view('user.leaderboard', [
            'leaderboard' => $leaderboard,
            'currentUserPosition' => $currentUserPosition,
            'currentUserOborot' => $currentUserOborot,
            'prizes' => $prizes,
            'currency' => $currency,
            'exchangeService' => $this->exchangeService,
            'yesterdayWinner' => $yesterdayWinner,
        ]);
    }
}
