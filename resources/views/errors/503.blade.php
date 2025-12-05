<x-layouts.app>
    <div class="bg-[#0f212e] min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-lg w-full">

            <div class="bg-[#1a2c38] border border-[#2d3748] rounded-2xl p-8 md:p-12 text-center">

                <div class="w-20 h-20 bg-[#3b82f6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-white mb-6">
                    {{ __('Технические работы') }}
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8">
                    {{ isset($settings) && $settings->text_maintenance ? $settings->text_maintenance : __('Сайт временно недоступен в связи с проведением технических работ') }}
                </p>

                <button
                    onclick="location.reload()"
                    class="w-full bg-[#3b82f6] hover:bg-[#2563eb] text-white py-4 px-6 rounded-xl font-semibold transition-all duration-200 shadow-lg shadow-[#3b82f6]/20 mb-6"
                >
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('Обновить страницу') }}
                </button>

                @if(isset($settings) && isset($settings->support_tg) && $settings->support_tg)
                <div class="pt-6 border-t border-[#2d3748]">
                    <p class="text-gray-400 text-sm mb-4">{{ __('Связаться с поддержкой:') }}</p>
                    <a href="https://t.me/{{ $settings->support_tg }}"
                       target="_blank"
                       class="inline-flex items-center bg-[#3b82f6] hover:bg-[#2563eb] text-white py-3 px-6 rounded-xl font-medium transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.697.064-1.226-.461-1.901-.903-1.056-.693-1.653-1.124-2.678-1.8-1.185-.781-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.248-.024c-.106.024-1.793 1.139-5.062 3.345-.479.329-.913.489-1.302.481-.428-.008-1.252-.241-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.831-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                        </svg>
                        @ {{ $settings->support_tg }}
                    </a>
                </div>
                @endif

            </div>

            <div class="mt-6 flex justify-center space-x-6 text-sm text-gray-400">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-[#3b82f6] rounded-full mr-2 animate-pulse"></div>
                    <span>{{ __('Обновление системы') }}</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-[#4dda30] rounded-full mr-2"></div>
                    <span>{{ __('Данные защищены') }}</span>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>