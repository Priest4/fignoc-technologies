{{--
  /pricing — one page covering all three lines of business.

  The competitor who owns the money terms in this market ("how much does a
  website cost in Zimbabwe", "website prices Zimbabwe") does so largely because
  they have a single pricing page and an article to match. /website-design
  publishes website prices but is a paid-traffic landing page with no
  navigation, so it cannot serve that job organically. See docs/SEO-KEYWORDS.md.

  Website prices come from config('fignoc.landing_website'), the same source the
  landing page renders, so the two can never quote different figures.

  Software and app work is quoted, not listed. The only figure we have is the
  $1,500 floor, and inventing tiers to fill a table would be inventing prices.
--}}
@php
    $brand = config('fignoc.brand');
    $lp = config('fignoc.landing_website');
    $packages = $lp['packages'];

    $offerEnds = \Carbon\Carbon::parse($lp['offer']['ends_at'])->endOfDay();
    $offerLive = $offerEnds->isFuture();

    $pageUrl = route('pricing');
    $origin = 'https://' . ltrim($brand['domain'], '/');

    $tick = '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.5 8 14.5 16 6"/></svg>';

    /* Answers to the questions this page is meant to rank for, phrased the way
       they are searched. Short first sentence with the number in it — that is
       the shape AI answers quote. */
    $faqs = [
        [
            'q' => 'How much does a website cost in Zimbabwe?',
            'a' => 'Ours are $80, $150 and from $320, once-off. Around the market you will find templates from about $40, small-business sites between $120 and $350, and agency builds from $350 to $900 or more. The gap matters less than what happens after launch — most cheap sites are never looked at again.',
        ],
        [
            'q' => 'How much does an app cost in Zimbabwe?',
            'a' => 'Ours start at $1,500, quoted after a free scoping call. The market runs from roughly $800 for something very simple to many times that once payments, accounts or offline use are involved. A fast mobile website is often the better first step, and we will say so if it is.',
        ],
        [
            'q' => 'How much does custom software cost?',
            'a' => 'From $1,500, and genuinely dependent on scope — a POS system, an internal platform and a marketplace are three different projects. We scope it free and quote a fixed price before anything is built.',
        ],
        [
            'q' => 'What does hosting and a domain cost?',
            'a' => 'You pay the host directly, so you are never locked to us. A .co.zw domain is around $10 a year and shared hosting is commonly around $80 a year. We set both up in your name and put the renewal date in writing.',
        ],
        [
            'q' => 'Do I pay everything upfront?',
            'a' => 'No. A 20% deposit starts the work and the balance is due only when you approve the finished site. If the design is not right, the deposit comes back.',
        ],
        [
            'q' => 'Are these prices once-off or monthly?',
            'a' => 'The build prices are once-off. The only recurring cost is hosting, which you pay the host, and the optional Insight Plan at $' . $lp['insight_plan']['price'] . ' a month if you want us reading the numbers and updating the site for you.',
        ],
    ];

    $offers = collect($packages)->map(fn ($p) => [
        '@type' => 'Offer',
        'name' => $p['name'] . ' website',
        'description' => $p['tagline'],
        'price' => (string) $p['price'],
        'priceCurrency' => 'USD',
        'availability' => 'https://schema.org/InStock',
        'areaServed' => ['@type' => 'Country', 'name' => 'Zimbabwe'],
    ] + ($offerLive ? ['priceValidUntil' => $offerEnds->toDateString()] : []))->all();

    $ld = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'OfferCatalog',
                '@id' => $pageUrl . '#catalog',
                'name' => 'Fignoc pricing',
                'url' => $pageUrl,
                'provider' => ['@id' => $origin . '/#organization'],
                'itemListElement' => $offers,
            ],
            [
                '@type' => 'FAQPage',
                '@id' => $pageUrl . '#faq',
                'mainEntity' => collect($faqs)->map(fn ($f) => [
                    '@type' => 'Question',
                    'name' => $f['q'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                ])->all(),
            ],
        ],
    ];
@endphp

<x-layout
    title="Pricing — websites, software and apps in Zimbabwe"
    description="What a website, custom software or an app costs in Zimbabwe. Websites from $80 once-off, software and apps from $1,500. Published prices, 20% deposit, balance on approval."
    :canonical="$pageUrl">

@push('head')
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

<x-breadcrumbs :items="[
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Pricing', 'url' => $pageUrl],
]" />

<header class="svc-detail-head">
    <div class="section container-x">
        <span class="eyebrow">Pricing</span>
        <h1 class="display mt-5" style="max-width: 18ch;">What it costs to build with us.</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8" style="color: var(--color-body);">
            Published, not quoted in a back room. Websites start at ${{ $packages[0]['price'] }} once-off.
            Software and apps are scoped free and quoted at a fixed price before anything is built.
        </p>
        @if ($offerLive)
            <p class="mt-5 svc-pricelink">
                <b>{{ $lp['offer']['label'] }}</b>
                <span>{{ $lp['offer']['note'] }} Ends
                    <time datetime="{{ $lp['offer']['ends_at'] }}">{{ $offerEnds->format('j F Y') }}</time>.</span>
            </p>
        @endif
    </div>
