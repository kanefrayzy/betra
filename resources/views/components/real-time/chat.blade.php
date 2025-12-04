<div id="right-sidebar"
    class="fixed inset-y-0 right-0 z-30 flex h-full w-full sm:w-96 md:w-80 lg:w-80 transform flex-col bg-dark-900 transition-all duration-500 ease-in-out shadow-[_-12px_0_24px_rgba(0,0,0,0.35)]"
     :class="{'translate-x-0 opacity-100': chatOpen, 'translate-x-full opacity-0': !chatOpen}"
     x-cloak
     @auth
     data-user-id="{{ Auth::user()->id }}"
     data-username="{{ Auth::user()->username }}"
     data-avatar="{{ Auth::user()->avatar }}"
     data-rank="{{ Auth::user()->rank->name }}"
     data-rank-picture="{{ Auth::user()->rank->picture }}"
     data-is-moderator="{{ Auth::user()->is_moder || Auth::user()->is_admin ? 'true' : 'false' }}"
     data-currency="{{ Auth::user()->currency->symbol }}"
     @endauth>


    <div class="flex h-16 sm:h-16 items-center justify-between bg-customBoldDark px-3 sm:px-4 shadow-[0_2px_6px_rgba(0,0,0,0.20)] z-10 relative">
        <div class="flex items-center">
            <div>
                <!-- <span class="text-base sm:text-lg font-bold text-white">{{ __('Чат') }}</span> -->
                <div class="flex items-center mt-0.5">
                    <div class="h-1.5 w-1.5 sm:h-2 sm:w-2 rounded-full bg-green-500 mr-1 sm:mr-1.5 animate-pulse"></div>
                    <span id="online-count" class="text-xs sm:text-sm text-gray-400">{{ __('Онлайн: 0') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center">
            <button
                @click="chatOpen = false"
                type="button"
                class="flex items-center justify-center h-8 w-8 sm:h-9 sm:w-9 rounded-xl text-gray-400 hover:text-red-400 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <div class="relative flex-1 overflow-visible" wire:ignore>
        <div id="messages" class="h-full overflow-y-auto bg-customBoldDark p-3 sm:p-2 scrollbar-thin scrollbar-track-dark-900 scrollbar-thumb-dark-700 hover:scrollbar-thumb-dark-600">

        </div>

        <button id="scroll-to-new" class="absolute w-3/4 text-center bottom-4 left-1/2 -translate-x-1/2 rounded-lg bg-[#2a2f3a] hover:bg-[#323844] px-4 py-2 text-sm font-medium text-white shadow-lg border border-[#ffb300]/20 hover:border-[#ffb300]/40 z-50 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300 transform translate-y-2">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#ffb300]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="7 13 12 18 17 13"></polyline>
                    <polyline points="7 6 12 11 17 6"></polyline>
                </svg>
                <span>{{__('Новые сообщения')}}</span>
            </div>
        </button>
    </div>


    <div class="border-t border-dark-700/50 bg-[#1e2329] p-3 sm:p-4">
        @auth
            <div class="relative mb-2 sm:mb-3">
                <input type="text"
                       id="message-input"
                       placeholder="{{ __('Введите ваше сообщение...') }}"
                       class="w-full rounded-xl bg-[#242932] py-2 sm:py-3 pl-3 sm:pl-4 pr-16 sm:pr-20 text-sm sm:text-base text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#ffb300]/50 focus:border-[#ffb300]/50 transition-all duration-200"
                       maxlength="160">


                <button id="emoji-button" class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#ffb300] transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                </button>


                <div id="emoji-picker"
                     x-data="{
                         open: false,
                         search: '',
                         category: 'all',
                         get filteredEmojis() {
                             // Здесь можно добавить фильтрацию по категориям и поиску
                             return this.search ? [] : [];
                         }
                     }"
                     @click.away="open = false"
                     class="absolute bottom-full left-0 right-0 mb-2 hidden"
                     style="z-index: 100;">

                    <div class="bg-[#1e2329] rounded-xl border border-gray-800 shadow-2xl overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800 bg-[#252a32]">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-white">Emotes</span>
                                <span class="text-xs text-gray-500 bg-gray-800 px-2 py-0.5 rounded-full">{{ count($img_emoj) }}</span>
                            </div>
                            <button class="emoji-close p-1 hover:bg-gray-800 rounded-lg text-gray-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Emoji Grid -->
                        <div class="max-h-64 overflow-y-auto custom-scrollbar p-2 sm:p-3">
                            <div class="grid grid-cols-5 sm:grid-cols-6 gap-1">
                                @foreach($img_emoj as $k => $v)
                                    <button type="button"
                                            class="emoji-item group relative aspect-square flex items-center justify-center rounded-lg hover:bg-[#252a32] transition-all duration-200 hover:scale-110 active:scale-95"
                                            data-emoji="{{ $k }}"
                                            title="{{ $k }}">
                                        <img draggable="false"
                                             class="chat-emoji-img w-6 h-6 sm:w-8 sm:h-8 object-contain"
                                             alt="{{ $k }}"
                                             src="/assets/images/emoj/{{ $v }}"
                                             loading="lazy">
                                    </button>
                                @endforeach
                            </div>

                            <!-- Empty State -->
                            @if(count($img_emoj) === 0)
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-400 text-sm">{{ __('Нет доступных эмодзи') }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="px-4 py-2 border-t border-gray-800 bg-[#252a32]">
                            <p class="text-xs text-gray-500 text-center">
                                {{ __('Нажмите на эмодзи чтобы добавить в сообщение') }}
                            </p>
                        </div>
                    </div>
                </div>

                <style>
                .emoji-item img {
                    transition: transform 0.2s ease;
                }

                .emoji-item:hover img {
                    transform: scale(1.1);
                }

                .emoji-item:active img {
                    transform: scale(0.95);
                }
                </style>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center text-xs text-gray-400 rounded-full px-1.5 sm:px-2 py-1 bg-dark-700 border border-dark-600">

                                      <a href="#" onclick="openChatRules();" class="text-gray-400 hover:text-white transition-colors">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                              <polyline points="14 2 14 8 20 8"></polyline>
                                              <line x1="16" y1="13" x2="8" y2="13"></line>
                                              <line x1="16" y1="17" x2="8" y2="17"></line>
                                              <polyline points="10 9 9 9 8 9"></polyline>
                                          </svg>
                                      </a>
                </div>

                <div class="flex items-center space-x-1.5 sm:space-x-2">
                    <div id="char-counter" class="text-xs text-gray-400 bg-dark-700 px-1.5 sm:px-2 py-1 rounded-full border border-dark-600" title="{{ __('Осталось символов') }}">160</div>


                    <button id="send-message" class="rounded-xl bg-gradient-to-r from-[#ffb300] to-[#ff9500] hover:from-[#ff9500] hover:to-[#ff8000] px-2.5 sm:px-3 py-1.5 text-xs font-medium text-black shadow-sm hover:shadow-[#ffb300]/25 transition-all hover:scale-105">
                        <span class="hidden sm:inline">{{ __('Отправить') }}</span>
                        <span class="sm:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        @else
            <button onclick="openLoginModal();" class="w-full rounded-xl bg-gradient-to-r from-[#ffb300] to-[#ff9500] hover:from-[#ff9500] hover:to-[#ff8000] py-2 sm:py-2.5 text-sm sm:text-base text-white font-medium transition-all shadow-md hover:shadow-[#ffb300]/20">
                {{ __('Войдите в аккаунт') }}
            </button>
        @endauth
    </div>
</div>
