<x-layouts.app>
  @include('components.slider')
      <div class="container mx-auto px-2 md:px-4 relative z-2 mt-2">
          @livewire('home-search')
      </div>

    <!-- Games Section with Dynamic Categories -->
    @if(isset($categories) && $categories->isNotEmpty())
    <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-4 lg:py-2" x-data="{ activeTab: 'all' }">
        
        <!-- Category Tabs - Dynamically Generated -->
        <div class="flex items-center gap-2 mb-2 overflow-x-auto scrollbar-hide pb-2">
            <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'" class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
                <svg  class="w-4 h-4" data-ds-icon="AllGames" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" class="inline-block shrink-0">
                    <path fill="currentColor" d="M9.08 1H3a2 2 0 0 0-2 2v6.08a2 2 0 0 0 2 2h6.08a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2M21 1h-6.08a2 2 0 0 0-2 2v6.08a2 2 0 0 0 2 2H21a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2M9.08 12.92H3a2 2 0 0 0-2 2V21a2 2 0 0 0 2 2h6.08a2 2 0 0 0 2-2v-6.08a2 2 0 0 0-2-2m11.92 0h-6.08a2 2 0 0 0-2 2V21a2 2 0 0 0 2 2H21a2 2 0 0 0 2-2v-6.08a2 2 0 0 0-2-2"></path>
                </svg>
                {{__('Все игры')}}
            </button>
            
            <!-- Recent Games Tab for Authenticated Users -->
            @auth
            @if(isset($recentGames) && $recentGames->isNotEmpty())
            <button @click="activeTab = 'recent'" :class="activeTab === 'recent' ? 'bg-[#8b5cf6] text-white' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'" class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('Недавние') }}
            </button>
            @endif
            @endauth
            @foreach($categories as $category)
            <button @click="activeTab = '{{ $category->slug }}'" 
                    :class="activeTab === '{{ $category->slug }}' ? 'text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'"
                    :style="activeTab === '{{ $category->slug }}' ? 'background-color: {{ $category->color }}' : ''"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
                @if($category->icon)
                    <div class="w-5 h-5">
                        {!! $category->icon !!}
                    </div>
                @endif
                {{ $category->name }}
            </button>
            @endforeach
        </div>

        <div class="relative">
        
        <!-- Recent Games Section for Authenticated Users -->
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

            <div id="recent-slider" class="flex gap-3 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                @foreach($recentGames as $game)
                <div class="flex-shrink-0 w-40 sm:w-44 snap-start group" x-data="{ showActions: false }">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 cursor-pointer"
                         @click="showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <img src="{{$game->image}}" 
                                 alt="{{$game->title ?? $game->name}}" 
                                 loading="lazy" 
                                 decoding="async" 
                                 width="176" 
                                 height="235" 
                                 class="w-full h-full object-cover">

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
                                   @auth wire:navigate @endauth
                                   @guest onclick="event.preventDefault(); openLoginModal(); return false;" @endguest
                                   class="w-full py-2.5 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all">
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
        
        <!-- Dynamic Category Sections -->
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
                <h2 class="text-xl font-bold text-white">{{ $category->name }}</h2>
                <div class="flex items-center gap-2">
                    <button id="{{ $category->slug }}-prev" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                    </button>
                    <button id="{{ $category->slug }}-next" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    </button>
                </div>
            </div>

            <div id="{{ $category->slug }}-slider" class="flex gap-3 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                @foreach($category->activeGames as $game)
                <div class="flex-shrink-0 w-40 sm:w-44 snap-start group" x-data="{ showActions: false }">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 cursor-pointer"
                         @click="showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <img src="{{$game->image}}" 
                                 alt="{{$game->title ?? $game->name}}" 
                                 loading="lazy" 
                                 decoding="async" 
                                 width="176" 
                                 height="235" 
                                 class="w-full h-full object-cover">

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
                                   @auth wire:navigate @endauth
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
        
        </div><!-- End Tab Content Wrapper -->
    </div>

    @else
    <!-- Fallback to old system if no categories exist -->
    @include('main.index_old')
    @endif

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        [x-cloak] { 
            display: none !important; 
        }
        
        [x-show] {
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }
        
        [x-show][style*="display: none"] {
            position: absolute !important;
            pointer-events: none;
        }
    </style>

    <script>
        function initGameSliders() {
            function initGameSlider(sliderId, prevId, nextId) {
                const slider = document.getElementById(sliderId);
                const prevBtn = document.getElementById(prevId);
                const nextBtn = document.getElementById(nextId);
                if (!slider || !prevBtn || !nextBtn) return;

                const slideWidth = slider.querySelector('div') ? slider.querySelector('div').offsetWidth + 12 : 176;

                // Удаляем старые обработчики
                const newPrevBtn = prevBtn.cloneNode(true);
                const newNextBtn = nextBtn.cloneNode(true);
                prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
                nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);

                newPrevBtn.addEventListener('click', () => {
                    slider.scrollBy({ left: -slideWidth * 3, behavior: 'smooth' });
                });

                newNextBtn.addEventListener('click', () => {
                    slider.scrollBy({ left: slideWidth * 3, behavior: 'smooth' });
                });
            }

            @if(isset($categories))
            @foreach($categories as $category)
            initGameSlider('{{ $category->slug }}-slider', '{{ $category->slug }}-prev', '{{ $category->slug }}-next');
            @endforeach
            @endif
            
            const tabButtons = document.querySelectorAll('[\\@click^="activeTab"]');
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    if (window.innerWidth < 768) {
                        const tabsContainer = button.closest('.container');
                        if (tabsContainer) {
                            setTimeout(() => {
                                tabsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }, 100);
                        }
                    }
                });
            });
        }

        // Инициализация при загрузке и при навигации Livewire
        document.addEventListener('DOMContentLoaded', initGameSliders);
        document.addEventListener('livewire:navigated', initGameSliders);
    </script>

        <x-layouts.partials.footer/>
</x-layouts.app>
