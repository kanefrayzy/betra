<div x-data="{ open: false }"
     @open-login-modal.window="open = true"
     @close-login-modal.window="open = false"
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
                        <h2 class="text-xl font-bold text-white">{{ __('Вход в аккаунт') }}</h2>
                        <button @click="open = false"
                                class="text-gray-500 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-2 space-y-5">
                    <div x-data="loginForm()">
                        <form @submit.prevent="submitForm" class="space-y-4">
                            @csrf

                            <div>
                                <label for="login-username" class="block text-sm font-medium text-gray-400 mb-2">
                                    {{ __('Email адрес') }}
                                </label>
                                <input type="text" id="login-username" name="email" required autocomplete="email"
                                    x-model="formData.email"
                                    :class="{'border-red-500': errors.email}"
                                    class="w-full h-12 px-4 bg-[#1a2c38] border border-[#2d3748] rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#3b82f6] focus:ring-1 focus:ring-[#3b82f6] transition-all"
                                    placeholder="name@example.com">
                                <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-500"></p>
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label for="login-password" class="block text-sm font-medium text-gray-400 mb-2">
                                    {{ __('Пароль') }}
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" id="login-password" name="password" required autocomplete="current-password"
                                        x-model="formData.password"
                                        :class="{'border-red-500': errors.password}"
                                        class="w-full h-12 px-4 pr-12 bg-[#1a2c38] border border-[#2d3748] rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#3b82f6] focus:ring-1 focus:ring-[#3b82f6] transition-all"
                                        placeholder="••••••••">
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
                                <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-500"></p>
                            </div>

                            <div x-show="errors.general" class="p-3 bg-red-500/10 border border-red-500/30 rounded-lg">
                                <p x-text="errors.general" class="text-sm text-red-400"></p>
                            </div>

                            <div x-show="success" class="p-3 bg-[#4dda30]/10 border border-[#4dda30]/30 rounded-lg">
                                <p x-text="success" class="text-sm text-[#4dda30]"></p>
                            </div>

                            <div class="flex justify-end">
                                <button type="button" id="forgot-password-btn"
                                        class="text-sm text-gray-400 hover:text-white transition-colors">
                                    {{ __('Забыли пароль?') }}
                                </button>
                            </div>

                            <button type="submit"
                                :disabled="loading"
                                :class="{'opacity-50 cursor-not-allowed': loading}"
                                class="w-full h-12 bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-[#3b82f6]/20">
                                <span x-text="loading ? '{{ __('Вход...') }}' : '{{ __('Войти') }}'"></span>
                            </button>
                        </form>

                        <div class="relative flex items-center my-5">
                            <div class="flex-grow border-t border-[#2d3748]"></div>
                            <span class="flex-shrink-0 mx-4 text-xs font-medium text-gray-500 uppercase">{{ __('или') }}</span>
                            <div class="flex-grow border-t border-[#2d3748]"></div>
                        </div>

                        <div class="space-y-3">
                            <div id="telegram-webview-auth" style="display: none;">
                                <button type="button" id="telegram-auth-btn" 
                                        class="w-full h-12 bg-[#1a2c38] hover:bg-[#2d3748] border border-[#2d3748] text-white rounded-xl font-medium transition-all flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                    </svg>
                                    {{ __('Войти через Telegram') }}
                                </button>
                            </div>
                            
                            <div id="telegram-auth" x-data="telegramAuth('login')">
                                <button type="button" @click="handleTelegramAuth($event)"
                                   :disabled="loading"
                                   :class="{'opacity-50 cursor-not-allowed': loading}"
                                   class="w-full h-12 bg-[#1a2c38] hover:bg-[#2d3748] disabled:hover:bg-[#1a2c38] border border-[#2d3748] text-white rounded-xl font-medium transition-all flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                    </svg>
                                    <span x-show="!loading">{{ __('Войти через Telegram') }}</span>
                                    <span x-show="loading" x-cloak>{{ __('Загрузка...') }}</span>
                                </button>
                            </div>
                            
                            <div id="uLogin2"
                                 data-ulogin="display=panel;theme=flat;fields=first_name,last_name;providers=google,steam;hidden=;redirect_uri={{ urlencode(route('auth.ulogin')) }};mobilebuttons=0;"></div>
                        </div>

                        <div class="pt-5 border-t border-[#1a2c38] mt-5">
                            <p class="text-center text-sm text-gray-400">
                                {{ __('Ещё нет аккаунта?') }}
                                <button @click="open = false; $dispatch('open-register-modal')"
                                        class="text-[#3b82f6] font-semibold hover:text-[#2563eb] transition-colors ml-1">
                                    {{ __('Зарегистрироваться') }}
                                </button>
                            </p>
                        </div>

                        <script>
                            function loginForm() {
                                return {
                                    formData: {
                                        email: '',
                                        password: ''
                                    },
                                    errors: {},
                                    success: '',
                                    loading: false,

                                    async submitForm() {
                                        this.errors = {};
                                        this.success = '';
                                        this.loading = true;

                                        try {
                                            const formData = new FormData();
                                            formData.append('email', this.formData.email);
                                            formData.append('password', this.formData.password);
                                            formData.append('_token', document.querySelector('input[name="_token"]').value);

                                            const response = await fetch('{{ route('auth.login') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                },
                                                body: formData
                                            });

                                            const data = await response.json();

                                            if (response.ok) {
                                                this.success = data.message || '{{ __('С возвращением!') }}';
                                                
                                                setTimeout(() => {
                                                    window.location.href = data.redirect || '/';
                                                }, 1500);
                                            } else {
                                                if (response.status === 429) {
                                                    this.errors.general = data.message;
                                                } else if (response.status === 403) {
                                                    this.errors.general = data.message;
                                                } else if (response.status === 422) {
                                                    if (data.errors) {
                                                        this.errors = data.errors;
                                                    } else if (data.message) {
                                                        this.errors.general = data.message;
                                                    }
                                                } else {
                                                    this.errors.general = data.message || '{{ __('Произошла ошибка') }}';
                                                }
                                            }
                                        } catch (error) {
                                            console.error('Login error:', error);
                                            this.errors.general = '{{ __('Ошибка соединения с сервером') }}';
                                        } finally {
                                            this.loading = false;
                                        }
                                    }
                                }
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openLoginModal() {
    window.dispatchEvent(new CustomEvent('open-login-modal'));
}

