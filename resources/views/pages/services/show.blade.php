{{-- Service detail (brief §7.3). Renders every service from one template. --}}
@php
    $d = $service->detail ?? [];
    $faqs = $d['faqs'] ?? [];

    // Header visual per service — real product screenshots where we have them,
    // relevant imagery otherwise. Keeps every service page from being a wall of text.
    $serviceImages = [
        'web-development'                => 'images/live/cv263.jpg',
        'web-systems'                    => 'images/live/nestzim.jpg',
        'custom-software'                => 'images/live/recruitment263.jpg',
        'app-development'                => 'images/live/mobile/recruitment263.jpg',
        'ngo-systems'                    => 'images/live/wlsa.jpg',
        'ecommerce'                      => 'images/live/shop263.jpg',
        'seo'                            => 'images/proof/seo-results.jpg',
        'aeo'                            => 'images/proof/ai-overview.jpg',
        'geo'                            => 'images/proof/ai-overview.jpg',
        'content-strategy'               => 'images/svc-content.jpg',
        'google-ads'                     => 'images/svc-ads.jpg',
        'social-ads'                     => 'images/people/client-happy.jpg',
        'customer-journey-optimisation'  => 'images/journey-funnel.png',
    ];
    $heroImg = $serviceImages[$service->slug] ?? 'images/stock-photo-web-design-concept.jpg';

    // Each service is grounded in a REAL platform we built / run / rank — proof, not promises.
    $proofMap = [
        'web-development'  => ['name' => 'CV263',          'slug' => 'cv263',          'line' => 'a fast, crawlable careers platform we designed, built and run.'],
        'web-systems'      => ['name' => 'NestZim',        'slug' => 'nestzim',        'line' => 'our live rentals marketplace — logins, dashboards and real-time alerts.'],
        'custom-software'  => ['name' => 'Recruitment263', 'slug' => 'recruitment263', 'line' => 'a national job platform we architected, built and now operate.'],
        'app-development'  => ['name' => 'NiceJob', 'slug' => 'nicejob', 'line' => 'our live marketplace app — two-way reviews, real-time chat, and no cut taken on payments.'],
        'ngo-systems'      => ['name' => 'WLSA Zimbabwe',  'slug' => null,             'line' => 'the content and information platform we built and maintain for the NGO.'],
        'ecommerce'        => ['name' => 'Shop263',        'slug' => 'shop263',        'line' => 'our own store platform with EcoCash, Paynow and a built-in POS.'],
        'seo'              => ['name' => 'Recruitment263', 'slug' => 'recruitment263', 'line' => 'a platform we rank — 12.5K Google impressions a month at an average position of #6.'],
        'aeo'              => ['name' => 'Recruitment263', 'slug' => 'recruitment263', 'line' => "cited by name in Google's AI Overview when buyers ask about jobs in Zimbabwe."],
        'geo'              => ['name' => 'Recruitment263', 'slug' => 'recruitment263', 'line' => 'described and cited accurately by AI — because we shaped the sources it reads.'],
    ];
    $proof = $proofMap[$service->slug] ?? null;
    $proofHref = $proof ? ($proof['slug'] ? route('products.show', $proof['slug']) : route('work')) : null;
    $origin = 'https://' . ltrim(config('fignoc.brand.domain'), '/');
