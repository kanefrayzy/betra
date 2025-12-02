<!-- Модальное окно регистрации -->
<div x-data="registerForm()"
     @open-register-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/90 backdrop-blur-md"
         @click="open = false"></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md">

            <!-- Modal Content -->
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-[#1a1f2e] to-[#0f1419] shadow-2xl border border-[#2a3441]">

                <!-- Header with gradient -->
                <div class="relative px-6 py-5 border-b border-[#2a3441]">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0iZ3JpZCIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj48cGF0aCBkPSJNIDQwIDAgTCAwIDAgMCA0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwxMDYsMzIsMC4wNSkiIHN0cm9rZS13aWR0aD0iMSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmlkKSIvPjwvc3ZnPg==')] opacity-30"></div>

                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#ffb300] flex items-center justify-center">
                                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">{{ __('Регистрация') }}</h2>
                        </div>
                        <button @click="open = false"
                                class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-all group">
                            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <!-- Social Login -->
                    <div class="relative">
                        <div class="absolute inset-0 rounded-xl"></div>
                        <div class="relative bg-white/[0.02] border border-white/5 rounded-xl p-4 backdrop-blur-sm">
                            <p class="text-sm text-gray-400 mb-3 text-center font-medium">{{ __('Быстрая регистрация через:') }}</p>
                            
                            <!-- Telegram Auth Button -->
                            <div id="telegram-auth-register" class="mb-3" x-data="telegramAuth('register')">
                                <a :href="deepLink || '#'" @click="handleTelegramAuth($event)" target="_blank"
                                   :class="{'pointer-events-none opacity-50': loading}"
                                   class="w-full h-12 bg-[#0088cc] hover:bg-[#0077b3] text-white rounded-xl font-semibold transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                    </svg>
                                    <span x-show="!loading">{{ __('Регистрация через Telegram') }}</span>
                                    <span x-show="loading" x-cloak>{{ __('Загрузка...') }}</span>
                                </a>
                            </div>
                            
                            <div id="uLogin"
                                 data-ulogin="display=panel;theme=flat;fields=first_name,last_name;providers=google,steam;hidden=;redirect_uri={{ urlencode(route('auth.ulogin')) }};mobilebuttons=0;"></div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="relative flex items-center">
                        <div class="flex-grow border-t border-white/10"></div>
                        <span class="flex-shrink-0 mx-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('или') }}</span>
                        <div class="flex-grow border-t border-white/10"></div>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        @csrf

                        <div x-show="successMessage" x-transition class="p-3 bg-green-500/10 border border-green-500/50 rounded-lg text-green-400 text-sm" style="display: none;">
                            <span x-text="successMessage"></span>
                        </div>

                        <div x-show="errorMessage" x-transition class="p-3 bg-red-500/10 border border-red-500/50 rounded-lg text-red-400 text-sm" style="display: none;">
                            <span x-text="errorMessage"></span>
                        </div>
                        <!-- Username and Currency Row -->
                        <div class="grid grid-cols-12 gap-3" x-data="{ 
                            open: false,
                            currencies: [
                                { code: 'USD', flag: 'en' },
                                { code: 'RUB', flag: 'ru' },
                                <!-- { code: 'KZT', flag: 'kz' }, -->
                                <!-- { code: 'TRY', flag: 'tr' }, -->
                                <!-- { code: 'AZN', flag: 'az' }, -->
                                <!-- { code: 'UZS', flag: 'uz' }, -->
                                <!-- { code: 'EUR', flag: 'eur' }, -->
                                <!-- { code: 'PLN', flag: 'pl' } -->
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
                                // Auto-detect currency based on locale
                                const locale = '{{ app()->getLocale() }}';
                                const currencyMap = {
                                    'ru': 'RUB',
                                    <!-- 'kz': 'KZT', -->
                                    <!-- 'tr': 'TRY', -->
                                    <!-- 'az': 'AZN', -->
                                    <!-- 'uz': 'UZS', -->
                                    'en': 'USD',
                                    <!-- 'de': 'EUR', -->
                                    <!-- 'pr': 'PLN' -->
                                };
                                this.selectedCurrency = currencyMap[locale] || 'USD';
                                form.currency = this.selectedCurrency;
                            }
                        }"
                        @click.away="open = false">
                            
                            <!-- Username (70% width) -->
                            <div class="col-span-8">
                                <label for="reg-username" class="block text-sm font-medium text-gray-400 mb-2">
                                    {{ __('Имя пользователя') }}
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500 group-focus-within:text-[#4a5568] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="reg-username" x-model="form.username" required
                                        class="w-full h-12 pl-12 pr-4 bg-white/[0.03] border transition-all rounded-xl text-white placeholder-gray-600 focus:outline-none focus:ring-1"
                                        :class="errors.username ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/10 focus:border-[#4a5568] focus:ring-[#4a5568]'"
                                        placeholder="{{ __('Введите имя пользователя') }}">
                                </div>
                                <p x-show="errors.username" x-text="errors.username?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                            </div>

                            <!-- Currency (30% width) -->
                            <div class="col-span-4 relative">
                                <label class="block text-sm font-medium text-gray-400 mb-2">
                                    {{ __('Валюта') }}
                                </label>
                                
                                <!-- Selected Currency Display -->
                                <div @click="open = !open" 
                                    class="relative w-full h-12 px-3 bg-white/[0.03] border rounded-xl cursor-pointer transition-all group"
                                    :class="errors.currency ? 'border-red-500' : (open ? 'border-[#4a5568] ring-1 ring-[#4a5568]' : 'border-white/10 hover:border-white/20')">
                                    
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
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Compact Dropdown Menu -->
                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 w-full mt-2 bg-[#1a1f2e] border border-white/10 rounded-xl shadow-2xl overflow-hidden"
                                    style="display: none;">
                                    
                                    <div class="max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                                        <template x-for="currency in currencies" :key="currency.code">
                                            <div @click="selectCurrency(currency.code)"
                                                class="flex items-center gap-2 px-3 py-2.5 hover:bg-white/5 cursor-pointer transition-colors"
                                                :class="selectedCurrency === currency.code ? 'bg-[#ffb300]/10' : ''">
                                                
                                                <img :src="`/assets/images/lang/${currency.flag}.png`" 
                                                    :alt="currency.code"
                                                    class="w-5 h-5 rounded object-cover">
                                                
                                                <span class="text-white font-medium text-sm flex-1" x-text="currency.code"></span>
                                                
                                                <span x-show="selectedCurrency === currency.code" class="text-[#ffb300]">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <p x-show="errors.currency" x-text="errors.currency?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                            </div>
                        </div>
                            <p class="mt-1.5 text-xs text-gray-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('Валюту нельзя будет изменить после регистрации') }}
                            </p>
                        <div>
                            <label for="reg-email" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ __('Email') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-[#4a5568] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" id="reg-email" x-model="form.email" required
                                    class="w-full h-12 pl-12 pr-4 bg-white/[0.03] border transition-all rounded-xl"
                                    :class="errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/10 focus:border-[#4a5568] focus:ring-[#4a5568]'"
                                    class="rounded-xl text-white placeholder-gray-600 focus:outline-none focus:ring-1"
                                    placeholder="{{ __('Введите email') }}">
                            </div>
                            <p x-show="errors.email" x-text="errors.email?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                        </div>

                        <div x-data="{ showPassword: false }">
                            <label for="reg-password" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ __('Пароль') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-[#4a5568] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" id="reg-password" x-model="form.password" required
                                    class="w-full h-12 pl-12 pr-12 bg-white/[0.03] border transition-all rounded-xl"
                                    :class="errors.password ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/10 focus:border-[#4a5568] focus:ring-[#4a5568]'"
                                    class="rounded-xl text-white placeholder-gray-600 focus:outline-none focus:ring-1"
                                    placeholder="{{ __('Минимум 6 символов') }}">
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition-colors">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="errors.password" x-text="errors.password?.[0]" class="mt-1.5 text-xs text-red-400" style="display: none;"></p>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="relative w-full h-12 group relative px-5 py-2.5 bg-[#ffb300] rounded-xl text-black font-semibold transition-all duration-300 hover:scale-105 active:scale-95 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            <span class="relative z-10" x-text="loading ? '{{ __('Загрузка...') }}' : '{{ __('Создать аккаунт') }}'"></span>
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="relative pt-4 border-t border-white/5">
                        <p class="text-center text-sm text-gray-400">
                            {{ __('Уже есть аккаунт?') }}
                            <button @click="open = false; $dispatch('open-login-modal')"
                                    class="text-[#ffb300] font-semibold hover:text-[#6b7280] transition-colors ml-1">
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

<style>
/* Custom scrollbar for currency dropdown */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
