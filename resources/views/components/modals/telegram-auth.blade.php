<div x-data="{ 
        open: false, 
        selectedCurrency: '',
        telegramUser: null,
        loading: false,
        
        proceedWithTelegramAuth() {
            console.log('proceedWithTelegramAuth called', {
                selectedCurrency: this.selectedCurrency,
                telegramUser: this.telegramUser
            });
            
            if (!this.selectedCurrency) {
                console.warn('No currency selected');
                return;
            }

            if (!this.telegramUser) {
                console.warn('No telegram user data');
                return;
            }

            this.loading = true;
            
            // Добавляем валюту к данным пользователя
            const authData = {
                ...this.telegramUser,
                currency: this.selectedCurrency
            };

            console.log('Proceeding with Telegram auth:', authData);

            // Вызываем функцию авторизации
            if (window.authenticateWithTelegram) {
                window.authenticateWithTelegram(authData).finally(() => {
                    this.loading = false;
                });
            } else {
                console.error('authenticateWithTelegram function not found');
                this.loading = false;
            }
        }
     }"
     @open-telegram-auth-modal.window="
        open = true; 
        telegramUser = $event.detail.user || null;
        console.log('Telegram auth modal opened with user:', telegramUser);
     "
     @close-all-modals.window="open = false"
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
                            <div class="w-10 h-10 rounded-xl bg-[#0088cc] flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">{{ __('Вход через Telegram') }}</h2>
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
                    <!-- User Info Section -->
                    <div x-show="telegramUser" class="relative">
                        <div class="relative bg-white/[0.02] border border-white/5 rounded-xl p-4 backdrop-blur-sm">
                            <p class="text-sm text-gray-400 mb-3 text-center font-medium">{{ __('Ваш профиль Telegram') }}</p>
                            
                            <div class="flex items-center gap-4 mb-4">
                                <!-- Avatar -->
                                <div class="relative">
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0088cc] to-[#0066aa] flex items-center justify-center text-white font-bold text-xl border-2 border-[#0088cc]/30 shadow-lg">
                                        <img x-show="telegramUser?.photo_url" 
                                             :src="telegramUser?.photo_url" 
                                             :alt="telegramUser?.first_name"
                                             class="w-full h-full rounded-full object-cover">
                                        <span x-show="!telegramUser?.photo_url" 
                                              x-text="telegramUser?.first_name?.charAt(0) || '?'"></span>
                                    </div>
                                    <!-- Online indicator -->
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-[#1a1f2e] flex items-center justify-center">
                                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                                
                                <!-- User info -->
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold text-lg" 
                                        x-text="`${telegramUser?.first_name || ''} ${telegramUser?.last_name || ''}`.trim() || 'Пользователь Telegram'">
                                    </h3>
                                    <p class="text-gray-400 text-sm" x-show="telegramUser?.username" x-text="'@' + telegramUser?.username"></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="w-2 h-2 bg-[#0088cc] rounded-full"></div>
                                        <span class="text-xs text-[#0088cc] font-medium">Telegram WebApp</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Currency Selection -->
                    <div>
                        <label for="telegram-currency" class="block text-sm font-medium text-gray-400 mb-3">
                            {{ __('Выберите валюту аккаунта') }}
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500 group-focus-within:text-[#0088cc] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <select id="telegram-currency" x-model="selectedCurrency" required
                                class="w-full h-14 pl-12 pr-10 bg-white/[0.03] border border-white/10 rounded-xl text-white focus:outline-none focus:border-[#0088cc] focus:ring-1 focus:ring-[#0088cc] transition-all appearance-none cursor-pointer text-base">
                                <option value="" class="bg-[#1a1f2e]">{{ __('Выберите валюту') }}</option>
                                {{-- @foreach(\App\Models\Currency::where('active', true)->orderBy('name')->get() as $curr)
                                    <option value="{{ $curr->name }}" class="bg-[#1a1f2e]">
                                        {{ $curr->symbol }} - {{ $curr->name }}
                                    </option>
                                @endforeach --}}
                                <option value="RUB" class="bg-[#1a1f2e]">₽ - RUB</option>
                                <option value="USD" class="bg-[#1a1f2e]">$ - USD</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Валюту нельзя будет изменить после регистрации') }}
                        </p>
                    </div>

                    <!-- Login Button -->
                    <div class="space-y-3">
                        <button type="button" 
                                @click="proceedWithTelegramAuth()"
                                :disabled="!selectedCurrency || loading"
                                :class="{ 
                                    'opacity-50 cursor-not-allowed': !selectedCurrency || loading,
                                    'hover:scale-105 active:scale-95': selectedCurrency && !loading
                                }"
                                class="w-full h-14 bg-gradient-to-r from-[#0088cc] to-[#0066aa] hover:from-[#0077b3] hover:to-[#005599] text-white rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-3 shadow-lg disabled:hover:scale-100">
                            
                            <!-- Loading Spinner -->
                            <div x-show="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            
                            <!-- Telegram Icon -->
                            <svg x-show="!loading" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                            
                            <span x-show="!loading">{{ __('Войти через Telegram') }}</span>
                            <span x-show="loading">{{ __('Вход...') }}</span>
                        </button>

                        <!-- Security Info -->
                        <div class="bg-[#0088cc]/5 border border-[#0088cc]/20 rounded-xl p-3">
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-[#0088cc]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-[#0088cc]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-[#0088cc] font-medium mb-1">{{ __('Безопасный вход') }}</p>
                                    <p class="text-xs text-gray-400 leading-relaxed">
                                        {{ __('Ваши данные защищены технологией Telegram WebApp. Мы не имеем доступа к вашему номеру телефона.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Глобальная функция для открытия модала
        window.openTelegramAuthModal = function(userData) {
            window.dispatchEvent(new CustomEvent('open-telegram-auth-modal', {
                detail: { user: userData }
            }));
        };
    </script>
</div>