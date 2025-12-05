<div x-data="{ open: false }"
     @open-crypto-guide.window="open = true"
     @close-crypto-guide.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[60] overflow-y-auto modaler"
     style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm"
         @click="open = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="open = false"
             class="relative w-full max-w-4xl bg-[#1e2329] rounded-xl md:rounded-2xl shadow-2xl border border-gray-800 max-h-[95vh] md:max-h-[90vh] flex flex-col">

            <!-- Header with Close Button -->
            <div class="sticky top-0 z-10 bg-[#1e2329] border-b border-gray-800 rounded-t-xl md:rounded-t-2xl px-4 md:px-6 py-3 md:py-4 flex items-center justify-between">
                <h2 class="text-lg md:text-xl font-bold text-white pr-2">{{__('Как пополнить через криптовалюту')}}</h2>
                <button @click="open = false"
                        class="p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-white transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content - Scrollable -->
            <div class="overflow-y-auto flex-1 px-4 md:px-6 py-4 md:py-6">
                <!-- Banner Image -->
                <div class="relative h-[260px] md:h-[360px] rounded-xl overflow-hidden mb-6">
                    <!-- Desktop Image -->
                    <img src="{{ asset('assets/images/instructions-desktop.webp') }}" 
                         alt="Crypto Guide Banner"
                         onerror="this.style.display='none'; this.nextElementSibling.nextElementSibling.style.display='flex';"
                         class="hidden md:block absolute inset-0 w-full h-full object-cover">
                    
                    <!-- Mobile Image -->
                    <img src="{{ asset('assets/images/instructions-mobile.webp') }}" 
                         alt="Crypto Guide Banner"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                         class="md:hidden absolute inset-0 w-full h-full object-cover">
                    
                    <!-- Fallback Gradient Background -->
                    <div class="absolute inset-0 bg-gradient-to-br from-[#ffb300]/30 via-purple-500/30 to-blue-500/30" style="display: none;"></div>
                    
                    <!-- Overlay Text -->
                    <div class="absolute inset-0 flex items-center justify-center z-10 bg-black/30 backdrop-blur-[2px]">
                        <h3 class="text-2xl md:text-[40px] font-bold text-white text-center px-6 leading-tight md:leading-[44px] tracking-tight md:tracking-[-1.64px] max-w-xs md:max-w-md drop-shadow-lg">
                            {{__('Как пополниться в криптовалюте')}}
                        </h3>
                    </div>
                </div>

                <!-- Tabs for Different Services -->
                <div x-data="{ activeService: 'general' }" class="mb-4 md:mb-6">
                    <div class="flex gap-2 overflow-x-auto pb-2 mb-4 md:mb-6 scrollbar-hide">
                        <button @click="activeService = 'general'"
                                :class="activeService === 'general' ? 'bg-[#ffb300] text-black' : 'bg-[#252a32] text-gray-300 hover:bg-gray-800'"
                                class="flex items-center gap-2 px-3 md:px-4 py-2 md:py-2.5 rounded-lg font-semibold whitespace-nowrap transition-all text-sm md:text-base">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            {{__('Общая инструкция')}}
                        </button>
                        <button @click="activeService = 'bestchange'"
                                :class="activeService === 'bestchange' ? 'bg-[#ffb300] text-black' : 'bg-[#252a32] text-gray-300 hover:bg-gray-800'"
                                class="flex items-center gap-2 px-3 md:px-4 py-2 md:py-2.5 rounded-lg font-semibold whitespace-nowrap transition-all text-sm md:text-base">
                            <svg class="w-4 h-4 md:w-5 md:h-5" viewBox="0 0 41 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2.68994" y="2" width="36" height="36" rx="18" fill="#86B200"/>
                                <path d="M10.8299 9C9.09559 9 7.68994 10.406 7.68994 12.14V26.8894C7.68994 28.6237 9.0959 30.0293 10.8299 30.0293H20.3455V9H10.8299Z" fill="#494949"/>
                                <path d="M29.9241 9H20.3455V30.0296H29.9241C31.6584 30.0296 33.064 28.6237 33.064 26.8897V12.14C33.064 10.406 31.6581 9 29.9241 9Z" fill="#F3F3F3"/>
                                <path d="M19.1177 20.1444C18.8973 19.7667 18.6455 19.263 18.1103 18.9796C18.4094 18.775 18.9446 17.9565 19.0075 17.138C19.0705 16.3194 19.039 14.8083 18.8186 14.2731C18.5983 13.738 18.1113 12.3528 16.0568 12.0852C15.4186 12.0065 14.6316 12.0065 14.6316 12.0065H9.86216V12.0852V27.1018H15.3494C15.3494 27.1018 18.2362 27.2278 19.0548 24.8037C19.8733 22.3796 19.3381 20.5222 19.1177 20.1444ZM12.5853 14.8398H15.3009C15.8528 14.8398 16.3001 15.2872 16.3001 15.839V16.7995C16.3001 17.3514 15.8528 17.7987 15.3009 17.7987H12.5853V14.8398ZM16.6464 23.0111C16.6464 23.5926 16.1751 24.0639 15.5937 24.0639H12.5853V20.5694H15.555C16.1578 20.5694 16.6464 21.058 16.6464 21.6609V23.0111Z" fill="#F3F3F3"/>
                                <path d="M24.3641 19.7522C24.3641 22.9252 25.1873 24.5116 26.8341 24.5116C28.3223 24.5116 29.1077 23.6442 29.1905 21.9096H31.6813C31.6813 22.6246 31.5573 23.3653 31.3092 24.1322C31.1232 24.725 30.8682 25.2523 30.5445 25.7142C30.2068 26.2197 29.7075 26.6403 29.046 26.9759C28.3846 27.3118 27.647 27.4796 26.8341 27.4796C25.091 27.4796 23.8094 26.7474 22.9896 25.2832C22.1695 23.8187 21.7596 21.9751 21.7596 19.7525C21.7596 17.5035 22.1695 15.647 22.9896 14.1825C23.8094 12.718 25.091 11.986 26.8341 11.986C27.647 11.986 28.3843 12.1538 29.0457 12.4894C29.7071 12.825 30.2068 13.2456 30.5442 13.7512C30.8682 14.2133 31.1229 14.7407 31.3089 15.3331C31.557 16.1003 31.681 16.6006 31.681 17.3152H29.1902C29.1074 15.5806 28.322 14.9538 26.8338 14.9538C25.1873 14.9538 24.3641 16.5534 24.3641 19.7522Z" fill="#333333"/>
                            </svg>
                            BestChange
                        </button>
                        <button @click="activeService = 'wallet'"
                                :class="activeService === 'wallet' ? 'bg-[#ffb300] text-black' : 'bg-[#252a32] text-gray-300 hover:bg-gray-800'"
                                class="flex items-center gap-2 px-3 md:px-4 py-2 md:py-2.5 rounded-lg font-semibold whitespace-nowrap transition-all text-sm md:text-base">
                            <svg class="w-4 h-4 md:w-5 md:h-5" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#1AD2A4" d="M30.4,14.51c0.19,0.4,0.1,0.87-0.21,1.17L16.7,28.7c-0.39,0.38-1.01,0.38-1.4,0L1.81,15.68c-0.31-0.3-0.4-0.77-0.21-1.17L6.59,4.08C6.76,3.72,7.11,3.5,7.51,3.5h16.98c0.4,0,0.75,0.22,0.92,0.58L30.4,14.51z"/>
                                <path fill="#455A64" d="M16,29.49c-0.38,0-0.75-0.14-1.05-0.43L1.46,16.04C1,15.59,0.87,14.89,1.15,14.3L6.14,3.86C6.39,3.33,6.92,3,7.51,3h16.98c0.59,0,1.12,0.33,1.37,0.87l4.99,10.43c0.28,0.59,0.15,1.29-0.31,1.75L17.05,29.06C16.75,29.35,16.38,29.49,16,29.49z M7.51,4C7.3,4,7.13,4.11,7.04,4.29L2.05,14.73c-0.09,0.2-0.05,0.44,0.11,0.6l13.49,13.02c0.2,0.19,0.51,0.19,0.7,0l13.49-13.02c0.16-0.15,0.2-0.4,0.11-0.6L24.96,4.3C24.87,4.11,24.7,4,24.49,4H7.51z"/>
                                <path fill="#FFFFFF" d="M16,17c-3.53,0-9.5-0.53-9.5-2.5c0-1.94,5.77-2.37,7.54-2.45c0.31-0.02,0.51,0.2,0.52,0.48c0.01,0.28-0.2,0.51-0.47,0.52c-4.37,0.21-6.48,1.07-6.59,1.46C7.65,15.02,10.7,16,16,16s8.35-0.98,8.5-1.51c-0.11-0.37-2.2-1.22-6.53-1.44c-0.28-0.01-0.49-0.25-0.47-0.52c0.01-0.27,0.22-0.48,0.52-0.47c1.75,0.09,7.48,0.53,7.48,2.45C25.5,16.47,19.53,17,16,17z"/>
                                <path fill="#FFFFFF" d="M22.5,9h-13C9.22,9,9,8.78,9,8.5S9.22,8,9.5,8h13C22.78,8,23,8.22,23,8.5S22.78,9,22.5,9z"/>
                                <path fill="#FFFFFF" d="M16,23.5c-0.28,0-0.5-0.22-0.5-0.5v-6.5c0-0.28,0.22-0.5,0.5-0.5s0.5,0.22,0.5,0.5V23C16.5,23.28,16.28,23.5,16,23.5z"/>
                                <path fill="#FFFFFF" d="M16,15c-0.28,0-0.5-0.22-0.5-0.5v-6C15.5,8.22,15.72,8,16,8s0.5,0.22,0.5,0.5v6C16.5,14.78,16.28,15,16,15z"/>
                            </svg>
                            {{ __('Крипто-кошельки') }}
                        </button>


                    </div>

                    <!-- General Instructions -->
                    <div x-show="activeService === 'general'" class="space-y-3 md:space-y-4">
                        <div class="bg-[#252a32] rounded-lg md:rounded-xl p-3 md:p-4 border border-gray-800">
                            <div class="flex items-start gap-3 md:gap-4">
                                <div class="flex-shrink-0 w-8 h-8 md:w-10 md:h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-base md:text-lg">1</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-sm md:text-base mb-1 md:mb-2">{{__('Выберите криптовалюту')}}</h4>
                                    <p class="text-gray-400 text-xs md:text-sm leading-relaxed">
                                        {{__('В форме пополнения выберите подходящую криптовалюту. Рекомендуем USDT TRC-20 — низкая комиссия (~$1) и быстрое подтверждение.')}}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-lg">2</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-base mb-2">{{__('Скопируйте адрес кошелька')}}</h4>
                                    <p class="text-gray-400 text-sm leading-relaxed">
                                        {{__('После выбора криптовалюты скопируйте адрес нашего кошелька. Проверьте, что выбрана правильная сеть (например, TRC-20 для USDT).')}}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-lg">3</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-base mb-2">{{__('Отправьте криптовалюту')}}</h4>
                                    <p class="text-gray-400 text-sm leading-relaxed">
                                        {{__('Откройте свой крипто-кошелек или биржу (Binance, Bybit, Trust Wallet и т.д.) и отправьте нужную сумму на полученный адрес.')}}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-lg">4</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-base mb-2">{{__('Получите средства')}}</h4>
                                    <p class="text-gray-400 text-sm leading-relaxed">
                                        {{__('После подтверждения транзакции в блокчейне (обычно 1-5 минут) средства автоматически зачислятся на ваш игровой баланс.')}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BestChange Instructions -->
                    <div x-show="activeService === 'bestchange'" class="space-y-4">
                        <div class="bg-gradient-to-r from-green-500/10 to-green-600/10 border border-green-500/30 rounded-xl p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-green-400 font-semibold mb-1">{{__('Что такое BestChange?')}}</p>
                                    <p class="text-green-300/80 text-sm">{{__('BestChange — это мониторинг обменников криптовалют, который помогает найти лучший курс для покупки крипты за рубли, карты и другие способы.')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-lg">1</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-base mb-2">{{__('Перейдите на BestChange')}}</h4>
                                    <p class="text-gray-400 text-sm leading-relaxed mb-3">
                                        {{__('Откройте сайт BestChange.ru и выберите валюту для покупки (USDT TRC-20) и способ оплаты (карта, СБП, наличные).')}}
                                    </p>
                                    <a href="https://www.bestchange.ru/" target="_blank" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-medium transition-colors">
                                        {{__('Открыть BestChange')}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-lg">2</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-base mb-2">{{__('Выберите обменник')}}</h4>
                                    <p class="text-gray-400 text-sm leading-relaxed">
                                        {{__('Выберите обменник с лучшим курсом и хорошими отзывами. Обратите внимание на минимальную и максимальную сумму обмена.')}}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-lg">3</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-base mb-2">{{__('Укажите наш адрес кошелька')}}</h4>
                                    <p class="text-gray-400 text-sm leading-relaxed">
                                        {{__('Вернитесь в нашу форму пополнения, скопируйте адрес кошелька и вставьте его в форму обменника на BestChange.')}}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#ffb300] text-black rounded-full flex items-center justify-center font-bold text-lg">4</div>
                                <div class="flex-1">
                                    <h4 class="text-white font-semibold text-base mb-2">{{__('Оплатите и получите крипту')}}</h4>
                                    <p class="text-gray-400 text-sm leading-relaxed">
                                        {{__('Оплатите заказ удобным способом. После обработки платежа обменник отправит криптовалюту на наш адрес, и средства зачислятся на ваш баланс.')}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Instructions -->
                    <div x-show="activeService === 'wallet'" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- Trust Wallet -->
                            <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800 hover:border-blue-500/50 transition-all cursor-pointer">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10" viewBox="0 0 2000 2000" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="1000" cy="1000" r="1000" fill="#3375bb"/>
                                            <path d="M1490.78 504.57a65.1 65.1 0 0 1 65 65.94c-3.12 186.3-10.31 328.8-23.75 441.6-13.1 112.8-32.81 196.6-62.5 264.4-20 45.3-44.69 82.8-73.7 114.7-39.1 42.2-83.7 72.82-132.5 101.9-20.84 12.46-42.51 24.77-65.2 37.68-48.5 27.54-101.8 57.83-162.3 98.3a64.66 64.66 0 0 1 -72.18 0c-61.4-40.91-115.4-71.6-164.3-99.3q-16.32-9.25-31.92-18.2c-57.2-33.1-108.7-64.7-153.7-110.3-30-30-55.94-66.6-76.6-110-28.1-58.1-47.2-128.4-61.2-219.4C457 950.2 447.7 791.1 444.2 570.5A65.13 65.13 0 0 1 462.66 524a66.25 66.25 0 0 1 46.56 -19.38H536.1c82.8.31 265.6-7.81 423.7-130.9a65.21 65.21 0 0 1 79.69 0c158.1 123.1 340.9 131.3 424.1 130.9m-118.1 730.3c20.31-41.87 37.2-99.7 50-182.8 15.31-99.4 24.69-234.4 29.1-418.1-97.5-2.82-265-21.57-424.7-129.1C840.2 612.1 672.7 630.8 575.5 634c3.44 151.9 10.31 270 21.25 362.8 12.5 105.6 30.3 177.2 52.5 227.5 14.69 33.44 30.94 57.5 50.3 78.8 25.94 28.44 58.75 51.87 103.4 78.8 18.54 11.1 39 22.69 61.2 35.3 39.64 22.43 85 48.1 135.6 80.3 49.71-31.7 94.4-57.1 133.6-79.4 11.81-6.72 23.1-13.1 33.89-19.37 55-31.56 95.6-57.81 125.9-88.4C1313.6 1289.3 1330.2 1266.5 1345.5 1234.9" fill="#fff" fill-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-white font-semibold">Trust Wallet</h4>
                                </div>
                                <p class="text-gray-400 text-sm">{{__('Популярный мобильный кошелек с простым интерфейсом')}}</p>
                            </div>

            <!-- MetaMask -->
            <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800 hover:border-orange-500/50 transition-all cursor-pointer">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 507.83 470.86">
                            <defs>
                                <style>
                                    .a{fill:#e2761b;stroke:#e2761b;}
                                    .a,.b,.c,.d,.e,.f,.g,.h,.i,.j{stroke-linecap:round;stroke-linejoin:round;}
                                    .b{fill:#e4761b;stroke:#e4761b;}
                                    .c{fill:#d7c1b3;stroke:#d7c1b3;}
                                    .d{fill:#233447;stroke:#233447;}
                                    .e{fill:#cd6116;stroke:#cd6116;}
                                    .f{fill:#e4751f;stroke:#e4751f;}
                                    .g{fill:#f6851b;stroke:#f6851b;}
                                    .h{fill:#c0ad9e;stroke:#c0ad9e;}
                                    .i{fill:#161616;stroke:#161616;}
                                    .j{fill:#763d16;stroke:#763d16;}
                                </style>
                            </defs>
                            <title>metamask</title>
                            <polygon class="a" points="482.09 0.5 284.32 147.38 320.9 60.72 482.09 0.5"/>
                            <polygon class="b" points="25.54 0.5 221.72 148.77 186.93 60.72 25.54 0.5"/>
                            <polygon class="b" points="410.93 340.97 358.26 421.67 470.96 452.67 503.36 342.76 410.93 340.97"/>
                            <polygon class="b" points="4.67 342.76 36.87 452.67 149.57 421.67 96.9 340.97 4.67 342.76"/>
                            <polygon class="b" points="143.21 204.62 111.8 252.13 223.7 257.1 219.73 136.85 143.21 204.62"/>
                            <polygon class="b" points="364.42 204.62 286.91 135.46 284.32 257.1 396.03 252.13 364.42 204.62"/>
                            <polygon class="b" points="149.57 421.67 216.75 388.87 158.71 343.55 149.57 421.67"/>
                            <polygon class="b" points="290.88 388.87 358.26 421.67 348.92 343.55 290.88 388.87"/>
                            <polygon class="c" points="358.26 421.67 290.88 388.87 296.25 432.8 295.65 451.28 358.26 421.67"/>
                            <polygon class="c" points="149.57 421.67 212.18 451.28 211.78 432.8 216.75 388.87 149.57 421.67"/>
                            <polygon class="d" points="213.17 314.54 157.12 298.04 196.67 279.95 213.17 314.54"/>
                            <polygon class="d" points="294.46 314.54 310.96 279.95 350.71 298.04 294.46 314.54"/>
                            <polygon class="e" points="149.57 421.67 159.11 340.97 96.9 342.76 149.57 421.67"/>
                            <polygon class="e" points="348.72 340.97 358.26 421.67 410.93 342.76 348.72 340.97"/>
                            <polygon class="e" points="396.03 252.13 284.32 257.1 294.66 314.54 311.16 279.95 350.91 298.04 396.03 252.13"/>
                            <polygon class="e" points="157.12 298.04 196.87 279.95 213.17 314.54 223.7 257.1 111.8 252.13 157.12 298.04"/>
                            <polygon class="f" points="111.8 252.13 158.71 343.55 157.12 298.04 111.8 252.13"/>
                            <polygon class="f" points="350.91 298.04 348.92 343.55 396.03 252.13 350.91 298.04"/>
                            <polygon class="f" points="223.7 257.1 213.17 314.54 226.29 382.31 229.27 293.07 223.7 257.1"/>
                            <polygon class="f" points="284.32 257.1 278.96 292.87 281.34 382.31 294.66 314.54 284.32 257.1"/>
                            <polygon class="g" points="294.66 314.54 281.34 382.31 290.88 388.87 348.92 343.55 350.91 298.04 294.66 314.54"/>
                            <polygon class="g" points="157.12 298.04 158.71 343.55 216.75 388.87 226.29 382.31 213.17 314.54 157.12 298.04"/>
                            <polygon class="h" points="295.65 451.28 296.25 432.8 291.28 428.42 216.35 428.42 211.78 432.8 212.18 451.28 149.57 421.67 171.43 439.55 215.75 470.36 291.88 470.36 336.4 439.55 358.26 421.67 295.65 451.28"/>
                            <polygon class="i" points="290.88 388.87 281.34 382.31 226.29 382.31 216.75 388.87 211.78 432.8 216.35 428.42 291.28 428.42 296.25 432.8 290.88 388.87"/>
                            <polygon class="j" points="490.44 156.92 507.33 75.83 482.09 0.5 290.88 142.41 364.42 204.62 468.37 235.03 491.43 208.2 481.49 201.05 497.39 186.54 485.07 177 500.97 164.87 490.44 156.92"/>
                            <polygon class="j" points="0.5 75.83 17.39 156.92 6.66 164.87 22.56 177 10.44 186.54 26.34 201.05 16.4 208.2 39.26 235.03 143.21 204.62 216.75 142.41 25.54 0.5 0.5 75.83"/>
                            <polygon class="g" points="468.37 235.03 364.42 204.62 396.03 252.13 348.92 343.55 410.93 342.76 503.36 342.76 468.37 235.03"/>
                            <polygon class="g" points="143.21 204.62 39.26 235.03 4.67 342.76 96.9 342.76 158.71 343.55 111.8 252.13 143.21 204.62"/>
                            <polygon class="g" points="284.32 257.1 290.88 142.41 321.1 60.72 186.93 60.72 216.75 142.41 223.7 257.1 226.09 293.27 226.29 382.31 281.34 382.31 281.74 293.27 284.32 257.1"/>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold">MetaMask</h4>
                </div>
                <p class="text-gray-400 text-sm">{{ __('Расширение для браузера и мобильное приложение') }}</p>
            </div>


                            <!-- Binance -->
                            <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800 hover:border-[#f0b90b]/50 transition-all cursor-pointer">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10" viewBox="-52.785 -88 457.47 528" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M79.5 176l-39.7 39.7L0 176l39.7-39.7zM176 79.5l68.1 68.1 39.7-39.7L176 0 68.1 107.9l39.7 39.7zm136.2 56.8L272.5 176l39.7 39.7 39.7-39.7zM176 272.5l-68.1-68.1-39.7 39.7L176 352l107.8-107.9-39.7-39.7zm0-56.8l39.7-39.7-39.7-39.7-39.8 39.7z" fill="#f0b90b"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-white font-semibold">Binance</h4>
                                </div>
                                <p class="text-gray-400 text-sm">{{__('Крупнейшая криптовалютная биржа')}}</p>
                            </div>

                            <!-- Bybit -->
                            <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800 hover:border-[#F7A600]/50 transition-all cursor-pointer">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                                            <g filter="url(#filter0_ii_1743_63067)">
                                                <rect x="2" y="2" width="36" height="36" rx="18" fill="#15192A"/>
                                            </g>
                                            <path d="M25.623 22.0457V15.5586H26.9176V22.0457H25.623Z" fill="#F7A600"/>
                                            <path d="M10.5349 23.9725H7.75946V17.4854H10.4234C11.718 17.4854 12.4722 18.1962 12.4722 19.3076C12.4722 20.0272 11.9877 20.4923 11.6523 20.6472C12.0528 20.8292 12.565 21.2392 12.565 22.105C12.565 23.3164 11.718 23.9725 10.5349 23.9725ZM10.3208 18.6152H9.05411V20.1096H10.3205C10.8699 20.1096 11.1773 19.809 11.1773 19.3621C11.1773 18.9161 10.8702 18.6152 10.3208 18.6152ZM10.4044 21.2484H9.0544V22.8432H10.4049C10.9918 22.8432 11.2706 22.4787 11.2706 22.0412C11.2706 21.6039 10.9912 21.2484 10.4049 21.2484H10.4044ZM16.5138 21.3123V23.9725H15.2283V21.3123L13.2351 17.4854H14.6415L15.8803 20.1003L17.1003 17.4854H18.5064L16.5138 21.3123ZM22.1769 23.9725H19.4015V17.4854H22.0651C23.3598 17.4854 24.1143 18.1962 24.1143 19.3076C24.1143 20.0272 23.6297 20.4923 23.2944 20.6472C23.6948 20.8292 24.207 21.2392 24.207 22.105C24.207 23.3164 23.3601 23.9725 22.1769 23.9725ZM21.9629 18.6152H20.6962V20.1096H21.9629C22.5122 20.1096 22.8196 19.809 22.8196 19.3621C22.8196 18.9161 22.5122 18.6152 21.9629 18.6152ZM22.0464 21.2484H20.6959V22.8432H22.0464C22.6335 22.8432 22.9124 22.4787 22.9124 22.0412C22.9124 21.6039 22.6335 21.2484 22.0464 21.2484ZM31.0902 18.6155V23.973H29.7956V18.6152H28.0636V17.4854H32.8228V18.6152L31.0902 18.6155Z" fill="white"/>
                                            <defs>
                                                <filter id="filter0_ii_1743_63067" x="2" y="0" width="36" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                                    <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                                    <feOffset dy="2"/>
                                                    <feGaussianBlur stdDeviation="1"/>
                                                    <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                                                    <feBlend mode="normal" in2="shape" result="effect1_innerShadow_1743_63067"/>
                                                </filter>
                                            </defs>
                                        </svg>
                                    </div>
                                    <h4 class="text-white font-semibold">Bybit</h4>
                                </div>
                                <p class="text-gray-400 text-sm">{{__('Популярная биржа с низкими комиссиями')}}</p>
                            </div>
                        </div>

                        <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-blue-400 font-semibold mb-1">{{__('Совет')}}</p>
                                    <p class="text-blue-300/80 text-sm">{{__('Если у вас еще нет криптокошелька, рекомендуем начать с Trust Wallet — он простой в использовании и поддерживает все популярные криптовалюты.')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Warning -->
                <div class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-4 mt-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-orange-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-orange-400 font-semibold mb-2">{{__('Важная информация!')}}</p>
                            <ul class="space-y-1 text-orange-300/80 text-sm">
                                <li class="flex items-start gap-2">
                                    <span class="text-orange-400 mt-1">•</span>
                                    <span>{{__('Всегда проверяйте адрес кошелька и выбранную сеть перед отправкой')}}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-orange-400 mt-1">•</span>
                                    <span>{{__('Соблюдайте минимальную сумму пополнения, указанную в форме')}}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-orange-400 mt-1">•</span>
                                    <span>{{__('Отправка на неправильный адрес или в неправильной сети приведет к потере средств')}}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-orange-400 mt-1">•</span>
                                    <span>{{__('Время зачисления зависит от загруженности блокчейна (обычно 1-30 минут)')}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer with Action Button -->
            <div class="sticky bottom-0 bg-[#1e2329] border-t border-gray-800 rounded-b-xl md:rounded-b-2xl px-4 md:px-6 py-3 md:py-4">
                <button @click="open = false"
                        class="w-full h-11 md:h-12 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold text-sm md:text-base rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                    {{__('Понятно, начать пополнение')}}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openCryptoGuide() {
    window.dispatchEvent(new CustomEvent('open-crypto-guide'));
}

function closeCryptoGuide() {
    window.dispatchEvent(new CustomEvent('close-crypto-guide'));
}
</script>
