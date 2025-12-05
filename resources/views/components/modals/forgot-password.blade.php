<div x-data="{ open: false }"
     @open-forgot-password-modal.window="open = true"
     @close-forgot-password-modal.window="open = false"
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
                        <h2 class="text-xl font-bold text-white">{{ __('Восстановление пароля') }}</h2>
                        <button @click="open = false"
                                class="text-gray-500 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="bg-[#1a2c38] border border-[#2d3748] rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-5 h-5 mt-0.5">
                                <svg class="w-5 h-5 text-[#3b82f6]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 leading-relaxed">
                                {{ __('Введите email, указанный при регистрации, и мы отправим вам инструкции по сбросу пароля.') }}
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="forgot-email" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ __('Email адрес') }}
                            </label>
                            <input type="email" id="forgot-email" name="email" required autofocus
                                class="w-full h-12 px-4 bg-[#1a2c38] border border-[#2d3748] rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#3b82f6] focus:ring-1 focus:ring-[#3b82f6] transition-all"
                                placeholder="name@example.com">
                        </div>

                        <button type="submit"
                            class="w-full h-12 bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-[#3b82f6]/20 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                            </svg>
                            {{ __('Отправить инструкции') }}
                        </button>
                    </form>

                    <div class="pt-5 border-t border-[#1a2c38]">
                        <p class="text-center">
                            <button @click="open = false; $dispatch('open-login-modal')"
                                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
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
function openForgotPasswordModal() {
    window.dispatchEvent(new CustomEvent('open-forgot-password-modal'));
}

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