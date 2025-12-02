<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Game;

class GameModal extends Component
{
    public $game;

    protected $listeners = ['openModal'];

    public function openModal($gameId)
    {
        $this->game = Game::find($gameId);
        $this->dispatchBrowserEvent('show-game-modal');
    }

    public function render()
    {
        return view('livewire.game-modal');
    }
}
