<div x-data="{
    open: false,
    showNotifications: false
 }"
 x-init="
    Livewire.on('notificationUpdated', () => {
        $wire.refreshNotifications();
    });
    Livewire.on('allNotificationsRead', () => {
        $wire.refreshNotifications();
    });
 ">

    <div class="relative">
        <button
            @click.stop.prevent="$event.stopPropagation(); $wire.toggleNotifications(); showNotifications = !showNotifications; $nextTick(() => { if(typeof adjustNotificationsPosition === 'function') adjustNotificationsPosition(); })"
            class="relative text-gray-400 hover:text-white p-2 rounded-full hover:bg-[#1a2c38] transition-colors notifications-toggle-button"
            id="notifications-button"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>

            <div x-show="$wire.newNotificationsCount > 0"
                 class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#ffb300] text-xs text-black font-bold">
                <span x-text="$wire.newNotificationsCount"></span>
            </div>
        </button>

        <div x-show="showNotifications"
             @click.away="showNotifications = false; $wire.toggleNotifications()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="absolute right-0 mt-2 w-96 max-h-[500px] bg-[#0f212e] rounded-xl shadow-2xl border border-[#1a2c38] z-50 notifications-dropdown-arrow overflow-hidden"
             id="notifications-dropdown"
             x-cloak>

            <div class="p-4 border-b border-[#1a2c38] bg-[#0f212e]">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <h3 class="text-base font-bold text-white">{{ __('Уведомления') }}</h3>
                    </div>
                    <button @click="showNotifications = false; $wire.toggleNotifications()"
                            class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <div class="flex-1">
                        <button class="text-white font-medium pb-1">
                            {{ __('Новый') }}
                        </button>
                    </div>
                    @if(count($notifications) > 0)
                        <div>
                            <button wire:click="markAllAsRead"
                                    class="px-3 py-1.5 rounded-lg text-[#ffffff] font-semibold shadow hover:text-[#213743] transition-all duration-200">
                                {{ __('Отметить все как прочитанное') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="overflow-y-auto max-h-[420px] custom-scrollbar p-2">
                @forelse($notifications as $notification)
                    <div class="flex items-stretch gap-0 p-0 mb-3 rounded-xl overflow-hidden bg-transparent border-0 shadow-none cursor-pointer"
                         wire:click="markAsRead('{{ $notification['id'] }}')">
                        
                        <div class="flex-shrink-0 flex items-center justify-center bg-[#1a2c38] w-16 h-auto">
                            @switch($notification['data']['event'] ?? 'default')
                                @case('deposit')
                                    <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 6h-4c0 .71-.16 1.39-.43 2H20c.55 0 1 .45 1 1s-.45 1-1 1H4c-.55 0-1-.45-1-1s.45-1 1-1h3.43C7.16 7.39 7 6.71 7 6H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2m-2 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2"></path>
                                            <path d="M9.38 9h5.24C15.46 8.27 16 7.2 16 6c0-2.21-1.79-4-4-4S8 3.79 8 6c0 1.2.54 2.27 1.38 3"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('withdrawal')
                                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('bonus')
                                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('level')
                                    <div class="w-10 h-10 rounded-lg bg-[#ffb300]/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('rain')
                                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('transfer')
                                    <div class="w-10 h-10 rounded-lg bg-teal-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('win')
                                    <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                    </div>
                                    @break
                                @default
                                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                            @endswitch
                        </div>

                        <div class="flex-1 min-w-0 bg-[#213743] px-5 py-4 flex flex-col justify-center rounded-r-xl">
                            @php
                                $title = '';
                                switch($notification['data']['event'] ?? 'default') {
                                    case 'deposit':
                                        $title = __('Депозит подтвержден');
                                        break;
                                    case 'withdrawal':
                                        $title = __('Вывод обработан');
                                        break;
                                    case 'bonus':
                                        $title = __('Бонус получен');
                                        break;
                                    case 'level':
                                        $title = __('Новый уровень');
                                        break;
                                    case 'rain':
                                        $title = __('Rain раздача');
                                        break;
                                    case 'transfer':
                                        $title = __('Перевод');
                                        break;
                                    case 'win':
                                        $title = __('Выигрыш');
                                        break;
                                    default:
                                        $title = __('Уведомление');
                                }

                                $message = $notification['data']['data']['message'] ?? $notification['data']['message'] ?? '';
                                $amount = $notification['data']['data']['amount'] ?? $notification['data']['amount'] ?? null;
                                $currency = $notification['data']['data']['currency'] ?? $notification['data']['currency'] ?? null;
                                $createdAt = \Carbon\Carbon::parse($notification['created_at']);
                                $isUnread = is_null($notification['read_at'] ?? null);
                            @endphp

                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h4 class="text-base font-bold text-white leading-tight">{{ $title }}</h4>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($isUnread)
                                        <div class="w-2 h-2 bg-[#10b981] rounded-full"></div>
                                    @endif
                                    <span class="text-xs text-gray-500 whitespace-nowrap">
                                        {{ $createdAt->diffForHumans(null, true) }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm text-gray-400 leading-relaxed">
                                @if($amount && $currency)
                                    {{ __('Ваш депозит в сумме') }}
                                    <span class="inline-flex items-center gap-1 text-white font-medium">
                                        <span class="tabular-nums">{{ number_format($amount, 8, '.', '') }}</span>
                                        @if(file_exists(public_path('assets/images/curr/'.$currency.'.png')))
                                            <img src="{{ asset('assets/images/curr/'.$currency.'.png') }}" 
                                                 alt="{{ $currency }}" 
                                                 class="w-4 h-4 inline-block">
                                        @else
                                            <span class="text-xs font-bold">{{ $currency }}</span>
                                        @endif
                                    </span>
                                    {{ __('успешно обработан.') }}
                                @else
                                    {{ $message }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-[#1a2c38] rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400 font-medium">{{ __('Нет уведомлений') }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ __('Здесь появятся ваши уведомления') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>