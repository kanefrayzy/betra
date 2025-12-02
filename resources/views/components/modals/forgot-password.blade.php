<!-- Модальное окно восстановления пароля -->
<div x-data="{ open: false }"
     @open-forgot-password-modal.window="open = true"
     @close-forgot-password-modal.window="open = false"
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
                            <div class="w-10 h-10 rounded-xl bg-[#ffb300] flex items-center justify-center shadow-lg shadow-[#ff6a20]/20">
                                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">{{ __('Восстановление пароля') }}</h2>
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
                    <!-- Info Message -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent rounded-xl"></div>
                        <div class="relative bg-white/[0.02] border border-blue-500/20 rounded-xl p-4 backdrop-blur-sm">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-5 h-5 mt-0.5">
                                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-300 leading-relaxed">
                                    {{ __('Введите email, указанный при регистрации, и мы отправим вам инструкции по сбросу пароля.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Password Form -->
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="forgot-email" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ __('Email адрес') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-[#ff6a20] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" id="forgot-email" name="email" required autofocus
                                    class="w-full h-12 pl-12 pr-4 bg-white/[0.03] border border-white/10 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-[#ff6a20] focus:ring-1 focus:ring-[#ff6a20] transition-all"
                                    placeholder="name@example.com">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="relative w-full h-12 group relative px-5 py-2.5 bg-[#ffb300] rounded-xl text-black font-semibold transition-all duration-300 hover:scale-105 active:scale-95 overflow-hidden">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                                </svg>
                                {{ __('Отправить инструкции') }}
                            </span>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="relative pt-4 border-t border-white/5">
                        <p class="text-center">
                            <button @click="open = false; $dispatch('open-login-modal')"
                                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#ff6a20] transition-colors font-medium group">
                                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                {{ __('Вернуться ко входу') }}
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// Открытие модалки восстановления пароля
function openForgotPasswordModal() {
    window.dispatchEvent(new CustomEvent('open-forgot-password-modal'));
}

// Закрытие модалки восстановления пароля
function closeForgotPasswordModal() {
    window.dispatchEvent(new CustomEvent('close-forgot-password-modal'));
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.id === 'forgot-password-btn' || e.target.closest('#forgot-password-btn')) {
            e.preventDefault();
            closeLoginModal();
            setTimeout(() => openForgotPasswordModal(), 200);
        }
    });
});
</script>
