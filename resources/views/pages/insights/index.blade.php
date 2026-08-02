{{-- Insights index — editorial journal (commercial-grade). --}}
@php
    $topics = ['AEO', 'GEO', 'SEO', 'Ecommerce', 'NGO'];
@endphp
<x-layout
    title="Insights"
    description="Practical writing from Fignoc Technologies on AEO, GEO, SEO and building software for the Zimbabwean market."
    :canonical="route('insights')">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Insights', 'url' => route('insights')],
    ]" />

    {{-- Editorial hero --}}
    <header class="insights-hero">
        <div class="container-x insights-hero-inner">
            <div class="reveal">
                <span class="eyebrow">Insights</span>
                <h1 class="display mt-5" style="max-width: 14ch;">Notes on being the answer.</h1>
                <p class="mt-6 max-w-xl text-lg leading-8" style="color: var(--color-body);">
                    Plain-language writing on AEO, GEO, SEO and software that wins customers in Zimbabwe — written the way people search now.
                </p>
            </div>
            <div class="insights-topics reveal" aria-label="Topics">
                @foreach ($topics as $topic)
                    <span class="insights-topic">{{ $topic }}</span>
                @endforeach
            </div>
        </div>
    </header>

    @if ($featured)
        {{-- Featured lead --}}
        <section class="container-x section insights-featured-wrap" aria-labelledby="featured-insight">
            <a href="{{ route('insights.show', $featured) }}" class="insights-featured{{ $featured->cover_path ? ' insights-featured--media' : '' }} reveal">
                @if ($featured->cover_url)
                    <div class="insights-featured-media">
                        <img
                            src="{{ $featured->cover_url }}"
                            alt=""
                            width="960"
                            height="640"
                            loading="eager"
                            decoding="async"
                        >
                    </div>
                @endif
                <div class="insights-featured-copy">
                    <div class="insights-featured-meta">
                        <span class="chip chip-accent">Featured</span>
                        <span class="chip">{{ $featured->topic }}</span>
                        <span class="insights-meta-line">
                            <time datetime="{{ optional($featured->published_at)->toDateString() }}">{{ optional($featured->published_at)->format('j M Y') }}</time>
                            @if ($featured->read_minutes) · {{ $featured->read_minutes }} min read @endif
                        </span>
                    </div>
                    <h2 id="featured-insight" class="insights-featured-title">{{ $featured->title }}</h2>
                    @if ($featured->excerpt)
                        <p class="insights-featured-excerpt">{{ $featured->excerpt }}</p>
                    @endif
                    <span class="insights-featured-cta">
                        Read the piece
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
            </a>
        </section>
    @endif

    <section class="container-x section" style="{{ $featured ? 'padding-top: 0;' : '' }}">
        @if ($all->isNotEmpty())
            @if ($posts->isNotEmpty())
                <div class="insights-grid-head reveal">
                    <h2 class="insights-section-title">More from the journal</h2>
                    <p class="insights-section-sub">{{ $all->count() }} pieces · updated as the search landscape moves</p>
                </div>
            @endif

            <ul class="insights-grid" data-reveal-group>
                @foreach (($posts->isNotEmpty() ? $posts : $all) as $i => $post)
                    <li class="reveal">
                        <a href="{{ route('insights.show', $post) }}" class="insight-card">
                            @if ($post->cover_url)
                                <div class="insight-card-media">
                                    <img
                                        src="{{ $post->cover_url }}"
                                        alt=""
                                        width="640"
                                        height="400"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                            @endif
                            <div class="insight-card-body">
                                <div class="insight-card-top">
                                    <span class="insight-card-index">{{ str_pad((string) ($i + ($featured ? 2 : 1)), 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="chip">{{ $post->topic }}</span>
                                </div>
                                <h3 class="insight-card-title">{{ $post->title }}</h3>
                                @if ($post->excerpt)
                                    <p class="insight-card-desc">{{ $post->excerpt }}</p>
                                @endif
                                <div class="insight-card-foot">
                                    <span class="insights-meta-line">
                                        <time datetime="{{ optional($post->published_at)->toDateString() }}">{{ optional($post->published_at)->format('M Y') }}</time>
                                        @if ($post->read_minutes) · {{ $post->read_minutes }} min @endif
                                    </span>
                                    <span class="insight-card-arrow" aria-hidden="true">→</span>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="reveal insights-empty">
                <span class="eyebrow" style="justify-content: center;">Coming soon</span>
                <h2 class="mt-4" style="font-size: 1.5rem; text-align: center;">The journal is warming up.</h2>
                <p class="mt-3 text-center" style="color: var(--color-muted); max-width: 36ch; margin-inline: auto;">Practical notes on being found — on Google and in AI answers — are on the way.</p>
                <div class="mt-8" style="text-align: center;">
                    <a href="{{ route('contact') }}" class="btn btn-primary">Start a project</a>
                </div>
            </div>
        @endif
    </section>

    <section class="insights-close" aria-labelledby="insights-close-h">
        <div class="container-x insights-close-inner reveal">
            <div class="insights-close-copy">
                <span class="insights-close-eyebrow">Be the answer</span>
                <h2 id="insights-close-h" class="insights-close-title">Want AI to name you — not your competitor?</h2>
                <p class="insights-close-text">We build the platforms and the answer-engine work that gets Zimbabwean businesses recommended.</p>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-primary insights-close-cta">
                Start a project
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>
</x-layout>
