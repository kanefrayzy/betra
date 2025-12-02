<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentLeaderboard;
use Illuminate\Support\Facades\Auth;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\Redirect;

class TournamentController extends Controller
{
    public function index()
    {
        $tournament = Tournament::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$tournament) {
            return redirect::back();
        }

        $userPosition = null;
        $userTurnover = null;
        $exchangeService = new ExchangeService();
        $currency = Auth::check() ? Auth::user()->currency->symbol : config('app.currency');

        // Получаем таблицу лидеров
        $leaderboard = TournamentLeaderboard::where('tournament_id', $tournament->id)
            ->with(['user'])
            ->orderByDesc('turnover')
            ->limit(100)
            ->get();

        // Если пользователь авторизован
        if (Auth::check()) {
            $userId = Auth::id();

            // Получаем позицию пользователя
            $userEntry = TournamentLeaderboard::where('tournament_id', $tournament->id)
                ->where('user_id', $userId)
                ->first();

            if ($userEntry) {
                $userPosition = TournamentLeaderboard::where('tournament_id', $tournament->id)
                    ->where('turnover', '>', $userEntry->turnover)
                    ->count() + 1;

                $userTurnover = $userEntry->turnover;
            }
        }

        // Конвертируем призы в валюту пользователя
        // $prizes = collect($tournament->prize_distribution)->map(function($prize) use ($exchangeService, $currency) {
        //     return $currency !== 'USD'
        //         ? $exchangeService->convert($prize, 'USD', $currency)
        //         : $prize;
        // })->toArray();

        $prizes = $tournament->prize_distribution;
        return view('tournament.show', compact(
            'tournament',
            'leaderboard',
            'userPosition',
            'userTurnover',
            'prizes',
            'currency'
        ));
    }

    public function show()
    {
        return $this->index();
    }
}
