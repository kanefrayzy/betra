<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\Auth;

class WageringProgress extends Component
{
    public $wageringRequirement;
    public $wageredAmount;
    public $progress;
    public $bonusInfo;
    protected $user;

    public function mount()
    {
        $this->user = Auth::user();
        if ($this->user) {
            $this->updateProgress();
        }
    }

    public function updateProgress()
    {
        $this->user = Auth::user();
        if (!$this->user) {
              return;
        }
        $exchangeService = new ExchangeService();

        $this->wageringRequirement = $this->user->currency->symbol !== 'AZN'
            ? $exchangeService->convert($this->user->wagering_requirement, 'AZN', $this->user->currency->symbol)
            : $this->user->wagering_requirement;

        $this->wageredAmount = $this->user->currency->symbol !== 'AZN'
            ? $exchangeService->convert($this->user->wagered_amount, 'AZN', $this->user->currency->symbol)
            : $this->user->wagered_amount;

        $this->progress = $this->user->wagering_progress;
        $this->bonusInfo = json_decode($this->user->active_bonuses, true);
    }

    public function getListeners()
    {
        return [
            "echo-private:User." . auth()->id() . ",.BetPlaced" => 'updateProgress',
        ];
    }

    public function render()
    {
        return view('livewire.wagering-progress');
    }
}
