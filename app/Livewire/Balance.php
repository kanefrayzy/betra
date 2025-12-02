<?php
namespace App\Livewire;
use App\Models\Currency;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Middleware;
use Illuminate\Support\Facades\Cache;

#[Middleware(['auth'])]
class Balance extends Component
{
    public float $balance = 0;
    public string $selectedCurrency = '';
    public $currencies = [];
    
    protected $listeners = ['balanceUpdated' => 'refreshBalance'];

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }
        $this->refreshBalance();
        $this->selectedCurrency = $user->currency->code ?? '';
        $this->currencies = Currency::select('id', 'name', 'code', 'symbol')->get();
    }

    public function render()
    {
        return view('livewire.balance');
    }

    public function changeCurrency(int $currencyId): void
    {
        $user = Auth::user();
        $newCurrency = Currency::find($currencyId);
        
        if (!$user || !$newCurrency || $user->currency_id === $currencyId) {
            return;
        }

        if ($user->balance > 0) {
            $user->balance = (new ExchangeService())->convert(
                $user->balance,
                $user->currency->code,
                $newCurrency->code
            );
        }

        $user->currency_id = $currencyId;
        $user->save();
        
        $this->balance = $user->balance;
        $this->selectedCurrency = $newCurrency->code;
        
        $this->dispatch('balanceUpdated');
    }

    public function selectCurrency(string $symbol): void
    {
        $user = Auth::user();
        $newCurrency = Currency::firstWhere('symbol', $symbol);
        
        if (!$user || !$newCurrency || $user->currency->symbol === $symbol) {
            return;
        }

        if ($user->balance > 0) {
            $user->balance = (new ExchangeService())->convert(
                $user->balance,
                $user->currency->symbol,
                $symbol
            );
        }

        $user->currency()->associate($newCurrency);
        $user->save();
        
        $this->balance = $user->balance;
        $this->selectedCurrency = $symbol;
        
        $this->dispatch('balanceUpdated');
    }

    public function refreshBalance(): void
    {
        $this->balance = Auth::user()->balance ?? 0;
    }

    public function getFormattedBalance(): string
    {
        return number_format($this->balance, 2, '.', '');
    }
}