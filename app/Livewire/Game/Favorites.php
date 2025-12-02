<?php

namespace App\Livewire\Game;

use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Livewire\WithPagination;

class Favorites extends BaseComponent
{
    use WithPagination;


    public function render()
    {
        $user = Auth::user();
        $games = $user->favoriteGames()
            ->latest()
            ->get();
            $agent = new Agent();
            $this->isMobile = $agent->isMobile();

        return view('livewire.game.favorites', [
            'games' => $games,
            'isMobile' => $this->isMobile, 
        ]);
    }
}
