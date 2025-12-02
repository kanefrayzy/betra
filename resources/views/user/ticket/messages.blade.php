<x-layouts.app>
    <div class="chat-container">
        <h1 class="chat-title">{{ __('Тикет') }}: {{ $ticket->subject }}</h1>

        <div class="chat-messages" id="chat-messages">
            @foreach($messages as $message)
                <div class="chat-message {{ $message->user_id == auth()->id() ? 'message-sent' : 'message-received' }}">
                    <p>{{ $message->content }}</p>
                    @if($message->image)
                        <img src="{{ asset('storage/' . $message->image) }}" alt="image" class="chat-image">
                    @endif
                    <small class="chat-time">{{ $message->created_at->format('H:i d.m.Y') }}</small>
                </div>
            @endforeach
        </div>

        <form id="chat-form" class="chat-form">
            @csrf
            <textarea name="content" id="message-content" placeholder="{{ __('Напишите сообщение...') }}" required class="chat-textarea"></textarea>
            <input type="file" name="image" id="file-upload" class="chat-file-input">
            <label for="file-upload" class="chat-file-label">{{ __('Выберите файл') }}</label>
            <button type="submit" class="chat-send-btn">{{ __('Отправить') }}</button>
        </form>
    </div>

    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script>
        const messagesContainer = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const messageContent = document.getElementById('message-content');

        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('private-user.{{ auth()->id() }}');

        channel.bind('SupportMessageSent', function(data) {
            if (data.message.ticket_id == {{ $ticket->id }}) {
                appendMessage(data.message);
            }
        });

        function appendMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = `chat-message ${message.user_id == {{ auth()->id() }} ? 'message-sent' : 'message-received'}`;

            messageElement.innerHTML = `
                <p>${message.content}</p>
                ${message.image ? `<img src="${message.image}" alt="image" class="chat-image">` : ''}
                <small class="chat-time">${new Date(message.created_at).toLocaleString()}</small>
            `;

            messagesContainer.appendChild(messageElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(chatForm);

            try {
                const response = await fetch('{{ route('support.ticket.message.store', $ticket->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error('{{ __('Ошибка при отправке сообщения') }}');
                }

                const data = await response.json();
                appendMessage(data.message);
                messageContent.value = '';
                document.getElementById('file-upload').value = '';
            } catch (error) {
                console.error('Error:', error);
                alert('{{ __('Произошла ошибка при отправке сообщения') }}');
            }
        });

        // Scroll chat to bottom on page load
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    </script>
</x-layouts.app>

<style>
    /* Основной контейнер чата */
    .chat-container {
        background-color: #2b2e33;
        border-radius: 10px;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .chat-title {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #ffffff;
    }

    /* Контейнер сообщений */
    .chat-messages {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 20px;
        background-color: #2b2d33;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #3a3d42;
    }

    .chat-message {
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 10px;
        position: relative;
        max-width: 70%;
        font-size: 14px;
        line-height: 1.4;
    }

    .message-sent {
        background-color: #4a90e2;
        color: white;
        margin-left: auto;
        text-align: right;
    }

    .message-received {
        background-color: #35383e;
        color: white;
        text-align: left;
    }

    /* Время сообщения */
    .chat-time {
        font-size: 12px;
        color: #a8a8a8;
        margin-top: 5px;
        display: block;
    }

    /* Изображение в сообщении */
    .chat-image {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 10px;
    }

    /* Форма отправки */
    .chat-form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .chat-textarea {
        flex-grow: 1;
        padding: 10px;
        border-radius: 8px;
        background-color: #35383e;
        color: #fff;
        border: 1px solid #4a4f58;
        resize: none;
        font-size: 14px;
    }

    .chat-textarea::placeholder {
        color: #b9bbbe;
    }

    /* Кнопка выбора файла */
    .chat-file-input {
        display: none;
    }

    .chat-file-label {
        padding: 8px 12px;
        background-color: #5b626c;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        text-align: center;
    }

    .chat-file-label:hover {
        background-color: #494f57;
    }

    /* Кнопка отправки */
    .chat-send-btn {
        background-color: #4a90e2;
        border: none;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
    }

    .chat-send-btn:hover {
        background-color: #357ab8;
    }
</style>
