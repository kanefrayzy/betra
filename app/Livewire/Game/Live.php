<?php
namespace App\Livewire\Game;

use App\Models\SlotegratorGame;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Cache;

class Live extends BaseComponent
{
    public $isMobile;
    public $perPage = 30;

    protected $queryString = ['query', 'selectedProviders'];

    public function mount()
    {
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
        $gamesData = $this->getGames();
        $providers = $this->getCachedProviders();

        return view('livewire.game.main', [
            'games' => $gamesData['games'],
            'hasMore' => $gamesData['hasMore'],
            'totalGames' => $gamesData['totalGames'],
            'providers' => $providers,
            'isMobile' => $this->isMobile,
        ]);
    }

    private function getGames()
    {
        $query = empty($this->query)
            ? SlotegratorGame::where('is_live', true)->where('is_active', true)
            : SlotegratorGame::search($this->query)->where('is_live', true)->where('is_active', true);

        $query->when(!empty($this->selectedProviders), function ($q) {
            $q->whereIn('provider', $this->selectedProviders);
        });

        if (empty($this->query)) {
            $query->latest('updated_at');
        }

        $games = $query->take($this->perPage + 1)->get();

        $hasMore = $games->count() > $this->perPage;
        $displayGames = $games->take($this->perPage);

        return [
            'games' => $displayGames,
            'hasMore' => $hasMore,
            'totalGames' => $displayGames->count()
        ];
    }

    private function getCachedProviders()
    {
        return Cache::remember('live_providers', now()->addHours(20), function () {
            return SlotegratorGame::select('provider')
                ->selectRaw('count(*) as games_count')
                ->where('is_live', true)
                ->groupBy('provider')
                ->get();
        });
    }
}
