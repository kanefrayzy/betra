<?php

// /var/www/html/app/Livewire/Game/GameModalComponent.php
namespace App\Livewire\Game;
use App\Models\GameLike;

class GameModalComponent extends BaseComponent
{
    public $gameId;

    protected $listeners = ['updateGameId'];

    public function mount($gameId = null)
    {
        $this->gameId = $gameId;
    }

    public function updateGameId($gameId)
    {
        $this->gameId = $gameId;
        $this->render(); // Вызов рендера для обновления данных
    }

    public function toggleFavorite($gameId): void
    {
        parent::toggleFavorite($gameId);
    }

    public function toggleLike($gameId): void
    {
        parent::toggleLike($gameId);
    }

    public function getLikeCount($gameId)
    {
        return GameLike::where('slotegrator_game_id', $gameId)->count();
    }

    public function render()
    {
        return view('livewire.game-modal-component', [
            'isFavorite' => $this->isFavorite($this->gameId),
            'isLiked' => $this->checkIsLiked($this->gameId),
            'likeCount' => $this->getLikeCount($this->gameId),
            'gameId' => $this->gameId
        ]);
    }
}
