{{-- Home (brief §7.1). Long scroll-driven page. --}}
<x-layout
    :title="null"
    :canonical="route('home')"
    description="Harare software company. We build custom software, websites and online stores — then run the SEO, AEO and ads that get Zimbabwean businesses found.">

    {{-- 1 ─ CINEMATIC HERO — fullscreen video collapses to a centred rectangle while
         two image rows assemble above & below (Elementor-style). motion.js drives it. --}}
    @php
        // Our platforms — 4 on top, 4 below, zig-zagged. Arranged so no column repeats.
        $cineTop = ['images/live/cv263.jpg', 'images/live/recruitment263.jpg', 'images/live/shop263.jpg', 'images/live/wlsa.jpg'];
        $cineBottom = ['images/live/nestzim.jpg', 'images/live/shop263.jpg', 'images/live/cv263.jpg', 'images/live/recruitment263.jpg'];
    @endphp
    @push('head')
        <link rel="preload" as="image" href="{{ asset('images/hero-poster-city.png') }}" fetchpriority="high">
    @endpush
    <section class="cine-hero" data-cine>
        <div class="cine-stage">
            {{-- top row --}}
            <div class="cine-row cine-row--top" data-cine-top aria-hidden="true">
                @foreach ($cineTop as $img)
                    <span class="cine-cell"><img src="{{ asset($img) }}" alt="" loading="lazy" decoding="async"></span>
                @endforeach
            </div>

            {{-- centre: collapsing video + big headline (over the fullscreen video) --}}
            <div class="cine-center">
                <div class="cine-video" data-cine-video>
                    {{-- preload=none + no autoplay: mobile never fetches the ~2MB MP4.
                         Desktop motion.js assigns src and plays once the cine hero inits. --}}
                    <video muted loop playsinline preload="none" poster="{{ asset('images/hero-poster-city.png') }}" aria-hidden="true" tabindex="-1" data-cine-video-el>
                        <source data-src="{{ asset('videos/hero-network.mp4') }}" type="video/mp4">
                    </video>
                    <div class="cine-scrim" aria-hidden="true"></div>
                </div>

                <div class="cine-headline" data-cine-headline>
                    <h1 class="display" style="font-size: clamp(2.5rem, 6.2vw, 4.75rem); max-width: 15ch; margin-inline: auto; line-height: 1.03; color: var(--color-paper);">
                        We build software that wins you customers.
                    </h1>
                    <p class="mt-6 text-lg leading-8" style="max-width: 44ch; margin-inline: auto; color: color-mix(in srgb, var(--color-on-dark) 84%, transparent);">
                        Custom software, websites and stores — then the growth that gets you found and paid.
                    </p>
                    <div class="mt-8" style="display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: center;">
                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            Start a project
                            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <a href="{{ route('work') }}" class="btn btn-outline-light">See the work</a>
                    </div>
                    <div class="mt-7" style="display: flex; flex-wrap: wrap; gap: 0.55rem; justify-content: center;">
                        @foreach (['Free consultation', 'You own the code', 'No lock-in', 'Reply within 1 business day'] as $rc)
                            <span class="hero-chip">
                                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8.5l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ $rc }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- bottom row --}}
            <div class="cine-row cine-row--bottom" data-cine-bottom aria-hidden="true">
                @foreach ($cineBottom as $img)
                    <span class="cine-cell"><img src="{{ asset($img) }}" alt="" loading="lazy" decoding="async"></span>
                @endforeach
            </div>
        </div>

        {{-- centred heading revealed once the video collapses (Elementor-style) --}}
        <div class="cine-title" data-cine-title>
            <h2 class="display cine-title-text">The professional standard for software development.</h2>
        </div>
    </section>

    {{-- 2 ─ PROOF STATS (count-up). Dock target sits in the right column (desktop). --}}
    <section class="section" style="background: var(--color-paper);">
        <div class="container-x">
            <div class="grid gap-12 lg:grid-cols-[1.05fr_1fr] lg:items-center">
                <div class="reveal">
                    <span class="eyebrow">The proof</span>
                    <h2 class="display mt-4" style="max-width: 16ch;">You get working software, not promises.</h2>
                    <p class="mt-5 max-w-lg leading-7" style="color: var(--color-body);">
                        We build, run and rank our own platforms. Here's <strong style="color: var(--color-heading);">Recruitment263</strong> — one of ours — in the last 28 days on Google. Every number is real; type the URL and check.
                    </p>
                    {{-- Real Search Console figures (last 28 days). Confirm the property/period before launch. --}}
                    <dl class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3 sm:gap-5" data-reveal-group>
                        <div class="reveal">
                            <dt class="sr-only">Google impressions per month</dt>
                            <dd class="display" style="font-size: clamp(2rem, 6vw, 2.6rem); color: var(--color-accent-deep);">12.5K</dd>
                            <p class="mt-1 text-sm" style="color: var(--color-muted);">Google impressions / month</p>
                        </div>
                        <div class="reveal">
                            <dt class="sr-only">Average Google position</dt>
                            <dd class="display" style="font-size: clamp(2rem, 6vw, 2.6rem);">#6</dd>
                            <p class="mt-1 text-sm" style="color: var(--color-muted);">average Google position</p>
                        </div>
                        <div class="reveal">
                            <dt class="sr-only">Search click-through rate</dt>
                            <dd class="display" style="font-size: clamp(2rem, 6vw, 2.6rem);">7.6%</dd>
                            <p class="mt-1 text-sm" style="color: var(--color-muted);">search click-through rate</p>
                        </div>
                    </dl>
                    <p class="mt-6 reveal" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--color-navy);">
                        <x-ficon name="sparkles" :size="18" />
                        Cited by name in Google's AI Overview.
                    </p>
                </div>

                {{-- proof visual — the team behind the platforms (balances the section, no screenshot repeat) --}}
                <div class="reveal spotlight-visual" style="aspect-ratio: 4 / 3;">
                    <img src="{{ asset('images/people/dev-night.jpg') }}" alt="A Fignoc developer at work" loading="lazy" decoding="async" width="900" height="680" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                </div>
            </div>
        </div>
    </section>

    {{-- 2b ─ TRUST STRIP --}}
    <section class="container-x" style="padding-block: 2.25rem; border-top: 1px solid var(--color-line-soft);">
        <div class="reveal flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <p class="eyebrow" style="margin:0;">The studio behind Zimbabwe's own platforms</p>
            <div class="trust-strip">
                @foreach (['Recruitment263', 'NestZim', 'CV263', 'Shop263', 'NiceJob'] as $p)
                    <span class="trust-pill"><span class="dot"></span>{{ $p }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 2c ─ WHAT WE DO — Elementor-style alternating image/text feature rows --}}
    @php
        $grouped = $services->groupBy('category');
        $features = [
            ['cat' => 'Build', 'icon' => 'code',        'title' => 'Build the software', 'blurb' => 'Websites, web systems, apps and online stores — designed and engineered on modern, maintainable stacks. Fast on any phone, and every line is yours to own.', 'img' => 'images/live/shop263.jpg'],
            ['cat' => 'Rank',  'icon' => 'search',      'title' => 'Get found on Google & AI', 'blurb' => 'SEO to rank on Google, plus answer-engine optimisation (AEO/GEO) so ChatGPT and Gemini recommend you by name — right where your buyers now ask.', 'img' => 'images/proof/ai-overview.jpg', 'frame' => 'Google · AI Overview', 'framedark' => true, 'shotbg' => '#1e2330', 'pos' => 'top'],
            ['cat' => 'Grow',  'icon' => 'trending-up', 'title' => 'Grow & convert', 'blurb' => 'Google and social ads tracked to real enquiries, plus customer-journey fixes that turn the visitors you already have into customers — without spending more.', 'img' => 'images/proof/seo-results.jpg', 'frame' => 'Google Search Console', 'shotbg' => '#ffffff', 'pos' => 'top'],
        ];
    @endphp
    <section class="section container-x" style="border-top: 1px solid var(--color-line-soft);">
        <div class="mx-auto text-center reveal" style="max-width: 44rem;">
            <span class="eyebrow" style="justify-content: center;">What we do</span>
            <h2 class="display mt-4">Build it. Rank it. Grow it.</h2>
            <p class="mt-4 leading-7" style="color: var(--color-body);">One team for the whole job — build the platform, get it found, and turn visitors into customers.</p>
        </div>

        @foreach ($features as $i => $f)
            <div class="spotlight {{ $i % 2 === 1 ? 'spotlight-reverse' : '' }} reveal" style="margin-top: {{ $i === 0 ? '3.5rem' : '4.5rem' }};">
                <div>
                    <span class="icon-tile"><x-ficon :name="$f['icon']" /></span>
                    <h3 class="mt-4" style="font-size: 1.7rem; letter-spacing: -0.02em; color: var(--color-heading);">{{ $f['title'] }}</h3>
                    <p class="mt-3 leading-7" style="color: var(--color-body); max-width: 46ch;">{{ $f['blurb'] }}</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach ($grouped->get($f['cat'], collect()) as $s)
                            <a href="{{ route('services.show', $s) }}" class="chip" style="text-decoration: none;">{{ $s->name }}</a>
                        @endforeach
                    </div>
                    <a href="{{ route('services') }}" class="link-accent font-semibold mt-6 inline-block">Explore {{ strtolower($f['cat']) }} services →</a>
                </div>
                <div class="spotlight-visual {{ ! empty($f['frame']) ? 'proof-frame' : '' }} {{ ! empty($f['framedark']) ? 'is-dark' : '' }}">
                    @if (! empty($f['frame']))
                        <div class="proof-bar">
                            <span class="d" style="background:#ff5f57"></span>
                            <span class="d" style="background:#febc2e"></span>
                            <span class="d" style="background:#28c840"></span>
                            <span class="proof-url">{{ $f['frame'] }}</span>
                        </div>
                        <div class="proof-shot" style="background: {{ $f['shotbg'] ?? '#fff' }};">
                            <img src="{{ asset($f['img']) }}" alt="{{ $f['title'] }} — Fignoc" loading="lazy" decoding="async" style="object-position: {{ $f['pos'] ?? 'top' }};">
                        </div>
                    @else
                        <img src="{{ asset($f['img']) }}" alt="{{ $f['title'] }} — Fignoc" loading="lazy" decoding="async" width="900" height="620" style="width:100%; height:100%; object-fit: cover; object-position: {{ $f['pos'] ?? 'center' }};">
                    @endif
                </div>
            </div>
        @endforeach
    </section>

    {{-- Work teaser removed (audit): it duplicated the products showcase.
         Work lives in the nav and the CTA band's "See the work". --}}

    {{-- 5 ─ PRODUCTS — full-bleed pinned horizontal-scroll gallery. On all
         devices the section pins and scroll drives the track left→right through
         every product, then the page continues. Reduced-motion / no-JS: swipe
         carousel. Each card opens the LIVE product — the real, working demo. --}}
    <section class="hscroll showcase-grad" data-hscroll style="border-top: 1px solid var(--color-line-soft);">
        <div class="hscroll-viewport">
            <div class="hscroll-track" data-hscroll-track>
                {{-- intro / heading panel --}}
                <div class="hscroll-intro">
                    <span class="eyebrow">Our products</span>
                    <h2 class="display mt-4" style="font-size: clamp(2rem, 3.6vw, 3rem); max-width: 14ch;">Don't take our word for it. Take our products.</h2>
                    <p class="mt-4 leading-7" style="color: var(--color-body); max-width: 34ch;">{{ $products->where('status', 'live')->count() }} live platforms we built, run and grow ourselves. Keep scrolling — and tap any one to try it live.</p>
                    <a href="{{ route('products') }}" class="link-accent font-semibold mt-6 inline-block">All products →</a>
                </div>

                @foreach ($products as $p)
                    @php $isLive = $p->isLive() && $p->external_url; @endphp
                    <a href="{{ $isLive ? $p->external_url : route('products.show', $p) }}"
                       @if ($isLive) target="_blank" rel="noopener noreferrer" @endif
                       class="hscroll-card shot-card" aria-label="{{ $isLive ? 'Try ' . $p->name . ' live' : $p->name }}">
                        <div class="shot-bar">
                            <span class="d" style="background:var(--color-line)"></span>
                            <span class="d" style="background:var(--color-line-strong)"></span>
                            <span class="d" style="background:var(--color-muted)"></span>
                            <span style="margin-left:.4rem;font-size:.72rem;color:var(--color-muted);">{{ $isLive ? preg_replace('#^https?://#', '', $p->external_url) : 'launching soon' }}</span>
                        </div>
                        <div class="hscroll-media">
                            <img src="{{ asset(ltrim($p->screenshot_path, '/')) }}" alt="{{ $p->name }} preview" loading="lazy" decoding="async" width="640" height="440">
                            <span class="hscroll-try">
                                {{ $isLive ? 'Try ' . $p->name . ' live' : 'Explore ' . $p->name }}
                                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3h7v7M13 3 4 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </div>
                        <div class="shot-meta">
                            <span>
                                <span class="card-title" style="font-size:1.02rem;">{{ $p->name }}</span>
                                <span class="block text-sm" style="color:var(--color-muted);">{{ $p->tag }}</span>
                            </span>
                            <span class="chip {{ $p->isLive() ? 'chip-live' : 'chip-accent' }}">{{ $p->isLive() ? 'Live' : 'Soon' }}</span>
                        </div>
                    </a>
                @endforeach

                {{-- end panel --}}
                <div class="hscroll-end">
                    <a href="{{ route('products') }}" class="btn btn-primary">See all products
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 5a ─ TESTIMONIALS — HIDDEN for launch: the quotes in config/fignoc.php are
         PLACEHOLDERS, and fake reviews must not go live. Add real, attributable
         quotes there, then un-comment the line below to re-enable. --}}
    {{-- <x-testimonials :items="$testimonials" heading="What our clients say." /> --}}

    {{-- 5b ─ HOW A PROJECT WORKS (process — the services-firm differentiator) --}}
    <section class="section container-x" style="border-top: 1px solid var(--color-line-soft);">
        <div class="mx-auto text-center reveal" style="max-width: 44rem;">
            <span class="eyebrow" style="justify-content: center;">How it works</span>
            <h2 class="display mt-4" style="font-size: 2.2rem;">From first call to found on Google.</h2>
            <p class="mt-4 leading-7" style="color: var(--color-body);">A clear path — no jargon, no surprises, and you own everything we build at every step.</p>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group>
            @foreach ([
                ['n' => '01', 'i' => 'message',     't' => 'Brief', 'd' => 'A free call to understand the problem. We scope it honestly and quote up front — no obligation.'],
                ['n' => '02', 'i' => 'code',        't' => 'Build', 'd' => 'We design and engineer the platform on modern stacks, showing you progress as it takes shape.'],
                ['n' => '03', 'i' => 'rocket',      't' => 'Launch', 'd' => 'We ship it live and hand over clean — fast, and working on real connections and phones.'],
                ['n' => '04', 'i' => 'trending-up', 't' => 'Grow', 'd' => 'We get you found on Google and AI, then tune the journey so visitors become customers.'],
            ] as $step)
                <div class="reveal step-card">
                    <div class="flex items-center gap-3">
                        <span class="icon-tile"><x-ficon :name="$step['i']" /></span>
                        <span class="card-index">{{ $step['n'] }}</span>
                    </div>
                    <h3 class="mt-3" style="font-size: 1.2rem;">{{ $step['t'] }}</h3>
                    <p class="mt-2 leading-7 text-sm" style="color: var(--color-body);">{{ $step['d'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 6 ─ FAQ (subset) + FAQPage JSON-LD --}}
    <div style="border-top: 1px solid var(--color-line-soft);">
        <x-faq :items="$faqs" eyebrow="FAQ" heading="Questions people ask us." />
    </div>

    {{-- 7 ─ CTA band --}}
    <x-cta-band />
</x-layout>
