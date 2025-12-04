<?php

namespace App\Livewire;

use App\Models\Currency;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Middleware;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

#[Middleware(['auth'])]
class Balance extends Component
{
    public float $balance = 0;
    public string $selectedCurrency = '';
    public $currencies = [];
    
    protected $listeners = ['balanceUpdated' => 'refreshBalance'];

    /**
     * ═══════════════════════════════════════════════════════════
     *  Lazy Loading Placeholder
     * ═══════════════════════════════════════════════════════════
     */
    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex items-stretch">
            <div class="flex items-center h-12 bg-[#0f212e] px-4 rounded-l-xl border border-r-0 border-[#1a2c38] min-w-[160px] animate-pulse">
                <div class="bg-gray-700 h-5 w-20 rounded"></div>
            </div>
            <div class="flex items-center justify-center h-12 w-12 md:w-auto md:px-4 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] rounded-r-xl border border-l-0 border-[#1a2c38] animate-pulse">
                <div class="bg-white/20 h-4 w-16 rounded hidden md:block"></div>
                <div class="w-4 h-4 bg-white/20 rounded md:hidden"></div>
            </div>
        </div>
        HTML;
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Component Mount
     * ═══════════════════════════════════════════════════════════
     */
    public function mount(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $user->load(['currency']);
        
        $this->balance = $user->balance;
        $this->selectedCurrency = $user->currency->symbol ?? '';
        $this->currencies = $this->getCachedCurrencies();
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Get Cached Currencies
     * ═══════════════════════════════════════════════════════════
     */
    protected function getCachedCurrencies()
    {
        return Cache::remember('active_currencies', 3600, function () {
            return Currency::select('id', 'name', 'symbol')
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Change User Currency
     * ═══════════════════════════════════════════════════════════
     */
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
                $user->currency->symbol,
                $newCurrency->symbol
            );
        }

        $user->currency_id = $currencyId;
        $user->save();
        
        $this->balance = $user->balance;
        $this->selectedCurrency = $newCurrency->symbol;
        
        $this->dispatch('balanceUpdated');
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Refresh Balance (Polled every 3s)
     * ═══════════════════════════════════════════════════════════
     */
    public function refreshBalance(): void
    {
        $user = Auth::user();
        
        if ($user) {
            $this->balance = $user->balance;
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Get Formatted Balance
     * ═══════════════════════════════════════════════════════════
     */
    #[Computed]
    public function getFormattedBalance(): string
    {
        return number_format($this->balance, 2, '.', '');
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Render Component
     * ═══════════════════════════════════════════════════════════
     */
    public function render()
    {
        return view('livewire.balance');
    }
}