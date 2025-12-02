<div class="home-search-component lg:px-2">
    <div class="relative mb-2">
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            placeholder="{{ __('Введите название игры...') }}"
            class="w-full h-12 pl-11 pr-11 bg-dark-800/60 text-white placeholder-gray-400 border border-dark-700/50 rounded-lg focus:outline-none focus:border-[#ffb300] transition-all text-sm"
            autocomplete="off"
        >
        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        
        <!-- Loading indicator -->
        @if($isLoading)
            <div class="absolute right-12 top-1/2 -translate-y-1/2">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-[#ffb300] border-t-transparent"></div>
            </div>
        @endif
        
        @if($query)
            <button
                wire:click="clearSearch"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>

    <!-- Results Section -->
    <div class="relative">
        @if($showResults)
            <div class="search-results-enter">
                @if(count($results) > 0)
                    <div class="mb-4">
                        <p class="text-gray-400 text-sm">
                            {{ __('Найдено:') }} <span class="text-white font-semibold">{{ count($results) }}</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-8 gap-3 md:gap-4">
                        @foreach($results as $index => $game)
                            <div class="game-card relative group game-card-fade-in" style="animation-delay: {{ $index * 50 }}ms" x-data="{ showActions: false }">
                                <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                                     @click="showActions = !showActions"
                                     @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                                     @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                                    <div class="relative aspect-[3/4]">
                                        <img src="{{ $game->image }}"
                                             alt="{{ $game->name }}"
                                             class="w-full h-full object-cover"
                                             loading="lazy">

                                        <div class="absolute top-2 left-2 flex flex-col gap-1">
                                            @if($game->is_new)
                                                <span class="px-2 py-0.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded">NEW</span>
                                            @endif
                                            @if($game->is_higher)
                                                <span class="px-2 py-0.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-black text-xs font-bold rounded">HIGH RTP</span>
                                            @endif
                                        </div>

                                        <div class="absolute top-2 right-2">
                                            <span class="px-2 py-1 bg-dark-800/80 backdrop-blur-sm text-white text-xs font-medium rounded-md border border-dark-700/50">{{ $game->provider }}</span>
                                        </div>

                                        <div x-show="showActions"
                                             x-cloak
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                             @click.stop>
                                            <a href="{{ route('slots.play', $game->slug) }}"
                                               wire:navigate
                                               class="w-full py-2.5 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all"
                                               @click.stop="if (!requireAuth(null, $event)) return false">
                                                {{ __('Играть') }}
                                            </a>
                                            {{-- <a href="#"
                                               onclick="handleGameClick('{{ route('slots.fun', $game->slug) }}', event)"
                                               class="w-full py-2.5 bg-dark-700/80 hover:bg-dark-600/80 text-white rounded-lg text-sm font-medium text-center border border-dark-600 transition-all"
                                               @click.stop>
                                                {{ __('Демо') }}
                                            </a> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-dark-800/30 rounded-xl border border-dark-700/50 search-no-results">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-white text-lg font-semibold mb-2">{{ __('Игры не найдены') }}</p>
                        <p class="text-gray-400 text-sm">{{ __('Попробуйте изменить запрос') }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        .search-results-enter {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        .game-card-fade-in {
            opacity: 0;
            transform: translateY(20px) scale(0.9);
            animation: gameCardFadeIn 0.5s ease-out forwards;
        }

        .search-no-results {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gameCardFadeIn {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Smooth loading spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Staggered animation for game cards */
        .game-card:nth-child(1) { animation-delay: 0ms; }
        .game-card:nth-child(2) { animation-delay: 50ms; }
        .game-card:nth-child(3) { animation-delay: 100ms; }
        .game-card:nth-child(4) { animation-delay: 150ms; }
        .game-card:nth-child(5) { animation-delay: 200ms; }
        .game-card:nth-child(6) { animation-delay: 250ms; }
        .game-card:nth-child(7) { animation-delay: 300ms; }
        .game-card:nth-child(8) { animation-delay: 350ms; }
    </style>
</div>