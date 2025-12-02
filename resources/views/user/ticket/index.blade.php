<x-layouts.app>
    <div class="support-ticket-list-container">
        <h1 class="support-ticket-list-title">{{ __('Мои Тикеты') }}</h1>

        @if($tickets->isEmpty())
            <p class="support-ticket-empty">{{ __('У вас нет тикетов') }}</p>
        @else
            <div class="support-ticket-list">
                @foreach($tickets as $ticket)
                    <div class="support-ticket-card">
                        <h3 class="support-ticket-card-title">{{ $ticket->subject }}</h3>
                        <p class="support-ticket-status">{{ __('Статус') }}: {{ $ticket->status == 'open' ? __('Открыт') : __('Закрыт') }}</p>

                        @if($ticket->has_new_messages)
                            <span class="support-ticket-new-message">{{ __('Новое сообщение!') }}</span>
                        @endif

                        <a href="{{ route('support.ticket.message.index', $ticket->id) }}" class="support-ticket-view-btn">
                            {{ __('Открыть тикет') }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>

<style>
    .support-ticket-list-container {
        padding: 30px;
        color: white;
    }

    .support-ticket-list-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .support-ticket-empty {
        color: #b9bbbe;
        font-size: 16px;
    }

    .support-ticket-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .support-ticket-card {
        background-color: #2c2f33;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #3a3d42;
        color: white;
    }

    .support-ticket-card-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .support-ticket-status {
        font-size: 14px;
        margin-bottom: 10px;
    }

    .support-ticket-new-message {
        background-color: red;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .support-ticket-view-btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #4a90e2;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
    }

    .support-ticket-view-btn:hover {
        background-color: #357ab8;
    }
</style>
