<div>
    <!-- Заголовок -->
    <div class="p-3 border-b border-gray-800">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-white">{{ __('Уведомления') }}</h4>
            @if(count($notifications) > 0)
                <button wire:click="markAllAsRead"
                        class="text-xs text-[#ffb300] hover:text-[#e6a000] transition-colors">
                    {{ __('Прочитать все') }}
                </button>
            @endif
        </div>
    </div>

    <div class="divide-y divide-gray-800">
        @forelse($notifications->take(5) as $notification)
            <div class="p-3 hover:bg-gray-800/50 transition-colors cursor-pointer"
                 wire:click="markAsRead('{{ $notification->id }}')">
                <div class="flex items-start gap-2">
                    <!-- Мини иконка -->
                    <div class="flex-shrink-0 mt-1">
                        @switch($notification->data['event'])
                            @case('rain')
                                <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                @break
                            @case('transfer')
                                <div class="w-2 h-2 bg-teal-400 rounded-full"></div>
                                @break
                            @case('level')
                                <div class="w-2 h-2 bg-[#ffb300] rounded-full"></div>
                                @break
                            @case('deposit')
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                @break
                            @case('withdrawal')
                                <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                                @break
                            @case('bonus')
                                <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                @break
                            @case('win')
                                <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                                @break
                            @default
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        @endswitch
                    </div>

                    <!-- Содержание -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-white leading-relaxed line-clamp-2">
                            {{ $notification->data['data']['message'] ?? $notification->data['message'] ?? 'Уведомление' }}
                        </p>
                        <span class="text-xs text-gray-500 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Непрочитанный индикатор -->
                    @if(is_null($notification->read_at))
                        <div class="w-1.5 h-1.5 bg-[#ffb300] rounded-full mt-2"></div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-4 text-center">
                <p class="text-xs text-gray-500">{{ __('Нет уведомлений') }}</p>
            </div>
        @endforelse
    </div>

    @if(count($notifications) > 5)
        <div class="p-3 border-t border-gray-800">
            <button onclick="showAllNotifications()" 
                    class="w-full text-xs text-[#ffb300] hover:text-[#e6a000] transition-colors">
                {{ __('Показать все') }} ({{ count($notifications) }})
            </button>
        </div>
    @endif
</div>