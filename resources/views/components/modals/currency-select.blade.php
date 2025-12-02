<!-- Currency Selection Modal -->
<div
    x-data="{
        open: false,
        selectedCurrency: 'USD',
        authType: null,
        authData: null,
        currencies: [
            { code: 'USD', flag: 'en' },
            { code: 'RUB', flag: 'ru' }
        ],
        error: '',
        submitting: false,
        
        init() {
            @if(Session::has('show_currency_modal'))
                this.open = true;
                
                @if(Session::has('social_auth_data'))
                    this.authType = 'social';
                @elseif(Session::has('telegram_auth_data'))
                    this.authType = 'telegram';
                @endif
            @endif
            
            const locale = '{{ app()->getLocale() }}';
            const currencyMap = {
                'ru': 'RUB',
                'en': 'USD'
            };
            this.selectedCurrency = currencyMap[locale] || 'USD';
            
            // Слушаем событие для повторного открытия модалки
            window.addEventListener('show-currency-modal', (event) => {
                this.authType = event.detail?.authType || 'social';
                this.error = '';
                this.submitting = false;
                this.open = true;
            });
            
            // Слушаем событие для открытия модалки выбора валюты из telegram-code-auth
            window.addEventListener('open-currency-select', (event) => {
                this.authType = event.detail?.authType || 'telegram-code';
                this.authData = event.detail?.authData || null;
                this.error = '';
                this.submitting = false;
                this.open = true;
            });
        },
        
        selectCurrency(code) {
            this.selectedCurrency = code;
        },
        
        async submitCurrency() {
            if (this.submitting) return;
            
            this.submitting = true;
            this.error = '';
            
            try {
                let url;
                let bodyData = { currency: this.selectedCurrency };
                
                if (this.authType === 'telegram') {
                    url = '{{ route('auth.telegram-webview.complete') }}';
                } else if (this.authType === 'telegram-code') {
                    url = '{{ route('auth.telegram-code.complete') }}';
                    bodyData = { ...bodyData, ...this.authData };
                } else {
                    url = '{{ route('auth.ulogin.complete') }}';
                }
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(bodyData)
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    this.open = false;
                    window.location.href = data.redirect || '/';
                } else {
                    this.error = data.message || '{{ __('Произошла ошибка') }}';
                }
            } catch (error) {
                console.error('Currency selection error:', error);
                this.error = '{{ __('Произошла ошибка при обработке запроса') }}';
            } finally {
                this.submitting = false;
            }
        }
    }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 overflow-y-auto modaler"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"
        @click="open = false"
    ></div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-gray-800 shadow-xl transition-all"
            @click.stop
        >
            <!-- Header -->
            <div class="bg-gray-900 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">
                    {{ __('Выберите валюту') }}
                </h3>
                <p class="mt-1 text-sm text-gray-400">
                    {{ __('Для завершения регистрации выберите предпочитаемую валюту') }}
                </p>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                <!-- Error Message -->
                <div x-show="error" class="mb-4 rounded-md bg-red-900/50 p-3 text-sm text-red-200">
                    <span x-text="error"></span>
                </div>

                <!-- Currency Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="currency in currencies" :key="currency.code">
                        <button
                            type="button"
                            @click="selectCurrency(currency.code)"
                            :class="{
                                'ring-2 ring-blue-500 bg-gray-700': selectedCurrency === currency.code,
                                'bg-gray-900 hover:bg-gray-700': selectedCurrency !== currency.code
                            }"
                            class="flex items-center justify-center gap-2 rounded-lg p-3 transition-all duration-150"
                        >
                            <img
                                :src="`/assets/images/lang/${currency.flag}.png`"
                                :alt="currency.code"
                                class="h-8 w-8 object-cover rounded"
                            />
                            <span class="font-medium text-white" x-text="currency.code"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-900 px-6 py-4 flex gap-3">
                <button
                    type="button"
                    @click="submitCurrency()"
                    :disabled="submitting"
                    class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 font-medium text-white transition-colors hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!submitting">{{ __('Продолжить') }}</span>
                    <span x-show="submitting">{{ __('Загрузка...') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
