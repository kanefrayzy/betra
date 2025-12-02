<x-layouts.app>
    <div class="min-h-screen bg-[#0f1419] py-4 sm:py-8 md:py-12">
        <div class="max-w-2xl mx-auto px-3 sm:px-4">
            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-[#ffb300]/10 rounded-xl sm:rounded-2xl mb-3 sm:mb-4">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2 sm:mb-4">
                    {{ __('Ежедневный бонус')}}
                </h1>
                <p class="text-gray-400 text-sm sm:text-base">
                    {{ __('Ваш текущий ранг:')}}
                </p>
            </div>

            <!-- Main Card -->
            <div class="bg-[#1e2329] rounded-xl sm:rounded-2xl border border-gray-800 overflow-hidden mb-4 sm:mb-6">
                <!-- Rank Image Section -->
                @if($rank->picture)
                <div class="bg-gradient-to-r from-[#ffb300]/10 to-transparent p-4 sm:p-6 md:p-8 border-b border-gray-800">
                    <div class="flex justify-center">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 bg-[#ffb300]/10 rounded-xl sm:rounded-2xl flex items-center justify-center p-3 sm:p-4">
                            <img src="{{ asset('storage/' . $rank->picture) }}" 
                                 alt="Rank" 
                                 class="w-full h-full object-contain">
                        </div>
                    </div>
                </div>
                @endif

                <!-- Bonus Info Section -->
                <div class="p-4 sm:p-6 md:p-8">
                    <div class="bg-[#16181d] rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-800 mb-4 sm:mb-6">
                        <div class="text-center">
                            <p class="text-gray-400 text-xs sm:text-sm mb-3 sm:mb-4">
                                {{ __('Вы можете получить ежедневный бонус в размере:')}}
                            </p>
                            <div class="flex items-center justify-center gap-2 sm:gap-3 flex-wrap">
                                <div class="bg-gray-800/50 rounded-lg px-3 py-2 sm:px-4 sm:py-3 min-w-[100px] sm:min-w-[120px]">
                                    <p class="text-xs text-gray-400 mb-1">{{ __('От')}}</p>
                                    <p class="text-[#ffb300] font-bold text-base sm:text-xl">
                                        {{ moneyFormat(toUSD($rank->daily_min, $u->currency->symbol))}} {{ $u->currency->symbol}}
                                    </p>
                                </div>
                                <span class="text-gray-600 text-base sm:text-xl">—</span>
                                <div class="bg-gray-800/50 rounded-lg px-3 py-2 sm:px-4 sm:py-3 min-w-[100px] sm:min-w-[120px]">
                                    <p class="text-xs text-gray-400 mb-1">{{ __('До')}}</p>
                                    <p class="text-[#ffb300] font-bold text-base sm:text-xl">
                                        {{ moneyFormat(toUSD($rank->daily_max, $u->currency->symbol))}} {{ $u->currency->symbol}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Claim Button -->
                    <form action="{{ route('daily-bonus.claim') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <button type="submit" 
                                class="w-full bg-[#ffb300] hover:bg-[#ffc633] active:bg-[#e6a200] text-black font-bold py-3 sm:py-4 rounded-lg sm:rounded-xl transition-colors duration-200 text-base sm:text-lg">
                            {{ __('Получить бонус')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
