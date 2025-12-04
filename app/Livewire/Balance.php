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
                ->where('active', 1)
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

        // Принудительно перезагружаем отношение currency для актуальности
        $user->load('currency');
        
        $oldBalance = $user->balance;
        $oldCurrency = $user->currency->symbol;
        $newCurrencySymbol = $newCurrency->symbol;

        try {
            // Конвертируем баланс только если он больше 0
            if ($oldBalance > 0) {
                $convertedBalance = (new ExchangeService())->convert(
                    $oldBalance,
                    $oldCurrency,
                    $newCurrencySymbol
                );
                
                $user->balance = $convertedBalance;
            }

            // Обновляем валюту
            $user->currency_id = $currencyId;
            $user->save();
            
            // Обновляем локальное состояние ПОСЛЕ успешного сохранения
            $this->balance = $user->balance;
            $this->selectedCurrency = $newCurrencySymbol;
            
            $this->dispatch('balanceUpdated');
            
        } catch (\Exception $e) {
            \Log::error('Currency change error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'old_currency' => $oldCurrency,
                'new_currency' => $newCurrencySymbol,
                'old_balance' => $oldBalance
            ]);
            
            // Откатываем изменения
            $user->refresh();
            $this->balance = $user->balance;
            $this->selectedCurrency = $user->currency->symbol;
        }
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
            // Обновляем только баланс, валюта уже установлена
            $this->balance = $user->fresh()->balance;
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