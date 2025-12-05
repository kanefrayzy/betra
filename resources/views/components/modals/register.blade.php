<div x-data="registerForm()"
     @open-register-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/85 backdrop-blur-sm"
         @click="open = false"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md">

            <div class="relative rounded-2xl overflow-hidden bg-[#0f212e] shadow-2xl border border-[#1a2c38]">

                <div class="relative px-6 py-4 border-b border-[#1a2c38]">
                    <div class="relative flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">{{ __('Регистрация') }}</h2>
                        <button @click="open = false"
                                class="text-gray-500 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <form @submit.prevent="submitForm" class="space-y-4">
                        @csrf

                        <div x-show="successMessage" x-transition class="p-3 bg-[#4dda30]/10 border border-[#4dda30]/30 rounded-lg text-[#4dda30] text-sm" style="display: none;">
                            <span x-text="successMessage"></span>
                        </div>

                        <div x-show="errorMessage" x-transition class="p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 text-sm" style="display: none;">
                            <span x-text="errorMessage"></span>
                        </div>

                        <div class="grid grid-cols-12 gap-3" x-data="{ 
                            open: false,
                            currencies: [
                                { code: 'USD', flag: 'en' },
                                { code: 'RUB', flag: 'ru' },
                                { code: 'KZT', flag: 'kz' },
                                { code: 'TRY', flag: 'tr' },
                                { code: 'AZN', flag: 'az' },
                            ],
                            selectedCurrency: null,
                            get selected() {
                                return this.currencies.find(c => c.code === this.selectedCurrency) || null;
                            },
                            selectCurrency(code) {
                                this.selectedCurrency = code;
                                form.currency = code;
                                this.open = false;
                            },
                            init() {
                                const locale = '{{ app()->getLocale() }}';
                                const currencyMap = {
                                    'ru': 'RUB',
                                    'en': 'USD',
                                    'kz': 'KZT',
                                    'tr': 'TRY',
                                    'az': 'AZN',
                                };
                                this.selectedCurrency = currencyMap[locale] || 'USD';
                                form.currency = this.selectedCurrency;
                            }
                        }"
                        @click.away="open = false">
                            
                            <div class="col-span-8">
                                <label for="reg-username" class="block text-sm font-medium text-gray-400 mb-2">
                                    {{ __('Имя пользователя') }}
                                </label>
                                <input type="text" id="reg-username" x-model="form.username" required
                                    class="w-full h-12 px-4 bg-[#1a2c38] border transition-all rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-1"
                                    :class="errors.username ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-[#2d3748] focus:border-[#3b82f6] focus:ring-[#3b82f6]'"
                                    placeholder="{{ __('Введите имя пользователя') }}">
                                <p x-show="errors.username" x-text="errors.username?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                            </div>

                            <div class="col-span-4 relative">
                                <label class="block text-sm font-medium text-gray-400 mb-2">
                                    {{ __('Валюта') }}
                                </label>
                                
                                <div @click="open = !open" 
                                    class="relative w-full h-12 px-3 bg-[#1a2c38] border rounded-xl cursor-pointer transition-all"
                                    :class="errors.currency ? 'border-red-500' : (open ? 'border-[#3b82f6] ring-1 ring-[#3b82f6]' : 'border-[#2d3748] hover:border-[#3b82f6]/50')">
                                    
                                    <div class="flex items-center justify-center h-full gap-2">
                                        <template x-if="selected">
                                            <div class="flex items-center gap-2">
                                                <img :src="`/assets/images/lang/${selected.flag}.png`" 
                                                    :alt="selected.code"
                                                    class="w-5 h-5 rounded object-cover">
                                                <span class="text-white font-medium text-sm" x-text="selected.code"></span>
                                            </div>
                                        </template>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" 
                                            :class="open ? 'rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>

                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 w-full mt-2 bg-[#1a2c38] border border-[#2d3748] rounded-xl shadow-2xl overflow-hidden"
                                    style="display: none;">
                                    
                                    <div class="max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2d3748] scrollbar-track-transparent">
                                        <template x-for="currency in currencies" :key="currency.code">
                                            <div @click="selectCurrency(currency.code)"
                                                class="flex items-center gap-2 px-3 py-2.5 hover:bg-[#0f212e] cursor-pointer transition-colors"
                                                :class="selectedCurrency === currency.code ? 'bg-[#3b82f6]/10' : ''">
                                                
                                                <img :src="`/assets/images/lang/${currency.flag}.png`" 
                                                    :alt="currency.code"
                                                    class="w-5 h-5 rounded object-cover">
                                                
                                                <span class="text-white font-medium text-sm flex-1" x-text="currency.code"></span>
                                                
                                                <span x-show="selectedCurrency === currency.code" class="text-[#3b82f6]">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <p x-show="errors.currency" x-text="errors.currency?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#3b82f6]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('Валюту нельзя будет изменить после регистрации') }}
                        </p>

                        <div>
                            <label for="reg-email" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ __('Email') }}
                            </label>
                            <input type="email" id="reg-email" x-model="form.email" required
                                class="w-full h-12 px-4 bg-[#1a2c38] border transition-all rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-1"
                                :class="errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-[#2d3748] focus:border-[#3b82f6] focus:ring-[#3b82f6]'"
                                placeholder="{{ __('Введите email') }}">
                            <p x-show="errors.email" x-text="errors.email?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                        </div>

                        <div x-data="{ showPassword: false }">
                            <label for="reg-password" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ __('Пароль') }}
                            </label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" 
                                       id="reg-password" 
                                       x-model="form.password" 
                                       autocomplete="new-password"
                                       required
                                    class="w-full h-12 px-4 pr-12 bg-[#1a2c38] border transition-all rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-1"
                                    :class="errors.password ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-[#2d3748] focus:border-[#3b82f6] focus:ring-[#3b82f6]'"
                                    placeholder="{{ __('Минимум 6 символов') }}">
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition-colors">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="errors.password" x-text="errors.password?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="w-full h-12 bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#3b82f6] shadow-lg shadow-[#3b82f6]/20">
                            <span x-text="loading ? '{{ __('Загрузка...') }}' : '{{ __('Создать аккаунт') }}'"></span>
                        </button>
                    </form>

                    <div class="relative flex items-center">
                        <div class="flex-grow border-t border-[#2d3748]"></div>
                        <span class="flex-shrink-0 mx-4 text-xs font-medium text-gray-500 uppercase">{{ __('или') }}</span>
                        <div class="flex-grow border-t border-[#2d3748]"></div>
                    </div>

                    <div class="space-y-3">
                        <div id="telegram-auth-register" x-data="telegramAuth('register')">
                            <button type="button" @click="handleTelegramAuth($event)"
                               :disabled="loading"
                               :class="{'opacity-50 cursor-not-allowed': loading}"
                               class="w-full h-12 bg-[#1a2c38] hover:bg-[#2d3748] disabled:hover:bg-[#1a2c38] border border-[#2d3748] text-white rounded-xl font-medium transition-all flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                <span x-show="!loading">{{ __('Регистрация через Telegram') }}</span>
                                <span x-show="loading" x-cloak>{{ __('Загрузка...') }}</span>
                            </button>
                        </div>
                        
                        <div id="uLogin"
                             data-ulogin="display=panel;theme=flat;fields=first_name,last_name;providers=google,steam;hidden=;redirect_uri={{ urlencode(route('auth.ulogin')) }};mobilebuttons=0;"></div>
                    </div>

                    <div class="pt-5 border-t border-[#1a2c38]">
                        <p class="text-center text-sm text-gray-400">
                            {{ __('Уже есть аккаунт?') }}
                            <button @click="open = false; $dispatch('open-login-modal')"
                                    class="text-[#3b82f6] font-semibold hover:text-[#2563eb] transition-colors ml-1">
                                {{ __('Войти') }}
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function registerForm() {
    return {
        open: false,
        loading: false,
        errors: {},
        successMessage: '',
        errorMessage: '',
        form: {
            username: '',
            email: '',
            password: '',
            currency: ''
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route('auth.register') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.successMessage = data.message;
                    setTimeout(() => {
                        window.location.href = data.redirect || '/';
                    }, 1000);
                } else if (response.status === 422) {
                    this.errors = data.errors || {};
                } else {
                    this.errorMessage = data.message || '{{ __('Произошла ошибка') }}';
                }
            } catch (error) {
                console.error('Registration error:', error);
                this.errorMessage = '{{ __('Произошла ошибка соединения') }}';
            } finally {
                this.loading = false;
            }
        }
    }
}

function openRegisterModal() {
    window.dispatchEvent(new CustomEvent('open-register-modal'));
}
</script>
