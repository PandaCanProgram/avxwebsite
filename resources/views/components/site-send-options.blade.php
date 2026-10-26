@props(['idPrefix' => 'send-via'])

@php
    $sendOptions = [
        [
            'key' => 'viber',
            'label' => 'Viber',
            'description' => 'Opens our chat; your message is copied to paste',
            'color' => '#7360F2',
            'icon' => '<path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />',
        ],
        [
            'key' => 'gmail',
            'label' => 'Gmail',
            'description' => 'Opens an email with your message filled in',
            'color' => '#EA4335',
            'icon' => '<path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />',
        ],
        [
            'key' => 'facebook',
            'label' => 'Facebook Messenger',
            'description' => 'Opens our page chat; your message is copied to paste',
            'color' => '#0866FF',
            'icon' => '<path d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /><path d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />',
        ],
    ];
@endphp

<dialog class="send-dialog m-auto w-[calc(100%-2rem)] max-w-lg p-0 shadow-2xl" aria-labelledby="{{ $idPrefix }}-title" aria-describedby="{{ $idPrefix }}-description" data-send-via-dialog>
    <div class="send-dialog-panel p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="send-dialog-kicker font-display text-xs font-semibold tracking-[0.3em] uppercase">Almost done</p>
                <h3 id="{{ $idPrefix }}-title" class="mt-2 font-display text-3xl font-extrabold uppercase">Where should we send it?</h3>
            </div>

            <button type="button" class="send-dialog-close" aria-label="Close send options" data-send-via-close autofocus>
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <p id="{{ $idPrefix }}-description" class="send-dialog-description mt-3">Choose an app to continue. Review your message there, then send it when you're ready.</p>

        <div class="mt-6 grid gap-3">
            @foreach ($sendOptions as $sendOption)
                <a href="#" target="_blank" rel="noopener" class="send-channel group flex items-center gap-4 p-4" style="--channel-color: {{ $sendOption['color'] }}" data-send-via="{{ $sendOption['key'] }}">
                    <span class="flex size-11 shrink-0 items-center justify-center text-white" style="background-color: {{ $sendOption['color'] }}" aria-hidden="true">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">{!! $sendOption['icon'] !!}</svg>
                    </span>

                    <span class="flex-1">
                        <span class="block font-display text-lg font-bold tracking-wide uppercase">{{ $sendOption['label'] }}</span>
                        <span class="send-channel-description block text-sm">{{ $sendOption['description'] }}</span>
                    </span>

                    <svg class="size-5 text-gray-300 transition-transform group-hover:translate-x-1 group-hover:text-brand" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @endforeach
        </div>

        <p class="send-dialog-status mt-4 hidden px-4 py-3 text-sm" role="status" data-send-via-status></p>

        <details class="send-preview mt-6">
            <summary class="cursor-pointer font-display text-xs font-semibold tracking-[0.2em] uppercase">Preview your message</summary>

            <pre class="mt-3 max-h-48 overflow-auto p-4 font-sans text-sm whitespace-pre-wrap" data-send-via-preview></pre>

            <button type="button" class="mt-3 font-medium" data-send-via-copy>Copy message</button>
        </details>
    </div>
</dialog>
