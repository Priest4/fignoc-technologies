{{--
  Website-service landing page (/website-design) — copy v6.

  Navigation-free: one offer, one decision, the only route out is the header
  hamburger. Built to the Starlink structure — short headings, one idea per
  chapter, the price in the hero, and every picture beside the words rather
  than behind them.

  All copy and every figure lives in config('fignoc.landing_website'), which is
  also where the numbers that must be verified before launch are flagged.
--}}
@php
    $brand = config('fignoc.brand');
    $wa = preg_replace('/\D+/', '', $brand['whatsapp'] ?? '');

    /** Build a wa.me link with the first message already written. */
    $waLink = fn (string $text) => 'https://wa.me/' . $wa . '?text=' . rawurlencode($text);

    $waStart = $waLink("Hi Fignoc, I'd like a website. Which package suits me?");
    $waCheck = $waLink("Hi Fignoc, I'd like the free Visibility Check for [website].");

    // The offer is live until its fixed end date, then the page quietly
    // reverts to standard pricing with no banner and no struck-through figures.
    $offerEnds = \Carbon\Carbon::parse($lp['offer']['ends_at'])->endOfDay();
    $offerLive = $offerEnds->isFuture();

    $pageUrl = route('landing.website');
    $origin = 'https://' . ltrim($brand['domain'], '/');

    $tick = '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.5 8 14.5 16 6"/></svg>';
    $ext = '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3H3.5v9.5H13V10"/><path d="M9.5 3H13v3.5"/><path d="M7 9l6-6"/></svg>';

    /* Chapters 3–7: one idea each, copy beside a real picture. 'link' points at
       the main-site service page that goes deeper on the same subject. */
    $chapters = [
        [
            'id' => 'fast',
            'heading' => 'Fast on any network',
            'body' => ['Most Zimbabwean websites are built on WordPress and loaded with plugins until they crawl. Ours are coded from scratch on Laravel or Django. So the customer on the last dollar of an Econet bundle actually sees your business — instead of a white screen, and then your competitor.'],
            'photo' => 'people/afr-man-stairs.jpg',
            'alt' => 'A customer checking a business website on a phone while out.',
            'link' => 'web-development',
        ],
        [
            'id' => 'cms',
            'heading' => 'Change your own prices in two minutes.',
            'body' => ['Your own content management system, built around how your business works. New price, new service, new photos — from your phone, in two minutes. No waiting three days for a developer. No $20 invoice to fix a typo.'],
            'photo' => 'people/afr-woman-laptop.jpg',
            'alt' => 'A business owner updating her own website on a laptop.',
            'link' => 'web-systems',
        ],
        [
            'id' => 'see',
            'heading' => 'You see everything',
            'body' => [
                'Every website we build ships with Google Search Console, Google Analytics and Microsoft Clarity — installed, connected and explained.',
                'You’ll see the words people typed into Google before they found you. You’ll see the visitor who scrolled to your contact form, hesitated, and left. Most business owners are guessing why enquiries are low. You won’t have to.',
            ],
            'photo' => 'people/dev-night.jpg',
            'alt' => 'Reading site analytics late in the evening.',
            'link' => 'customer-journey-optimisation',
        ],
        [
            'id' => 'aeo',
            'heading' => 'Named in AI answers',
            'body' => [
                'When someone asks ChatGPT or Gemini who does what you do in Harare, your business should be in the answer — not buried on page two of Google.',
                'We build the structure and markup that makes that possible. It’s the same work we do on our own platforms, which is why Recruitment263 gets cited by name.',
            ],
            'photo' => 'proof/ai-overview.jpg',
            'alt' => 'A Google AI Overview citing Recruitment263 by name in its answer.',
            'link' => 'aeo',
        ],
        [
            'id' => 'who',
            'heading' => 'Websites for shops, services and schools',
            'body' => ['Retailers, salons, lodges, clinics, driving schools, hardware suppliers, law firms, NGOs. If your customers look you up before they call you, you need this.'],
            'photo' => 'people/team-office.jpg',
            'alt' => 'A small team working together in an office.',
            'link' => 'ecommerce',
        ],
    ];

    /* Service + OfferCatalog built from the same package data the cards render,
       so the published price and the structured price cannot disagree. */
    $offers = collect($lp['packages'])->map(fn ($p) => [
        '@type' => 'Offer',
        'name' => $p['name'] . ' website',
        'description' => $p['tagline'],
        'price' => (string) $p['price'],
        'priceCurrency' => 'USD',
        'priceSpecification' => [
            '@type' => 'PriceSpecification',
            'price' => (string) $p['price'],
            'priceCurrency' => 'USD',
            'valueAddedTaxIncluded' => false,
        ],
        'itemOffered' => [
            '@type' => 'Service',
            'name' => $p['name'] . ' website package',
            'description' => collect($p['features'])->pluck('text')->implode('. ') . '.',
        ],
        'availability' => 'https://schema.org/InStock',
        'areaServed' => ['@type' => 'Country', 'name' => 'Zimbabwe'],
    ] + ($offerLive ? ['priceValidUntil' => $offerEnds->toDateString()] : []))->all();

    $ld = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebPage',
                '@id' => $pageUrl . '#webpage',
                'url' => $pageUrl,
                'name' => 'Website design in Zimbabwe from $80 — Fignoc Technologies',
                'isPartOf' => ['@id' => $origin . '/#website'],
                'about' => ['@id' => $origin . '/#organization'],
                'inLanguage' => 'en',
            ],
            [
                '@type' => 'Service',
                '@id' => $pageUrl . '#service',
                'name' => 'Website design and development',
                'serviceType' => 'Website design and development',
                'description' => 'Fast, custom-coded websites from $80 once-off, with Google Search Console, Google Analytics, Microsoft Clarity and a Google Business Profile on every build.',
                'provider' => ['@id' => $origin . '/#organization'],
                'areaServed' => array_merge(
                    array_map(fn ($c) => ['@type' => 'City', 'name' => $c], $lp['cities']),
                    [['@type' => 'Country', 'name' => 'Zimbabwe']],
                ),
                'offers' => $offers,
                'hasOfferCatalog' => [
                    '@type' => 'OfferCatalog',
                    'name' => 'Fignoc website packages',
                    'itemListElement' => $offers,
                ],
            ],
            [
                '@type' => 'FAQPage',
                '@id' => $pageUrl . '#faq',
                'mainEntity' => collect($lp['faqs'])->map(fn ($f) => [
                    '@type' => 'Question',
                    'name' => $f['q'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                ])->all(),
            ],
        ],
    ];
