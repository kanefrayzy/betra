<x-layouts.app>
    <div class="bg-[#0f1419] py-8 md:py-12">
        <div class="mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#ffb300]/10 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    404 - {{ __('Страница не найдена') }}
                </h1>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                    {{ __('К сожалению, запрашиваемая страница не существует или была удалена. Воспользуйтесь навигацией ниже, чтобы вернуться на сайт.') }}
                </p>
            </div>

            <!-- Actions -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-[#1e2329] rounded-2xl border border-gray-800 overflow-hidden">
                    
                    <!-- Links List -->
                    <div class="p-6 space-y-3">
                        <a href="/" wire:navigate class="flex items-center gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-semibold group-hover:text-[#ffb300] transition-colors">{{ __('Главная страница') }}</p>
                                <p class="text-gray-400 text-sm">{{ __('Вернуться на главную') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-[#ffb300] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>

                        <a href="{{ route('slots.lobby') }}" wire:navigate class="flex items-center gap-4 group hover:bg-gray-800/30 p-4 rounded-lg transition-colors">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#ffb300]/10 rounded-lg flex items-center justify-center text-[#ffb300]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-semibold group-hover:text-[#ffb300] transition-colors">{{ __('Все игры') }}</p>
                                <p class="text-gray-400 text-sm">{{ __('Перейти в лобби игр') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-[#ffb300] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>