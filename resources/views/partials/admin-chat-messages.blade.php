@foreach ($messages as $message)
    <article class="admin-chat-message {{ $message->user_id === auth()->id() ? 'admin-chat-message-own' : '' }}" data-chat-message="{{ $message->id }}">
        <span class="admin-avatar" aria-hidden="true">{{ Str::upper(Str::substr($message->sender->name, 0, 1)) }}</span>
        <div class="admin-chat-message-content">
            <div class="admin-chat-message-meta"><strong>{{ $message->sender->name }}</strong><time datetime="{{ $message->created_at->toIso8601String() }}" title="{{ $message->created_at->format('M j, Y g:i A') }}">{{ $message->created_at->format('M j, g:i A') }}</time></div>
            <p class="admin-chat-bubble">{{ $message->body }}</p>
        </div>
    </article>
@endforeach