</header>

{{-- ══ Websites ═══════════════════════════════════════════════════════ --}}
<section class="section container-x" aria-labelledby="pricing-web">
    <span class="eyebrow">Websites</span>
    <h2 id="pricing-web" class="display mt-4" style="font-size: 1.9rem;">Website packages</h2>
    <p class="mt-4 max-w-2xl leading-8" style="color: var(--color-body);">
        Every package includes a custom content management system you update yourself, and Google Search
        Console, Analytics, Clarity and a Google Business Profile set up and explained.
    </p>

    <div class="pricing-grid mt-7">
        @foreach ($packages as $p)
            <div class="pricing-card{{ $p['featured'] ? ' is-featured' : '' }}">
                @if ($p['badge'])
                    <span class="chip chip-accent pricing-card-badge">{{ $p['badge'] }}</span>
                @endif
                <h3 class="pricing-card-name">{{ $p['name'] }}</h3>
                <p class="pricing-card-tag">{{ $p['tagline'] }}</p>

                <ul class="pricing-card-list">
                    @foreach ($p['features'] as $f)
                        <li>{!! $tick !!}<span>{{ $f['text'] }}</span></li>
                    @endforeach
                </ul>

                <div class="pricing-card-foot">
                    @if ($offerLive && ! empty($p['list_price']))
                        <p class="pricing-was"><s>${{ $p['list_price'] }}</s> <span>save ${{ $p['list_price'] - $p['price'] }}</span></p>
                    @endif
                    <p class="pricing-card-price">
                        <span class="pricing-card-pre">from</span>
                        <span class="pricing-card-num">${{ $p['price'] }}</span>
                        <span class="pricing-card-unit">{{ $p['unit'] }}</span>
                    </p>
                    <a href="{{ route('landing.website') }}#pricing" class="btn {{ $p['featured'] ? 'btn-primary' : 'btn-ghost' }}">
                        See what's included
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <p class="mt-6 text-sm" style="color: var(--color-muted); max-width: 62ch;">
        Hosting and domain renewal are paid by you directly to the host, so you are never dependent on us
        to stay online. We set both up in your name.
    </p>
</section>

{{-- ══ Software and apps ══════════════════════════════════════════════ --}}
<section class="section container-x" style="border-top: 1px solid var(--color-line-soft);" aria-labelledby="pricing-software">
    <span class="eyebrow">Software &amp; apps</span>
    <h2 id="pricing-software" class="display mt-4" style="font-size: 1.9rem;">Quoted, not listed.</h2>
    <p class="mt-4 max-w-2xl leading-8" style="color: var(--color-body);">
        A POS system, an internal platform and a marketplace app are three different projects, and a price
        list that pretended otherwise would be wrong before you finished reading it. So we scope it
        properly, for free, and quote a fixed price before anything is built.
    </p>

    <div class="pricing-quote mt-7">
        <div class="pricing-quote-figure">
            <span class="pricing-card-pre">From</span>
            <span class="pricing-card-num">$1,500</span>
            <span class="pricing-card-unit">fixed price after scoping</span>
        </div>
        <ul class="pricing-quote-list">
            <li>{!! $tick !!}<span>Custom software, internal platforms and POS systems</span></li>
            <li>{!! $tick !!}<span>Android and iOS apps from one codebase</span></li>
            <li>{!! $tick !!}<span>EcoCash and Paynow payments wired in</span></li>
            <li>{!! $tick !!}<span>Free scoping call and a written fixed quote</span></li>
            <li>{!! $tick !!}<span>You own the code and the store listing</span></li>
        </ul>
    </div>

    <div class="mt-7 flex flex-wrap gap-3">
        <a href="{{ route('contact') }}" class="btn btn-primary">
            Book a free scoping call
            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        <a href="{{ route('services.show', 'app-development') }}" class="btn btn-ghost">App development</a>
        <a href="{{ route('services.show', 'custom-software') }}" class="btn btn-ghost">Custom software</a>
    </div>
</section>

{{-- ══ Keeping it working ═════════════════════════════════════════════ --}}
<section class="section container-x" style="border-top: 1px solid var(--color-line-soft);" aria-labelledby="pricing-plan">
    <span class="eyebrow">Optional, after launch</span>
    <h2 id="pricing-plan" class="display mt-4" style="font-size: 1.9rem;">Insight Plan — ${{ $lp['insight_plan']['price'] }} a month</h2>
    <p class="mt-4 max-w-2xl leading-8" style="color: var(--color-body);">
        {{ $lp['insight_plan']['close'] }} We read the numbers every month, name the page costing you the
        most enquiries, and fix it. {{ $lp['insight_plan']['terms'] }}
    </p>
    <a href="{{ route('landing.website') }}" class="link-accent font-semibold mt-5 inline-block">See what the plan covers →</a>
</section>

<x-faq :items="$faqs" eyebrow="Pricing questions" heading="Straight answers on cost" />

<x-cta-band />
</x-layout>
