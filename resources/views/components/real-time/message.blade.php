<!-- Обновленный шаблон сообщений messages.blade.php -->

@props(['message'])
<div class="mb-4 {{ $message->user_id == auth()->id() ? 'ml-auto max-w-[85%]' : 'mr-auto max-w-[85%]' }} chat-message-appear" id="message-{{ $message->id }}">
    <div class="{{ $message->user_id == auth()->id()
        ? 'bg-gradient-to-r from-primary/5 to-primary/15 border-primary/20'
        : 'bg-dark-800/80 backdrop-blur-sm border-dark-700/80' }}
        rounded-lg border p-3 shadow-md transition-all duration-200 hover:shadow-lg
        {{ $message->user_id == auth()->id() ? 'rounded-br-none' : 'rounded-bl-none' }}">

        <!-- Шапка сообщения с улучшенным дизайном -->
        <div class="mb-1.5 flex items-center justify-between">
            <div class="flex items-center">
                <!-- Аватар с рамкой -->
                <div class="relative mr-2">
                    <div class="h-7 w-7 rounded-full overflow-hidden border border-dark-700 shadow-sm">
                        <img src="{{ $message->user->avatar ?? '/assets/images/avatar-placeholder.png' }}"
                             alt="{{ $message->user->name }}"
                             class="h-full w-full object-cover">
                    </div>
                    <!-- Индикатор администратора/модератора -->
                    @if($message->user->is_moder || $message->user->is_admin)
                        <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-primary border border-dark-800"></div>
                    @endif
                </div>

                <div class="flex flex-col">
                    <!-- Имя пользователя с бейджем -->
                    <div class="flex items-center">
                        <span class="font-medium text-white text-sm">{{ $message->user->name }}</span>
                        @if($message->user->is_moder || $message->user->is_admin)
                            <span class="ml-1 text-[10px] px-1 py-0.5 rounded bg-primary/20 text-primary font-medium">MOD</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Время с улучшенным отображением -->
            <span class="text-xs text-gray-500 bg-dark-900/50 px-1.5 py-0.5 rounded-full border border-dark-700/30">
                {{ $message->created_at->format('H:i') }}
            </span>
        </div>

        <!-- Содержимое сообщения -->
        <div class="text-sm {{ $message->user_id == auth()->id() ? 'text-white' : 'text-gray-200' }}">
            {{ $message->content }}
        </div>

        <!-- Изображение, если есть -->
        @if($message->image)
            <div class="mt-2 rounded-md overflow-hidden border border-dark-700">
                <img src="{{ asset('storage/' . $message->image) }}"
                     alt="Attached Image"
                     class="max-w-full h-auto"
                     loading="lazy">
            </div>
        @endif

        <!-- Индикатор для своих сообщений -->
        @if($message->user_id == auth()->id())
            <div class="mt-1 flex items-center justify-end">
                <span class="text-[10px] text-gray-500">
                    <!-- Иконка проверки доставки -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline-block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
            </div>
        @endif
    </div>
</div>

<style>
    /* Эффект наведения на сообщения */
    .chat-message-appear:hover {
        z-index: 5;
    }

    /* Анимация для сообщений */
    @keyframes messageAppear {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-message-appear {
        animation: messageAppear 0.3s ease-out forwards;
    }

    /* Улучшенные тени для сообщений */
    #message-{{ $message->id }}:hover .shadow-md {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
