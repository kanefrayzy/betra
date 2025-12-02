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
     class="fixed inset-0 z-50 overflow-y-auto modaler"
     style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/80"
         @click="open = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md bg-[#1e2329] rounded-2xl shadow-2xl">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#ffb300] to-[#ff9500] rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 5H3a2 2 0 00-2 2v10a2 2 0 002 2h18a2 2 0 002-2V7a2 2 0 00-2-2zm-9 2h2v2h-2V7zm0 4h2v2h-2v-2zM5 7h2v2H5V7zm0 4h2v2H5v-2zm0 4h2v2H5v-2zm12 0h2v2h-2v-2zm0-4h2v2h-2v-2zm0-4h2v2h-2V7zm-4 8h2v2h-2v-2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ __('Промокод') }}</h2>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="bg-gradient-to-br from-[#ffb300]/10 to-transparent rounded-xl p-6 border border-[#ffb300]/20 mb-4">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#ffb300] to-[#ff9500] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-bold text-lg mb-2">{{ __('Активация промокода') }}</h4>
                        <p class="text-gray-400 text-sm">{{ __('Введите промокод для получения бонуса') }}</p>
                    </div>

                    <!-- Input -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-3 font-medium">{{ __('Промокод') }}</label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="code"
                                @input="code = code.toUpperCase(); message = ''"
                                @keydown.enter="activatePromo()"
                                class="w-full bg-[#252a32] border border-gray-800 focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] rounded-lg py-4 px-4 pr-12 text-white placeholder-gray-500 focus:outline-none transition-all duration-200 font-medium text-center text-lg tracking-wider uppercase"
                                placeholder="BONUS100"
                                maxlength="20"
                            >
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Message -->
                    <div x-show="message" 
                         x-transition
                         class="mb-4 p-3 rounded-lg border-l-4 text-sm"
                         :class="messageType === 'error' ? 'bg-red-500/10 border-red-500 text-red-400' : 'bg-green-500/10 border-green-500 text-green-400'"
                         x-text="message"></div>

                    <!-- Button -->
                    <button
                        @click="activatePromo()"
                        :disabled="loading || !code"
                        class="w-full py-4 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold rounded-lg transition-all duration-200 shadow-lg flex items-center justify-center text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <template x-if="loading">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? '{{ __('Проверка...') }}' : '{{ __('Активировать промокод') }}'"></span>
                    </button>
                </div>

                <!-- Telegram Info -->
                <div class="bg-gradient-to-r from-blue-500/10 to-blue-600/10 rounded-lg p-4 border border-blue-500/20">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248-1.64 8.173c-.125.55-.462.68-.937.424l-2.59-1.916-1.250 1.204c-.135.135-.25.25-.515.25-.265 0-.372-.118-.487-.487L9.374 12.5l-2.68-1c-.575-.176-.588-.575.125-.852l10.52-4.06c.485-.184.924.112.785.66z"></path>
                            </svg>
                        </div>
                        <h5 class="text-white font-bold">{{ __('Где найти промокоды?') }}</h5>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        {{ __('Ищите актуальные промокоды в нашем') }}
                        <a href="https://t.me/{{ $settings->support_tg }}" target="_blank" class="text-blue-400 hover:text-blue-300 font-bold underline">
                            Telegram канале
                        </a>
                        {{ __('и получайте эксклюзивные бонусы!') }}
                    </p>
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

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
