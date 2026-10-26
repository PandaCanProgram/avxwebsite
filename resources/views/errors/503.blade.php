<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b1d31">
    <title>Coming Soon | AVX Engineering Solutions</title>
    <link rel="icon" type="image/png" href="{{ asset('avx-logo.png') }}">
    <style>
        :root {
            color-scheme: light;
            --brand: #2a6cb0;
            --brand-dark: #1f5590;
            --ink: #10263c;
            --muted: #526376;
            --paper: #f7f9fc;
            --line: #dce4ee;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; }
        .page { min-height: 100vh; min-height: 100svh; display: flex; flex-direction: column; }
        .shell { width: min(1140px, calc(100% - 48px)); margin-inline: auto; }
        .header { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding-block: 30px; border-bottom: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 16px; }
        .brand-logo { width: 48px; height: 48px; object-fit: contain; flex: none; }
        .brand-mark { font-size: 34px; font-weight: 800; letter-spacing: -.06em; line-height: 1; }
        .brand-name { padding-left: 16px; border-left: 1px solid var(--line); font-size: 10px; font-weight: 700; line-height: 1.6; letter-spacing: .16em; text-transform: uppercase; }
        .header-note { color: var(--muted); font-size: 12px; letter-spacing: .06em; }
        .hero { flex: 1; display: grid; grid-template-columns: 1.15fr 1fr; align-items: center; gap: 60px; padding-block: 72px; }
        .copy { min-width: 0; }
        .eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 9px 13px; border: 1px solid #c8daed; border-radius: 999px; background: #edf4fc; color: var(--brand-dark); font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--brand); }
        h1 { margin: 26px 0 22px; font-size: clamp(44px, 5.4vw, 72px); line-height: 1.06; letter-spacing: -.055em; font-weight: 800; }
        h1 span { color: var(--brand); }
        .intro { max-width: 460px; margin: 0; color: var(--muted); font-size: 17px; line-height: 1.8; }
        .actions { display: flex; flex-direction: column; align-items: flex-start; gap: 18px; margin-top: 32px; }
        .contact-button { display: inline-flex; align-items: center; justify-content: center; gap: 22px; min-height: 48px; padding: 15px 22px; border-radius: 6px; background: var(--brand); color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; }
        .contact-button:hover { background: var(--brand-dark); }
        .contact-button svg { width: 18px; height: 18px; flex: none; }
        a:focus-visible { outline: 3px solid var(--ink); outline-offset: 5px; }
        .email { color: var(--muted); font-size: 14px; line-height: 1.5; overflow-wrap: anywhere; }
        .visual { position: relative; aspect-ratio: 1; min-width: 0; overflow: hidden; border: 1px solid #27435e; border-radius: 20px; background-color: #0b1d31; background-image: linear-gradient(#8ab9e60d 1px, transparent 1px), linear-gradient(90deg, #8ab9e60d 1px, transparent 1px); background-size: 30px 30px; color: #a8cef2; }
        .visual svg { display: block; width: 100%; height: 100%; }
        .visual-label { position: absolute; top: 24px; left: 24px; color: #bad4ed; font-size: 10px; letter-spacing: .16em; text-transform: uppercase; }
        .visual-caption { position: absolute; inset-inline: 24px; bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-top: 16px; border-top: 1px solid #a8cef233; color: #bad4ed; font-size: 10px; letter-spacing: .1em; text-transform: uppercase; }
        .footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding-block: 24px; border-top: 1px solid var(--line); color: var(--muted); font-size: 12px; line-height: 1.6; }
        .footer p { margin: 0; }

        @media (max-width: 850px) {
            .hero { gap: 32px; padding-block: 56px; }
            .header-note { display: none; }
            h1 { font-size: clamp(44px, 6vw, 64px); }
            .visual-label { top: 18px; left: 18px; }
            .visual-caption { inset-inline: 18px; bottom: 18px; font-size: 9px; }
        }

        @media (max-width: 640px) {
            .shell { width: calc(100% - 40px); }
            .header { padding-block: 24px; }
            .hero { grid-template-columns: 1fr; gap: 40px; padding-block: 48px; }
            h1 { font-size: clamp(44px, 11vw, 64px); }
            .intro { font-size: 16px; }
            .visual { width: min(100%, 360px); justify-self: center; }
            .footer { align-items: flex-start; flex-direction: column; gap: 6px; }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="shell header">
            <div class="brand" aria-label="AVX Engineering Solutions">
                <img class="brand-logo" src="{{ asset('avx-logo.png') }}" alt="" width="48" height="48">
                <span class="brand-mark" aria-hidden="true">AVX</span>
                <span class="brand-name" aria-hidden="true">Engineering<br>Solutions</span>
            </div>
            <span class="header-note">Ideas into engineered solutions.</span>
        </header>

        <main class="shell hero">
            <div class="copy">
                <span class="eyebrow"><span class="status-dot" aria-hidden="true"></span> A new website is on the way</span>
                <h1>Engineering<br>what&rsquo;s next.<br><span>Coming soon.</span></h1>
                <p class="intro">We&rsquo;re preparing a new home for our engineering services, projects, and ideas. In the meantime, let&rsquo;s talk about your next project.</p>
                <div class="actions">
                    <a class="contact-button" href="mailto:avxengineeringsolutions@gmail.com">
                        Contact our team
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <span class="email">avxengineeringsolutions@gmail.com</span>
                </div>
            </div>

            <div class="visual" aria-hidden="true">
                <span class="visual-label">AVX / Design in progress</span>
                <svg viewBox="0 0 480 480" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <g opacity=".2" stroke-width="1">
                        <circle cx="240" cy="230" r="162"/>
                        <circle cx="240" cy="230" r="126"/>
                        <path d="M240 46v368M56 230h368"/>
                        <path d="M112 102l256 256M368 102 112 358" stroke-dasharray="3 7"/>
                    </g>
                    <g stroke-width="2">
                        <path d="m211 201-65-65m123 65 65-65m-65 123 65 65m-123-65-65 65" stroke-width="9" opacity=".16"/>
                        <path d="m211 201-65-65m123 65 65-65m-65 123 65 65m-123-65-65 65"/>
                        <rect x="207" y="197" width="66" height="66" rx="17"/>
                        <rect x="222" y="212" width="36" height="36" rx="8" opacity=".5"/>
                        <circle cx="136" cy="126" r="40"/>
                        <circle cx="344" cy="126" r="40"/>
                        <circle cx="136" cy="334" r="40"/>
                        <circle cx="344" cy="334" r="40"/>
                        <path d="m112 102 48 48m160-48 48 48m-48 160 48 48m-256-48 48 48" opacity=".6"/>
                    </g>
                    <g stroke-width="1" opacity=".45">
                        <path d="M96 387h288M96 381v12m288-12v12M417 86v288m-6-288h12m-12 288h12"/>
                    </g>
                    <g fill="#b8dcff" stroke="none">
                        <circle cx="136" cy="126" r="4"/>
                        <circle cx="344" cy="126" r="4"/>
                        <circle cx="136" cy="334" r="4"/>
                        <circle cx="344" cy="334" r="4"/>
                        <circle cx="240" cy="230" r="4"/>
                    </g>
                </svg>
                <div class="visual-caption"><span>Concept. Design. Build.</span><span>AVX Engineering</span></div>
            </div>
        </main>

        <footer class="shell footer">
            <p>&copy; {{ date('Y') }} AVX Engineering Solutions.</p>
            <p>Thank you for visiting. We look forward to connecting.</p>
        </footer>
    </div>
</body>
</html>