@endphp

<x-layout
    chrome="bare"
    title="Custom-built websites from $80 once-off"
    description="Custom-coded websites from $80 once-off. Search Console, Analytics and Clarity on every build. 20% deposit, balance on approval. Zimbabwe-wide."
    :canonical="$pageUrl"
    ogImage="images/proof/seo-results.jpg">

@push('head')
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

<div class="lp">

    {{-- ══ 1 · HERO ═══════════════════════════════════════════════════════ --}}
    <section class="lp-ch lp-hero">
        <div class="lp-wrap lp-hero-grid">
            <div class="lp-ch-copy">
                <h1 class="lp-h1">{{ $lp['headline'] }}</h1>
                <p class="lp-lede">{{ $lp['subhead'] }}</p>

                <div class="lp-price">
                    <span class="lp-price-lab">{{ $lp['price_label'] }}</span>
                    <span class="lp-price-row">
                        <span class="lp-price-num">${{ $lp['packages'][0]['price'] }}</span>
                        <span class="lp-price-unit">{{ $lp['price_unit'] }}</span>
                    </span>
                </div>

                <div class="lp-cta-row">
                    <button type="button" class="lp-btn lp-btn--primary" data-quote>Get started</button>
                    <a href="#check" class="lp-btn lp-btn--quiet">Free site check</a>
                </div>

                <div class="lp-trust">
                    @foreach ($lp['trust'] as $t)
                        <span>{{ $t }}</span>
                    @endforeach
                </div>
            </div>

            {{-- A site we actually run, on the two screens customers use.
                 Eager on the laptop screen: it is the LCP element. --}}
            <div class="lp-hero-visual">
                <div class="lp-devices">
                    <div class="lp-device-stage">
                    <div class="lp-laptop">
                        <div class="lp-laptop-lid">
                            <div class="lp-laptop-screen">
                                <x-lp-img
                                    src="live/laptop/fignoconline.jpg"
                                    eager
                                    sizes="(min-width: 1024px) 560px, (min-width: 640px) 88vw, 92vw"
                                    alt="The Fignoc Online store home page, shown on a laptop." />
                            </div>
                        </div>
                        <div class="lp-laptop-base" aria-hidden="true"></div>
                    </div>

                    <div class="lp-phones">
                        <div class="lp-phone">
                            <div class="lp-phone-screen">
                                <x-lp-img
                                    src="live/mobile/recruitment263.jpg"
                                    sizes="168px"
                                    alt="The Recruitment263 job board as it renders on a phone." />
                            </div>
                        </div>

                        <div class="lp-phone">
                            <div class="lp-phone-screen">
                                <x-lp-img
                                    src="live/mobile/shop263.jpg"
                                    sizes="168px"
                                    alt="The Shop263 store as it renders on a phone." />
                            </div>
                        </div>
                    </div>
                    </div>

                    </div>

                    <span class="lp-device-cap">Live now on every screen &mdash; fignoconline, recruitment263 and shop263</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ 1b · THE SCOREBOARD ════════════════════════════════════════════
         The numbers sit here, ahead of the price, because a cold visitor was
         otherwise asked to judge $80–$320 with nothing but two trust bullets.
         Text only, so it costs nothing to render this high. --}}
    <section class="lp-ch lp-ch--short" aria-labelledby="lp-fig-h">
        <div class="lp-wrap">
            <h2 id="lp-fig-h" class="lp-lede" style="max-width:46rem;">
                <span class="lp-strong">We run our own job board.</span> Here is what it did on Google in
                {{ $lp['proof']['window'] }} &mdash; the same work goes into your site.
            </h2>

            <div class="lp-figs" style="margin-top:1.4rem;">
                @foreach ($lp['proof']['metrics'] as $m)
                    <div class="lp-fig">
                        <span class="lp-fig-v">{{ $m['value'] }}</span>
                        <span class="lp-fig-l">{{ $m['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══ 2 · PRICING ════════════════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short lp-ch--etched" id="pricing" aria-labelledby="lp-price-h">
        <div class="lp-wrap">
            <h2 id="lp-price-h" class="lp-h2">Once-off pricing. Published.</h2>

            @if ($offerLive)
                <div class="lp-offer-band">
                    <b>{{ $lp['offer']['label'] }}</b>
                    <span>{{ $lp['offer']['note'] }}
                    Ends <time datetime="{{ $lp['offer']['ends_at'] }}">{{ $offerEnds->format('j F Y') }}</time>.</span>
                </div>
            @endif

            <div class="lp-tiers" style="margin-top:1.6rem;">
                @foreach ($lp['packages'] as $p)
                    <div class="lp-tier{{ $p['featured'] ? ' lp-tier--hot' : '' }}">
                        @if ($p['badge'])
                            <span class="lp-tier-badge">{{ $p['badge'] }}</span>
                        @endif

                        <h3 class="lp-tier-name">{{ $p['name'] }}</h3>
                        <p class="lp-tier-tag">{{ $p['tagline'] }}</p>

                        <ul>
                            @foreach ($p['features'] as $f)
                                <li>
                                    {!! $tick !!}
                                    <span>@if (! empty($f['strong']))<b>{{ $f['text'] }}</b>@else{{ $f['text'] }}@endif</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="lp-tier-foot">
                            @if ($offerLive && ! empty($p['list_price']))
                                <p class="lp-was">
                                    <s>${{ $p['list_price'] }}</s>
                                    <span class="lp-save">Save ${{ $p['list_price'] - $p['price'] }}</span>
                                </p>
                            @endif
                            <div class="lp-price">
                                <span class="lp-price-row">
                                    @if ($p['prefix'])
                                        <span class="lp-price-lab" style="align-self:flex-end;padding-bottom:.35rem;">{{ $p['prefix'] }}</span>
                                    @endif
                                    <span class="lp-price-num">${{ $p['price'] }}</span>
                                    <span class="lp-price-unit">{{ $p['unit'] }}</span>
                                </span>
                            </div>
                            <button type="button" data-quote data-package="{{ $p['name'] }}"
                                    class="lp-btn {{ $p['featured'] ? 'lp-btn--primary' : 'lp-btn--quiet' }}">
                                Get started
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="lp-note" style="margin-top:1.5rem;max-width:60ch;">
                Hosting and domain renewal go straight to the host &mdash;
                @if ($lp['hosting_cost'])
                    about <span style="color:var(--lp-ink);">${{ $lp['hosting_cost'] }} a year</span>.
                @else
                    {{-- Real Webzim figure not confirmed; publish the number, never a guess. --}}
                    a published annual figure we put in writing.
                @endif
                We set it up in your name.
            </p>
        </div>
    </section>

    {{-- ══ 3–7 · ONE IDEA PER CHAPTER ═════════════════════════════════════ --}}
    @foreach ($chapters as $i => $c)
        {{-- The picture sits beside the copy, not behind it: photography at this
             size stays sharp, and the words never fight the image for contrast.
             Sides alternate so five chapters don't read as five identical rows. --}}
        <section class="lp-ch lp-ch--split{{ $i % 2 ? ' is-flip' : '' }}"
                 id="{{ $c['id'] }}" aria-labelledby="lp-{{ $c['id'] }}-h">
            <div class="lp-wrap">
                <div class="lp-ch-copy">
                    <h2 id="lp-{{ $c['id'] }}-h" class="lp-h2">{{ $c['heading'] }}</h2>
                    <div class="lp-copy lp-lede">
                        @foreach ($c['body'] as $para)
                            <p>{{ $para }}</p>
                        @endforeach
                    </div>
                    {{-- The depth link opens in a new tab: on a paid page it was the
                         only action in this whole stretch, and it navigated away. --}}
                    <div class="lp-cta-row">
                        <button type="button" class="lp-btn lp-btn--primary" data-quote>Get started</button>
                        <a href="{{ route('services.show', $c['link']) }}" target="_blank" rel="noopener"
                           class="lp-btn lp-btn--quiet">How it works</a>
                    </div>
                </div>

                <div class="lp-ch-media">
                    <x-lp-img
                        :src="$c['photo']"
                        sizes="(min-width: 900px) 560px, 92vw"
                        :alt="$c['alt']" />
                </div>
            </div>
        </section>
    @endforeach

    {{-- ══ 8 · THE PROOF ══════════════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short" aria-labelledby="lp-proof-h">
        <div class="lp-wrap">
            <p class="lp-eyebrow">The proof</p>
            <h2 id="lp-proof-h" class="lp-h2" style="margin-top:0.8rem;">We run our own.</h2>

            <p class="lp-lede" style="margin-top:1.35rem;">
                Recruitment263 is ours. Not a client’s. Type the URL and check it yourself.
            </p>

            <div class="lp-frame" style="margin-top:2rem;max-width:56rem;">
                <div class="lp-frame-bar">
                    <i></i><i></i><i></i>
                    <span class="lp-frame-url">search.google.com/search-console &middot; recruitment263.co.zw</span>
                    <span class="lp-frame-live">Our own site</span>
                </div>
                <x-lp-img
                    src="proof/seo-results.jpg"
                    sizes="(min-width: 960px) 896px, 92vw"
                    :alt="'Google Search Console performance for recruitment263.co.zw: ' . $lp['proof']['metrics'][0]['value'] . ' monthly impressions, average position ' . ltrim($lp['proof']['metrics'][1]['value'], '#') . ', ' . $lp['proof']['metrics'][2]['value'] . ' click-through rate.'" />
            </div>

            <div class="lp-ch-cta">
                <button type="button" class="lp-btn lp-btn--primary" data-quote>Get started</button>
                <p>Or send us your address and we&rsquo;ll check yours free.</p>
            </div>
        </div>
    </section>

    {{-- ══ 9 · BUILT AND RUNNING ══════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short" aria-labelledby="lp-live-h">
        <div class="lp-wrap">
            <p class="lp-eyebrow">Built and running</p>
            <h2 id="lp-live-h" class="lp-h2" style="margin-top:0.8rem;">Every one of these is live. Click any of them.</h2>

            <div class="lp-rail" id="lp-rail" style="margin-top:1.5rem;">
                @foreach ($lp['showcase'] as $site)
                    @php
                        $img = $site['image'] ?? null;
                        $hasShot = $img && file_exists(public_path($img));
                        // Key the optimiser and ImageSet use: relative to /public/images.
                        // Card frames are 16:10; the 1440x900 laptop capture
                        // matches that exactly, so nothing is cropped.
                        $imgKey = $img ? preg_replace('#^images/live/#', 'live/laptop/', $img) : null;
                        $imgKey = $imgKey ? preg_replace('#^images/#', '', $imgKey) : null;
                        $host = preg_replace('#^https?://(www\.)?#', '', rtrim($site['url'], '/'));
                    @endphp
                    <a href="{{ $site['url'] }}" target="_blank" rel="noopener noreferrer" class="lp-card">
                        <div class="lp-card-shot">
                            @if ($hasShot)
                                <x-lp-img
                                    :src="$imgKey"
                                    sizes="(min-width: 1024px) 560px, 74vw"
                                    :alt="'The ' . $site['name'] . ' website, built and run by Fignoc.'" />
                            @else
                                {{-- No screenshot on disk yet. Drop the file at the configured
                                     path and this slot becomes the real card. --}}
                                <span class="lp-card-pending">
                                    <b>Screenshot pending</b>
                                    <small>{{ $img }}</small>
                                </span>
                            @endif
                        </div>
                        <p class="lp-card-cap"><b>{{ $site['name'] }}</b> &mdash; {{ $site['desc'] }}</p>
                        <span class="lp-card-url">{{ $host }} {!! $ext !!}</span>
                    </a>
                @endforeach
            </div>

            <div class="lp-rail-nav">
                <button type="button" class="lp-rail-btn" data-rail="prev" aria-label="Previous platforms">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                </button>
                <button type="button" class="lp-rail-btn" data-rail="next" aria-label="More platforms">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <p class="lp-note" style="margin-top:1.5rem;max-width:56ch;">
                Same stack, same speed work, same measurement setup that goes into your build.
            </p>
        </div>
    </section>

    {{-- ══ 9b · CLIENT REVIEWS ═══════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short" aria-labelledby="lp-rev-h">
        <div class="lp-wrap">
            <p class="lp-eyebrow">In their words</p>
            <h2 id="lp-rev-h" class="lp-h2" style="margin-top:0.8rem;">What clients say.</h2>

            <div class="lp-reviews" style="margin-top:1.5rem;">
                @foreach ($lp['reviews'] as $r)
                    @if (! empty($r['quote']))
                        <figure class="lp-review{{ ! empty($r['is_sample']) ? ' lp-review--sample' : '' }}">
                            <span class="lp-review-chip">{{ $r['sector'] }}@if (! empty($r['is_sample']))
                                <span class="lp-review-sample">&middot; Sample</span>
                            @endif</span>
                            <blockquote class="lp-review-quote">{{ $r['quote'] }}</blockquote>
                            <figcaption class="lp-review-by">
                                <b>{{ $r['name'] }}</b>
                                {{ $r['role'] }}@if ($r['role'] && $r['business']), @endif{{ $r['business'] }}
                            </figcaption>
                        </figure>
                    @else
                        {{-- Unfilled on purpose. A written-in placeholder would be a
                             fabricated review about a real organisation. --}}
                        <div class="lp-review lp-review--empty">
                            <span class="lp-review-chip">{{ $r['sector'] }} &mdash; slot</span>
                            <p class="lp-review-todo"><b>Real quote needed.</b> {{ $r['prompt'] }}</p>
                            <p class="lp-review-hint">Paste it into <code>config/fignoc.php &rarr;
                            landing_website.reviews</code> and this becomes a live review card.</p>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="lp-ch-cta">
                <button type="button" class="lp-btn lp-btn--primary" data-quote>Get started</button>
                <p>Three questions, one business day.</p>
            </div>
        </div>
    </section>

    {{-- ══ 10 · GUARANTEE ═════════════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short lp-ch--etched" aria-labelledby="lp-guar-h">
        <div class="lp-wrap">
            <div class="lp-guar">
                <div style="display:grid;gap:.9rem;">
                    <h2 id="lp-guar-h" class="lp-h2">20% to start. The rest when you approve.</h2>
                    <p class="lp-lede">You’ve heard the stories. Deposit paid, developer disappears.</p>
                </div>

                <div class="lp-guar-list">
                    @foreach ($lp['guarantees'] as $i => $g)
                        <div class="lp-guar-item">
                            <span class="lp-guar-n" aria-hidden="true">{{ $i + 1 }}</span>
                            <div>
                                <h3 class="lp-h3">{{ $g['title'] }}</h3>
                                <p>{{ $g['body'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="lp-guar-close">
                    Our name is on four live platforms &mdash; click any of them above and check for
                    yourself.
                </p>

                <div class="lp-ch-cta">
                    <button type="button" class="lp-btn lp-btn--primary" data-quote>Get started</button>
                    <p>Free consultation. 20% to begin.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ 11 · COVERAGE ══════════════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short" aria-labelledby="lp-where-h">
        <div class="lp-wrap">
            <h2 id="lp-where-h" class="lp-h2">Anywhere in Zimbabwe</h2>
            <p class="lp-lede" style="margin-top:0.9rem;">
                {{ implode(', ', $lp['cities']) }}. Most of our work runs over WhatsApp and short calls
                &mdash; the way business already works here.
            </p>
        </div>
    </section>

    {{-- ══ 12 · INSIGHT PLAN ══════════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short lp-ch--etched" aria-labelledby="lp-plan-h">
        <div class="lp-wrap">
            <p class="lp-eyebrow">Optional, after launch</p>
            <h2 id="lp-plan-h" class="lp-h2" style="margin-top:0.8rem;">We keep watching</h2>
            <p class="lp-lede" style="margin-top:1.1rem;max-width:44rem;">
                Most websites are worse a year after launch. Yours will be better. Your site will tell you
                exactly what’s wrong &mdash; which page loses people, which searches bring buyers. But only
                if somebody reads it every month and changes something. Almost nobody does. So we do.
            </p>

            <div class="lp-plan" style="margin-top:1.5rem;">
                <div class="lp-plan-head">
                    <div>
                        <h3 class="lp-tier-name">Insight Plan</h3>
                        <div class="lp-price" style="margin-top:.6rem;">
                            <span class="lp-price-row">
                                <span class="lp-price-num" style="font-size:clamp(2.2rem,4.5vw,2.8rem);">${{ $lp['insight_plan']['price'] }}</span>
                                <span class="lp-price-unit">a month</span>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="lp-btn lp-btn--quiet" data-quote data-package="Insight Plan">Add at handover</button>
                </div>

                <ul style="margin:0;padding:0;list-style:none;display:grid;gap:.55rem;">
                    @foreach ($lp['insight_plan']['items'] as $item)
                        <li style="display:grid;grid-template-columns:auto 1fr;gap:.6rem;font-size:.9rem;line-height:1.55;">
                            {!! $tick !!}
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>

                <div style="display:grid;gap:.5rem;">
                    <p class="lp-note">{{ $lp['insight_plan']['terms'] }}</p>
                    <p class="lp-strong" style="margin:0;">{{ $lp['insight_plan']['close'] }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ 13 · WHAT WE DON'T PROMISE ═════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short" aria-labelledby="lp-honest-h">
        <div class="lp-wrap">
            <h2 id="lp-honest-h" class="lp-h2">What we don’t promise</h2>
            <div class="lp-copy lp-lede" style="margin-top:1.1rem;max-width:44rem;">
                <p>Ranking on Google takes three to six months for competitive terms. Anyone promising page
                one in a week is selling you something.</p>
                <p class="lp-strong">What we guarantee is that Google can find, read and index every page
                from day one &mdash; and that you’ll be able to see exactly what’s happening while it builds.</p>
            </div>
        </div>
    </section>

    {{-- ══ 14 · STRAIGHT ANSWERS ══════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--short" aria-labelledby="lp-faq-h">
        <div class="lp-wrap">
            <h2 id="lp-faq-h" class="lp-h2" style="margin-bottom:1.4rem;">Straight answers</h2>

            <div class="lp-faq">
                @foreach ($lp['faqs'] as $i => $f)
                    <details @if ($i === 0) open @endif>
                        <summary>{{ $f['q'] }}</summary>
                        <div class="lp-faq-a">{{ $f['a'] }}</div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══ 15 · CLOSING ═══════════════════════════════════════════════════ --}}
    <section class="lp-ch lp-ch--mid lp-ch--center lp-ch--etched" id="check" aria-labelledby="lp-close-h">
        <div class="lp-wrap">
            <div class="lp-ch-copy">
                <h2 id="lp-close-h" class="lp-h2">Send us your website address.</h2>
                <p class="lp-lede">
                    Within one business day we’ll tell you what Google sees, what’s slowing your site down,
                    and where visitors leave before they contact you. Free, with no obligation. If your site
                    is already performing, we’ll tell you so.
                </p>

                {{-- Types straight into the WhatsApp message: no form handler, no
                     data stored, and the enquiry arrives already filled in. --}}
<form class="lp-check" method="POST" action="{{ route('landing.website.enquiry') }}" id="lp-check">
                    @csrf
                    <span class="lp-check-lab">Where should we send the report?</span>

                    <div class="lp-check-grid">
                        <div class="lp-field">
                            <label for="c-name">Your name <span aria-hidden="true">*</span></label>
                            <input id="c-name" name="name" type="text" required autocomplete="name" maxlength="100">
                        </div>
                        <div class="lp-field">
                            <label for="c-phone">WhatsApp number <span aria-hidden="true">*</span></label>
                            <input id="c-phone" name="phone" type="tel" required autocomplete="tel"
                                   inputmode="tel" placeholder="077 000 0000" maxlength="30">
                        </div>
                        <div class="lp-field">
                            <label for="c-business">Business name <span class="lp-field-opt">optional</span></label>
                            <input id="c-business" name="business" type="text"
                                   autocomplete="organization" maxlength="120">
                        </div>
                        <div class="lp-field">
                            <label for="lp-check-url">Website address <span aria-hidden="true">*</span></label>
                            <input class="lp-check-input" id="lp-check-url" name="website" type="text" required
                                   inputmode="url" autocomplete="url" placeholder="yourbusiness.co.zw" maxlength="200">
                        </div>
                    </div>

                    {{-- The endpoint is shared with the scoping dialog, so the
                         request has to say which of the two this was. --}}
                    <input type="hidden" name="package" value="Free Visibility Check">
                    <input type="hidden" name="goal" value="Free Visibility Check requested from the landing page.">
                    <div class="lp-hp" aria-hidden="true">
                        <label for="c-company-url">Company URL</label>
                        <input id="c-company-url" name="company_url" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="lp-check-row">
                        <button type="submit" class="lp-btn lp-btn--primary">Check it free</button>
                    </div>
                    <p class="lp-check-hint">One business day, one reply, nothing else. No website yet?
                    <button type="button" class="lp-check-link" data-quote>Start from scratch instead.</button></p>
                </form>

                <p class="lp-note">
                    Or WhatsApp {{ $brand['phone'] }} &middot; {{ $brand['email'] }}
                </p>
            </div>
        </div>
    </section>
    <x-lp-quote :packages="$lp['packages']" :wa="$wa" />
</div>

@push('scripts')
<x-lp-script />
@endpush
</x-layout>
