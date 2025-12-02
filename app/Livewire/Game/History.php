<?php
namespace App\Livewire\Game;

use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Livewire\Component;

class History extends BaseComponent
{
    public $games;
    public $isMobile;

    public function mount()
    {
        $user = Auth::user();
        $gamesHistory = $user->gamesHistory()
            ->with('slotegratorGame')
            ->latest()
            ->get();

        $this->games = $gamesHistory->map(function ($gameHistory) {
            return $gameHistory->slotegratorGame;
        })->filter()->unique('id');

        $agent = new Agent();
        $this->isMobile = $agent->isMobile();

        if (is_null($this->isMobile)) {
            $this->isMobile = false;
        }
    }

    public function render()
    {
        return view('livewire.game.history', [
            'games' => $this->games,
            'isMobile' => $this->isMobile,
        ]);
    }
}
