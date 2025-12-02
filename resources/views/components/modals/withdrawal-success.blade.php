<!-- Красивое модальное окно успешного вывода средств -->
<div class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto" id="withdrawal-success-modal">
    <!-- Темный фон с размытием -->
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity duration-300 modal-overlay"></div>

    <!-- Контейнер модального окна -->
    <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-gradient-to-b from-[#121c35] to-[#0d1524] shadow-2xl transition-all duration-300 sm:w-full modal-content">
        <!-- Декоративные элементы фона -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-16 -right-16 h-32 w-32 rounded-full bg-[#00E8BA]/10 blur-xl"></div>
            <div class="absolute -bottom-20 -left-10 h-40 w-40 rounded-full bg-[#3F5EFF]/10 blur-xl"></div>
        </div>

        <!-- Шапка модального окна -->
        <div class="relative flex items-center justify-between border-b border-white/10 px-6 py-4 modal-header">
            <h5 class="text-lg font-bold text-white modal-title">{{ __('Статус выплаты') }}</h5>
            <button type="button" class="text-white/70 hover:text-white transition-colors close" onclick="closeWithdrawalSuccessModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Основной контент модального окна -->
        <div class="px-6 py-8 modal-body">
            <div class="flex flex-col items-center text-center withdrawal-status2">
                <!-- Анимированная иконка успеха с пульсацией -->
                <div class="relative mb-6 status-icon2">
                    <!-- Пульсирующее кольцо -->
                    <div class="absolute inset-0 h-20 w-20 animate-ping rounded-full bg-[#00E8BA]/20 opacity-75"></div>
                    <!-- Фон для иконки -->
                    <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-[#00E8BA]/10">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#00E8BA" stroke-width="2" class="h-10 w-10">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>

                <!-- Заголовок статуса -->
                <div class="mb-6 text-2xl font-bold text-white status-title2">
                    {{ __('Заявка успешно создана') }}
                </div>

                <!-- Время обработки в стильном блоке -->
                <div class="mb-6 w-full rounded-xl bg-white/5 p-4 shadow-inner processing-time">
                    <div class="mb-2 text-sm font-medium text-white/80 processing-title">{{ __('Время обработки:') }}</div>
                    <div class="flex items-center justify-center space-x-2 text-lg font-bold time-range">
                        <span class="text-[#00E8BA] time-min">10 {{ __('минут') }}</span>
                        <span class="text-white/50 time-separator">—</span>
                        <span class="text-[#00E8BA] time-max">24 {{ __('часа') }}</span>
                    </div>
                </div>

                <!-- Информация о статусе -->
                <div class="text-white/70 status-info2">
                    {{ __('Пожалуйста, дождитесь окончания обработки заявки') }}
                </div>

                <!-- Прогресс-бар обработки -->
                <div class="mt-8 w-full">
                    <div class="h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full w-1/3 animate-progress bg-gradient-to-r from-[#00E8BA] to-[#3F5EFF] rounded-full"></div>
                    </div>
                    <div class="mt-2 flex justify-between text-xs text-white/50">
                        <span>{{ __('Заявка создана') }}</span>
                        <span>{{ __('В обработке') }}</span>
                        <span>{{ __('Завершено') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопка закрытия внизу -->
        <div class="border-t border-white/10 px-6 py-4">
            <button onclick="closeWithdrawalSuccessModal()" class="w-full rounded-lg bg-gradient-to-r from-[#00E8BA] to-[#3F5EFF] px-4 py-3 text-sm font-medium text-white transition-transform hover:scale-[0.98] focus:outline-none">
                {{ __('Закрыть') }}
            </button>
        </div>
    </div>
</div>

<style>
@keyframes progress {
  0% { width: 0%; }
  33% { width: 33%; }
  100% { width: 33%; }
}

.animate-progress {
  animation: progress 2s ease-out forwards;
}

@keyframes ping {
  0% {
    transform: scale(0.95);
    opacity: 1;
  }
  75%, 100% {
    transform: scale(1.2);
    opacity: 0;
  }
}

.animate-ping {
  animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(Session::has('showWithdrawalModal'))
        showWithdrawalSuccessModal();
    @endif
});

function showWithdrawalSuccessModal() {
    const modal = document.getElementById('withdrawal-success-modal');
    const overlay = document.querySelector('.overlay');
    if (!modal) return;

    // Анимированное появление
    modal.classList.add('flex');
    modal.classList.remove('hidden');
    overlay.style.display = 'block';

    // Анимируем появление модального окна
    const content = modal.querySelector('.modal-content');
    content.classList.add('scale-100', 'opacity-100');
    content.classList.remove('scale-95', 'opacity-0');
}

function closeWithdrawalSuccessModal() {
    const modal = document.getElementById('withdrawal-success-modal');
    const overlay = document.querySelector('.overlay');
    if (!modal) return;

    // Анимированное скрытие
    const content = modal.querySelector('.modal-content');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');

    // Задержка перед скрытием
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        overlay.style.display = 'none';
    }, 300);
}

// Закрытие по клику на оверлей
document.querySelector('.modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWithdrawalSuccessModal();
    }
});
</script>
