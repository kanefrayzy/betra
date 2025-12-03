@auth
<div x-data="{
    open: false,
    type: 'rank',
    openAccordion: null
}"
     @open-rank-modal.window="open = true"
     @close-rank-modal.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto modaler"
     style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/80"
         @click="open = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-lg bg-[#1e2329] rounded-2xl shadow-2xl">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-800">
                <h2 class="text-xl font-bold text-white">{{ __('Система Уровней') }}</h2>
                <button @click="open = false" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- User Info -->
            <div class="p-6 bg-gradient-to-r from-[#ffb300]/10 to-transparent border-b border-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-800">
                            @if($u->avatar)
                                <img src="{{ $u->avatar }}" alt="{{ $u->username }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#ffb300]">
                                    <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-white font-bold">{{ $u->username }}</h4>
                            <p class="text-gray-400 text-sm">
                                {{ __('Оборот') }}: <span class="text-[#ffb300] font-semibold">{{ moneyFormat(toUSD($u->oborot, $u->currency->symbol)) }} {{ $u->currency->symbol }}</span>
                            </p>
                        </div>
                    </div>

                    @if ($current_rank)
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-lg bg-gray-800 border-2 border-[#ffb300] flex items-center justify-center mb-1">
                            <img src="{{ asset('storage/' . $current_rank->picture) }}" alt="{{ $current_rank->name }}" class="w-8 h-8" loading="lazy">
                        </div>
                        <span class="text-[#ffb300] font-bold text-sm">LVL {{ $current_rank->id }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-800">
                <button @click="type = 'rank'"
                        class="flex-1 py-3 text-sm font-medium transition relative"
                        :class="type === 'rank' ? 'text-white' : 'text-gray-500 hover:text-gray-300'">
                    {{ __('Прогресс') }}
                    <div x-show="type === 'rank'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                </button>
                <button @click="type = 'ranks'"
                        class="flex-1 py-3 text-sm font-medium transition relative"
                        :class="type === 'ranks' ? 'text-white' : 'text-gray-500 hover:text-gray-300'">
                    {{ __('Все уровни') }}
                    <div x-show="type === 'ranks'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 max-h-96 overflow-y-auto custom-scroll">
                <!-- Progress Tab -->
                <div x-show="type === 'rank'" x-transition>
                    <!-- Progress Card -->
                    <div class="bg-[#16181d] rounded-lg p-5 border border-gray-800 mb-4">
                        <!-- Progress Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-white font-semibold text-sm">{{ __('Прогресс до следующего уровня') }}</h3>
                            <span class="text-[#ffb300] font-bold text-lg">{{ $percentage }}%</span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="relative mb-5">
                            <div class="h-3 bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-[#ffb300] rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                            
                            <!-- Level Markers -->
                            <div class="flex justify-between my-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-[#ffb300] flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $current_rank->picture) }}" alt="{{ $current_rank->name }}" class="w-5 h-5" loading="lazy">
                                    </div>
                                    <span class="text-xs text-gray-400">LVL {{ $current_rank->id }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400">
                                        @if ($next_rank)
                                            LVL {{ $next_rank->id }}
                                        @else
                                            MAX
                                        @endif
                                    </span>
                                    <div class="w-8 h-8 rounded-lg bg-gray-800 border border-gray-700 flex items-center justify-center">
                                        @if ($next_rank)
                                            <img src="{{ asset('storage/' . $next_rank->picture) }}" alt="{{ $next_rank->name }}" class="w-5 h-5" loading="lazy">
                                        @else
                                            <span class="text-[#ffb300] text-xs font-bold">★</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-800/50 rounded-lg p-3">
                                <p class="text-gray-400 text-xs mb-1">{{ __('Осталось') }}</p>
                                <p class="text-[#ffb300] font-semibold text-sm">
                                    @if ($next_rank)
                                        {{ moneyFormat(toUSD($next_rank->oborot_min - $u->oborot, $u->currency->symbol)) }} {{ $u->currency->symbol }}
                                    @else
                                        0 {{ $u->currency->symbol }}
                                    @endif
                                </p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-3">
                                <p class="text-gray-400 text-xs mb-1">{{ __('Следующий уровень') }}</p>
                                <p class="text-white font-semibold text-sm">
                                    @if ($next_rank)
                                        {{ $next_rank->name }}
                                    @else
                                        MAX
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits -->
                    @if ($current_rank)
                    <div class="bg-gray-800/50 rounded-lg p-4">
                        <h5 class="text-white font-semibold mb-3 text-sm">{{ __('Ваши привилегии') }}</h5>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">{{ __('Ежедневный бонус') }}</span>
                                <span class="text-white font-medium text-sm">
                                    {{ moneyFormat(toUSD($current_rank->daily_min, $u->currency->symbol)) }} - {{ moneyFormat(toUSD($current_rank->daily_max, $u->currency->symbol)) }} {{ $u->currency->symbol }}
                                </span>
                            </div>
                            @if ($current_rank->rakeback)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">{{ __('Rakeback') }}</span>
                                <span class="text-[#ffb300] font-bold">{{ $current_rank->rakeback }}%</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Ranks List Tab -->
                <div x-show="type === 'ranks'" x-transition class="space-y-2">
                    @foreach ($ranks as $rank)
                    <div class="bg-gray-800/50 rounded-lg border border-gray-700 hover:border-[#ffb300]/30 transition"
                         x-data="{ open: {{ $current_rank && $current_rank->id == $rank->id ? 'true' : 'false' }} }">

                        <!-- Rank Header -->
                        <div @click="open = !open" class="flex items-center justify-between p-4 cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $rank->picture) }}" alt="{{ $rank->name }}" class="w-6 h-6" loading="lazy">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-white font-semibold text-sm">LVL {{ $rank->id }}</span>
                                        @if($current_rank && $current_rank->id == $rank->id)
                                            <span class="px-2 py-0.5 text-xs bg-[#ffb300] text-black rounded font-semibold">{{ __('Текущий') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-400 text-xs">{{ moneyFormat(toUSD($rank->oborot_min, $u->currency->symbol)) }} {{ $u->currency->symbol }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($rank->rakeback)
                                    <span class="text-[#ffb300] font-bold text-sm">{{ $rank->rakeback }}%</span>
                                @endif
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Rank Details -->
                        <div x-show="open" x-collapse class="px-4 pb-4 border-t border-gray-700">
                            <div class="pt-3 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('Ежедневный бонус') }}</span>
                                    <span class="text-white">{{ moneyFormat(toUSD($rank->daily_min, $u->currency->symbol)) }} - {{ moneyFormat(toUSD($rank->daily_max, $u->currency->symbol)) }} {{ $u->currency->symbol }}</span>
                                </div>
                                @if ($rank->rakeback)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('Rakeback') }}</span>
                                    <span class="text-[#ffb300] font-bold">{{ $rank->rakeback }}%</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openRankModal() {
    window.dispatchEvent(new CustomEvent('open-rank-modal'));
}

function closeRankModal() {
    window.dispatchEvent(new CustomEvent('close-rank-modal'));
}
</script>
@endauth
