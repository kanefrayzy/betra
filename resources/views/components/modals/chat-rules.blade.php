<div x-data="{ open: false }"
     @open-chat-rules.window="open = true"
     @close-chat-rules.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/85 backdrop-blur-sm"
         @click="open = false"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-2xl bg-[#0f212e] rounded-2xl shadow-2xl border border-[#1a2c38]">

            <div class="relative px-6 py-4 border-b border-[#1a2c38]">
                <button @click="open = false"
                        class="absolute top-4 right-4 text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ __('Правила чата') }}</h2>
                        <p class="text-gray-400 text-sm">{{ __('Пожалуйста, ознакомьтесь с правилами') }}</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6 max-h-[60vh] overflow-y-auto">
                <div class="space-y-3">
                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            1
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Не спамьте и не злоупотребляйте заглавными буквами при общении в чате.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            2
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Не приставайте к другим пользователям и не оскорбляйте их. Не оскорбляйте сотрудников Flash.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            3
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Запрещается распространение любой персональной информации о себе или о других игроках (включая контакты в соцсетях).') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            4
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Не попрошайничайте и не просите займы, дожди и переводы.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            5
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Не используйте свои дополнительные аккаунты ("альты") в чате - это категорически запрещено.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            6
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Не ведите себя подозрительно, ваши действия могут расценить как мошеннические.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            7
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Не разрешается размещение сообщений с рекламой, предложением обмена, купли-продажи чего-либо и оказания услуг в любой форме.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            8
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Обсуждение стримеров с Twitch и любых других аналогичных платформ запрещено.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            9
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Не используйте сервис коротких ссылок URL. Всегда размещайте полные ссылки.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            10
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Запрещено распространение программных кодов, скриптов и любых других сведений, касающихся ботов.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            11
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Пользуйтесь только тем языком, который предназначен для данного чата. Нарушение данного правила будет наказываться.') }}
                        </p>
                    </div>

                    <div class="flex gap-3 p-3 bg-[#1a2c38] rounded-lg border border-[#2d3748]">
                        <div class="flex-shrink-0 w-6 h-6 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center text-[#3b82f6] font-bold text-sm">
                            12
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ __('Избегайте обсуждения политики и религии в чате - это строго запрещено.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-[#1a2c38]">
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-red-400 text-sm font-semibold mb-1">{{ __('Важно!') }}</p>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ __('Нарушение правил может привести к временной блокировке или полному бану в чате. Администрация оставляет за собой право модерации без объяснения причин.') }}
                        </p>
                    </div>
                </div>

                <button @click="open = false"
                        class="w-full py-3 bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-[#3b82f6]/20">
                    {{ __('Понятно') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openChatRules() {
    window.dispatchEvent(new CustomEvent('open-chat-rules'));
}

function closeChatRules() {
    window.dispatchEvent(new CustomEvent('close-chat-rules'));
}
</script>