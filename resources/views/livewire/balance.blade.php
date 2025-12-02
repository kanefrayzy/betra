<div class="flex items-center space-x-2 md:space-x-3">
    <div class="relative" x-data="{ open: false }">
        <!-- Mobile Button -->
        <button @click="open = !open" class="md:hidden flex items-center justify-between h-12 bg-[#181b21] text-white px-3 py-2 rounded-xl border border-[#2a3441] shadow-lg w-[180px] hover:border-[#ffb300]/50 transition-all">
            <div class="flex items-center space-x-2 flex-1 min-w-0">
                <div class="w-7 h-7 rounded-full flex items-center justify-center shadow-inner flex-shrink-0">
                    <img src="{{ asset('assets/images/curr/'.$selectedCurrency.'.png') }}"
                         alt="{{ $selectedCurrency }}"
                         class="h-4 w-4"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="text-black text-xs font-bold hidden">RUB</span>
                </div>

                <span class="text-white font-bold text-base tabular-nums truncate flex-1 text-center" wire:poll.3s.visible="refreshBalance">{{ $this->getFormattedBalance() }}</span>
            </div>

            <div class="flex items-center space-x-1">
                <span class="text-[#ffb300] text-sm font-bold uppercase">{{ $selectedCurrency }}</span>
                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </button>

        <!-- Desktop Button -->
        <button @click="open = !open" class="hidden md:flex items-center h-14 bg-[#181b21] text-white px-4 py-3 rounded-2xl border border-[#2a3441] hover:border-[#ffb300]/50 transition-all justify-between min-w-[220px]">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center">
                    <img src="{{ asset('assets/images/curr/'.$selectedCurrency.'.png') }}"
                         alt="{{ $selectedCurrency }}"
                         class="h-5 w-5"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="text-black text-sm font-bold hidden">₼</span>
                </div>

                <div class="flex items-baseline space-x-2">
                    <span class="text-white font-bold text-xl tabular-nums font-sans" wire:poll.3s.visible="refreshBalance">{{ $this->getFormattedBalance() }}</span>
                    <span class="text-[#ffb300] text-base font-bold uppercase">{{ $selectedCurrency }}</span>
                </div>
            </div>
            
            <svg class="w-5 h-5 text-gray-400 transition-transform ml-2" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Currency Dropdown -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="absolute top-full mt-2 w-full md:w-56 bg-[#1e2329] rounded-xl shadow-xl overflow-hidden z-50 border border-[#2a3441]"
             style="display: none;">
            
            @foreach($currencies as $currency)
                <button 
                    wire:click="changeCurrency({{ $currency->id }})"
                    @click="open = false"
                    class="w-full flex items-center justify-between px-4 py-3 transition-colors duration-150 {{ $selectedCurrency === $currency->symbol ? 'bg-[#ffb300] text-black' : 'text-white hover:bg-[#2a3441]' }} {{ $loop->last ? '' : 'border-b border-[#2a3441]' }}">
                    
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/images/curr/'.$currency->symbol.'.png') }}"
                             alt="{{ $currency->symbol }}"
                             class="h-5 w-5"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span class="{{ $selectedCurrency === $currency->symbol ? 'text-black' : 'text-[#ffb300]' }} text-xs font-bold hidden">{{ substr($currency->symbol, 0, 1) }}</span>
                        
                        <span class="font-bold text-sm">{{ $currency->symbol }}</span>
                    </div>
                    
                    @if($selectedCurrency === $currency->symbol)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- Wallet Buttons -->
    <button
        onclick="openCashModal();"
        class="xl:hidden flex items-center justify-center h-10 w-10 bg-gradient-to-r from-[#ffb300] to-[#e6a000] hover:bg-[#2b3139] text-white border border-[#2b3139] hover:border-[#ffb300]/30 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group relative overflow-hidden"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5z" />
            <path d="M16 12h2" />
        </svg>
    </button>

    <button
        onclick="openCashModal();"
        class="hidden xl:flex items-center space-x-2 h-12 px-5 py-2 bg-gradient-to-r from-[#ffb300] to-[#e6a000] hover:bg-[#2b3139] text-black border border-[#2b3139] hover:border-[#ffb300]/30 rounded-xl shadow-lg hover:shadow-xl font-medium group relative overflow-hidden transition-all duration-200"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5z" />
            <path d="M16 12h2" />
        </svg>
        <span class="font-bold font-manrope">{{ __('Кошелек') }}</span>
    </button>
</div>