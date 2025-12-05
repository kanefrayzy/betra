<div class="p-2 min-h-screen">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-3">
            <svg class="w-8 h-8 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"/>
            </svg>
            {{ __('Популярные игры') }}
        </h1>
        <p class="text-gray-400">{{ __('Топ-20 самых любимых игр нашего сообщества') }}</p>
    </div>

    <div id="lobby">
        <div class="p-5 min-h-screen" x-data @open-register-modal.window="window.dispatchEvent(new CustomEvent('open-register-modal'))">


    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 md:gap-4"
             wire:loading.class="opacity-50"
             wire:target="toggleFavorite">
            @foreach($games as $key => $game)
                @if($game && $game->id)
                    <div class="game-card relative group"
                         x-data="{ showActions: false }"
                         wire:key="popular-game-{{ $game->id }}">
                        <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-[#ffb300]/50 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                             @click="showActions = !showActions"
                             @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                             @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                            <div class="relative aspect-[3/4]">
                                <picture>
                                    <source srcset="{{ webp_url($game->image) }}" type="image/webp">
                                    <img src="{{ $game->image }}"
                                         alt="{{ $game->name }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </picture>

                                <div class="absolute top-2 left-2 right-2 flex items-start justify-between gap-2">
                                    <div class="flex flex-col gap-1">
                                        <div class="px-2 py-0.5 bg-gradient-to-r from-[#ffb300] to-orange-500 text-black text-xs font-bold rounded flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            #{{ $key + 1 }}
                                        </div>

                                        @if(isset($game->is_new) && $game->is_new)
                                        <span class="px-2 py-0.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded">NEW</span>
                                        @endif

                                        @if(isset($game->is_higher) && $game->is_higher > 0.5)
                                        <span class="px-2 py-0.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-black text-xs font-bold rounded">HIGH RTP</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-col items-end gap-1">
                                        @php $favoriteCount = $this->getFavoriteCount($game->id); @endphp
                                        @if($favoriteCount > 0)
                                        <div class="px-2 py-0.5 bg-black/60 backdrop-blur-sm text-white text-xs rounded flex items-center gap-1">
                                            <svg class="w-3 h-3 text-[#f43f5e]" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $favoriteCount }}
                                        </div>
                                        @endif

                                        <button wire:click="toggleFavorite({{ $game->id }})"
                                                class="w-9 h-9 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center transition-all duration-200 hover:scale-110 z-20"
                                                @click.stop>
                                            @if($this->isFavorite($game->id))
                                                <svg class="w-5 h-5 text-[#f43f5e]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-300 hover:text-[#f43f5e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </div>

                                <div class="absolute top-[4.5rem] left-0">
                                    <span class="px-3 py-1 bg-dark-800/80 backdrop-blur-sm text-white text-xs font-medium rounded-r-lg border border-dark-700/50">{{ $game->provider }}</span>
                                </div>

                                <div x-show="showActions"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                     @click.stop>

                                    <div class="w-full bg-dark-900/80 rounded-lg p-2 mb-2 text-xs text-white">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-gray-400">{{ __('В избранном у') }}:</span>
                                            <span class="font-semibold text-[#ffb300]">{{ $favoriteCount }} {{ __('ч') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400">{{ __('Рейтинг') }}:</span>
                                            <span class="font-semibold text-[#ffb300]">#{{ $key + 1 }}</span>
                                        </div>
                                    </div>

                                    <a href="{{ route('slots.play', $game->slug ?? $game->name) }}"
                                       
                                       class="w-full py-2.5 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all"
                                       @click.stop="if (!requireAuth(null, $event)) return false">
                                        {{__('Играть')}}
                                    </a>
                                    <a href="{{ route('slots.fun', $game->slug ?? $game->name) }}"
                                       class="w-full py-2.5 bg-dark-700/80 hover:bg-dark-600/80 text-white rounded-lg text-sm font-medium text-center border border-dark-600 transition-all"
                                       @click.stop>
                                        {{__('Демо')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        @if($games->isEmpty())
            <div class="flex flex-col items-center justify-center py-16">
                <div class="w-24 h-24 bg-dark-800/60 rounded-full flex items-center justify-center mb-4 border border-dark-700/50">
                    <svg class="w-12 h-12 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">{{ __('Пока нет популярных игр') }}</h3>
                <p class="text-gray-400 text-center max-w-md">
                    {{ __('Станьте первым, кто добавит игру в избранное! Ваш выбор поможет другим игрокам найти лучшие игры.') }}
                </p>
            </div>
        @endif
    </div>
</div>
