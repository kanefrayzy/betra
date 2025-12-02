<?php
namespace App\Livewire\Game;

use App\Models\FavoriteGame;
use App\Models\GameLike;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

abstract class BaseComponent extends Component
{
    public $query;
    public $selectedProvider;
    public $selectedProviders;
    public $perPage = 24; // Количество игр на одну загрузку

    #[On('submit-search')]
    public function submitSearch(): void
    {
        $this->resetLoadMore();
    }

    public function filterByProvider($provider): void
    {
        $this->selectedProvider = $provider;
        $this->resetLoadMore();
    }

    public function filterByProviders(array $providers): void
    {
        $this->selectedProviders = $providers;
        $this->resetLoadMore();
    }

    public function filterClean(): void
    {
        $this->selectedProvider = null;
        $this->selectedProviders = null;
        $this->resetLoadMore();
    }

    // Метод для загрузки большего количества игр
    public function loadMore(): void
    {
        $this->perPage += 24; // Увеличиваем на 24 игры
    }

    // Метод для сброса при фильтрации
    protected function resetLoadMore(): void
    {
        $this->perPage = 24;
    }

    public function toggleFavorite($gameId): void
    {
        if (!auth()->check()) {
            $this->dispatch('open-register-modal');
            return;
        }

        try {
            $user = Auth::user();
            $isFavorite = $this->isFavorite($gameId);

            if ($isFavorite) {
                FavoriteGame::where('user_id', $user->id)
                    ->where('slotegrator_game_id', $gameId)
                    ->delete();
            } else {
                FavoriteGame::create([
                    'user_id' => $user->id,
                    'slotegrator_game_id' => $gameId,
                ]);
            }

            $this->dispatch('favorite-toggled', gameId: $gameId);
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'An error occurred while toggling favorite.');
        }
    }

    public function toggleLike($gameId): void
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }

        try {
            $user = Auth::user();
            $isLiked = $this->checkIsLiked($gameId);

            if ($isLiked) {
                GameLike::where('user_id', $user->id)
                    ->where('slotegrator_game_id', $gameId)
                    ->delete();
            } else {
                GameLike::create([
                    'user_id' => $user->id,
                    'slotegrator_game_id' => $gameId,
                ]);
            }

            $this->dispatch('like-toggled', gameId: $gameId);
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'An error occurred while toggling like.');
        }
    }

    public function isFavorite($gameId)
    {
        return FavoriteGame::where('user_id', Auth::id())
            ->where('slotegrator_game_id', $gameId)
            ->exists();
    }

    public function getLikeCount($gameId)
    {
        return GameLike::where('slotegrator_game_id', $gameId)->count();
    }

    public function checkIsLiked($gameId)
    {
        return GameLike::where('user_id', Auth::id())
            ->where('slotegrator_game_id', $gameId)
            ->exists();
    }
}
