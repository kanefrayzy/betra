@props(['message'])

<div class="support-message {{ $message->user_id == auth()->id() ? 'message-sent' : 'message-received' }}"
     id="message-{{ $message->id }}">
    <div class="message-header">
        <span class="sender">{{ $message->user->name }}</span>
        <span class="timestamp">{{ $message->created_at->format('d.m.Y H:i') }}</span>
    </div>
    <div class="message-content">
        {{ $message->content }}
    </div>
    @if($message->image)
        <div class="message-image">
            <img src="{{ asset('storage/' . $message->image) }}" alt="Attached Image">
        </div>
    @endif
</div>
