<x-layouts.app>
    <div class="container mx-auto px-3 sm:px-4 lg:px-4 py-3 sm:py-4 lg:py-3 min-h-screen flex items-center justify-center">

        <div class="max-w-lg w-full text-center">

            <div class="bg-gradient-to-br from-dark-800/50 to-dark-900/80 backdrop-blur-sm shadow-xl border border-dark-700/30 rounded-2xl p-8 md:p-12">

                <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/25">
                    <i class="fa-solid fa-screwdriver-wrench text-white text-2xl"></i>
                </div>

                <h1 class="text-3xl font-bold text-white mb-6">
                    {{ __('Технические работы') }}
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8">
                    {{ isset($settings) && $settings->text_maintenance ? $settings->text_maintenance : 'Сайт временно недоступен в связи с проведением технических работ' }}
                </p>


                <button
                    onclick="location.reload()"
                    class="w-full bg-gradient-to-r from-[#8b5cf6] to-[#7c3aed] hover:from-[#7c3aed] hover:to-[#6d28d9] text-white py-4 px-6 rounded-xl font-semibold transition-all duration-200 hover:scale-105 shadow-xl mb-6"
                >
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    {{ __('Обновить страницу') }}
                </button>

                @if(isset($settings) && isset($settings->support_tg) && $settings->support_tg)
                <div class="pt-6 border-t border-dark-600/50">
                    <p class="text-gray-400 text-sm mb-4">{{ __('Связаться с поддержкой:') }}</p>
                    <a href="https://t.me/{{ $settings->support_tg }}"
                       target="_blank"
                       class="inline-flex items-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 px-6 rounded-xl font-medium transition-all duration-200 hover:scale-105">
                        <i class="fab fa-telegram mr-2"></i>
                        @ {{ $settings->support_tg }}
                    </a>
                </div>
                @endif

            </div>

            <div class="mt-6 flex justify-center space-x-4 text-sm text-gray-400">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-orange-500 rounded-full mr-2 animate-pulse"></div>
                    <span>{{ __('Обновление системы') }}</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span>{{ __('Данные защищены') }}</span>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