@endphp
<x-layout
    :title="$service->name"
    :description="$service->description"
    :canonical="route('services.show', $service)"
    :og-image="$heroImg">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Services', 'url' => route('services')],
        ['label' => $service->name, 'url' => route('services.show', $service)],
    ]" />

    <header class="svc-detail-head">
        <div class="section container-x">
        <div class="spotlight">
            <div class="reveal">
                <div class="flex items-center gap-3">
                    <span class="eyebrow" style="margin:0;">{{ $service->category }} · Service</span>
                    @if ($service->is_featured)
                        <span class="chip chip-accent">Featured</span>
                    @endif
                </div>
                <h1 class="display mt-5" style="max-width: 16ch;">{{ $service->name }}</h1>
                <p class="mt-5 text-lg leading-8" style="color: var(--color-body); max-width: 54ch;">{{ $service->description }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('contact') }}" class="btn btn-primary">Start a project
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="{{ route('services') }}" class="btn btn-ghost">All services</a>
                </div>
                <p class="mt-5 text-sm" style="color: var(--color-muted);">Free consultation · You own what we build · We reply within one business day.</p>
            </div>
            @php
                $isDiagram = str_contains($heroImg, 'proof/') || str_contains($heroImg, 'journey-funnel');
                $shotBg = str_contains($heroImg, 'ai-overview') ? '#1e2330'
                    : (str_contains($heroImg, 'journey-funnel') ? '#e9f6fb' : '#f3f5f9');
            @endphp
            <div class="spotlight-visual reveal" @if ($isDiagram) style="background: {{ $shotBg }};" @endif>
                <img src="{{ asset($heroImg) }}" alt="{{ $service->name }} — Fignoc Technologies"
                     fetchpriority="high" decoding="async" width="900" height="620"
                     style="width:100%; height:100%; object-fit: {{ $isDiagram ? 'contain' : 'cover' }}; object-position: {{ $isDiagram ? 'center' : 'top' }};">
            </div>
        </div>
        </div>
    </header>

    <div class="container-x pb-4" style="padding-top: 0.25rem;">
        <div class="grid gap-8 lg:gap-10 lg:grid-cols-[1.4fr_1fr]" style="border-top: 1px solid var(--color-line-soft); padding-top: 1.75rem;">
            <div class="space-y-7">
                @if (! empty($d['what_it_is']))
                    <section class="reveal">
                        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>What it is</h2>
                        <p class="mt-3 leading-8" style="color: var(--color-body); max-width: 60ch;">{{ $d['what_it_is'] }}</p>
                    </section>
                @endif

                @if (! empty($d['who_for']))
                    <section class="reveal">
                        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>Who it's for</h2>
                        <p class="mt-3 leading-8" style="color: var(--color-body); max-width: 60ch;">{{ $d['who_for'] }}</p>
                    </section>
                @endif

                @if (! empty($d['why']))
                    <section class="reveal{{ $service->is_featured ? ' surface' : '' }}" @if ($service->is_featured) style="background: var(--color-accent-tint); padding: 1.5rem;" @endif>
                        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>Why Fignoc</h2>
                        <p class="mt-3 leading-8" style="color: var(--color-body); max-width: 60ch;">{{ $d['why'] }}</p>
                    </section>
                @endif
            </div>

            @if (! empty($d['delivers']))
                <aside class="reveal">
                    <div class="surface surface-hover" style="padding: 1.75rem; position: sticky; top: 104px;">
                        <h2 class="eyebrow" style="margin:0;">What we deliver</h2>
                        <ul class="mt-6 space-y-4">
                            @foreach ($d['delivers'] as $item)
                                <li class="flex items-start gap-3">
                                    <span style="flex:none; width:26px; height:26px; border-radius:8px; background:var(--color-brand-tint); color:var(--color-navy); display:grid; place-items:center;">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8.5l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <span style="color: var(--color-heading); font-weight: 500; line-height: 1.5;">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            @endif
        </div>
    </div>

    {{-- Proof band — grounds the service in a real platform we built / run / rank. --}}
    @if ($proof)
    <section class="section container-x" aria-labelledby="svc-proof-h">
        <div class="svc-proof reveal">
            <div class="svc-proof-copy">
                <span class="svc-proof-eyebrow">Proof, not promises</span>
                <h2 id="svc-proof-h" class="svc-proof-title">We don't just offer this — we run it ourselves.</h2>
                <p class="svc-proof-text"><strong>{{ $proof['name'] }}</strong> is {{ $proof['line'] }}</p>
            </div>
            <a href="{{ $proofHref }}" class="btn btn-on-dark svc-proof-cta">See {{ $proof['name'] }}
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>
    @endif

    <x-faq :items="$faqs" eyebrow="FAQ" heading="Common questions" />

    @if ($related->isNotEmpty())
        @php
            $svcIcon = [
                'web-development' => 'globe', 'web-systems' => 'server', 'custom-software' => 'code',
                'ngo-systems' => 'heart', 'ecommerce' => 'cart', 'seo' => 'search',
                'aeo' => 'sparkles', 'geo' => 'sparkles', 'content-strategy' => 'layers',
                'google-ads' => 'trending-up', 'social-ads' => 'trending-up', 'customer-journey-optimisation' => 'trending-up',
        'app-development' => 'phone',
            ];
        @endphp
        <section class="svc-related section container-x" aria-labelledby="related-services">
            <div class="svc-related-head reveal">
                <div>
                    <span class="eyebrow">Next up</span>
                    <h2 id="related-services" class="svc-related-title">Related services</h2>
                    <p class="svc-related-sub">Other ways we can build, rank or grow with you.</p>
                </div>
                <a href="{{ route('services') }}" class="svc-related-all">
                    All services
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            <ul class="svc-related-grid" data-reveal-group>
                @foreach ($related as $i => $r)
                    @php $thumb = $serviceImages[$r->slug] ?? 'images/stock-photo-web-design-concept.jpg'; @endphp
                    <li class="reveal">
                        <a href="{{ route('services.show', $r) }}" class="svc-related-card">
                            <div class="svc-related-media">
                                <img
                                    src="{{ asset($thumb) }}"
                                    alt=""
                                    width="640"
                                    height="400"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                            <div class="svc-related-body">
                                <div class="svc-related-top">
                                    <span class="svc-related-index">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="chip">{{ $r->category ?? $r->tag }}</span>
                                </div>
                                <div class="svc-related-title-row">
                                    <span class="svc-related-ico" aria-hidden="true">
                                        <x-ficon :name="$svcIcon[$r->slug] ?? 'code'" :size="18" />
                                    </span>
                                    <h3 class="svc-related-card-title">{{ $r->name }}</h3>
                                </div>
                                @if ($r->description)
                                    <p class="svc-related-desc">{{ \Illuminate\Support\Str::limit($r->description, 90) }}</p>
                                @endif
                                <span class="svc-related-arrow">
                                    Learn more
                                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <x-cta-band />

    @push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service->name,
        'description' => $service->description,
        'serviceType' => $service->category,
        'url' => route('services.show', $service),
        'provider' => [
            '@id' => $origin . '/#organization',
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Harare'],
            ['@type' => 'Country', 'name' => 'Zimbabwe'],
        ],
        'image' => asset($heroImg),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layout>
