@auth
<div x-data="{
    open: false,
    type: 'rank',
    openAccordion: null,
    favorited: false
}"
     @open-rank-modal.window="open = true"
     @close-rank-modal.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/85"
         @click="open = false"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-lg bg-[#0f212e] rounded-2xl shadow-2xl">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#1a2c38]">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <h2 class="text-lg font-bold text-white">{{ __('Система Уровней') }}</h2>
                </div>
                <button @click="open = false" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex border-b border-[#1a2c38]">
                <button @click="type = 'rank'"
                        class="flex-1 py-3.5 text-sm font-semibold transition"
                        :class="type === 'rank' ? 'text-white bg-[#1a2c38]' : 'text-gray-400 hover:text-gray-300'">
                    {{ __('Прогресс') }}
                </button>
                <button @click="type = 'ranks'"
                        class="flex-1 py-3.5 text-sm font-semibold transition"
                        :class="type === 'ranks' ? 'text-white bg-[#1a2c38]' : 'text-gray-400 hover:text-gray-300'">
                    {{ __('Все уровни') }}
                </button>
            </div>

            <div class="p-5 max-h-[500px] overflow-y-auto">
                <div x-show="type === 'rank'" x-transition>
                    <div class="relative bg-[#1a2c38] rounded-xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.4)] mb-4">

                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#0f212e] ring-2 ring-[#2d3748]">
                                @if($u->avatar)
                                    <img src="{{ $u->avatar }}" alt="{{ $u->username }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#ffb300] to-[#ff8c00]">
                                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-white font-bold text-lg mb-0.5">{{ $u->username }}</h4>
                                <p class="text-gray-400 text-xs">
                                    {{ __('Оборот') }}: <span class="text-[#ffb300] font-semibold">{{ moneyFormat(toUSD($u->oborot, $u->currency->symbol)) }} {{ $u->currency->symbol }}</span>
                                </p>
                            </div>
                            @if ($current_rank)
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-lg bg-[#0f212e] flex items-center justify-center mb-1">
                                    <img src="{{ asset('storage/' . $current_rank->picture) }}" alt="{{ $current_rank->name }}" class="w-7 h-7" loading="lazy">
                                </div>
                                <span class="text-[#ffb300] font-bold text-xs">LVL {{ $current_rank->id }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-gray-300 text-sm font-medium">{{ __('Ваш ВИП-прогресс') }}</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[#ffb300] font-bold text-lg">{{ $percentage }}%</span>
                                <button class="text-gray-400 hover:text-gray-300 transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <div class="h-2.5 bg-[#0f212e] rounded-full overflow-hidden">
                                <div class="h-full bg-[#4edc30] rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $current_rank->picture) }}" alt="{{ $current_rank->name }}" class="w-5 h-5" loading="lazy">
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-xs text-gray-400 font-medium">{{ $current_rank->name }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-xs text-gray-400 font-medium">
                                        @if ($next_rank)
                                            {{ $next_rank->name }}
                                        @else
                                            MAX
                                        @endif
                                    </span>
                                </div>
                                <div class="w-8 h-8 rounded-lg border-2 border-[#2d3748] flex items-center justify-center">
                                    @if ($next_rank)
                                        <img src="{{ asset('storage/' . $next_rank->picture) }}" alt="{{ $next_rank->name }}" class="w-5 h-5" loading="lazy">
                                    @else
                                        <span class="text-[#ffb300] text-xs font-bold">★</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-[#0f212e] rounded-lg p-3">
                                <p class="text-gray-500 text-xs mb-1">{{ __('Осталось') }}</p>
                                <p class="text-[#ffb300] font-bold text-sm">
                                    @if ($next_rank)
                                        {{ moneyFormat(toUSD($next_rank->oborot_min - $u->oborot, $u->currency->symbol)) }} {{ $u->currency->symbol }}
                                    @else
                                        0 {{ $u->currency->symbol }}
                                    @endif
                                </p>
                            </div>
                            <div class="bg-[#0f212e] rounded-lg p-3">
                                <p class="text-gray-500 text-xs mb-1">{{ __('Следующий уровень') }}</p>
                                <p class="text-white font-bold text-sm">
                                    @if ($next_rank)
                                        {{ $next_rank->name }}
                                    @else
                                        MAX
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($current_rank)
                    <div class="bg-[#1a2c38] rounded-xl p-4 border border-[#2d3748]">
                        <button @click="openAccordion = openAccordion === 'benefits' ? null : 'benefits'" class="w-full flex items-center justify-between mb-3">
                            <h5 class="text-white font-semibold text-sm">{{ __('Привилегии уровня ВИП') }}</h5>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openAccordion === 'benefits' && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openAccordion === 'benefits'" x-collapse class="space-y-2.5">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-400 text-sm">{{ __('Ежедневный бонус') }}</span>
                                <span class="text-white font-semibold text-sm">
                                    {{ moneyFormat(toUSD($current_rank->daily_min, $u->currency->symbol)) }} - {{ moneyFormat(toUSD($current_rank->daily_max, $u->currency->symbol)) }} {{ $u->currency->symbol }}
                                </span>
                            </div>
                            @if ($current_rank->rakeback)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-400 text-sm">{{ __('Rakeback') }}</span>
                                <span class="text-[#ffb300] font-bold text-sm">{{ $current_rank->rakeback }}%</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <div x-show="type === 'ranks'" x-transition class="space-y-2.5">
                    @foreach ($ranks as $rank)
                    <div class="bg-[#1a2c38] rounded-xl border border-[#2d3748] hover:border-[#ffb300]/40 transition"
                         x-data="{ open: {{ $current_rank && $current_rank->id == $rank->id ? 'true' : 'false' }} }">

                        <div @click="open = !open" class="flex items-center justify-between p-4 cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#0f212e] flex items-center justify-center ring-1 ring-[#2d3748]">
                                    <img src="{{ asset('storage/' . $rank->picture) }}" alt="{{ $rank->name }}" class="w-6 h-6" loading="lazy">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-white font-semibold text-sm">{{ $rank->name }}</span>
                                        @if($current_rank && $current_rank->id == $rank->id)
                                            <span class="px-2 py-0.5 text-xs bg-[#ffb300] text-black rounded-md font-bold">{{ __('Текущий') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-500 text-xs">{{ moneyFormat(toUSD($rank->oborot_min, $u->currency->symbol)) }} {{ $u->currency->symbol }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($rank->rakeback)
                                    <span class="text-[#ffb300] font-bold text-sm">{{ $rank->rakeback }}%</span>
                                @endif
                                <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        <div x-show="open" x-collapse class="px-4 pb-4 border-t border-[#2d3748]">
                            <div class="pt-3 space-y-2.5">
                                <div class="flex justify-between text-sm py-1.5">
                                    <span class="text-gray-400">{{ __('Ежедневный бонус') }}</span>
                                    <span class="text-white font-semibold">{{ moneyFormat(toUSD($rank->daily_min, $u->currency->symbol)) }} - {{ moneyFormat(toUSD($rank->daily_max, $u->currency->symbol)) }} {{ $u->currency->symbol }}</span>
                                </div>
                                @if ($rank->rakeback)
                                <div class="flex justify-between text-sm py-1.5">
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