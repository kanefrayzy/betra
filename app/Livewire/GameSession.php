<?php

// app//Livewire/GameSession.php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\SlotegratorGame;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use App\Services\Slotegrator\SlotegratorClient;

class GameSession extends Component
{
    public $gameUrl;
    public $sessionToken;
    public $isMobile;
    public $gameName;

    protected $client;

    public function mount($gameName)
    {
        $this->client = app(SlotegratorClient::class);
        $this->gameName = $gameName;
        $this->initializeGameSession();
    }

    public function initializeGameSession()
    {
        $locale = App::getLocale();
        $game = SlotegratorGame::where('name', $this->gameName)->firstOrFail();
        $user = Auth::user();

        $this->sessionToken = Str::uuid()->toString();

        $user->gamesHistory()->create([
            'slotegrator_game_id' => $game->id,
            'session_token' => $this->sessionToken,
            'ip' => request()->ip(),
            'device' => (new Agent())->device(),
        ]);

        $locale = $locale === 'az' ? 'tr' : $locale;

        $response = $this->client->post('/games/init', [
            'game_uuid' => $game->uuid,
            'player_id' => $user->id,
            'player_name' => $user->username,
            'currency' => $user->currency->symbol,
            'session_id' => $this->sessionToken,
            'language' => $locale,
        ]);

        $user->gameSession()->updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $this->sessionToken]
        );

        $this->gameUrl = $response['url'] ?? null;
        $this->isMobile = (new Agent())->isMobile();
    }

    public function render()
    {
        $view = $this->isMobile ? 'games.mobile' : 'games.play';
        return view($view, ['gameUrl' => $this->gameUrl]);
    }
}
