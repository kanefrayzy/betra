<x-layouts.app>
    <div class="min-h-screen bg-[#0f1419] py-8 md:py-12">
        <div class="mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#ffb300]/10 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    {{ __('Правила и Политика Конфиденциальности') }}
                </h1>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                    {{ __('Добро пожаловать на FlashGame! Пожалуйста, ознакомьтесь с нашими правилами и политикой конфиденциальности перед использованием сайта.') }}
                </p>
            </div>

            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2 mb-8 p-2 bg-[#1e2329] rounded-xl border border-gray-800">
                <button onclick="scrollToSection('rules')"
                        class="flex-1 min-w-[140px] px-4 py-3 rounded-lg font-medium text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Правила') }}
                    </span>
                </button>
                <button onclick="scrollToSection('privacy')"
                        class="flex-1 min-w-[140px] px-4 py-3 rounded-lg font-medium text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ __('Конфиденциальность') }}
                    </span>
                </button>
            </div>

            <!-- Rules Section -->
            <section id="rules" class="mb-8 scroll-mt-20">
                <div class="bg-[#1e2329] rounded-2xl border border-gray-800 overflow-hidden">
                    <!-- Section Header -->
                    <div class="bg-gradient-to-r from-[#ffb300]/10 to-transparent p-6 border-b border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#ffb300]/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-white">{{ __('Правила использования') }}</h2>
                        </div>
                    </div>

                    <!-- Rules List -->
                    <div class="p-6 space-y-4">
                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                1
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('FlashGame предназначен только для лиц старше 18 лет.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                2
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Пользователи обязаны предоставлять достоверную информацию при регистрации.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                3
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Все транзакции и действия на сайте должны соответствовать законам и правилам, установленным в вашей юрисдикции.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                4
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Администрация сайта оставляет за собой право изменять правила использования в любое время без предварительного уведомления.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                5
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Запрещается использовать учетную запись для мошеннических действий или попыток взлома сайта.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                6
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Пользователи несут ответственность за сохранность своих учетных данных и не должны передавать их третьим лицам.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                7
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('В случае нарушения правил администрация имеет право приостановить или заблокировать учетную запись без предварительного уведомления.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300] font-bold text-sm">
                                8
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Пользователи обязаны соблюдать правила и условия проведения акций, конкурсов и других мероприятий на сайте.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Privacy Section -->
            <section id="privacy" class="mb-8 scroll-mt-20">
                <div class="bg-[#1e2329] rounded-2xl border border-gray-800 overflow-hidden">
                    <!-- Section Header -->
                    <div class="bg-gradient-to-r from-blue-500/10 to-transparent p-6 border-b border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-white">{{ __('Политика конфиденциальности') }}</h2>
                        </div>
                    </div>

                    <!-- Privacy List -->
                    <div class="p-6 space-y-4">
                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                1
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Мы уважаем вашу конфиденциальность и обязуемся защищать ваши персональные данные.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                2
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Вся информация, предоставленная пользователями, будет использоваться только в целях обеспечения качественного сервиса.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                3
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Мы не передаем ваши данные третьим лицам без вашего согласия, за исключением случаев, предусмотренных законом.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                4
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Администрация сайта может собирать и хранить информацию о действиях пользователей на сайте для анализа и улучшения качества предоставляемых услуг.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                5
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Мы используем файлы cookie для улучшения работы сайта и предоставления персонализированного контента.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                6
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Вы можете в любой момент отказаться от использования файлов cookie, изменив настройки браузера, однако это может повлиять на функциональность сайта.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                7
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('Мы предпринимаем все необходимые меры для защиты ваших данных от несанкционированного доступа, изменения или уничтожения.') }}
                            </p>
                        </div>

                        <div class="flex gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 font-bold text-sm">
                                8
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ __('В случае утечки данных мы немедленно уведомим вас о случившемся и предпримем все возможные меры для минимизации последствий.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer CTA -->
            <div class="bg-gradient-to-r from-[#ffb300]/10 to-blue-500/10 rounded-2xl border border-gray-800 p-8 text-center">
                <div class="max-w-2xl mx-auto">
                    <div class="w-12 h-12 bg-[#ffb300]/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">{{ __('Остались вопросы?') }}</h3>
                    <p class="text-gray-400 mb-6">
                        {{ __('Если у вас есть вопросы по поводу наших правил или политики конфиденциальности, пожалуйста, свяжитесь с нашей службой поддержки.') }}
                    </p>
                    <button onclick="openLiveChat()"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-[#ffb300] hover:bg-[#e6a000] text-black font-semibold rounded-xl transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        {{ __('Связаться с поддержкой') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    </script>
</x-layouts.app>
