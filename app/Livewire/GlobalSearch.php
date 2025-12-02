<?php
namespace App\Livewire;

use App\Models\SlotegratorGame;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];
    public $showResults = false;

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->results = SlotegratorGame::where('is_active', true)
                ->where('name', 'LIKE', '%' . $this->query . '%')
                ->limit(10)
                ->get();
            $this->showResults = true;
        } else {
            $this->results = [];
            $this->showResults = false;
        }
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
