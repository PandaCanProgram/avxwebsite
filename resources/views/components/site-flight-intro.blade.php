<dialog class="flight-intro" aria-label="Welcome to AVX Engineering Solutions" aria-describedby="flight-intro-description" data-flight-intro tabindex="-1">
    <div class="flight-intro-glow" aria-hidden="true"></div>
    <div class="preloader-brand">
        <img src="{{ asset('avx-logo.webp') }}" alt="AVX Engineering Solutions" width="88" height="88" fetchpriority="high">
        <span class="preloader-name" aria-hidden="true">AVX</span>
        <span class="preloader-detail" aria-hidden="true">Engineering Solutions</span>
    </div>

    <div class="flight-start-stage">
        <span class="flight-start-ring" aria-hidden="true"></span>
        <button type="button" class="flight-start-button" data-flight-start autofocus aria-label="Start drone and enter the AVX website">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><path d="M12 3v9M6.35 5.85a8 8 0 1 0 11.3 0" /></svg>
            <span>Start<br>Drone</span>
        </button>

        <div class="preloader-flight" aria-hidden="true">
            {{--
                Drone icon from Lucide: https://github.com/lucide-icons/lucide/blob/main/icons/drone.svg
                ISC License. Copyright (c) 2026 Lucide Icons and Contributors.
                Permission to use, copy, modify, and/or distribute this software for any
                purpose with or without fee is hereby granted, provided that the above
                copyright notice and this permission notice appear in all copies.
                THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
                WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
                MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
                ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
                WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
                ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
                OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
            --}}
            <svg class="preloader-drone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 10 7 7" />
                <path d="m10 14-3 3" />
                <path d="m14 10 3-3" />
                <path d="m14 14 3 3" />
                <path class="preloader-rotor" d="M14.205 4.139a4 4 0 1 1 5.439 5.863" />
                <path class="preloader-rotor" d="M19.637 14a4 4 0 1 1-5.432 5.868" />
                <path class="preloader-rotor" d="M4.367 10a4 4 0 1 1 5.438-5.862" />
                <path class="preloader-rotor" d="M9.795 19.862a4 4 0 1 1-5.429-5.873" />
                <rect x="10" y="8" width="4" height="8" rx="1" />
            </svg>
            <span class="preloader-shadow"></span>
        </div>
    </div>

    <p id="flight-intro-description" class="flight-start-prompt" data-flight-status role="status">Click to take flight</p>
    <button type="button" class="flight-start-skip" data-flight-skip>Skip intro <span aria-hidden="true">↗</span></button>
</dialog>