function closeLoginModal() {
    window.dispatchEvent(new CustomEvent('close-login-modal'));
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.isTelegramWebApp && window.isTelegramWebApp()) {
        const telegramAuthDiv = document.getElementById('telegram-webview-auth');
        const telegramAuthBtn = document.getElementById('telegram-auth-btn');
        
        if (telegramAuthDiv) {
            telegramAuthDiv.style.display = 'block';
        }
        
        if (telegramAuthBtn) {
            telegramAuthBtn.addEventListener('click', function() {
                if (window.telegramAuth) {
                    window.telegramAuth.authenticate();
                } else {
                    const auth = new TelegramWebViewAuth({
                        debug: true,
                        onAuthSuccess: function(user, response) {
                            closeLoginModal();
                            
                            if (window.showNotification) {
                                window.showNotification('success', response.message || 'Welcome!');
                            }
                            
                            if (response.redirect) {
                                setTimeout(() => {
                                    window.location.href = response.redirect;
                                }, 1500);
                            } else {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        },
                        onAuthError: function(error) {
                            if (window.showNotification) {
                                window.showNotification('error', error.message || 'Authorization Error');
                            } else {
                                alert('Ошибка: ' + (error.message || 'Authorization Error'));
                            }
                        }
                    });
                }
            });
        }
    }
    
    if (window.isTelegramWebApp && window.isTelegramWebApp() && !window.isAuthenticated) {
        setTimeout(() => {
            if (window.telegramAuth) {
                window.telegramAuth.authenticate();
            }
        }, 1000);
    }
});
</script>