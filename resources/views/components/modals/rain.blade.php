<div x-data="{
    open: false,
    amount: '',
    recipients: '',
    balance: {{ $u->balance }},
    currency: '{{ $u->currency->symbol }}',
    
    setMin() {
        this.amount = '0.00000001';
    },
    
    setMax() {
        this.recipients = '25';
    },
    
    async sendRain() {
        if (!this.amount || !this.recipients) {
            if (typeof showErrorNotification === 'function') {
                showErrorNotification('{{ __('Заполните все поля') }}');
            }
            return;
        }
        
        try {
            const response = await fetch('{{ route('rain.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({
                    amount: this.amount,
                    recipients: this.recipients
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                this.open = false;
                this.amount = '';
                this.recipients = '';
                
                if (typeof showSuccessNotification === 'function') {
                    showSuccessNotification(data.message);
                }
            } else {
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification(data.message);
                }
            }
        } catch (error) {
            console.error('Rain error:', error);
            if (typeof showErrorNotification === 'function') {
                showErrorNotification('{{ __('Ошибка соединения') }}');
            }
        }
    }
}"
     @open-rain-modal.window="open = true"
     @close-rain-modal.window="open = false"
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
             class="relative w-full max-w-md bg-[#0f212e] rounded-2xl shadow-2xl border border-[#1a2c38]">

            <div class="relative px-6 py-4 border-b border-[#1a2c38]">
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#3b82f6]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                        </svg>
                        <h2 class="text-xl font-bold text-white">{{ __('Дождь') }}</h2>
                    </div>
                    <button @click="open = false"
                            class="text-gray-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-5">
                <div class="bg-[#1a2c38] rounded-xl p-4 border border-[#2d3748]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-xs mb-1">{{ __('Баланс') }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-white font-bold text-lg" x-text="balance.toFixed(8)"></span>
                                <div class="flex items-center gap-1 px-2 py-1 bg-[#0f212e] rounded-lg">
                                    <div class="w-4 h-4 rounded-full bg-[#3b82f6] flex items-center justify-center">
                                        <span class="text-white text-[10px] font-bold" x-text="currency.charAt(0)"></span>
                                    </div>
                                    <span class="text-white text-sm font-medium" x-text="currency"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-400">
                            {{ __('Сумма') }}
                        </label>
                        <span class="text-gray-500 text-xs" x-text="'0,00 ' + currency"></span>
                    </div>
                    <div class="flex gap-2">
                        <input type="number" 
                               x-model="amount"
                               step="0.00000001"
                               min="0"
                               class="flex-1 h-12 px-4 bg-[#1a2c38] border border-[#2d3748] rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#3b82f6] focus:ring-1 focus:ring-[#3b82f6] transition-all"
                               placeholder="0,00000000">
                        <button @click="setMin()"
                                type="button"
                                class="px-4 h-12 bg-[#1a2c38] hover:bg-[#2d3748] border border-[#2d3748] text-white font-medium rounded-xl transition-all">
                            {{ __('Мин') }}
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-400">
                            {{ __('Количество пользователей (макс 25)') }}
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <input type="number" 
                               x-model="recipients"
                               min="1"
                               max="25"
                               class="flex-1 h-12 px-4 bg-[#1a2c38] border border-[#2d3748] rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#3b82f6] focus:ring-1 focus:ring-[#3b82f6] transition-all"
                               placeholder="10">
                        <button @click="setMax()"
                                type="button"
                                class="px-4 h-12 bg-[#1a2c38] hover:bg-[#2d3748] border border-[#2d3748] text-white font-medium rounded-xl transition-all">
                            {{ __('Макс') }}
                        </button>
                    </div>
                </div>

                <button @click="sendRain()"
                        type="button"
                        class="w-full h-12 bg-[#4dda30] hover:bg-[#3bb825] text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-[#4dda30]/20 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                    </svg>
                    {{ __('Сделать дождь') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openRainModal() {
    window.dispatchEvent(new CustomEvent('open-rain-modal'));
}

function closeRainModal() {
    window.dispatchEvent(new CustomEvent('close-rain-modal'));
}
</script>