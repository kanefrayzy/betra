<x-layouts.app>
  @include('components.slider')
      <div class="container mx-auto px-2 md:px-4 relative z-2 mt-2">
          @livewire('home-search')
      </div>

    <!-- Games Section -->
    <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-4 lg:py-2" x-data="{ activeTab: 'all' }">
        <!-- Category Tabs -->
        <div class="flex items-center gap-2 mb-2 overflow-x-auto scrollbar-hide pb-2">
            <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'" class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
                <svg  class="w-4 h-4" data-ds-icon="AllGames" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" class="inline-block shrink-0"><!----><path fill="currentColor" d="M9.08 1H3a2 2 0 0 0-2 2v6.08a2 2 0 0 0 2 2h6.08a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2M21 1h-6.08a2 2 0 0 0-2 2v6.08a2 2 0 0 0 2 2H21a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2M9.08 12.92H3a2 2 0 0 0-2 2V21a2 2 0 0 0 2 2h6.08a2 2 0 0 0 2-2v-6.08a2 2 0 0 0-2-2m11.92 0h-6.08a2 2 0 0 0-2 2V21a2 2 0 0 0 2 2H21a2 2 0 0 0 2-2v-6.08a2 2 0 0 0-2-2"></path></svg>
                {{__('Все игры')}}
            </button>
            <button @click="activeTab = 'slots'" :class="activeTab === 'slots' ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'" class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
              <svg fill="currentColor" viewBox="0 0 96 96" class="h-5 w-5">
                      <path fill-rule="evenodd" d="M56.8 47.08a49.76 49.76 0 0 0-5.6 22.8v5H32.32a55.6 55.6 0 0 1 5-22.76A87 87 0 0 1 50.8 31h-28V16.36H72v7.76a134 134 0 0 0-15.2 22.96m26.4 16.24a30.56 30.56 0 0 0-6 13.04l-.6 3L60 76.32a38.12 38.12 0 0 1 13.36-22.28l-12-2.36 5.04-10.64L96 46.88l-.92 4.64a85.5 85.5 0 0 0-11.88 11.8m-58.52 9.32a30.1 30.1 0 0 1 0-14.36 79.7 79.7 0 0 1 5.8-15.84l-1.12-4.6L0 44.88v11.68l12-2.84a37.88 37.88 0 0 0-2.88 25.92l16.28-4z" clip-rule="evenodd"></path>
                  </svg>
                {{__('Слоты')}}
            </button>
            <button @click="activeTab = 'live'" :class="activeTab === 'live' ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'" class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <polygon points="23 7 16 12 23 17 23 7"></polygon>
                      <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                  </svg>
                {{__('Live')}}
            </button>
            <button @click="activeTab = 'roulette'" :class="activeTab === 'roulette' ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'" class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3c-4.96252 0-9 4.03748-9 9 0 4.9625 4.03748 9 9 9 4.9625 0 9-4.0375 9-9 0-4.96252-4.0375-9-9-9zm.375 1.51904c.6346.03163 1.2502.13312 1.834.31348l-.5142 1.41504c-.4223-.12465-.8646-.20018-1.3198-.22852zm-3.28857.56983.51416 1.41504c-.41277.18095-.79903.41034-1.15723.67529l-.96387-1.1499c.4932-.37436 1.02947-.69599 1.60694-.94043zm8.00827 1.42236c.4553.42293.8589.89896 1.1983 1.42236l-1.2979.74854c-.2487-.3734-.5405-.71416-.8642-1.02246zm-5.0947.98877c2.4814 0 4.5 2.01855 4.5 4.5 0 2.4814-2.0186 4.5-4.5 4.5-2.48145 0-4.5-2.0186-4.5-4.5 0-2.48145 2.01855-4.5 4.5-4.5zm0 .75c-.4135 0-.75.33655-.75.75 0 .27393.155.50336.375.63428v.91842c-.5246.1364-.9359.5477-1.0723 1.0723h-.91842c-.13092-.22-.36035-.375-.63428-.375-.41345 0-.75.3365-.75.75s.33655.75.75.75c.27393 0 .50336-.155.63428-.375h.91842c.1364.5246.5477.9359 1.0723 1.0723v.9184c-.22.1309-.375.3604-.375.6343 0 .4135.3365.75.75.75s.75-.3365.75-.75c0-.2739-.155-.5034-.375-.6343v-.9184c.5246-.1364.9359-.5477 1.0723-1.0723h.9184c.1309.22.3604.375.6343.375.4135 0 .75-.3365.75-.75s-.3365-.75-.75-.75c-.2739 0-.5034.155-.6343.375h-.9184c-.1364-.5246-.5477-.9359-1.0723-1.0723v-.91842c.22-.13092.375-.36035.375-.63428 0-.41345-.3365-.75-.75-.75zm-6.66797.33252 1.30078.75146c-.19784.3967-.35202.81802-.45849 1.25832l-1.48096-.2608c.14044-.61492.35696-1.2015.63867-1.74898zm14.10347 2.48728c.0381.3056.0645.6145.0645.9302s-.0264.6246-.0645.9302l-1.4751-.2593c.0247-.2204.0396-.444.0396-.6709s-.0149-.4505-.0396-.6709zm-13.26118 2.3379c.10647.4403.26065.8616.45849 1.2583l-1.30078.7515c-.28171-.5475-.49823-1.1341-.63867-1.749zm10.82078 1.9102 1.2979.7485c-.3394.5234-.743.9994-1.1983 1.4224l-.9638-1.1485c.3237-.3083.6155-.649.8642-1.0224zm-8.55174 1.5029c.3582.265.74446.4943 1.15723.6753l-.51416 1.415c-.57747-.2444-1.11374-.566-1.60694-.9404zm5.25144.9316.5142 1.4151c-.5838.1803-1.1994.2818-1.834.3135v-1.5c.4552-.0284.8975-.1039 1.3198-.2286z"/>
              </svg>
                {{__('Рулетки')}}
            </button>
            <button @click="activeTab = 'blackjack'" :class="activeTab === 'blackjack' ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'" class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.24 5.634a.92.92 0 0 1 .817.5l.21.434-2.846 8.556-.031.133a.925.925 0 0 0 .61 1.02l7.779 2.61h.119l-.002.004a.87.87 0 0 1-.347.328l-7.342 3.686a.92.92 0 0 1-1.226-.412L1.084 10.615l.002.006a.926.926 0 0 1 .402-1.205l7.348-3.688-.006.002a.9.9 0 0 1 .41-.097z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M14.312 1c.1 0 .198.016.282.044l7.778 2.591.008.002a.923.923 0 0 1 .572 1.17l-4.189 12.586-.002.006a.92.92 0 0 1-1.145.565l-7.78-2.609-.006-.002a.92.92 0 0 1-.573-1.15l4.189-12.586.002-.007a.92.92 0 0 1 .864-.61zm3.341 6.719a.23.23 0 0 0-.073.092l-.157.435v.003c0 .062.043.113.101.126l.643.24-1.111 3.126a.1.1 0 0 0-.012.05c0 .059.04.108.093.124l.772.277a.1.1 0 0 0 .043.008.13.13 0 0 0 .123-.09l1.589-4.419a.14.14 0 0 0-.082-.175l-.634-.23zM15.25 5.948a2.1 2.1 0 0 0-1.033.268.14.14 0 0 0-.054.185l.248.646a.15.15 0 0 0 .193.072l-.004.003a1.04 1.04 0 0 1 .803-.113h.004a.58.58 0 0 1 .428.719l-.003.007a1.1 1.1 0 0 1-.364.454c-.643.434-2.158 1.466-2.755 1.844v.147a.14.14 0 0 0 .082.176l2.755.996h-.002a.1.1 0 0 0 .038.005c.063 0 .117-.04.138-.097l.22-.617a.15.15 0 0 0-.083-.184l-1.212-.434c.487-.34 1.314-.886 1.681-1.199l.004-.003a1.9 1.9 0 0 0 .583-.808h.001a1.516 1.516 0 0 0-1.075-1.983l.015.004a2.1 2.1 0 0 0-.608-.088"/>
              </svg>
                {{__('Blackjack')}}
            </button>
        </div>

        <div class="relative">
        
        <!-- Slots Section -->
        <div x-show="activeTab === 'all' || activeTab === 'slots'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="mb-4"
             :class="{ 'absolute inset-x-0 top-0': activeTab !== 'all' && activeTab !== 'slots' }">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-white">{{__('Слоты')}}</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('slots.lobby') }}" class="text-gray-400 hover:text-white text-sm font-medium transition-colors">{{__('Все')}}</a>
                    <button id="slots-prev" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                    </button>
                    <button id="slots-next" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    </button>
                </div>
            </div>

            <div id="slots-slider" class="flex gap-3 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                @foreach($slots as $game)
                <div class="flex-shrink-0 w-40 sm:w-44 snap-start group" x-data="{ showActions: false }">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                         @click="showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <img src="{{$game->image}}" alt="{{$game->title}}" class="w-full h-full object-cover">

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

                            <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/90 to-transparent">
                                <h3 class="text-white font-bold text-xs sm:text-sm truncate">{{$game->title ?? $game->name}}</h3>
                            </div>

                            <div x-show="showActions"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                 @click.stop>
                                <a href="#" onclick="handleGameClick('{{ route('slots.play', $game->slug) }}', event)"
                                   class="w-full py-2.5 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all">
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

        <!-- Live Games Section -->
        <div x-show="activeTab === 'all' || activeTab === 'live'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="mb-4"
             :class="{ 'absolute inset-x-0 top-0': activeTab !== 'all' && activeTab !== 'live' }">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    {{__('LIVE')}}
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                </h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('slots.live') }}" class="text-gray-400 hover:text-white text-sm font-medium transition-colors">{{__('Все')}}</a>
                    <button id="live-prev" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                    </button>
                    <button id="live-next" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    </button>
                </div>
            </div>

            <div id="live-slider" class="flex gap-3 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                @foreach($lives as $game)
                <div class="flex-shrink-0 w-40 sm:w-44 snap-start group" x-data="{ showActions: false }">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                         @click="showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <img src="{{$game->image}}" alt="{{$game->title}}" class="w-full h-full object-cover">

                            <div class="absolute top-2 left-2">
                                <span class="flex items-center gap-1.5 px-2 py-1 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-bold rounded-md">
                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                                    LIVE
                                </span>
                            </div>

                            <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/90 to-transparent">
                                <h3 class="text-white font-bold text-xs sm:text-sm truncate">{{$game->title ?? $game->name}}</h3>
                                <p class="text-gray-400 text-xs">{{$game->provider}}</p>
                            </div>

                            <div x-show="showActions"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4"
                                 @click.stop>
                                <a href="#" onclick="handleGameClick('{{ route('slots.play', $game->slug) }}', event)"
                                   class="w-full py-2.5 bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white rounded-lg text-sm font-bold text-center transition-all">
                                    {{__('Играть')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Roulette Section -->
        <div x-show="activeTab === 'all' || activeTab === 'roulette'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="mb-4"
             :class="{ 'absolute inset-x-0 top-0': activeTab !== 'all' && activeTab !== 'roulette' }">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-white">{{__('Рулетки')}}</h2>
                <div class="flex items-center gap-2">
                    <button id="roulette-prev" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                    </button>
                    <button id="roulette-next" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    </button>
                </div>
            </div>

            <div id="roulette-slider" class="flex gap-3 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                @foreach($roulettes as $game)
                <div class="flex-shrink-0 w-40 sm:w-44 snap-start group" x-data="{ showActions: false }">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                         @click="showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <img src="{{$game->image}}" alt="{{$game->title}}" class="w-full h-full object-cover">

                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 bg-dark-800/80 backdrop-blur-sm text-white text-xs font-medium rounded-md border border-dark-700/50">{{$game->provider}}</span>
                            </div>

                            <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/90 to-transparent">
                                <h3 class="text-white font-bold text-xs sm:text-sm truncate">{{$game->title ?? $game->name}}</h3>
                            </div>

                            <div x-show="showActions"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                 @click.stop>
                                <a href="#" onclick="handleGameClick('{{ route('slots.play', $game->slug) }}', event)"
                                   class="w-full py-2.5 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all">
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

        <!-- Blackjack Section -->
        <div x-show="activeTab === 'all' || activeTab === 'blackjack'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             :class="{ 'absolute inset-x-0 top-0': activeTab !== 'all' && activeTab !== 'blackjack' }">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-white">{{__('Blackjack')}}</h2>
                <div class="flex items-center gap-2">
                    <button id="tables-prev" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                    </button>
                    <button id="tables-next" class="w-9 h-9 rounded-lg bg-dark-800/60 border border-dark-700/50 flex items-center justify-center text-gray-400 hover:text-white hover:bg-dark-700/80 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    </button>
                </div>
            </div>

            <div id="tables-slider" class="flex gap-3 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                @foreach($tables as $game)
                <div class="flex-shrink-0 w-40 sm:w-44 snap-start group" x-data="{ showActions: false }">
                    <div class="relative rounded-xl overflow-hidden bg-dark-900 border border-dark-700/50 hover:border-dark-600 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                         @click="showActions = !showActions"
                         @mouseenter="window.innerWidth >= 768 && (showActions = true)"
                         @mouseleave="window.innerWidth >= 768 && (showActions = false)">

                        <div class="relative aspect-[3/4]">
                            <img src="{{$game->image}}" alt="{{$game->title}}" class="w-full h-full object-cover">

                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 bg-dark-800/80 backdrop-blur-sm text-white text-xs font-medium rounded-md border border-dark-700/50">{{$game->provider}}</span>
                            </div>

                            <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/90 to-transparent">
                                <h3 class="text-white font-bold text-xs sm:text-sm truncate">{{$game->title ?? $game->name}}</h3>
                            </div>

                            <div x-show="showActions"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="absolute inset-0 bg-black/80 backdrop-blur-sm flex flex-col items-center justify-center p-4 gap-2"
                                 @click.stop>
                                <a href="#" onclick="handleGameClick('{{ route('slots.play', $game->slug) }}', event)"
                                   class="w-full py-2.5 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg text-sm font-bold text-center transition-all">
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
        
        </div><!-- End Tab Content Wrapper -->
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Smooth height transitions for tab content */
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Ensure tab sections don't affect layout when hidden */
        [x-show] {
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }
        
        /* Prevent layout shift during tab transitions */
        [x-show][style*="display: none"] {
            position: absolute !important;
            pointer-events: none;
        }
        
        /* Smooth transitions for tab content wrapper */
        .relative.min-h-\[500px\],
        .relative.min-h-\[600px\] {
            transition: min-height 0.3s ease-out;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function initGameSlider(sliderId, prevId, nextId) {
                const slider = document.getElementById(sliderId);
                const prevBtn = document.getElementById(prevId);
                const nextBtn = document.getElementById(nextId);
                if (!slider || !prevBtn || !nextBtn) return;

                const slideWidth = slider.querySelector('div').offsetWidth + 12;

                prevBtn.addEventListener('click', () => {
                    slider.scrollBy({ left: -slideWidth * 3, behavior: 'smooth' });
                });

                nextBtn.addEventListener('click', () => {
                    slider.scrollBy({ left: slideWidth * 3, behavior: 'smooth' });
                });
            }

            initGameSlider('slots-slider', 'slots-prev', 'slots-next');
            initGameSlider('live-slider', 'live-prev', 'live-next');
            initGameSlider('roulette-slider', 'roulette-prev', 'roulette-next');
            initGameSlider('tables-slider', 'tables-prev', 'tables-next');
            
            // Smooth scroll to top when switching tabs on mobile
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
        });

        function handleGameClick(gameUrl, event) {
            event.preventDefault();
            @auth
                window.location.href = gameUrl;
            @else
                openLoginModal();
            @endauth
        }
    </script>

        <x-layouts.partials.footer/>
</x-layouts.app>
