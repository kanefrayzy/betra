<?php
namespace App\Livewire\Game;

use App\Models\SlotegratorGame;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Cache;

class Main extends BaseComponent
{
    public $isMobile;
    public $perPage = 30; // Начальное количество игр

    protected $queryString = ['query', 'selectedProviders'];

    public function mount()
    {
        $this->isMobile = app(Agent::class)->isMobile();
        // Гарантируем, что это массив
        $this->selectedProviders = is_array($this->selectedProviders) ? $this->selectedProviders : [];
    }

    // Переопределяем методы из BaseComponent для сброса perPage
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

    // Обновляем perPage при изменении query или фильтров
    public function updating($name, $value)
    {
        if ($name === 'query' || $name === 'selectedProviders') {
            $this->resetLoadMore();
        }
    }

    // Метод для загрузки большего количества игр
    public function loadMore(): void
    {
        $this->perPage += 30; // Увеличиваем на 30 игр
    }

    // Метод для сброса при фильтрации
    protected function resetLoadMore(): void
    {
        $this->perPage = 30;
    }

    public function render()
    {
        $providers = $this->getCachedProviders();
        $gamesData = $this->getGames();

        return view('livewire.game.main', [
            'games' => $gamesData['games'],
            'hasMore' => $gamesData['hasMore'],
            'totalGames' => $gamesData['totalGames'],
            'providers' => $providers,
        ]);
    }

    private function getCachedProviders()
    {
        return Cache::remember('providers_list', now()->addHours(6), function () {
            return SlotegratorGame::where('is_active', 1)
                ->select('provider')
                ->selectRaw('COUNT(*) as games_count')
                ->groupBy('provider')
                ->orderByDesc('games_count')
                ->get();
        });
    }

    private function getGames()
    {
        // Если есть поисковый запрос, используем Scout
        if (!empty($this->query)) {
            $scoutQuery = SlotegratorGame::search($this->query)
                ->where('is_active', true)
                ->when(!empty($this->selectedProviders), function ($query) {
                    $query->whereIn('provider', $this->selectedProviders);
                });

            // Получаем игры с увеличенным лимитом для подсчета
            $allGames = $scoutQuery->take($this->perPage + 1)->get();

            // Проверяем есть ли еще игры
            $hasMore = $allGames->count() > $this->perPage;

            // Берем только нужное количество
            $games = $allGames->take($this->perPage);

            return [
                'games' => $games,
                'hasMore' => $hasMore,
                'totalGames' => $games->count() // Приблизительное значение
            ];
        }

        // Если нет поискового запроса, используем обычный Eloquent
        $query = SlotegratorGame::where('is_active', true)
            ->when(!empty($this->selectedProviders), function ($query) {
                $query->whereIn('provider', $this->selectedProviders);
            })
            ->latest('updated_at');

        // Получаем общее количество игр
        $totalGames = $query->count();

        // Получаем игры с ограничением
        $games = $query->take($this->perPage)->get();

        // Проверяем, есть ли еще игры
        $hasMore = $totalGames > $this->perPage;

        return [
            'games' => $games,
            'hasMore' => $hasMore,
            'totalGames' => $totalGames
        ];
    }
}
