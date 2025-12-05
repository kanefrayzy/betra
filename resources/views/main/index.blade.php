<x-layouts.app>
  @include('components.slider')
      <div class="container mx-auto px-2 md:px-4 relative z-2 mt-2">
          @livewire('home-search')
      </div>
    <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-4 lg:py-2" x-data="{ activeTab: 'all' }">

        <div class="flex items-center gap-2 mb-4 overflow-x-auto scrollbar-hide bg-[#0f212e] p-2 rounded-full max-w-full">
            <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-[#40c920] text-black shadow-lg' : 'bg-transparent text-gray-400 hover:text-white hover:bg-[#1a2c38]'" class="flex items-center gap-2 px-5 py-3 rounded-full font-semibold whitespace-nowrap transition-all duration-200 text-sm">
                <svg class="w-4 h-4" data-ds-icon="AllGames" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <path fill="currentColor" d="M9.08 1H3a2 2 0 0 0-2 2v6.08a2 2 0 0 0 2 2h6.08a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2M21 1h-6.08a2 2 0 0 0-2 2v6.08a2 2 0 0 0 2 2H21a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2M9.08 12.92H3a2 2 0 0 0-2 2V21a2 2 0 0 0 2 2h6.08a2 2 0 0 0 2-2v-6.08a2 2 0 0 0-2-2m11.92 0h-6.08a2 2 0 0 0-2 2V21a2 2 0 0 0 2 2H21a2 2 0 0 0 2-2v-6.08a2 2 0 0 0-2-2"></path>
                </svg>
                {{__('Все игры')}}
            </button>
            
            @auth
            @if(isset($recentGames) && $recentGames->isNotEmpty())
            <button @click="activeTab = 'recent'" :class="activeTab === 'recent' ? 'bg-[#8b5cf6] text-white shadow-lg' : 'bg-transparent text-gray-400 hover:text-white hover:bg-[#1a2c38]'" class="flex items-center gap-2 px-5 py-3 rounded-full font-semibold whitespace-nowrap transition-all duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('Недавние') }}
            </button>
            @endif
            @endauth
            @foreach($categories as $category)
            <button @click="activeTab = '{{ $category->slug }}'" 
                    :class="activeTab === '{{ $category->slug }}' ? 'text-black shadow-lg' : 'bg-transparent text-gray-400 hover:text-white hover:bg-[#1a2c38]'"
                    :style="activeTab === '{{ $category->slug }}' ? 'background-color: {{ $category->color }}' : ''"
                    class="flex items-center gap-2 px-5 py-3 rounded-full font-semibold whitespace-nowrap transition-all duration-200 text-sm">
                @if($category->icon)
                    <div class="w-4 h-4">
                        {!! $category->icon !!}
                    </div>
                @endif
                {{ $category->name }}
            </button>
            @endforeach
        </div>

        <div class="relative">

        @auth
        @if(isset($recentGames) && $recentGames->isNotEmpty())
        <div x-show="activeTab === 'all' || activeTab === 'recent'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('Недавние игры') }}
                </h2>
                <a href="{{ route('slots.history') }}" class="text-[#8b5cf6] hover:text-[#7c3aed] text-sm font-medium transition-colors">
                    {{ __('Все') }} →
                </a>
            </div>

            <div id="recent-slider" class="flex gap-2.5 overflow-x-auto scrollbar-hide snap-x snap-mandatory pt-1 pb-2" style="-webkit-overflow-scrolling: touch;">
                @foreach($recentGames as $game)
                <div class="flex-shrink-0 w-36 sm:w-40 snap-start group" x-data="{ showActions: false }" style="touch-action: manipulation;">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                         @click="showActions = !showActions"
                         @touchend.prevent="if (window.innerWidth < 768) showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <x-optimized-image 
                                :src="$game->image" 
                                :alt="$game->title ?? $game->name"
                                width="176"
                                height="235"
                                class="w-full h-full object-cover"
                            />

                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-0.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-xs font-bold rounded flex items-center gap-1">
                                    <i class="fas fa-clock-rotate-left"></i>
                                </span>
                            </div>

                            <div x-show="showActions"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                 @click.stop>
                                <a href="{{ route('slots.play', $game->slug) }}"
                                   @guest onclick="event.preventDefault(); openLoginModal(); return false;" @endguest
                                   class="w-full py-2.5 bg-[#40c920] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all">
                                    {{__('Играть')}}
                                </a>
                                <a href="{{ route('slots.fun', $game->slug) }}"
                                   class="w-full py-2.5 bg-dark-700/80 hover:bg-dark-600/80 text-white rounded-lg text-sm font-medium text-center border border-dark-600 transition-all">
                                    {{__('Демо')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endauth

        @foreach($categories as $category)
        <div x-show="activeTab === 'all' || activeTab === '{{ $category->slug }}'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="mb-4"
             :class="{ 'absolute inset-x-0 top-0': activeTab !== 'all' && activeTab !== '{{ $category->slug }}' }">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    @if($category->icon)
                        <div class="w-5 h-5" style="color: {{ $category->color }}">
                            {!! $category->icon !!}
                        </div>
                    @endif
                    {{ $category->name }}
                </h2>
                <div class="inline-flex border-2 border-gray-700/30 rounded-full overflow-hidden">
                    <button id="{{ $category->slug }}-prev" class="w-14 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800/50 transition-all border-r border-gray-700/20">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                    </button>
                    <button id="{{ $category->slug }}-next" class="w-14 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800/50 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    </button>
                </div>
            </div>

            <div id="{{ $category->slug }}-slider" class="flex gap-2.5 overflow-x-auto scrollbar-hide snap-x snap-mandatory pt-1 pb-2" style="-webkit-overflow-scrolling: touch;">
                @foreach($category->activeGames as $game)
                <div class="flex-shrink-0 w-36 sm:w-40 snap-start group" x-data="{ showActions: false }" style="touch-action: manipulation;">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                         @click="showActions = !showActions"
                         @touchend.prevent="if (window.innerWidth < 768) showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <x-optimized-image 
                                :src="$game->image" 
                                :alt="$game->title ?? $game->name"
                                width="176"
                                height="235"
                                class="w-full h-full object-cover"
                            />

                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                @if(isset($game->is_new) && $game->is_new)
                                <span class="px-2 py-0.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded">NEW</span>
                                @endif
                                @if(isset($game->is_higher) && $game->is_higher > 0.5)
                                <span class="px-2 py-0.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-black text-xs font-bold rounded">HIGH RTP</span>
                                @endif
                            </div>

                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 bg-dark-800/80 backdrop-blur-sm text-white text-xs font-medium rounded-md border border-dark-700/50">{{$game->provider}}</span>
                            </div>

                            <div x-show="showActions"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                 @click.stop>
                                <a href="{{ route('slots.play', $game->slug) }}"
                                   @guest onclick="event.preventDefault(); openLoginModal(); return false;" @endguest
                                   class="w-full py-2.5 rounded-lg text-sm font-bold text-center transition-all text-black hover:opacity-90"
                                   style="background-color: {{ $category->color }}">
                                    {{__('Играть')}}
                                </a>
                                <a href="#" onclick="handleGameClick('{{ route('slots.fun', $game->slug) }}', event)"
                                   class="w-full py-2.5 bg-dark-700/80 hover:bg-dark-600/80 text-white rounded-lg text-sm font-medium text-center border border-dark-600 transition-all">
                                    {{__('Демо')}}
                                </a>
                            </div>
                        </div>
                    </div> 
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        
        </div>
    </div>


    <x-layouts.partials.footer/>
</x-layouts.app>
