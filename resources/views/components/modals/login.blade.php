<!-- Модальное окно входа -->
<div x-data="{ open: false }"
     @open-login-modal.window="open = true"
     @close-login-modal.window="open = false"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">{{ __('Вход в аккаунт') }}</h2>
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
                            <p class="text-sm text-gray-400 mb-3 text-center font-medium">{{ __('Войти через:') }}</p>
                            
                            <!-- Telegram WebView Button -->
                            <div id="telegram-webview-auth" class="mb-3" style="display: none;">
                                <button type="button" id="telegram-auth-btn" 
                                        class="w-full h-12 bg-[#0088cc] hover:bg-[#0077b3] text-white rounded-xl font-semibold transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                    </svg>
                                    {{ __('Войти через Telegram') }}
                                </button>
                            </div>
                            
                            <!-- Telegram Auth Button -->
                            <div id="telegram-auth" class="mb-3" x-data="telegramAuth('login')">
                                <button type="button" @click="handleTelegramAuth($event)"
                                   :disabled="loading"
                                   :class="{'opacity-50 cursor-not-allowed': loading}"
                                   class="w-full h-12 bg-[#0088cc] hover:bg-[#0077b3] disabled:hover:bg-[#0088cc] text-white rounded-xl font-semibold transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
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
                    </div>

                    <!-- Divider -->
                    <div class="relative flex items-center">
                        <div class="flex-grow border-t border-white/10"></div>
                        <span class="flex-shrink-0 mx-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('или') }}</span>
                        <div class="flex-grow border-t border-white/10"></div>
                    </div>

                    <!-- Login Form -->
                    <div x-data="loginForm()">
                        <form @submit.prevent="submitForm" class="space-y-4">
                            @csrf

                            <!-- Email -->
                            <div>
                                <label for="login-username" class="block text-sm font-medium text-gray-400 mb-2">
                                    {{ __('Email адрес') }}
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500 group-focus-within:text-[#4a5568] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="login-username" name="email" required autocomplete="email"
                                        x-model="formData.email"
                                        :class="{'border-red-500': errors.email}"
                                        class="w-full h-12 pl-12 pr-4 bg-white/[0.03] border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-[#4a5568] focus:ring-1 focus:ring-[#4a5568] transition-all"
                                        placeholder="name@example.com">
                                </div>
                                <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-500"></p>
                            </div>

                        <!-- Password -->
                        <div x-data="{ showPassword: false }">
                            <label for="login-password" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ __('Пароль') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-[#4a5568] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" id="login-password" name="password" required autocomplete="current-password"
                                    x-model="formData.password"
                                    :class="{'border-red-500': errors.password}"
                                    class="w-full h-12 pl-12 pr-12 bg-white/[0.03] border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-[#4a5568] focus:ring-1 focus:ring-[#4a5568] transition-all"
                                    placeholder="••••••••">
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
                            <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-500"></p>
                        </div>

                        <!-- General Error (rate limit, banned, etc) -->
                        <div x-show="errors.general" class="p-3 bg-red-500/10 border border-red-500/50 rounded-lg">
                            <p x-text="errors.general" class="text-sm text-red-400"></p>
                        </div>

                        <!-- Success Message -->
                        <div x-show="success" class="p-3 bg-green-500/10 border border-green-500/50 rounded-lg">
                            <p x-text="success" class="text-sm text-green-400"></p>
                        </div>

                        <!-- Forgot Password -->
                        <div class="flex justify-end">
                            <button type="button" id="forgot-password-btn"
                                    class="text-sm text-gray-400 hover:text-[#4a5568] transition-colors font-medium">
                                {{ __('Забыли пароль?') }}
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            :disabled="loading"
                            :class="{'opacity-50 cursor-not-allowed': loading}"
                            class="relative w-full h-12 group relative px-5 py-2.5 bg-[#ffb300] rounded-xl text-black font-semibold transition-all duration-300 hover:scale-105 active:scale-95 overflow-hidden">
                            <span class="relative z-10" x-text="loading ? '{{ __('Вход...') }}' : '{{ __('Войти в аккаунт') }}'"></span>
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        </button>
                    </form>

                    <!-- Register Link -->
                    <div class="relative pt-4 border-t border-white/5">
                        <p class="text-center text-sm text-gray-400">
                            {{ __('Ещё нет аккаунта?') }}
                            <button @click="open = false; $dispatch('open-register-modal')"
                                    class="text-[#ffb300] font-semibold hover:text-[#6b7280] transition-colors ml-1">
                                {{ __('Зарегистрироваться') }}
                            </button>
                        </p>
                    </div>
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
                                        
                                        // Redirect after short delay
                                        setTimeout(() => {
                                            window.location.href = data.redirect || '/';
                                        }, 1500);
                                    } else {
                                        // Handle different error types
                                        if (response.status === 429) {
                                            // Rate limit error
                                            this.errors.general = data.message;
                                        } else if (response.status === 403) {
                                            // Banned user
                                            this.errors.general = data.message;
                                        } else if (response.status === 422) {
                                            // Validation errors or wrong credentials
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

<script>
function openLoginModal() {
    window.dispatchEvent(new CustomEvent('open-login-modal'));
}

function closeLoginModal() {
    window.dispatchEvent(new CustomEvent('close-login-modal'));
}

// Инициализация Telegram WebView авторизации
document.addEventListener('DOMContentLoaded', function() {
    // Показываем кнопку Telegram только в WebView
    if (window.isTelegramWebApp && window.isTelegramWebApp()) {
        const telegramAuthDiv = document.getElementById('telegram-webview-auth');
        const telegramAuthBtn = document.getElementById('telegram-auth-btn');
        
        if (telegramAuthDiv) {
            telegramAuthDiv.style.display = 'block';
        }
        
        // Обработчик кнопки авторизации
        if (telegramAuthBtn) {
            telegramAuthBtn.addEventListener('click', function() {
                // Если глобальный объект telegramAuth доступен
                if (window.telegramAuth) {
                    window.telegramAuth.authenticate();
                } else {
                    // Создаем новый экземпляр
                    const auth = new TelegramWebViewAuth({
                        debug: true,
                        onAuthSuccess: function(user, response) {
                            // Закрываем модальное окно
                            closeLoginModal();
                            
                            // Показываем успешное сообщение
                            if (window.showNotification) {
                                window.showNotification('success', response.message || 'Welcome!');
                            }
                            
                            // Перенаправляем после небольшой задержки
                            if (response.redirect) {
                                setTimeout(() => {
                                    window.location.href = response.redirect;
                                }, 1500);
                            } else {
                                // Перезагружаем страницу для обновления состояния авторизации
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
    
    // Автоматическая авторизация при открытии в Telegram WebView
    if (window.isTelegramWebApp && window.isTelegramWebApp() && !window.isAuthenticated) {
        // Небольшая задержка для загрузки всех скриптов
        setTimeout(() => {
            if (window.telegramAuth) {
                window.telegramAuth.authenticate();
            }
        }, 1000);
    }
});
</script>
