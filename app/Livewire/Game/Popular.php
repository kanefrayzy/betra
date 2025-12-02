<?php
namespace App\Livewire\Game;

use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Livewire\Component;
use App\Models\SlotegratorGame;
use App\Models\FavoriteGame;

class Popular extends BaseComponent
{
    public $games;
    public $isMobile;

    public function mount()
    {
        $this->games = SlotegratorGame::select('slotegrator_games.*')
            ->leftJoin('favorite_games', 'slotegrator_games.id', '=', 'favorite_games.slotegrator_game_id')
            ->where('slotegrator_games.is_active', true)
            ->groupBy('slotegrator_games.id')
            ->orderByRaw('COUNT(favorite_games.id) DESC')
            ->orderBy('slotegrator_games.created_at', 'desc')
            ->limit(20)
            ->get();

        if ($this->games->count() < 20) {
            $existingIds = $this->games->pluck('id')->toArray();
            $additionalGames = SlotegratorGame::where('is_active', true)
                ->where('is_popular', true)
                ->whereNotIn('id', $existingIds)
                ->orderBy('created_at', 'desc')
                ->limit(20 - $this->games->count())
                ->get();
            $this->games = $this->games->merge($additionalGames);
        }

        if ($this->games->count() < 20) {
            $existingIds = $this->games->pluck('id')->toArray();
            $newGames = SlotegratorGame::where('is_active', true)
                ->whereNotIn('id', $existingIds)
                ->orderBy('created_at', 'desc')
                ->limit(20 - $this->games->count())
                ->get();
            $this->games = $this->games->merge($newGames);
        }

        $agent = new Agent();
        $this->isMobile = $agent->isMobile();
        if (is_null($this->isMobile)) {
            $this->isMobile = false;
        }
    }

    public function render()
    {
        return view('livewire.game.popular', [
            'games' => $this->games,
            'isMobile' => $this->isMobile,
        ]);
    }

    public function getFavoriteCount($gameId)
    {
        return FavoriteGame::where('slotegrator_game_id', $gameId)->count();
    }

    public function getPopularityStats()
    {
        return [
            'total_favorites' => FavoriteGame::count(),
            'total_popular_games' => SlotegratorGame::where('is_popular', true)->where('is_active', true)->count(),
            'total_active_games' => SlotegratorGame::where('is_active', true)->count(),
        ];
    }
}
