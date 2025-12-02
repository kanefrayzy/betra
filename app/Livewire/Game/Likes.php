<?php
namespace App\Livewire\Game;

use App\Models\GameLike;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Jenssegers\Agent\Agent;

class Likes extends Component
{
    public $gameId;
    public $likesCount;
    public $isLiked;
    public $isMobile;

    public function mount($gameId)
    {
        $this->gameId = $gameId;
        $this->likesCount = $this->getLikeCount($gameId);
        $this->isLiked = $this->checkIsLiked($gameId);
        $agent = new Agent();
        $this->isMobile = $agent->isMobile();
    }

    public function render()
    {
        return view('livewire.game.likes', [
            'likesCount' => $this->likesCount,
            'isLiked' => $this->isLiked,
            'isMobile' => $this->isMobile,
        ]);
    }

    public function getLikeCount($gameId)
    {
        return GameLike::where('slotegrator_game_id', $gameId)->count();
    }

    public function checkIsLiked($gameId)
    {
        return GameLike::where('user_id', Auth::id())
            ->where('slotegrator_game_id', $gameId)
            ->where('is_active', true)
            ->exists();
    }
}
