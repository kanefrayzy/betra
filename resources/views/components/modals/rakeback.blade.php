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

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/85"
         @click="open = false"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md bg-[#0f212e] rounded-2xl shadow-2xl">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#1a2c38]">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#3b82f6]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                    </svg>
                    <h2 class="text-lg font-bold text-white">{{ __('Бонусный Центр') }}</h2>
                </div>
                <button @click="open = false" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex border-b border-[#1a2c38]">
                <button @click="type = 'rake'"
                        class="flex-1 py-3.5 text-sm font-semibold transition"
                        :class="type === 'rake' ? 'text-white bg-[#1a2c38]' : 'text-gray-400 hover:text-gray-300'">
                    {{ __('Рейкбэк') }}
                </button>
                <button @click="type = 'day-bonus'"
                        class="flex-1 py-3.5 text-sm font-semibold transition"
                        :class="type === 'day-bonus' ? 'text-white bg-[#1a2c38]' : 'text-gray-400 hover:text-gray-300'">
                    {{ __('Ежедневный') }}
                </button>
            </div>

            <div class="p-6 max-h-[500px] overflow-y-auto">
                <div class="relative min-h-[280px]">
                
                <div x-show="type === 'rake'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-4"
                     :class="{ 'absolute inset-x-0 top-0': type !== 'rake' }">
                    <div class="relative bg-[#1a2c38] rounded-xl p-6 border border-[#2d3748] mb-4 overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#3b82f6]/10 to-transparent rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#3b82f6]/5 to-transparent rounded-full blur-xl"></div>
                        
                        <div class="relative text-center">
                            <h3 class="text-white font-bold text-lg mb-2">{{ __('Получите свой рейкбек') }}</h3>
                            <p class="text-gray-400 text-sm mb-4">{{ __('У вас есть доступный рейкбек! Получите его сейчас.') }}</p>
                            
                            <div class="flex items-center justify-center gap-3 mb-6">
                                    <div class="text-3xl font-bold text-white">
                                        <span class="rakeback_balance">{{ $rakeback_balance }}</span>
                                    </div>
                                    <p class="text-gray-500 text-xs">{{ $u->currency->symbol }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('collect.rakeback') }}" method="post">
                        @csrf
                        <button type="submit"
                                class="w-full py-3.5 bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold rounded-xl transition shadow-lg shadow-[#3b82f6]/20">
                            {{ __('Получить рейкбек') }}
                        </button>
                    </form>

                    <div class="mt-4 p-4 bg-[#1a2c38] border border-[#2d3748] rounded-xl">
                        <p class="text-gray-400 text-sm">
                            {{ __('Рейкбэк накапливается с каждой ставкой в казино.') }}
                        </p>
                    </div>
                </div>

                <div x-show="type === 'day-bonus'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-4"
                     :class="{ 'absolute inset-x-0 top-0': type !== 'day-bonus' }">
                    <div class="bg-[#1a2c38] rounded-xl p-6 border border-[#2d3748] mb-4">
                        <p class="text-gray-400 text-sm text-center mb-4">{{ __('До следующего бонуса') }}</p>

                        <div class="flex justify-center gap-2 mb-6">
                            <div class="bg-[#0f212e] border border-[#2d3748] rounded-xl p-3 min-w-[60px] text-center">
                                <div class="text-2xl font-bold text-[#3b82f6]" x-text="countdown.hours"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ __('ч') }}</div>
                            </div>
                            <div class="bg-[#0f212e] border border-[#2d3748] rounded-xl p-3 min-w-[60px] text-center">
                                <div class="text-2xl font-bold text-[#3b82f6]" x-text="countdown.minutes"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ __('м') }}</div>
                            </div>
                            <div class="bg-[#0f212e] border border-[#2d3748] rounded-xl p-3 min-w-[60px] text-center">
                                <div class="text-2xl font-bold text-[#3b82f6]" x-text="countdown.seconds"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ __('с') }}</div>
                            </div>
                        </div>

                        @if(isset($settings) && isset($settings->support_tg) && $settings->support_tg)
                        <a href="https://t.me/{{ $settings->support_tg }}" target="_blank"
                           class="block w-full py-3.5 bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold rounded-xl transition text-center shadow-lg shadow-[#3b82f6]/20">
                            {{ __('Получить в Telegram') }}
                        </a>
                        @endif
                    </div>

                    <div class="p-4 bg-[#1a2c38] border border-[#2d3748] rounded-xl">
                        <p class="text-gray-400 text-sm">
                            {{ __('Ваш бонус зависит от вашего уровня. Чем выше уровень, тем больше бонус.') }}
                        </p>
                    </div>
                </div>
                
                </div>
            </div>
        </div>
    </div>
</div> 

<style>
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
        collectForm.removeEventListener('submit', handleRakebackSubmit);
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

document.addEventListener('DOMContentLoaded', initRakebackForm);
document.addEventListener('livewire:navigated', initRakebackForm);
</script>