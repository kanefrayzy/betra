<div class="p-5 min-h-screen" x-data @open-register-modal.window="window.dispatchEvent(new CustomEvent('open-register-modal'))">
    <x-UI.search/>
    <x-UI.filter :providers="$providers"/>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 md:gap-4"
         wire:loading.class="opacity-50"
         wire:target="toggleFavorite, query, filterByProviders, loadMore">
        @foreach($games as $key => $game)
            <div class="game-card relative group"
                 x-data="{ showActions: false }"
                 wire:key="game-{{ $game->id }}">
                <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                     @click="showActions = !showActions"
                     @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                     @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                    <div class="relative aspect-[3/4]">
                        <img src="{{ $game->image }}"
                             alt="{{ $game->name }}"
                             class="w-full h-full object-cover"
                             loading="lazy">

                        <!-- Бейджи в верхнем левом углу -->
                        <div class="absolute top-2 left-2 flex flex-col gap-1">
                            @if(isset($game->is_new) && $game->is_new)
                            <span class="px-2 py-0.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded">NEW</span>
                            @endif
                            @if(isset($game->is_higher) && $game->is_higher > 0.5)
                            <span class="px-2 py-0.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-black text-xs font-bold rounded">HIGH RTP</span>
                            @endif
                        </div>

                        <!-- Провайдер и Избранное в одной линии -->
                        <div class="absolute top-2 left-2 right-2 flex items-center justify-between">
                            <!-- Провайдер слева -->
                            <span class="px-2 py-1 bg-dark-800/80 backdrop-blur-sm text-white text-xs font-medium rounded-md border border-dark-700/50">{{ $game->provider }}</span>

                            <!-- Избранное справа -->
                            <button wire:click="toggleFavorite({{ $game->id }})"
                                    class="w-9 h-9 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center transition-all duration-200 hover:scale-110 z-20"
                                    @click.stop>
                                @if($this->isFavorite($game->id))
                                    <i class="fas fa-heart text-[#f43f5e] text-base"></i>
                                @else
                                    <i class="far fa-heart text-gray-300 hover:text-[#f43f5e] text-base"></i>
                                @endif
                            </button>
                        </div>

                        <!-- Оверлей с кнопками -->
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
                                {{__('Играть')}}
                            </a>
                            {{-- <a href="{{ route('slots.fun', $game->slug) }}"
                               class="w-full py-2.5 bg-dark-700/80 hover:bg-dark-600/80 text-white rounded-lg text-sm font-medium text-center border border-dark-600 transition-all"
                               @click.stop>
                                {{__('Демо')}}
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Кнопка "Показать еще" -->
    @if($hasMore)
    <div class="mt-10 flex flex-col items-center gap-3">
        <!-- Информация о загруженных играх -->
        <div class="text-gray-400 text-sm">
            {{ __('Показано') }} <span class="text-white font-semibold">{{ count($games) }}</span> {{ __('из') }} <span class="text-white font-semibold">{{ $totalGames }}</span> {{ __('игр') }}
        </div>

        <!-- Кнопка загрузки -->
        <button wire:click="loadMore"
                wire:loading.attr="disabled"
                class="px-8 py-3 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg font-bold text-base transition-all duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            <span wire:loading.remove wire:target="loadMore">
                {{__('Показать еще')}}
            </span>
            <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>

            </span>
        </button>
    </div>
    @else
    <!-- Сообщение когда все игры загружены -->
    @if(count($games) > 0)
    <div class="mt-10 text-center text-gray-400">
        <p>Все игры загружены ({{ $totalGames }} {{ __('игр') }})</p>
    </div>
    @endif
    @endif

    <!-- Сообщение если игр нет -->
    @if(count($games) == 0)
    <div class="mt-10 text-center text-gray-400">
        <p>{{__('Игры не найдены')}}</p>
    </div>
    @endif
</div>
