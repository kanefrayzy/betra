<div class="flex items-center space-x-2 md:space-x-3">
    <div class="relative">
        <!-- Mobile Button -->
        <div class="md:hidden flex items-center justify-between h-12 bg-[#181b21] text-white px-3 py-2 rounded-xl border border-[#2a3441] shadow-lg w-[180px]">
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

            <span class="text-[#ffb300] text-sm font-bold uppercase">{{ $selectedCurrency }}</span>
        </div>

        <!-- Desktop Button -->
        <div class="hidden md:flex items-center h-14 bg-[#181b21] text-white px-4 py-3 rounded-2xl border border-[#2a3441] justify-between">
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
