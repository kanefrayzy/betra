<div class="p-5 min-h-screen" x-data @open-register-modal.window="window.dispatchEvent(new CustomEvent('open-register-modal'))">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white mb-2">{{ __('История игр') }}</h1>
        <p class="text-gray-400">{{ __('Ваши последние игровые сессии') }}</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 md:gap-4">
        @foreach($games as $game)
            @if($game && $game->id)
                <div class="game-card relative group"
                     x-data="{ showActions: false }"
                     wire:key="history-game-{{ $game->id }}">
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

                            <!-- Бейджи в верхнем левом углу -->
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                <!-- Индикатор истории -->
                                <span class="px-2 py-0.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-xs font-bold rounded flex items-center gap-1">
                                    <i class="fas fa-clock-rotate-left"></i> ИСТОРИЯ
                                </span>
                                @if(isset($game->is_new) && $game->is_new)
                                <span class="px-2 py-0.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded">NEW</span>
                                @endif
                                @if(isset($game->is_higher) && $game->is_higher > 0.5)
                                <span class="px-2 py-0.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-black text-xs font-bold rounded">HIGH RTP</span>
                                @endif
                            </div>

                            <!-- Избранное справа вверху -->
                            <div class="absolute top-2 right-2">
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
                                <a href="{{ route('slots.play', $game->slug ?? $game->name) }}"
                                   wire:navigate
                                   class="w-full py-2.5 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all"
                                   @click.stop="if (!requireAuth(null, $event)) return false">
                                    {{__('Играть снова')}}
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
            <div class="w-24 h-24 bg-dark-800 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-history text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">{{ __('История пуста') }}</h3>
            <p class="text-gray-400 text-center max-w-md">
                {{ __('Здесь будут отображаться игры, в которые вы играли. Начните играть, чтобы увидеть историю!') }}
            </p>
            <a
                href="/"
                wire:navigate
                class="mt-6 bg-[#ffb300] hover:bg-[#f5a300] text-black py-3 px-6 rounded-lg font-bold transition-all duration-200 flex items-center"
            >
                <i class="fas fa-gamepad mr-2"></i>{{ __('Перейти к играм') }}
            </a>
        </div>
    @endif
</div>
