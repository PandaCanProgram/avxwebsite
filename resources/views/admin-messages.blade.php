@php($conversationName = $group?->displayName())

<x-admin-shell title="Messages">
    <main id="main-content" class="admin-dashboard admin-messages-page">
        <div class="admin-users-heading">
            <div><p class="admin-eyebrow">Stay Connected</p><h1>Messages</h1><p class="admin-dashboard-intro">Your teams. Your conversations. One workspace.</p></div>
            <div class="admin-chat-heading-actions">
                <a class="admin-user-edit" href="{{ route('admin.messages.direct.create') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H4l-2 2v-8.5A8.5 8.5 0 0 1 10.5 5H13a8 8 0 0 1 8 6.5Z"/><path d="M7 11h9M7 15h6"/></svg> Message</a>
                <a class="admin-button admin-add-user" href="{{ route('admin.messages.create') }}"><span aria-hidden="true">+</span> New Group</a>
            </div>
        </div>
        @if (session('messageStatus'))<p class="admin-notice" role="status">{{ session('messageStatus') }}</p>@endif
        <div class="admin-chat-layout">
            <aside class="admin-chat-conversations" aria-label="Conversations">
                <div class="admin-chat-list-heading"><h2>Conversations</h2><span class="admin-participant-count">{{ $groups->total() }}</span></div>
                <nav class="admin-chat-group-list" aria-label="Choose a conversation">
                    @forelse ($groups as $conversation)
                        <a class="admin-chat-group" href="{{ route('admin.messages.show', $conversation) }}" @if ($group?->id === $conversation->id) aria-current="page" @endif>
                            <span class="admin-chat-group-icon" aria-hidden="true">{{ $conversation->isDirect() ? Str::upper(Str::substr($conversation->displayName(), 0, 1)) : '#' }}</span>
                            <span class="admin-chat-group-details"><strong>{{ $conversation->displayName() }}</strong><small>{{ $conversation->isDirect() ? 'Private message' : $conversation->members_count.' '.Str::plural('member', $conversation->members_count) }}</small><span data-chat-preview="{{ $conversation->id }}">{{ $conversation->latestMessage ? Str::limit($conversation->latestMessage->body, 65) : 'No messages yet. Say hello!' }}</span></span>
                        </a>
                    @empty
                        <p class="admin-chat-list-empty">Your conversations will appear here. Message a user or start a group with your team.</p>
                    @endforelse
                </nav>
                @if ($groups->hasPages())
                    <div class="admin-chat-pages">
                        @if ($groups->previousPageUrl())<a class="admin-user-edit" href="{{ $groups->previousPageUrl() }}" aria-label="Previous conversations">&larr; Previous</a>@endif
                        @if ($groups->nextPageUrl())<a class="admin-user-edit" href="{{ $groups->nextPageUrl() }}" aria-label="Next conversations">Next &rarr;</a>@endif
                    </div>
                @endif
            </aside>
            @if ($group)
                <section class="admin-chat-room" aria-label="{{ $conversationName }} conversation" data-chat-room data-feed-url="{{ route('admin.messages.feed', $group) }}" data-group-id="{{ $group->id }}" data-cursor="{{ $messages->last()?->id ?? 0 }}">
                    <header class="admin-chat-room-header">
                        <div class="admin-chat-room-title"><span class="admin-chat-group-icon" aria-hidden="true">{{ $group->isDirect() ? Str::upper(Str::substr($conversationName, 0, 1)) : '#' }}</span><div><h2>{{ $conversationName }}</h2><span>{{ $group->isDirect() ? 'Private conversation · Just you and '.$conversationName : $group->members->count().' '.Str::plural('member', $group->members->count()).' · Group conversation' }}</span></div></div>
                        @if ($group->isDirect())<span class="admin-access-badge">Private Message</span>@else<a class="admin-user-edit" href="{{ route('admin.messages.edit', $group) }}">Manage Members</a>@endif
                    </header>
                    @if (! $group->isDirect())<details class="admin-chat-members"><summary>View Members</summary><ul>@foreach ($group->members as $member)<li><strong>{{ $member->name }}</strong><span>{{ $member->is_admin ? 'Admin · Group manager' : ($member->position ?? 'Team Member') }}</span></li>@endforeach</ul></details>@endif
                    <div class="admin-chat-stream" data-chat-stream>
                        <div class="admin-chat-history"><button type="button" class="admin-user-edit" data-chat-older data-before="{{ $messages->first()?->id ?? 0 }}" @if (! $hasEarlier) hidden @endif>Load Earlier Messages</button></div>
                        <div data-chat-log role="log" aria-label="Message history" aria-live="polite" aria-relevant="additions">
                            @include('partials.admin-chat-messages')
                        </div>
                        <div class="admin-chat-room-empty" data-chat-empty @if ($messages->isNotEmpty()) hidden @endif><span class="admin-chat-empty-mark" aria-hidden="true">{{ $group->isDirect() ? Str::upper(Str::substr($conversationName, 0, 1)) : '#' }}</span><h2>{{ $group->isDirect() ? 'Start a conversation with '.$conversationName : 'This is the start of '.$conversationName }}</h2><p>Share an update, ask a question, or get the conversation going.</p></div>
                    </div>
                    <form class="admin-chat-composer" method="POST" action="{{ route('admin.messages.send', $group) }}" data-chat-composer novalidate>
                        @csrf
                        <input type="hidden" name="client_id" value="{{ is_string(old('client_id')) && Str::isUuid(old('client_id')) ? old('client_id') : Str::uuid() }}">
                        <div class="admin-field"><label for="chat-body">Message {{ $conversationName }}</label><textarea id="chat-body" name="body" rows="2" maxlength="5000" required placeholder="{{ $group->isDirect() ? 'Write a private message…' : 'Write a message to your group…' }}" aria-describedby="chat-send-error" @error('body', 'groupMessage') aria-invalid="true" @enderror>{{ is_string(old('body')) ? old('body') : '' }}</textarea></div>
                        <p class="admin-field-error" id="chat-send-error" data-chat-error role="alert" @if (! $errors->groupMessage->any()) hidden @endif>{{ $errors->groupMessage->first() }}</p>
                        <div class="admin-chat-composer-footer"><span class="admin-field-help" data-chat-status role="status">Messages are saved to this conversation.</span><button type="submit" class="admin-button admin-chat-send" data-chat-send>Send <span aria-hidden="true">&rarr;</span></button></div>
                    </form>
                </section>
            @else
                <section class="admin-chat-welcome"><span class="admin-chat-empty-mark" aria-hidden="true"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H4l-2 2v-8.5A8.5 8.5 0 0 1 10.5 5H13a8 8 0 0 1 8 6.5Z"/><path d="M7 11h9M7 15h6"/></svg></span><h2>A Space for Every Conversation</h2><p>Message a team member privately, or create a group and bring your team together.</p><a class="admin-button admin-add-user" href="{{ route('admin.messages.direct.create') }}">Message a User &rarr;</a></section>
            @endif
        </div>
    </main>
</x-admin-shell>
