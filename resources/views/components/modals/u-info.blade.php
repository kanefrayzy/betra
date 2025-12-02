<div x-data="userInfoModal()"
     x-show="open"
     x-cloak
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 overflow-y-auto">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="open = false"
             class="relative w-full max-w-md bg-[#1e2329] rounded-2xl overflow-hidden shadow-2xl border border-gray-800">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-800 bg-gradient-to-r from-[#ffb300]/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#ffb300]/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">{{__('Профиль игрока')}}</h3>
                </div>
                <button @click="open = false"
                        class="p-1.5 hover:bg-gray-800 rounded-lg text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="p-12">
                <div class="flex flex-col items-center justify-center">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full border-4 border-gray-800 border-t-[#ffb300] animate-spin"></div>
                        <div class="absolute inset-0 bg-[#ffb300]/20 rounded-full animate-pulse blur-md"></div>
                    </div>
                    <p class="text-gray-400 text-sm mt-4">{{__('Загрузка профиля...')}}</p>
                </div>
            </div>

            <!-- Content -->
            <div x-show="!loading && userData" class="p-6 space-y-6">
                <!-- User Card -->
                <div class="bg-gradient-to-br from-gray-800/50 to-transparent rounded-xl p-5 border border-gray-800">
                    <div class="flex items-center gap-4">
                        <!-- Avatar -->
                        <div class="relative flex-shrink-0">
                            <div class="w-20 h-20 rounded-xl overflow-hidden border-2 border-[#ffb300]/50">
                                <img :src="userData?.profile_image"
                                     :alt="userData?.username"
                                     class="w-full h-full object-cover">
                            </div>
                            <!-- Online Status -->
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-[#1e2329] flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <h4 x-text="userData?.username" class="text-lg font-bold text-white truncate mb-1"></h4>
                        </div>

                        <!-- Rank Badge -->
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-12 h-12 rounded-lg border-2 border-[#ffb300]/50 bg-gray-800 flex items-center justify-center">
                                <img :src="userData?.rank_picture"
                                     alt="Rank"
                                     class="w-8 h-8 object-contain">
                            </div>
                            <span x-text="'LVL ' + userData?.rank" class="text-xs text-[#ffb300] font-bold"></span>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-3">
                    <!-- Turnover -->
                    <div class="bg-gray-800/50 rounded-xl p-4 text-center border border-gray-800 hover:border-[#ffb300]/30 transition-colors group">
                        <div class="w-10 h-10 bg-[#ffb300]/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-[10px] text-gray-400 mb-1 uppercase tracking-wider">{{__('Оборот')}}</div>
                        <div class="text-white font-bold text-sm">
                            <span x-text="userData?.turnover"></span>
                        </div>
                        <div x-text="userData?.mycurrency" class="text-[#ffb300] text-xs mt-0.5"></div>
                    </div>

                    <!-- Wins -->
                    <div class="bg-gray-800/50 rounded-xl p-4 text-center border border-gray-800 hover:border-green-500/30 transition-colors group">
                        <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <div class="text-[10px] text-gray-400 mb-1 uppercase tracking-wider">{{__('Выигрыши')}}</div>
                        <div x-text="userData?.total_wins" class="text-green-400 font-bold text-lg"></div>
                    </div>

                    <!-- Games -->
                    <div class="bg-gray-800/50 rounded-xl p-4 text-center border border-gray-800 hover:border-purple-500/30 transition-colors group">
                        <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-[10px] text-gray-400 mb-1 uppercase tracking-wider">{{__('Игр')}}</div>
                        <div x-text="userData?.total_games" class="text-purple-400 font-bold text-lg"></div>
                    </div>
                </div>

                <!-- Admin Panel -->
                @auth
                    @if ($u->is_moder || $u->is_admin || $u->is_chat_moder)
                        <div class="bg-gradient-to-br from-red-500/10 to-transparent rounded-xl border border-red-500/20 overflow-hidden">
                            <!-- Admin Header -->
                            <div class="flex items-center justify-between p-4 border-b border-red-500/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <h4 class="text-white font-bold text-sm">{{__('Модерация')}}</h4>
                                </div>
                                <span class="text-[10px] text-red-400 bg-red-500/20 px-2 py-1 rounded-full font-semibold">{{__('ADMIN')}}</span>
                            </div>

                            <div class="p-4 space-y-3">
                                <!-- Toggle Ban Form Button -->
                                <button @click="showBanForm = !showBanForm"
                                        class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 0 5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                    <span x-text="showBanForm ? '{{__('Скрыть')}}' : '{{__('Управление')}}'"></span>
                                </button>

                                <!-- Ban Form -->
                                <div x-show="showBanForm"
                                     x-collapse
                                     class="space-y-3">
                                    <div class="text-white text-sm font-medium mb-2">{{__('Причина бана')}}</div>

                                    <!-- Ban Reasons -->
                                    <label class="flex items-center p-3 bg-gray-800/50 rounded-lg cursor-pointer hover:bg-gray-800 border border-transparent hover:border-red-500/30 transition-all group">
                                        <input type="radio"
                                               x-model="banReason"
                                               value="1"
                                               class="w-4 h-4 text-red-500 bg-transparent border-gray-600 focus:ring-red-500 focus:ring-2">
                                        <span class="ml-3 text-sm text-gray-300 group-hover:text-white transition-colors">{{__('Просит деньги')}}</span>
                                    </label>

                                    <label class="flex items-center p-3 bg-gray-800/50 rounded-lg cursor-pointer hover:bg-gray-800 border border-transparent hover:border-red-500/30 transition-all group">
                                        <input type="radio"
                                               x-model="banReason"
                                               value="2"
                                               class="w-4 h-4 text-red-500 bg-transparent border-gray-600 focus:ring-red-500 focus:ring-2">
                                        <span class="ml-3 text-sm text-gray-300 group-hover:text-white transition-colors">{{__('Просит дождь')}}</span>
                                    </label>

                                    <label class="flex items-center p-3 bg-gray-800/50 rounded-lg cursor-pointer hover:bg-gray-800 border border-transparent hover:border-red-500/30 transition-all group">
                                        <input type="radio"
                                               x-model="banReason"
                                               value="3"
                                               class="w-4 h-4 text-red-500 bg-transparent border-gray-600 focus:ring-red-500 focus:ring-2">
                                        <span class="ml-3 text-sm text-gray-300 group-hover:text-white transition-colors">{{__('Нарушение правил')}}</span>
                                    </label>

                                    <label class="flex items-center p-3 bg-gray-800/50 rounded-lg cursor-pointer hover:bg-gray-800 border border-transparent hover:border-red-500/30 transition-all group">
                                        <input type="radio"
                                               x-model="banReason"
                                               value="4"
                                               class="w-4 h-4 text-red-500 bg-transparent border-gray-600 focus:ring-red-500 focus:ring-2">
                                        <span class="ml-3 text-sm text-gray-300 group-hover:text-white transition-colors">{{__('Спам')}}</span>
                                    </label>

                                    <!-- Duration & Ban Button -->
                                    <div class="flex gap-2 pt-2">
                                        <div class="relative flex-1">
                                            <select x-model="banDuration"
                                                    class="w-full h-11 px-3 pr-10 bg-gray-800 border border-gray-700 focus:border-red-500 rounded-lg text-white text-sm appearance-none focus:outline-none transition-colors">
                                                <option value="60">{{__('1 час')}}</option>
                                                <option value="1440">{{__('1 день')}}</option>
                                                @if (!$u->is_chat_moder)
                                                    <option value="-1">{{__('Навсегда')}}</option>
                                                @endif
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <button @click="banUser()"
                                                :disabled="!banReason"
                                                class="bg-red-600 hover:bg-red-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold h-11 px-6 rounded-lg transition-all duration-200 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 0 5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                            {{__('Бан')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function userInfoModal() {
    return {
        open: false,
        loading: false,
        userData: null,
        userId: null,
        showBanForm: false,
        banReason: null,
        banDuration: '60',

        init() {
            window.addEventListener('open-user-info', (event) => {
                this.openModal(event.detail.userId);
            });
        },

        async openModal(userId) {
            this.userId = userId;
            this.open = true;
            this.loading = true;
            this.userData = null;
            this.showBanForm = false;
            this.banReason = null;

            try {
                const response = await fetch(`/chat/user-info/${userId}`);
                const data = await response.json();
                this.userData = data;
            } catch (error) {
                console.error('Error loading user info:', error);
            } finally {
                this.loading = false;
            }
        },

        async banUser() {
            if (!this.banReason) return;

            try {
                const response = await fetch(`/chat/ban-user/${this.userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        reason: this.banReason,
                        duration: this.banDuration
                    })
                });

                if (response.ok) {
                    this.open = false;
                    // Показать уведомление об успехе
                    if (typeof showSuccessNotification === 'function') {
                        showSuccessNotification('{{ __("Пользователь забанен") }}');
                    }
                }
            } catch (error) {
                console.error('Error banning user:', error);
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification('{{ __("Ошибка при бане пользователя") }}');
                }
            }
        }
    }
}

// Helper function to open modal
window.openModalWithMyInfo = function() {
    window.dispatchEvent(new CustomEvent('open-user-info', {
        detail: { userId: {{ auth()->id() ?? 0 }} }
    }));
};
</script>
