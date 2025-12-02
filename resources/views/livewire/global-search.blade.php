<div class="relative w-full" x-data="{ focused: false }">
    <!-- Поле поиска -->
    <div class="relative overflow-hidden rounded-lg bg-[#1e2329] border border-gray-800 shadow-lg transition-all duration-300"
         :class="focused ? 'border-[#ffb300] shadow-[#ffb300]/20' : 'hover:border-gray-700'">
        <div class="relative flex items-center group">
            <!-- Иконка поиска -->
            <div class="absolute left-3 transition-colors duration-300 z-10"
                 :class="focused ? 'text-[#ffb300]' : 'text-gray-400'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Input -->
            <input type="text"
                   wire:model.live.debounce.300ms="query"
                   @focus="focused = true"
                   @blur="focused = false"
                   placeholder="{{ __('Поиск игр...') }}"
                   class="w-full h-11 pl-10 pr-10 bg-transparent text-white text-sm placeholder-gray-500 border-0 focus:outline-none focus:ring-0"
                   autocomplete="off">

            <!-- Кнопка очистки -->
            @if($query)
                <button wire:click="clearSearch"
                        class="absolute right-3 text-gray-400 hover:text-white transition-all duration-200 hover:scale-110 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif

            <!-- Loading индикатор -->
            <div wire:loading wire:target="query"
                 class="absolute right-3 z-10">
                <svg class="animate-spin h-4 w-4 text-[#ffb300]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Результаты поиска -->
    @if($showResults && count($results) > 0)
        <div class="absolute z-[100] w-full mt-2 bg-[#1e2329] border border-gray-800 rounded-lg shadow-2xl overflow-hidden"
             x-data
             @click.away="$wire.clearSearch()">

            <!-- Header результатов -->
            <div class="px-3 py-2.5 border-b border-gray-800 bg-gradient-to-r from-[#ffb300]/5 to-transparent">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-white">
                        {{ __('Найдено') }}: {{ count($results) }}
                    </span>
                    <button wire:click="clearSearch"
                            class="text-xs text-gray-400 hover:text-white transition-colors">
                        {{ __('Закрыть') }}
                    </button>
                </div>
            </div>

            <!-- Список результатов -->
            <div class="max-h-80 overflow-y-auto custom-scrollbar">
                @foreach($results as $game)
                    <a href="{{ route('slots.play', $game->slug) }}"
                       wire:navigate
                       wire:click="clearSearch"
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 p-3 hover:bg-gray-800/50 transition-all duration-200 border-b border-gray-800 last:border-b-0 group">

                        <!-- Изображение игры -->
                        <div class="relative flex-shrink-0">
                            <img src="{{ $game->image }}"
                                 alt="{{ $game->name }}"
                                 class="w-14 h-14 object-cover rounded-lg group-hover:scale-105 transition-transform duration-200">

                            <!-- Play overlay -->
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 rounded-lg flex items-center justify-center transition-opacity duration-200">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Информация об игре -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-white font-semibold text-xs mb-1 truncate group-hover:text-[#ffb300] transition-colors">
                                {{ $game->name }}
                            </h4>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-gray-400 text-[10px]">{{ $game->provider }}</span>
                                @if($game->is_new)
                                    <span class="px-1.5 py-0.5 bg-green-500/10 text-green-400 text-[9px] font-semibold rounded">
                                        NEW
                                    </span>
                                @endif
                                @if($game->is_hot)
                                    <span class="px-1.5 py-0.5 bg-red-500/10 text-red-400 text-[9px] font-semibold rounded">
                                        HOT
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Стрелка -->
                        <div class="flex-shrink-0 text-gray-600 group-hover:text-[#ffb300] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    @endif

    <!-- Empty State -->
    @if($showResults && $query && count($results) == 0)
        <div class="absolute z-[100] w-full mt-2 bg-[#1e2329] border border-gray-800 rounded-lg shadow-2xl overflow-hidden"
             x-data
             @click.away="$wire.clearSearch()">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>


                <h3 class="text-white font-semibold text-xs mb-1">
                    {{ __('Игры не найдены') }}
                </h3>
                <p class="text-gray-500 text-[10px] mb-3">
                    {{ __('Попробуйте изменить поисковый запрос') }}
                </p>

                <!-- Кнопка очистки -->
                <button wire:click="clearSearch"
                        class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">
                    {{ __('Очистить поиск') }}
                </button>
            </div>
        </div>
    @endif
</div>
