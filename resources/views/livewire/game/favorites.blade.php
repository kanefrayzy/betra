<div class="p-5 min-h-screen" x-data @open-register-modal.window="window.dispatchEvent(new CustomEvent('open-register-modal'))">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-3">
          <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
          </svg>
            {{ __('Избранные игры') }}
        </h1>
        <p class="text-gray-400">{{ __('Ваши избранные слоты') }}</p>
    </div>

    <div id="lobby">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 md:gap-4"
             wire:loading.class="opacity-50"
             wire:target="toggleFavorite">
            @foreach($games as $key => $game)
                @if($game && $game->id)
                    <div class="game-card relative group"
                         x-data="{ showActions: false }"
                         wire:key="game-{{ $game->id }}">
                        <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
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

                                <div class="absolute top-2 left-2 right-2 flex items-center justify-between">
                                    <div class="flex flex-col gap-1">
                                        @if(isset($game->is_new) && $game->is_new)
                                        <span class="px-2 py-0.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded">NEW</span>
                                        @endif
                                        @if(isset($game->is_higher) && $game->is_higher > 0.5)
                                        <span class="px-2 py-0.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-black text-xs font-bold rounded">HIGH RTP</span>
                                        @endif
                                    </div>

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

                                <div class="absolute top-12 left-0">
                                    <span class="px-3 py-1 bg-dark-800/80 backdrop-blur-sm text-white text-xs font-medium rounded-r-lg border border-dark-700/50">{{ $game->provider }}</span>
                                </div>

                                <div x-show="showActions"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                     @click.stop>
                                    <a href="{{ route('slots.play', $game->slug ?? $game->name) }}"
                                       wire:navigate
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
                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">{{ __('Игры не найдены') }}</h3>
                <p class="text-gray-400 text-center max-w-md">
                    {{ __('В данный момент нет доступных игр. Попробуйте позже!') }}
                </p>
            </div>
        @endif
    </div>
</div>
