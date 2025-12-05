<div x-data="{
    open: false,
    code: '',
    loading: false,
    message: '',
    messageType: '',
    
    activatePromo() {
        if (!this.code || this.loading) return;

        this.loading = true;
        this.message = '';

        fetch('{{ route('promocodes.activate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: this.code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.open = false;
                this.code = '';
                this.message = '';
                
                if (typeof showSuccessNotification === 'function') {
                    showSuccessNotification(data.message);
                }
            } else {
                this.message = data.message;
                this.messageType = 'error';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.message = '{{ __('Произошла ошибка при активации промокода') }}';
            this.messageType = 'error';
        })
        .finally(() => {
            this.loading = false;
        });
    }
}"
     @open-promo-modal.window="open = true"
     @close-promo-modal.window="open = false"
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
         class="fixed inset-0 bg-black/85"
         @click="open = false"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md bg-[#0f212e] rounded-2xl shadow-2xl">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#1a2c38]">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-white">{{ __('Промокод') }}</h2>
                </div>
                <button @click="open = false" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="relative bg-[#1a2c38] rounded-xl p-6 border border-[#2d3748] mb-4 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#4dda30]/10 to-transparent rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#4dda30]/5 to-transparent rounded-full blur-xl"></div>
                    
                    <div class="relative text-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#4dda30] to-[#3bb825] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-[#4dda30]/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                        <h4 class="text-white font-bold text-lg mb-2">{{ __('Активация промокода') }}</h4>
                        <p class="text-gray-400 text-sm">{{ __('Введите промокод для получения бонуса') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-3 font-medium">{{ __('Промокод') }}</label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="code"
                                @input="code = code.toUpperCase(); message = ''"
                                @keydown.enter="activatePromo()"
                                class="w-full bg-[#0f212e] border border-[#2d3748] focus:border-[#4dda30] focus:ring-1 focus:ring-[#4dda30] rounded-xl py-4 px-4 pr-12 text-white placeholder-gray-500 focus:outline-none transition-all duration-200 font-semibold text-center text-lg tracking-wider uppercase"
                                placeholder="BONUS100"
                                maxlength="20"
                            >
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div x-show="message" 
                         x-transition
                         class="mb-4 p-3 rounded-lg border-l-4 text-sm font-medium"
                         :class="messageType === 'error' ? 'bg-red-500/10 border-red-500 text-red-400' : 'bg-[#4dda30]/10 border-[#4dda30] text-[#4dda30]'"
                         x-text="message"></div>

                    <button
                        @click="activatePromo()"
                        :disabled="loading || !code"
                        class="w-full py-4 bg-[#4dda30] hover:bg-[#3bb825] text-white font-bold rounded-xl transition-all duration-200 shadow-lg shadow-[#4dda30]/20 flex items-center justify-center text-base disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <template x-if="loading">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? '{{ __('Проверка...') }}' : '{{ __('Активировать промокод') }}'"></span>
                    </button>
                </div>

                <div class="bg-[#1a2c38] rounded-xl p-4 border border-[#2d3748]">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-9 h-9 bg-[#3b82f6] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248-1.64 8.173c-.125.55-.462.68-.937.424l-2.59-1.916-1.250 1.204c-.135.135-.25.25-.515.25-.265 0-.372-.118-.487-.487L9.374 12.5l-2.68-1c-.575-.176-.588-.575.125-.852l10.52-4.06c.485-.184.924.112.785.66z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h5 class="text-white font-semibold mb-1 text-sm">{{ __('Где найти промокоды?') }}</h5>
                            <p class="text-gray-400 text-sm leading-relaxed">
                                {{ __('Ищите актуальные промокоды в нашем') }}
                                <a href="https://t.me/{{ $settings->support_tg }}" target="_blank" class="text-[#3b82f6] hover:text-[#2563eb] font-semibold underline">
                                    Telegram канале
                                </a>
                                {{ __('и получайте эксклюзивные бонусы!') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openPromoModal() {
    window.dispatchEvent(new CustomEvent('open-promo-modal'));
}

function closePromoModal() {
    window.dispatchEvent(new CustomEvent('close-promo-modal'));
}
</script>