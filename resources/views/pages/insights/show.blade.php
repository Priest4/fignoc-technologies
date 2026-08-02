{{-- Insight article — modern editorial reading experience. --}}
@php
    $origin = 'https://' . ltrim(config('fignoc.brand.domain'), '/');
    $logoPath = ltrim(config('fignoc.brand.logo_path', 'images/og-default.jpg'), '/');
    $coverPath = $post->cover_path ? ltrim($post->cover_path, '/') : $logoPath;
    $articleImage = $origin . '/' . $coverPath;
    $ogImage = $coverPath;
    $publisherLogo = $origin . '/' . $logoPath;
@endphp
<x-layout
    :title="$post->title"
    :description="$post->excerpt"
    ogType="article"
    :canonical="route('insights.show', $post)"
    :og-image="$ogImage">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Insights', 'url' => route('insights')],
        ['label' => $post->title, 'url' => route('insights.show', $post)],
    ]" />

    <article>
        <header class="insight-article-hero{{ $post->cover_url ? ' insight-article-hero--split' : '' }}">
            <div class="container-x insight-article-hero-grid reveal">
                <div class="insight-article-hero-copy">
                    <div class="insight-article-meta">
                        <span class="chip chip-accent">{{ $post->topic }}</span>
                        <span class="insights-meta-line">
                            <time datetime="{{ optional($post->published_at)->toDateString() }}">{{ optional($post->published_at)->format('j F Y') }}</time>
                            @if ($post->read_minutes) · {{ $post->read_minutes }} min read @endif
                        </span>
                    </div>
                    <h1 class="display insight-article-title">{{ $post->title }}</h1>
                    @if ($post->excerpt)
                        <p class="insight-article-deck">{{ $post->excerpt }}</p>
                    @endif
                    @if ($post->author)
                        <p class="insight-article-by">By {{ $post->author }}</p>
                    @endif
                </div>

                @if ($post->cover_url)
                    <figure class="insight-article-cover">
                        <img
                            src="{{ $post->cover_url }}"
                            alt=""
                            width="960"
                            height="720"
                            loading="eager"
                            decoding="async"
                        >
                    </figure>
                @endif
            </div>
        </header>

        <div class="container-x">
            <div class="insight-article-layout">
                <aside class="insight-rail reveal" aria-label="Article actions">
                    <a href="{{ route('insights') }}" class="insight-rail-back">← Journal</a>
                    <div class="insight-rail-card">
                        <p class="insight-rail-label">Topic</p>
                        <p class="insight-rail-value">{{ $post->topic }}</p>
                        <p class="insight-rail-label mt-5">Reading time</p>
                        <p class="insight-rail-value">{{ $post->read_minutes ? $post->read_minutes . ' min' : '—' }}</p>
                    </div>
                    <a href="{{ route('contact') }}" class="btn btn-primary w-full" style="margin-top: 1rem;">Start a project</a>
                </aside>

                <div class="insight-article-body reveal">
                    <div class="prose prose-insight">
                        {!! $post->body_html !!}
                    </div>

                    <div class="insight-article-end">
                        <div class="insight-takeaway">
                            <p class="insight-takeaway-label">Next step</p>
                            <p class="insight-takeaway-copy">Want to see how AI engines currently describe your business? We’ll audit it — free, no obligation.</p>
                            <a href="{{ route('contact') }}" class="btn btn-primary mt-5">Talk to Fignoc</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="section container-x" style="border-top: 1px solid var(--color-line-soft);" aria-labelledby="related-insights">
            <div class="insights-grid-head reveal">
                <h2 id="related-insights" class="insights-section-title">Keep reading</h2>
                <p class="insights-section-sub">More from the journal</p>
            </div>
            <ul class="insights-grid" data-reveal-group>
                @foreach ($related as $i => $r)
                    <li class="reveal">
                        <a href="{{ route('insights.show', $r) }}" class="insight-card">
                            @if ($r->cover_url)
                                <div class="insight-card-media">
                                    <img
                                        src="{{ $r->cover_url }}"
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
                                    <span class="insight-card-index">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="chip">{{ $r->topic }}</span>
                                </div>
                                <h3 class="insight-card-title">{{ $r->title }}</h3>
                                @if ($r->excerpt)
                                    <p class="insight-card-desc">{{ \Illuminate\Support\Str::limit($r->excerpt, 120) }}</p>
                                @endif
                                <div class="insight-card-foot">
                                    <span class="insights-meta-line">{{ optional($r->published_at)->format('M Y') }}</span>
                                    <span class="insight-card-arrow" aria-hidden="true">→</span>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <x-cta-band
        heading="Ready to be the answer in your category?"
        text="We build the software and the answer-engine work that gets you found — on Google and in AI."
    />

    @push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->title,
        'description' => $post->excerpt,
        'image' => [$articleImage],
        'datePublished' => optional($post->published_at)->toIso8601String(),
        'dateModified' => optional($post->updated_at ?? $post->published_at)->toIso8601String(),
        'author' => ['@type' => 'Person', 'name' => $post->author ?? config('fignoc.brand.name')],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('fignoc.brand.name'),
            '@id' => $origin . '/#organization',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $publisherLogo,
            ],
        ],
        'mainEntityOfPage' => route('insights.show', $post),
        'articleSection' => $post->topic,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layout>
