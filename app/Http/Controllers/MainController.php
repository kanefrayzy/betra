<?php

namespace App\Http\Controllers;

use App\Models\SlotegratorGame;
use App\Models\GameCategory;
use App\Models\Banner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MainController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('homepage_categories', 60 * 60, function () {
            return GameCategory::where('is_active', true)
                ->where('show_on_homepage', true)
                ->orderBy('order')
                ->with(['activeGames' => function($query) {
                    $query->take(30);
                }])
                ->get();
        });

        $recentGames = null;
        if (Auth::check()) {
            $recentGames = $this->getRecentGames();
        }

        $mainBanners = Banner::getMainSliderBanners();
        $smallBanners = Banner::getSmallBanners();

        if ($categories->isEmpty()) {
            $slots = $this->getPopularGames();
            $lives = $this->getLiveGames();
            $roulettes = $this->getRouletteGames();
            $tables = $this->getTableGames();
            $history = null;

            return view('main.index', compact('slots', 'lives', 'roulettes', 'tables', 'history', 'mainBanners', 'smallBanners'));
        }

        return view('main.index', compact('categories', 'recentGames', 'mainBanners', 'smallBanners'));
    }

    private function getRecentGames()
    {
        return Cache::remember('recent_games_' . Auth::id(), 60 * 5, function () {
            $user = Auth::user();
            $gamesHistory = $user->gamesHistory()
                ->with('slotegratorGame')
                ->latest()
                ->take(15)
                ->get();

            return $gamesHistory->map(function ($gameHistory) {
                return $gameHistory->slotegratorGame;
            })->filter()->unique('id');
        });
    }

    // Методы для обратной совместимости
    private function getPopularGames()
    {
        return Cache::remember('popular_games', 60 * 60, function () {
            return SlotegratorGame::where('is_popular', true)
                ->where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->take(30)
                ->get();
        });
    }

    private function getLiveGames()
    {
        return Cache::remember('live_games', 60 * 60, function () {
            return SlotegratorGame::where('is_live', true)
                ->where('is_active', true)
                ->inRandomOrder()
                ->take(30)
                ->get();
        });
    }

    private function getRouletteGames()
    {
        return Cache::remember('roulette_games', 60 * 60, function () {
            return SlotegratorGame::where('is_roulette', true)
                ->where('is_active', true)
                ->inRandomOrder()
                ->take(30)
                ->get();
        });
    }

    private function getTableGames()
    {
        return Cache::remember('table_games', 60 * 60, function () {
            return SlotegratorGame::where('is_table', true)
                ->where('is_active', true)
                ->inRandomOrder()
                ->take(30)
                ->get();
        });
    }

    public function partnership()
    {
        return view('pages.partnership');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function rules()
    {
        return view('pages.rules');
    }

    public function contacts()
    {
        return view('pages.contacts');
    }
}
