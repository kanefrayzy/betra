<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentLeaderboard;
use Illuminate\Support\Facades\Auth;
use App\Services\ExchangeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{
    protected $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function index()
    {
        $tournament = Tournament::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$tournament) {
            return redirect()->back();
        }

        $userPosition = null;
        $userTurnover = null;
        $currency = Auth::check() ? Auth::user()->currency->symbol : 'USD';

        // Получаем таблицу лидеров
        $leaderboard = TournamentLeaderboard::with('user')
            ->where('tournament_id', $tournament->id)
            ->orderByDesc('turnover')
            ->limit(100)
            ->get();

        // Если пользователь авторизован
        if (Auth::check()) {
            $userId = Auth::id();

            $currentUserData = DB::table('tournament_leaderboard')
                ->where('tournament_id', $tournament->id)
                ->where('user_id', $userId)
                ->first();

            if ($currentUserData) {
                $userPosition = DB::table('tournament_leaderboard')
                    ->where('tournament_id', $tournament->id)
                    ->where('turnover', '>', $currentUserData->turnover)
                    ->count() + 1;

                $userTurnover = $currentUserData->turnover;
            }
        }

        return view('tournament.show', [
            'tournament' => $tournament,
            'leaderboard' => $leaderboard,
            'currentUserPosition' => $userPosition,
            'currentUserOborot' => $userTurnover,
            'prizes' => $tournament->prize_distribution,
            'currency' => $currency,
            'exchangeService' => $this->exchangeService
        ]);
    }

    private function updateTournamentLeaderboard(User $user, float $amount)
    {
        $activeTournament = Tournament::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($activeTournament) {
            DB::table('tournament_leaderboard')->updateOrInsert(
                [
                    'tournament_id' => $activeTournament->id,
                    'user_id' => $user->id
                ],
                [
                    'turnover' => DB::raw("turnover + $amount"),
                    'updated_at' => now()
                ]
            );
        }
    }
}
