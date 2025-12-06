<div class="flex items-stretch" 
     x-data="{ open: false }"
     @currency-changed.window="open = false; $wire.$refresh()">
    <div class="relative">
        <!-- Mobile Button -->
        <button @click="open = !open" 
                class="md:hidden flex items-center justify-between h-12 bg-[#0f212e] text-white px-3 py-2 rounded-l-xl border border-r-0 border-[#1a2c38] shadow-lg w-[140px] hover:bg-[#071824] transition-all">
            <div class="flex items-center space-x-2 flex-1 min-w-0">
                <div class="w-7 h-7 rounded-full bg-[#1a2c38] flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('assets/images/curr/'.$selectedCurrency.'.png') }}"
                         alt="{{ $selectedCurrency }}"
                         class="h-4 w-4"
                         loading="lazy"
                         onerror="this.style.display='none';">
                </div>

                <span class="text-white font-bold text-base tabular-nums truncate flex-1" 
                      wire:poll.3s.visible="refreshBalance">
                    {{ $this->getFormattedBalance() }} 
                </span>
            </div>

            <svg class="w-4 h-4 text-gray-400 transition-transform" 
                 :class="{ 'rotate-180': open }" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Desktop Button -->
        <button @click="open = !open" 
                class="hidden md:flex items-center h-12 bg-[#0f212e] text-white px-4 py-2.5 rounded-l-xl border border-r-0 border-[#1a2c38] hover:bg-[#071824] transition-all justify-between min-w-[160px]">
            <div class="flex items-center space-x-2.5">
                <span class="text-white font-bold text-sm tabular-nums" 
                      wire:poll.3s.visible="refreshBalance">
                    {{ $this->getFormattedBalance() }}
                </span>
                <div class="w-7 h-7 rounded-full bg-[#1a2c38] flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('assets/images/curr/'.$selectedCurrency.'.png') }}"
                         alt="{{ $selectedCurrency }}"
                         class="h-4 w-4"
                         loading="lazy"
                         onerror="this.style.display='none';">            
                </div>
            </div>
            
            <svg class="w-4 h-4 text-gray-400 transition-transform ml-3" 
                 :class="{ 'rotate-180': open }" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
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
             class="absolute top-full mt-2 w-[280px] bg-white rounded-xl shadow-2xl overflow-hidden z-50 border border-gray-200 dropdown-with-arrow"
             style="display: none;"
             @keydown.escape="open = false">
            
            <div class="max-h-[320px] overflow-y-auto custom-scrollbar">
                @foreach($currencies as $currency)
                    <button 
                        wire:click="changeCurrency({{ $currency->id }})"
                        @click="open = false"
                        class="w-full flex items-center justify-between px-4 py-3 transition-colors duration-150 border-b border-gray-100 last:border-b-0 {{ $selectedCurrency === $currency->symbol ? 'bg-blue-50 hover:bg-blue-100' : 'hover:bg-gray-50' }}">
                        
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <img src="{{ asset('assets/images/curr/'.$currency->symbol.'.png') }}"
                                     alt="{{ $currency->symbol }}"
                                     class="h-5 w-5"
                                     onerror="this.style.display='none';">
                            </div>
                            
                            <div class="flex flex-col items-start flex-1 min-w-0">
                                <span class="font-bold text-sm text-gray-900 truncate">{{ $currency->symbol }}</span>
                                <span class="text-xs text-gray-500 tabular-nums">
                                    @if($selectedCurrency === $currency->symbol)
                                        {{ $this->getFormattedBalance() }}
                                    @else
                                        0.00000000
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        @if($selectedCurrency === $currency->symbol)
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Wallet Buttons -->
    <button
        onclick="openCashModal();"
        class="md:hidden flex items-center justify-center h-12 w-12 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] hover:from-[#2563eb] hover:to-[#1d4ed8] text-white rounded-r-xl border border-l-0 border-[#1a2c38] shadow-lg hover:shadow-xl transition-all duration-200">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12h2" />
        </svg>
    </button>

    <button
        onclick="openCashModal();"
        class="hidden md:flex items-center space-x-2 h-12 px-4 py-2 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] hover:from-[#2563eb] hover:to-[#1d4ed8] text-white rounded-r-xl border border-l-0 border-[#1a2c38] shadow-lg hover:shadow-xl font-semibold transition-all duration-200 text-sm">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12h2" />
        </svg>
        <span>{{ __('Кошелек') }}</span>
    </button>
</div>