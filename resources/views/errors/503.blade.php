<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#071321">
    <title>Coming Soon | AVX Engineering Solutions</title>
    <link rel="icon" type="image/png" href="{{ asset('avx-logo.png') }}">
    <style>
        :root {
            color-scheme: dark;
            --ink: #071321;
            --surface: #0b1d30;
            --accent: #82d9ff;
            --text: #eef6ff;
            --muted: #acbfd1;
            --line: rgb(130 217 255 / 22%);
            --ease: cubic-bezier(.16, 1, .3, 1);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: var(--ink); color: var(--text); font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; }
        a { color: inherit; text-decoration: none; }
        a:focus-visible { outline: 2px solid var(--accent); outline-offset: 6px; }
        svg { flex-shrink: 0; }
        .page { position: relative; isolation: isolate; display: flex; flex-direction: column; min-height: 100vh; min-height: 100svh; overflow: clip; }
        .page::before { content: ''; position: absolute; z-index: -1; inset: 0; background-image: linear-gradient(rgb(130 217 255 / 3%) 1px, transparent 1px), linear-gradient(90deg, rgb(130 217 255 / 3%) 1px, transparent 1px); background-size: 72px 72px; mask-image: linear-gradient(transparent, #000 15%, #000 65%, transparent); pointer-events: none; }
        .page::after { content: ''; position: absolute; z-index: -2; inset: 0; background: radial-gradient(ellipse at 85% 35%, rgb(42 108 176 / 18%), transparent 55%); pointer-events: none; }
        .shell { width: min(1216px, calc(100% - 64px)); margin-inline: auto; }
        .header { display: flex; align-items: center; justify-content: space-between; gap: 24px; min-height: 100px; padding-block: 20px; border-bottom: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand-logo { width: 48px; height: 48px; padding: 3px; background: #eef6ff; object-fit: contain; flex: none; }
        .brand-copy { display: flex; flex-direction: column; gap: 6px; }
        .brand-mark { font-size: 28px; font-weight: 800; line-height: 1; letter-spacing: .12em; }
        .brand-name { color: var(--muted); font-size: 10px; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; }
        .header-status { display: flex; align-items: center; gap: 10px; color: var(--muted); font-size: 12px; line-height: 1.6; }
        .status-dot { position: relative; display: inline-block; width: 6px; height: 6px; flex-shrink: 0; background: var(--accent); border-radius: 50%; }
        .status-dot::after { content: ''; position: absolute; inset: -5px; border: 1px solid var(--accent); border-radius: inherit; animation: signal 3s ease-out infinite; }
        .hero { flex: 1; display: grid; grid-template-columns: 1.1fr 1fr; align-items: center; gap: 48px; padding-block: 64px 48px; }
        .copy { position: relative; z-index: 1; min-width: 0; }
        .eyebrow { display: flex; align-items: center; gap: 14px; margin: 0; color: var(--accent); font-size: 11px; font-weight: 700; line-height: 1.8; letter-spacing: .22em; text-transform: uppercase; }
        .eyebrow::before { content: ''; width: 30px; height: 1px; flex-shrink: 0; background: currentColor; }
        h1 { margin: 26px 0 22px; font-family: 'Arial Narrow', Impact, Arial, sans-serif; font-size: clamp(48px, 5.8vw, 80px); font-weight: 800; line-height: 1.02; letter-spacing: -.025em; text-transform: uppercase; }
        h1 span { display: block; margin-top: 8px; color: var(--accent); }
        .intro { max-width: 500px; margin: 0; color: var(--muted); font-size: 16px; line-height: 1.9; }
        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 14px 24px; margin-top: 30px; }
        .contact-button { display: inline-flex; align-items: center; justify-content: space-between; gap: 28px; min-height: 52px; padding: 16px 22px; border: 1px solid var(--accent); background: var(--accent); color: var(--ink); font-size: 14px; font-weight: 700; line-height: 1.5; transition: background 220ms, border-color 220ms; }
        .contact-button:hover { border-color: #b1eaff; background: #b1eaff; }
        .contact-button svg { width: 20px; height: 20px; transition: transform 250ms var(--ease); }
        .contact-button:hover svg { transform: translate(3px, -3px); }
        .contact-shortcut { display: inline-flex; align-items: center; gap: 10px; min-height: 44px; color: var(--muted); font-size: 13px; transition: color 220ms; }
        .contact-shortcut:hover { color: var(--accent); }
        .disciplines { display: flex; flex-wrap: wrap; gap: 10px 18px; margin: 32px 0 0; padding: 20px 0 0; list-style: none; border-top: 1px solid var(--line); color: var(--muted); font-size: 11px; line-height: 1.8; letter-spacing: .08em; text-transform: uppercase; }
        .disciplines li { display: flex; align-items: center; gap: 8px; }
        .disciplines li::before { content: ''; width: 3px; height: 3px; background: var(--accent); }
        .flight-visual { position: relative; width: 100%; aspect-ratio: 1; min-width: 0; }
        .visual-word { position: absolute; top: 7%; right: 0; color: rgb(130 217 255 / 5%); font: 800 clamp(110px, 12vw, 180px)/1 Arial, sans-serif; letter-spacing: -.08em; }
        .orbit { position: absolute; top: 18%; right: -2%; width: 104%; aspect-ratio: 1; border: 1px solid var(--line); border-radius: 50%; transform: rotateX(62deg) rotate(-20deg); }
        .orbit::before { content: ''; position: absolute; inset: 7%; border: 1px dashed rgb(130 217 255 / 18%); border-radius: inherit; }
        .orbit::after { content: ''; position: absolute; inset: -1px; border: 2px solid transparent; border-top-color: var(--accent); border-radius: inherit; animation: orbit 24s linear infinite; }
        .visual-cross { position: absolute; top: 12%; left: 50%; bottom: 12%; width: 1px; background: linear-gradient(transparent, var(--line), transparent); }
        .visual-cross::after { content: ''; position: absolute; top: 50%; left: -200px; width: 400px; height: 1px; background: linear-gradient(90deg, transparent, var(--line), transparent); }
        .drone-stage { position: absolute; inset: 7% -2% 10%; display: grid; place-items: center; animation: hover 7s ease-in-out infinite; }
        .drone { display: block; width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 28px 22px rgb(0 0 0 / 35%)); }
        .visual-topline { position: absolute; top: 0; inset-inline: 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; color: var(--muted); font-size: 10px; line-height: 1.8; letter-spacing: .15em; text-transform: uppercase; }
        .visual-topline > span:last-child { color: var(--accent); }
        .visual-caption { position: absolute; bottom: 0; inset-inline: 0; display: flex; align-items: end; justify-content: space-between; gap: 16px; padding: 14px 0 0 16px; border-left: 2px solid var(--accent); }
        .visual-caption p { margin: 0; color: var(--muted); font-size: 10px; line-height: 1.8; letter-spacing: .14em; text-transform: uppercase; }
        .visual-caption strong { display: block; margin-top: 4px; font-size: 18px; line-height: 1.4; letter-spacing: .03em; text-transform: uppercase; }
        .visual-caption > span { color: var(--muted); font-size: 11px; line-height: 1.8; }
        .contacts { padding-block: 24px 36px; }
        .contacts-heading { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 18px; }
        .contacts-heading h2 { margin: 0; color: var(--text); font-size: 14px; font-weight: 600; }
        .contacts-heading p { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.7; }
        .contact-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        .contact-card { display: flex; align-items: center; gap: 14px; min-width: 0; min-height: 92px; padding: 20px; border: 1px solid var(--line); background: var(--surface); transition: border-color 250ms, background 250ms; }
        .contact-card:hover { border-color: var(--accent); background: #10283e; }
        .contact-icon { display: grid; place-items: center; width: 38px; height: 38px; flex-shrink: 0; background: rgb(130 217 255 / 8%); color: var(--accent); }
        .contact-icon svg { width: 20px; height: 20px; }
        .contact-details { flex: 1; min-width: 0; }
        .contact-details small { display: block; color: var(--muted); font-size: 10px; line-height: 1.8; letter-spacing: .15em; text-transform: uppercase; }
        .contact-details strong { display: block; margin-top: 5px; color: var(--text); font-size: 14px; font-weight: 500; line-height: 1.6; overflow-wrap: anywhere; }
        .contact-arrow { width: 16px; height: 16px; color: var(--accent); transition: transform 250ms var(--ease); }
        .contact-card:hover .contact-arrow { transform: translate(2px, -2px); }
        .footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 24px; padding-block: 22px; border-top: 1px solid var(--line); color: var(--muted); font-size: 11px; line-height: 1.8; }
        .footer p { margin: 0; }
        .footer-tagline { letter-spacing: .12em; text-transform: uppercase; }
        .reveal { animation: arrive 800ms var(--ease) both; }
        .reveal-later { animation-delay: 100ms; }
        .reveal-last { animation-delay: 180ms; }

        @keyframes arrive { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes hover { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-12px) rotate(1deg); } }
        @keyframes orbit { to { transform: rotate(360deg); } }
        @keyframes signal { from { opacity: .6; transform: scale(.5); } to { opacity: 0; transform: scale(1.4); } }

        @media (max-width: 1000px) {
            .hero { gap: 32px; }
            h1 { font-size: clamp(46px, 6vw, 64px); }
            .contact-card { gap: 10px; padding: 16px 12px; }
            .contact-details strong { font-size: 13px; }
            .contact-arrow { display: none; }
        }

        @media (max-width: 760px) {
            .shell { width: calc(100% - 40px); }
            .header { min-height: 86px; }
            .header-status { max-width: 140px; font-size: 11px; }
            .hero { grid-template-columns: 1fr; gap: 44px; padding-block: 44px 24px; }
            h1 { max-width: 580px; font-size: clamp(38px, 8.7vw, 70px); }
            .intro { max-width: 560px; }
            .flight-visual { width: min(100%, 450px); justify-self: center; }
            .contacts-heading { align-items: start; flex-direction: column; gap: 8px; }
            .contact-grid { grid-template-columns: 1fr; gap: 10px; }
            .contact-card { min-height: 82px; padding: 16px; }
            .contact-details strong { font-size: 15px; }
            .contact-arrow { display: block; }
            .contacts { padding-top: 36px; }
        }

        @media (max-width: 420px) {
            .brand-mark { font-size: 24px; }
            .brand-name { max-width: 120px; font-size: 9px; line-height: 1.7; }
            .header-status { max-width: 110px; font-size: 10px; }
            .brand { gap: 10px; }
            .brand-logo { width: 40px; height: 40px; }
            .eyebrow { font-size: 10px; letter-spacing: .14em; }
            .visual-caption > span { display: none; }
            .footer { flex-direction: column; align-items: start; gap: 6px; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="shell header">
            <div class="brand" aria-label="AVX Engineering Solutions">
                <img class="brand-logo" src="{{ asset('avx-logo.webp') }}" alt="" width="48" height="48">
                <span class="brand-copy" aria-hidden="true"><span class="brand-mark">AVX</span><span class="brand-name">Engineering Solutions</span></span>
            </div>
            <span class="header-status"><span class="status-dot" aria-hidden="true"></span>Website upgrade in progress</span>
        </header>

        <main class="shell hero">
            <div class="copy reveal">
                <p class="eyebrow">A new flight is taking shape</p>
                <h1>Engineering<br>what&rsquo;s next.<span>Coming soon.</span></h1>
                <p class="intro">We&rsquo;re building a new home for our unmanned systems, engineering design, and software projects. While we prepare for launch, our team is here to talk about what you want to build.</p>
                <div class="actions">
                    <a class="contact-button" href="mailto:info@avxengineering.com">
                        Contact our team
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg>
                    </a>
                    <a class="contact-shortcut" href="#contact">All contact details <span aria-hidden="true">&darr;</span></a>
                </div>
                <ul class="disciplines" aria-label="Our capabilities"><li>Unmanned systems</li><li>Engineering design</li><li>Software &amp; AI</li></ul>
            </div>

            <div class="flight-visual reveal reveal-later" role="img" aria-label="AVX F500 quadcopter, a custom multirotor platform">
                <span class="visual-word" aria-hidden="true">AVX</span>
                <div class="visual-cross" aria-hidden="true"></div>
                <div class="orbit" aria-hidden="true"></div>
                <div class="visual-topline" aria-hidden="true"><span>AVX / Unmanned systems</span><span>Concept. Design. Build.</span></div>
                <div class="drone-stage" aria-hidden="true"><img class="drone" src="{{ asset('maintenance-drone.webp') }}" alt="" width="1278" height="1230" fetchpriority="high" decoding="async"></div>
                <div class="visual-caption" aria-hidden="true"><div><p>Featured platform</p><strong>F500 Quadcopter</strong></div><span>Custom hardware.<br>Connected possibilities.</span></div>
            </div>
        </main>

        <section id="contact" class="shell contacts reveal reveal-last" aria-labelledby="contact-title">
            <div class="contacts-heading"><h2 id="contact-title">The website is evolving. The conversation continues.</h2><p>Reach AVX directly through any of these channels.</p></div>
            <div class="contact-grid">
                <a class="contact-card" href="mailto:info@avxengineering.com">
                    <span class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m3 7 9 6 9-6" /></svg></span>
                    <span class="contact-details"><small>Email our team</small><strong>info@avxengineering.com</strong></span>
                    <svg class="contact-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg>
                </a>
                <a class="contact-card" href="tel:+639349170567">
                    <span class="contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.8 2.1Z" /></svg></span>
                    <span class="contact-details"><small>Call us</small><strong>0934 917 0567</strong></span>
                    <svg class="contact-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg>
                </a>
                <a class="contact-card" href="https://www.facebook.com/avxengineeringsolutions" target="_blank" rel="noopener" aria-label="Visit AVX Engineering Solutions on Facebook (opens in a new tab)">
                    <span class="contact-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 22v-9h3l.5-4h-3.5V7c0-1 .3-1.5 1.7-1.5H17V2.1a24 24 0 0 0-2.6-.1C11.8 2 10 3.6 10 6.6V9H7v4h3v9h3.5Z" /></svg></span>
                    <span class="contact-details"><small>Find us on Facebook</small><strong>AVX Engineering Solutions</strong></span>
                    <svg class="contact-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg>
                </a>
            </div>
        </section>

        <footer class="shell footer">
            <p>&copy; {{ date('Y') }} AVX Engineering Solutions. All rights reserved.</p>
            <p class="footer-tagline">Aviation &nbsp;/&nbsp; UAV &nbsp;/&nbsp; Systems</p>
        </footer>
    </div>
</body>
</html>
