@php
    $selectedProjects = collect(config('portfolio.projects', []))->where('featured', true)->take(3);
    $capabilities = [
        [
            'title' => 'Unmanned systems',
            'description' => 'Custom drone fabrication and UAV development in the Philippines, from multirotor and fixed-wing platforms to autopilot setup and payload integration.',
            'scope' => 'UAS and RPAS design, development and fabrication, with a broader design scope covering USV boats and UGV rovers.',
            'icon' => 'M6 6l12 12M6 18 18 6M9 9h6v6H9zM7 4a3 3 0 1 1-3 3M17 4a3 3 0 1 0 3 3M7 20a3 3 0 1 0-3-3M17 20a3 3 0 1 1 3-3',
        ],
        [
            'title' => 'Engineering & design',
            'description' => 'CFD simulation, finite element analysis, 3D CAD design, and product rendering connect design decisions to engineering evidence.',
            'scope' => 'From propeller flow and wing structures to thermal systems and service robot design, our portfolio shows how we approach technical problems.',
            'icon' => 'M3 21h18M3 3v18m4-5 4-5 4 3 6-8M8 4h5M18 18h3',
        ],
        [
            'title' => 'Software & AI',
            'description' => 'Business website development, management systems, AI applications, and UAV and aviation software extend our work into digital systems.',
            'scope' => 'Custom web development built around practical workflows, with existing projects in online retail, service requests, and resort booking.',
            'icon' => 'm8 7-5 5 5 5m8-10 5 5-5 5m-3-13-2 16',
        ],
    ];
    $projectOpportunities = [
        [
            'sector' => 'Agriculture',
            'headline' => 'Support the next generation of farming.',
            'description' => 'Potential agricultural drone projects could support crop monitoring, field assessment, and plant protection, with payloads designed around the needs of growers.',
            'scope' => 'Agricultural drones / Plant protection / Payload integration',
            'icon' => 'M12 21V11m0 5C5 16 3 12 3 6c6 0 9 3 9 8m0-3c0-5 3-8 9-8 0 6-2 10-9 10',
        ],
        [
            'sector' => 'Defense support',
            'headline' => 'Build awareness. Support readiness.',
            'description' => 'Possible defense-support and military drone research could focus on unarmed observation, perimeter inspection, and situational awareness through integrated aerial platforms.',
            'scope' => 'UAS & RPAS development / Observation / System integration',
            'icon' => 'M12 3 4 6v6c0 4 4 7 8 9 4-2 8-5 8-9V6l-8-3Zm-5 9h10m-5-5v10',
        ],
        [
            'sector' => 'Cargo hauling',
            'headline' => 'Move essentials beyond familiar routes.',
            'description' => 'Explore cargo UAV concepts for transporting supplies between locations, with engineering studies to assess payload handling, flight performance, and mission requirements.',
            'scope' => 'Cargo UAV concepts / Multirotor & fixed-wing / Payload design',
            'icon' => 'm3 7 9-4 9 4-9 4-9-4Zm0 0v10l9 4 9-4V7M12 11v10M7 5l9 4',
        ],
        [
            'sector' => 'Surveillance & inspection',
            'headline' => 'Bring a clearer view to complex sites.',
            'description' => 'Potential industrial drone projects could combine aerial monitoring, infrastructure inspection, and software dashboards to help teams review conditions across a site.',
            'scope' => 'Industrial drones / Aerial monitoring / UAV software',
            'icon' => 'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Zm13 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        ],
        [
            'sector' => 'Mapping & surveying',
            'headline' => 'Turn a wider view into useful information.',
            'description' => 'Possible mapping and surveying projects could connect aerial imagery, mission planning, and data-processing tools for land assessment, site planning, and project monitoring.',
            'scope' => 'UAV mapping / Aerial imagery / Mission planning',
            'icon' => 'm3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Zm6-3v15m6-12v15',
        ],
        [
            'sector' => 'Surface & ground systems',
            'headline' => 'Take the next mission beyond the sky.',
            'description' => 'Explore unmanned surface vehicle (USV) and unmanned ground vehicle (UGV) concepts, including unmanned boats and rovers for inspection, monitoring, and logistics support.',
            'scope' => 'USV boats / UGV rovers / Connected control systems',
            'icon' => 'M3 16h18l-3 4H6l-3-4Zm4 0V9h10v7M12 9V3m0 0 6 3h-6M2 22h20',
        ],
    ];
    $conversationSteps = [
        ['title' => 'Introduce yourself', 'description' => 'Share your background, organization, and what interests you about AVX.'],
        ['title' => 'Explore the company', 'description' => 'Discuss our capabilities, relevant projects, and the areas you would like to understand.'],
        ['title' => 'Discuss the next step', 'description' => 'Connect with our team about the information and conversations needed to explore an investment.'],
    ];
@endphp

<x-site-layout title="For Investors" page-class="investment-page investor-relations-page">
    <a href="#investment-main" class="investment-skip">Skip to content</a>

    <main id="investment-main" class="investment-main" tabindex="-1" data-investment-page>
        <section class="investor-hero" aria-labelledby="investment-title" data-investor-scene>
            <div class="investment-atmosphere" aria-hidden="true">
                <div class="investment-grid" data-investor-parallax="0.04"></div>
                <div class="investment-glow" data-investor-parallax="-0.06"></div>
            </div>

            <div class="investment-layout investor-hero-layout mx-auto max-w-7xl px-4 sm:px-8">
                <div class="investment-introduction">
                    <p class="investment-eyebrow" data-investment-reveal>Investor Relations / AVX Philippines</p>
                    <h1 id="investment-title" class="investment-title investor-title" data-investment-reveal>Built to solve.<br><span>Ready for<br>what's next.</span></h1>
                    <p class="investment-description investor-hero-description" data-investment-reveal>AVX Engineering Solutions connects unmanned systems, engineering design, and software development. Your investment could help turn these capabilities into new projects for agriculture, defense support, cargo hauling, surveillance, and mapping.</p>

                    <div class="investor-hero-actions" data-investment-reveal>
                        <a href="#investment-work" class="investor-button">See our work <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 4v16m-6-6 6 6 6-6" /></svg></a>
                        <button type="button" class="investor-text-link" data-investment-open aria-haspopup="dialog" aria-controls="investor-inquiry-dialog">Discuss investing <span aria-hidden="true">&rarr;</span></button>
                    </div>
                </div>

                <div class="investment-visual investor-hero-visual" data-investment-reveal>
                    <span class="investor-visual-word" aria-hidden="true" data-investor-parallax="0.12">AVX</span>
                    <div class="investment-visual-orbit" aria-hidden="true" data-investor-parallax="0.06"></div>
                    <img src="{{ Vite::asset('resources/images/drone2.webp') }}" alt="AVX F500 quadcopter with GPS and telemetry" width="1024" height="1024" class="investment-drone" decoding="async" fetchpriority="high" data-investor-parallax="-0.09">
                    <div class="investor-visual-label"><span>Featured platform</span><strong>F500 Quadcopter</strong><p>Custom hardware. Connected systems.</p></div>
                </div>
            </div>

            <nav class="investor-page-index mx-auto max-w-7xl px-4 sm:px-8" aria-label="Investor page sections">
                <a href="#investment-company"><span>01</span> The company</a>
                <a href="#investment-work"><span>02</span> The work</a>
                <a href="#investment-opportunities"><span>03</span> The possibilities</a>
                <a href="#investor-interest"><span>04</span> The conversation</a>
            </nav>
        </section>

        <section id="investment-company" class="investor-company investor-section investor-depth-scene" aria-labelledby="investor-company-title" data-investor-scene>
            <div class="investor-scene-atmosphere" aria-hidden="true"><span class="investor-depth-word" data-investor-parallax="0.08">DESIGN</span><div class="investor-depth-orbit" data-investor-parallax="-0.06"></div></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-8">
                <div class="investor-section-heading">
                    <div data-investment-reveal>
                        <p class="investment-eyebrow">01 / The company</p>
                        <h2 id="investor-company-title">One company.<br>Connected capabilities.</h2>
                    </div>
                    <p class="investor-section-intro" data-investment-reveal>Based in the Philippines, AVX turns engineering ideas into physical platforms and digital tools. Our work brings design, analysis, fabrication, and software into the same conversation, from the first concept to a practical solution.</p>
                </div>

                <div class="investor-capabilities">
                    @foreach ($capabilities as $capability)
                        <article class="investor-capability" data-investment-reveal data-investor-parallax="{{ 0.012 + $loop->index * 0.008 }}">
                            <div class="investor-capability-top">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $capability['icon'] }}" /></svg>
                                <span aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h3>{{ $capability['title'] }}</h3>
                            <p>{{ $capability['description'] }}</p>
                            <p class="investor-capability-scope">{{ $capability['scope'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="investor-connection" data-investment-reveal>
                    <p class="investment-eyebrow">Why the combination matters</p>
                    <p>A physical product can need an airframe, engineering analysis, electronics, and a software interface. AVX brings these disciplines together so the design of a system can be considered alongside how it will be built and used.</p>
                    <a href="{{ route('home') }}#services" class="investor-text-link">Explore our services <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
        </section>

        <section id="investment-work" class="investor-work investor-section investor-depth-scene" aria-labelledby="investor-work-title" data-investor-scene>
            <div class="investor-scene-atmosphere" aria-hidden="true"><span class="investor-depth-word" data-investor-parallax="0.07">BUILD</span></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-8">
                <div class="investor-section-heading">
                    <div data-investment-reveal>
                        <p class="investment-eyebrow">02 / Selected work</p>
                        <h2 id="investor-work-title">Get to know AVX<br>through what we build.</h2>
                    </div>
                    <p class="investor-section-intro" data-investment-reveal>A drone platform, a simulation study, and a business website show different sides of the same engineering approach: understand the requirements, develop the solution, and make the details work together.</p>
                </div>

                <div class="investor-work-grid">
                    @foreach ($selectedProjects as $project)
                        <div data-investment-reveal data-investor-parallax="{{ $loop->even ? -0.028 : 0.016 }}">
                            <x-site-project-card :project="$project" />
                        </div>
                    @endforeach
                </div>

                <div class="investor-portfolio-link" data-investment-reveal>
                    <p>Explore more UAV, simulation, product design, and software projects.</p>
                    <a id="project-collection-toggle" href="{{ route('projects', ['from' => 'investors']) }}" class="investor-text-link" data-project-collection-toggle aria-expanded="false" aria-controls="project-collection">View the full portfolio <span aria-hidden="true">&darr;</span></a>
                </div>

                <x-site-project-collection />
            </div>
        </section>

        <section id="investment-opportunities" class="investor-opportunities investor-section investor-depth-scene" aria-labelledby="investor-opportunities-title" data-investor-scene>
            <div class="investor-scene-atmosphere" aria-hidden="true"><span class="investor-depth-word" data-investor-parallax="0.06">EXPLORE</span><div class="investor-depth-orbit" data-investor-parallax="-0.04"></div></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-8">
                <div class="investor-section-heading">
                    <div data-investment-reveal>
                        <p class="investment-eyebrow">03 / Potential project directions</p>
                        <h2 id="investor-opportunities-title">Where your investment<br><span>could take AVX.</span></h2>
                    </div>
                    <p class="investor-section-intro" data-investment-reveal>Back the engineering behind the next mission. Investment could support research, prototype development, and testing as we explore how custom unmanned systems can serve different industries.</p>
                </div>

                <p class="investor-opportunity-context" data-investment-reveal>These are possible future projects to explore with investors. Each direction would begin with requirements, feasibility assessment, and a development plan.</p>

                <div class="investor-opportunity-grid">
                    @foreach ($projectOpportunities as $opportunity)
                        <article class="investor-opportunity" data-investment-reveal>
                            <div class="investor-opportunity-top">
                                <p>{{ $opportunity['sector'] }}</p>
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $opportunity['icon'] }}" /></svg>
                            </div>
                            <h3>{{ $opportunity['headline'] }}</h3>
                            <p class="investor-opportunity-description">{{ $opportunity['description'] }}</p>
                            <p class="investor-opportunity-scope">{{ $opportunity['scope'] }}</p>
                            <span class="investor-opportunity-number" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </article>
                    @endforeach
                </div>

            </div>
        </section>

        <section id="investor-interest" class="investor-conversation investor-section investor-depth-scene" aria-labelledby="investor-conversation-title" data-investor-scene>
            <div class="investor-scene-atmosphere" aria-hidden="true"><span class="investor-depth-word" data-investor-parallax="0.08">CONNECT</span><div class="investor-depth-orbit" data-investor-parallax="-0.05"></div></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-8">
                <div class="investor-conversation-heading">
                    <div data-investment-reveal>
                        <p class="investment-eyebrow">04 / The conversation</p>
                        <h2 id="investor-conversation-title">From a promising concept<br><span>to a mission worth building.</span></h2>
                        <p class="investment-description">Which possibility speaks to you? Connect with AVX to discuss the direction, the engineering work involved, and how you could be part of its development.</p>
                    </div>
                    <div class="investor-conversation-action" data-investment-reveal>
                        <button type="button" class="investor-button" data-investment-open aria-haspopup="dialog" aria-controls="investor-inquiry-dialog">Inquire about investing <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg></button>
                        <p>Start a conversation directly with our team.</p>
                    </div>
                </div>

                <ol class="investor-conversation-steps">
                    @foreach ($conversationSteps as $step)
                        <li data-investment-reveal>
                            <span aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div>
                        </li>
                    @endforeach
                </ol>
                <noscript><p class="investment-form-note">To inquire about investing, email <a href="mailto:avxengineeringsolutions@gmail.com?subject=Investment%20Inquiry">avxengineeringsolutions@gmail.com</a>.</p></noscript>
            </div>
        </section>

        <x-site-investment-dialog />
    </main>
</x-site-layout>
