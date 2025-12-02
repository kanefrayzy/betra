<?php

namespace App\Livewire\Game;

use App\Models\GameCategory;
use App\Models\SlotegratorGame;
use Jenssegers\Agent\Agent;

class Category extends BaseComponent
{
    public $categorySlug;
    public $category;
    public $isMobile;
    public $perPage = 30;

    protected $queryString = ['query', 'selectedProviders'];

    public function mount($slug)
    {
        $this->categorySlug = $slug;
        $this->category = GameCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        $this->isMobile = app(Agent::class)->isMobile();
        $this->selectedProviders = is_array($this->selectedProviders) ? $this->selectedProviders : [];
    }

    public function submitSearch(): void
    {
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

    public function updating($name, $value)
    {
        if ($name === 'query' || $name === 'selectedProviders') {
            $this->resetLoadMore();
        }
    }

    public function loadMore(): void
    {
        $this->perPage += 30;
    }

    protected function resetLoadMore(): void
    {
        $this->perPage = 30;
    }

    public function render()
    {
        $gamesQuery = $this->category->activeGames()->getQuery();

        if ($this->query) {
            $gamesQuery->where('name', 'LIKE', '%' . $this->query . '%');
        }

        if (!empty($this->selectedProviders)) {
            $gamesQuery->whereIn('provider', $this->selectedProviders);
        }

        $games = $gamesQuery->take($this->perPage)->get();
        $hasMore = $gamesQuery->count() > $this->perPage;

        $providers = SlotegratorGame::whereHas('categories', function ($query) {
                $query->where('game_categories.id', $this->category->id);
            })
            ->where('is_active', true)
            ->select('provider')
            ->selectRaw('COUNT(*) as games_count')
            ->groupBy('provider')
            ->orderByDesc('games_count')
            ->get();

        return view('livewire.game.category', [
            'games' => $games,
            'providers' => $providers,
            'hasMore' => $hasMore,
        ]);
    }
}
