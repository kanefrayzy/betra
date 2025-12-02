@extends('panel')

@section('content')
<div class="container mt-5">
    <h1 class="h3">Тикет: {{ $ticket->subject }}</h1>
    <p><strong>Пользователь:</strong> {{ $ticket->user->username ?? 'Неизвестный пользователь' }}</p>
    <p><strong>Статус:</strong> {{ $ticket->status == 'open' ? 'Открыт' : 'Закрыт' }}</p>

    <h3 class="mt-4">Сообщения</h3>

    <div id="message-list" class="message-list">
        @foreach($ticket->messages as $message)
            <div class="message-box mb-3 p-3 border rounded">
                <p>{{ $message->content }}</p>
                @if($message->image)
                    <p><strong>Изображение:</strong></p>
                    <img src="{{ asset('storage/' . $message->image) }}" alt="Изображение сообщения" class="img-fluid">
                @endif
                <small class="text-muted">{{ $message->created_at->format('d.m.Y H:i') }} от {{ $message->user->username ?? 'Неизвестный пользователь' }}</small>
            </div>
        @endforeach
    </div>

    @if($ticket->status == 'open')
        <h4 class="mt-4">Ответить на тикет</h4>
        <form id="reply-form" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <textarea name="content" class="form-control" rows="4" placeholder="Введите сообщение" required></textarea>
            </div>
            <div class="form-group">
                <input type="file" name="image" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    @endif
</div>

<script src="https://js.pusher.com/8.0/pusher.min.js"></script>
<script>
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    const channel = pusher.subscribe('private-user.{{ $ticket->user_id }}');

    channel.bind('SupportMessageSent', function(data) {
        if (data.message.ticket_id == {{ $ticket->id }}) {
            appendMessage(data.message);
        }
    });

    function appendMessage(message) {
        const messageList = document.getElementById('message-list');
        const messageBox = document.createElement('div');
        messageBox.className = 'message-box mb-3 p-3 border rounded';

        messageBox.innerHTML = `
            <p>${message.content}</p>
            ${message.image ? `<p><strong>Изображение:</strong></p><img src="/storage/${message.image}" alt="Изображение сообщения" class="img-fluid">` : ''}
            <small class="text-muted">${new Date(message.created_at).toLocaleString()} от ${message.user.username || 'Неизвестный пользователь'}</small>
        `;

        messageList.appendChild(messageBox);
        messageList.scrollTop = messageList.scrollHeight;
    }

    document.getElementById('reply-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route('admin.tickets.message.store', $ticket->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                appendMessage(data.message);
                this.reset();
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>

<style>
    .message-box {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .message-list {
        margin-top: 20px;
        max-height: 500px;
        overflow-y: auto;
    }

    .message-box img {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 10px;
    }
</style>
@endsection
