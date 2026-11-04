@props(['kind' => 'field'])

<svg viewBox="0 0 480 320" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->class(['package-diagram']) }} aria-hidden="true" focusable="false">
    <g class="package-diagram-guides" stroke="currentColor" stroke-width="0.7">
        <ellipse cx="240" cy="162" rx="181" ry="128" stroke-dasharray="3 8" />
        <ellipse cx="240" cy="162" rx="150" ry="104" />
        <path d="M240 10v300M24 162h432M70 282h340M70 276v12m340-12v12" />
    </g>
    @if ($kind === 'field')
        <g class="package-airframe" stroke="currentColor" stroke-width="2" stroke-linejoin="round">
            <path d="m216 140-89-54m137 54 89-54m-74 74h115m-130 22 89 54m-137-54-89 54M201 160H86" stroke-width="9" class="package-diagram-arms" />
            @foreach ([[116, 80], [364, 80], [403, 160], [364, 240], [116, 240], [77, 160]] as [$x, $y])
                <ellipse cx="{{ $x }}" cy="{{ $y }}" rx="42" ry="21" />
                <path d="m{{ $x - 30 }} {{ $y - 10 }} 60 20" />
                <circle cx="{{ $x }}" cy="{{ $y }}" r="5" class="package-diagram-hub" />
            @endforeach
            <path d="m214 127 52 0 24 33-24 33h-52l-24-33 24-33Z" class="package-diagram-body" />
            <rect x="219" y="142" width="42" height="34" rx="4" />
            <path d="M226 148h28m-28 7h28m-28 7h18M207 194l-12 27h20m58-27 12 27h-20" />
        </g>
    @else
        <g class="package-airframe" stroke="currentColor" stroke-width="2" stroke-linejoin="round">
            <path d="m240 56 113 59-113 60-113-60 113-59Z" class="package-diagram-body" />
            <path d="m127 115 0 22 113 60 113-60v-22M240 175v22" />
            <path d="m127 158 113 60 113-60M127 180l113 60 113-60M127 202l113 60 113-60" />
            <path d="M155 157v37m170-37v37M184 87v27m112-27v27M240 213v38" stroke-dasharray="3 5" />
            <path d="m240 89 47 25-47 25-47-25 47-25Z" />
            <path d="m162 111 23 12m-10-20 23 12m84-12 23 12m-36-5 23 12" />
            <circle cx="240" cy="113" r="6" class="package-diagram-hub" />
        </g>
    @endif
    <g class="package-diagram-guides" stroke="currentColor">
        <path d="M25 33h28M25 33v28m430-28h-28m28 0v28M25 287h28m-28 0v-28m430 28h-28m28 0v-28" />
    </g>
</svg>
