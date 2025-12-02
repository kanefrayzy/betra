@auth
<div x-data="{
    open: false,
    type: 'rake',
    countdown: {hours: '00', minutes: '00', seconds: '00'},
    calculateCountdown() {
        const now = new Date();
        const mskOffset = 3 * 60 * 60 * 1000;
        const mskNow = new Date(now.getTime() + (now.getTimezoneOffset() * 60000) + mskOffset);
        const target = new Date(mskNow);
        target.setHours(14, 0, 0, 0);

        if (mskNow > target) target.setDate(target.getDate() + 1);

        const diff = target - mskNow;
        this.countdown.hours = Math.floor(diff / 3600000).toString().padStart(2, '0');
        this.countdown.minutes = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
        this.countdown.seconds = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
    }
}"
     @open-rakeback-modal.window="open = true"
     @close-rakeback-modal.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     x-init="calculateCountdown(); setInterval(() => calculateCountdown(), 1000)"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/80"
         @click="open = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md bg-[#1e2329] rounded-2xl shadow-2xl">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-800">
                <h2 class="text-xl font-bold text-white">{{ __('Бонусный Центр') }}</h2>
                <button @click="open = false" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-800">
                <button @click="type = 'rake'"
                        class="flex-1 py-3 text-sm font-medium transition relative"
                        :class="type === 'rake' ? 'text-white' : 'text-gray-500 hover:text-gray-300'">
                    {{ __('Рейкбэк') }}
                    <div x-show="type === 'rake'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                </button>
                <button @click="type = 'day-bonus'"
                        class="flex-1 py-3 text-sm font-medium transition relative"
                        :class="type === 'day-bonus' ? 'text-white' : 'text-gray-500 hover:text-gray-300'">
                    {{ __('Ежедневный') }}
                    <div x-show="type === 'day-bonus'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 max-h-[500px] overflow-y-auto">
                <!-- Tab Content Wrapper with min-height -->
                <div class="relative min-h-[280px]">
                
                <!-- Rakeback -->
                <div x-show="type === 'rake'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-4"
                     :class="{ 'absolute inset-x-0 top-0': type !== 'rake' }">
                    <div class="bg-gradient-to-br from-[#ffb300]/10 to-transparent rounded-xl p-6 border border-[#ffb300]/20 mb-4">
                        <div class="text-center">
                            <p class="text-gray-400 text-sm mb-2">{{ __('Доступно') }}</p>
                            <div class="text-4xl font-bold text-white mb-1">
                                <span class="rakeback_balance">{{ $rakeback_balance }}</span>
                            </div>
                            <p class="text-gray-500 text-sm">{{ $u->currency->symbol }}</p>
                        </div>
                    </div>

                    <form action="{{ route('collect.rakeback') }}" method="post">
                        @csrf
                        <button type="submit"
                                class="w-full py-3 bg-[#ffb300] hover:bg-[#e6a000] text-black font-semibold rounded-lg transition">
                            {{ __('Получить') }}
                        </button>
                    </form>

                    <div class="mt-4 p-4 bg-gray-800/50 rounded-lg">
                        <p class="text-gray-400 text-sm">
                            {{ __('Рейкбэк накапливается с каждой ставкой в казино.') }}
                        </p>
                    </div>
                </div>

                <!-- Daily Bonus -->
                <div x-show="type === 'day-bonus'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-4"
                     :class="{ 'absolute inset-x-0 top-0': type !== 'day-bonus' }">
                    <div class="bg-gradient-to-br from-[#ffb300]/10 to-transparent rounded-xl p-6 border border-[#ffb300]/20 mb-4">
                        <p class="text-gray-400 text-sm text-center mb-4">{{ __('До следующего бонуса') }}</p>

                        <div class="flex justify-center gap-2 mb-6">
                            <div class="bg-gray-800 rounded-lg p-3 min-w-[60px] text-center">
                                <div class="text-2xl font-bold text-[#ffb300]" x-text="countdown.hours"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ __('ч') }}</div>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-3 min-w-[60px] text-center">
                                <div class="text-2xl font-bold text-[#ffb300]" x-text="countdown.minutes"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ __('м') }}</div>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-3 min-w-[60px] text-center">
                                <div class="text-2xl font-bold text-[#ffb300]" x-text="countdown.seconds"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ __('с') }}</div>
                            </div>
                        </div>

                        @if(isset($settings) && isset($settings->support_tg) && $settings->support_tg)
                        <a href="https://t.me/{{ $settings->support_tg }}" target="_blank"
                           class="block w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition text-center">
                            {{ __('Получить в Telegram') }}
                        </a>
                        @endif
                    </div>

                    <div class="p-4 bg-gray-800/50 rounded-lg">
                        <p class="text-gray-400 text-sm">
                            {{ __('Ваш бонус зависит от вашего уровня. Чем выше уровень, тем больше бонус.') }}
                        </p>
                    </div>
                </div>
                
                </div><!-- End Tab Content Wrapper -->
            </div>
        </div>
    </div>
</div> 

<style>
    /* Smooth transitions for rakeback modal tabs */
    [x-show][style*="display: none"] {
        pointer-events: none;
    }
</style>

<script>
function openRakebackModal() {
    window.dispatchEvent(new CustomEvent('open-rakeback-modal'));
}

function closeRakebackModal() {
    window.dispatchEvent(new CustomEvent('close-rakeback-modal'));
}

function initRakebackForm() {
    const collectForm = document.querySelector('form[action="{{ route("collect.rakeback") }}"]');
    if (collectForm) {
        // Удаляем старый обработчик если есть
        collectForm.removeEventListener('submit', handleRakebackSubmit);
        // Добавляем новый
        collectForm.addEventListener('submit', handleRakebackSubmit);
    }
}

function handleRakebackSubmit(e) {
    e.preventDefault();

    fetch('{{ route("collect.rakeback") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        closeRakebackModal();

        if (data.success) {
            document.querySelectorAll('.rakeback_balance').forEach(el => el.textContent = '0');
            if (typeof showSuccessNotification === 'function') {
                showSuccessNotification(data.message);
            }
        } else {
            if (typeof showErrorNotification === 'function') {
                showErrorNotification(data.message);
            }
        }
    })
    .catch(() => {
        if (typeof showErrorNotification === 'function') {
            showErrorNotification('An error occurred');
        }
    });
}

// Инициализация при загрузке и при навигации Livewire
document.addEventListener('DOMContentLoaded', initRakebackForm);
document.addEventListener('livewire:navigated', initRakebackForm);
</script>
@endauth
