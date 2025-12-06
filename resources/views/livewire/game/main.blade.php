<div class="p-5 min-h-screen" x-data @open-register-modal.window="window.dispatchEvent(new CustomEvent('open-register-modal'))">
    <x-UI.search/>
    <x-UI.filter :providers="$providers"/>

    <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-9 2xl:grid-cols-9 gap-2 md:gap-2"
         wire:loading.class="opacity-50"
         wire:target="toggleFavorite, query, filterByProviders, loadMore">
        @foreach($games as $key => $game)
            <div class="game-card relative group"
                 x-data="{ showActions: false }"
                 wire:key="game-{{ $game->id }}">
                <div class="relative rounded-xl overflow-hidden bg-[#1a2c38] border border-[#2d3748] hover:border-[#3b82f6]/50 transition-all duration-300 hover:scale-[1.02] cursor-pointer shadow-lg"
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

                        <div class="absolute top-2 left-2 flex flex-col gap-1.5 z-10">
                            @if(isset($game->is_new) && $game->is_new)
                            <span class="px-2 py-0.5 bg-[#4dda30] text-white text-xs font-bold rounded shadow-lg">NEW</span>
                            @endif
                            @if(isset($game->is_higher) && $game->is_higher > 0.5)
                            <span class="px-2 py-0.5 bg-[#3b82f6] text-white text-xs font-bold rounded shadow-lg">HIGH RTP</span>
                            @endif
                        </div>

                        <div class="absolute top-2 right-2 z-10">
                            <button wire:click="toggleFavorite({{ $game->id }})"
                                    class="w-9 h-9 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-lg"
                                    >
                                @if($this->isFavorite($game->id))
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 hover:text-red-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                @endif
                            </button>
                        </div>


                        <div x-show="showActions"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="absolute inset-0 bg-black/85 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2.5"
                             >
                            <a href="{{ route('slots.play', $game->slug) }}"
                               class="w-full py-3 bg-[#4dda30] hover:bg-[#3bb825] text-white rounded-lg text-sm font-bold text-center transition-all shadow-lg shadow-[#4dda30]/20"
                               @click="if (!requireAuth(null, $event)) return false">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                                {{__('Играть')}}
                            </a>
                            <a href="{{ route('slots.fun', $game->slug) }}"
                               class="w-full py-3 bg-[#1a2c38] hover:bg-[#2d3748] text-white rounded-lg text-sm font-semibold text-center border border-[#2d3748] transition-all"
                               >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{__('Демо')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($hasMore)
    <div class="mt-10 flex flex-col items-center gap-4">
        <div class="text-gray-400 text-sm">
            {{ __('Показано') }} 
            <span class="text-white font-semibold">{{ count($games) }}</span> 
            {{ __('из') }} 
            <span class="text-white font-semibold">{{ $totalGames }}</span> 
            {{ __('игр') }}
        </div>

        <button wire:click="loadMore"
                wire:loading.attr="disabled"
                class="px-8 py-3 bg-[#3b82f6] hover:bg-[#2563eb] text-white rounded-xl font-bold text-base transition-all duration-200 shadow-lg shadow-[#3b82f6]/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            <span wire:loading.remove wire:target="loadMore">
                {{__('Показать еще')}}
            </span>
            <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{__('Загрузка...')}}
            </span>
        </button>
    </div>
    @else
        @if(count($games) > 0)
        <div class="mt-10 text-center">
            <div class="inline-flex items-center gap-2 text-gray-500 text-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{__('Все игры загружены')}} ({{ $totalGames }} {{ __('игр') }})
            </div>
        </div>
        @endif
    @endif

    @if(count($games) == 0)
    <div class="mt-20 text-center">
        <div class="inline-flex flex-col items-center gap-4">
            <div class="w-20 h-20 bg-[#1a2c38] rounded-2xl flex items-center justify-center border border-[#2d3748]">
                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-white text-lg font-bold mb-1">{{__('Игры не найдены')}}</h3>
                <p class="text-gray-500 text-sm">{{__('Попробуйте изменить параметры поиска')}}</p>
            </div>
        </div>
    </div>
    @endif
</div>