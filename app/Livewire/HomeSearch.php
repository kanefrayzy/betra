<?php
namespace App\Livewire;

use App\Models\SlotegratorGame;
use Livewire\Component;

class HomeSearch extends Component
{
    public $query = '';
    public $results = [];
    public $showResults = false;
    public $isLoading = false;

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->isLoading = true;
            
            // Имитация задержки для плавности
            usleep(100000); // 100ms
            
            $this->results = SlotegratorGame::where('is_active', true)
                ->where('name', 'LIKE', '%' . $this->query . '%')
                ->latest('updated_at')
                ->limit(24)
                ->get();
            $this->showResults = true;
            $this->isLoading = false;
        } else {
            $this->results = [];
            $this->showResults = false;
            $this->isLoading = false;
        }
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
        $this->showResults = false;
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.home-search');
    }
}
