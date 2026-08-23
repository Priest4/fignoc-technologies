{{-- Services index — commercial Build / Rank / Grow catalogue. --}}
@php
    $groupMeta = [
        'Build' => [
            'no' => '01',
            'blurb' => 'Websites, web systems, custom software and online stores — engineered for performance, not dragged out of a template.',
            'accent' => null,
        ],
        'Rank' => [
            'no' => '02',
            'blurb' => 'Get found where Zimbabwe now searches — Google, and the AI answer engines your customers ask first.',
            'accent' => 'We optimise for AI answers, not just blue links.',
        ],
        'Grow' => [
            'no' => '03',
            'blurb' => 'Turn visibility into enquiries with accountable paid media and customer-journey optimisation.',
            'accent' => null,
        ],
    ];

    $serviceImages = [
        'web-development' => 'images/live/cv263.jpg',
        'web-systems' => 'images/live/nestzim.jpg',
        'custom-software' => 'images/live/recruitment263.jpg',
        'ngo-systems' => 'images/live/wlsa.jpg',
        'ecommerce' => 'images/live/shop263.jpg',
        'seo' => 'images/proof/seo-results.jpg',
        'aeo' => 'images/proof/ai-overview.jpg',
        'geo' => 'images/proof/ai-overview.jpg',
        'content-strategy' => 'images/svc-content.jpg',
        'google-ads' => 'images/svc-ads.jpg',
        'social-ads' => 'images/people/client-happy.jpg',
        'customer-journey-optimisation' => 'images/journey-funnel.png',
    ];

    $svcIcon = [
        'web-development' => 'globe', 'web-systems' => 'server', 'custom-software' => 'code',
        'ngo-systems' => 'heart', 'ecommerce' => 'cart', 'seo' => 'search',
        'aeo' => 'sparkles', 'geo' => 'sparkles', 'content-strategy' => 'layers',
        'google-ads' => 'trending-up', 'social-ads' => 'trending-up', 'customer-journey-optimisation' => 'trending-up',
    ];

    $total = $grouped->flatten(1)->count();
@endphp
<x-layout
    title="Services"
    description="We build websites, web systems, custom software and online stores — then rank you on Google and get you named in AI answers."
    :canonical="route('services')">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Services', 'url' => route('services')],
    ]" />

    <header class="svc-index-hero">
        <div class="container-x svc-index-hero-inner">
            <div class="reveal">
                <span class="eyebrow">Services</span>
                <h1 class="display mt-5" style="max-width: 14ch;">Build it. Rank it. Grow it.</h1>
                <p class="mt-6 max-w-xl text-lg leading-8" style="color: var(--color-body);">
                    One team for the whole job — a software company that builds the platform, and a growth agency that makes sure your customers (and the AI they ask) find you.
                </p>
                <div class="svc-index-actions mt-7">
                    <a href="{{ route('contact') }}" class="btn btn-primary">
                        Start a project
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="#grp-Build" class="btn btn-ghost">Browse services</a>
                </div>
            </div>

            <aside class="svc-index-jump reveal" aria-label="Service groups">
                <p class="svc-index-jump-label">{{ $total }} services · three chapters</p>
                <nav class="svc-index-jump-list">
                    @foreach (['Build', 'Rank', 'Grow'] as $cat)
                        @php $count = $grouped->get($cat, collect())->count(); @endphp
                        <a href="#grp-{{ $cat }}" class="svc-index-jump-link">
                            <span class="svc-index-jump-no">{{ $groupMeta[$cat]['no'] }}</span>
                            <span class="svc-index-jump-copy">
                                <span class="svc-index-jump-name">{{ $cat }}</span>
                                <span class="svc-index-jump-meta">{{ $count }} {{ \Illuminate\Support\Str::plural('service', $count) }}</span>
                            </span>
                            <span class="svc-index-jump-arrow" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </nav>
            </aside>
        </div>
    </header>

    @foreach (['Build', 'Rank', 'Grow'] as $cat)
        @php $items = $grouped->get($cat, collect()); @endphp
        <section class="svc-index-group section container-x" aria-labelledby="grp-{{ $cat }}">
            <div class="svc-index-group-grid">
                <div class="svc-index-group-intro reveal">
                    <span class="svc-index-group-no">{{ $groupMeta[$cat]['no'] }}</span>
                    <h2 id="grp-{{ $cat }}" class="svc-index-group-title">{{ $cat }}</h2>
                    <p class="svc-index-group-blurb">{{ $groupMeta[$cat]['blurb'] }}</p>
                    @if ($groupMeta[$cat]['accent'])
                        <p class="svc-index-group-accent">{{ $groupMeta[$cat]['accent'] }}</p>
                    @endif
                </div>

                <ul class="svc-index-cards" data-reveal-group>
                    @foreach ($items as $i => $s)
                        @php $thumb = $serviceImages[$s->slug] ?? 'images/stock-photo-web-design-concept.jpg'; @endphp
                        <li class="reveal">
                            <a href="{{ route('services.show', $s) }}" class="svc-index-card{{ $s->is_featured ? ' is-featured' : '' }}">
                                <div class="svc-index-card-media">
                                    <img
                                        src="{{ asset($thumb) }}"
                                        alt=""
                                        width="640"
                                        height="360"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                                <div class="svc-index-card-body">
                                    <div class="svc-index-card-top">
                                        <span class="svc-index-card-index">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="chip{{ $s->is_featured ? ' chip-accent' : '' }}">{{ $s->tag }}</span>
                                    </div>
                                    <div class="svc-index-card-title-row">
                                        <span class="svc-index-card-ico" aria-hidden="true">
                                            <x-ficon :name="$svcIcon[$s->slug] ?? 'code'" :size="18" />
                                        </span>
                                        <h3 class="svc-index-card-title">{{ $s->name }}</h3>
                                    </div>
                                    @if ($s->description)
                                        <p class="svc-index-card-desc">{{ \Illuminate\Support\Str::limit($s->description, 100) }}</p>
                                    @endif
                                    <span class="svc-index-card-arrow">
                                        Learn more
                                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endforeach

    <section class="svc-index-close" aria-labelledby="svc-index-close-h">
        <div class="container-x svc-index-close-inner reveal">
            <div class="svc-index-close-copy">
                <span class="svc-index-close-eyebrow">Start with clarity</span>
                <h2 id="svc-index-close-h" class="svc-index-close-title">Not sure which service fits? Tell us the outcome — we’ll map the work.</h2>
                <p class="svc-index-close-text">Free consultation, honest scope, and a reply within one business day.</p>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-primary svc-index-close-cta">
                Start a project
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>
</x-layout>
