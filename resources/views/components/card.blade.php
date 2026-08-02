{{--
  Card (brief §4) — one component, three variants: service | product | work.
  In-dev state → NO link + "Launching soon" chip (brief §7.4 rule). Only render
  a live external link when the item is actually live.

  Props:
    variant     service | product | work
    title       card heading
    tag         small label (service category / product tag / work type)
    description one-line summary
    href        internal detail-page URL (null + state=in-dev ⇒ not clickable)
    state       live | in-dev  (products & work)
    index       "01".."08" for numbered service cards
    image       work thumbnail (path relative to /public)
    meta        array of small labels (e.g. tech chips on work cards)
    featured    bool — slightly stronger border
--}}
@props([
    'variant' => 'service',
    'title' => '',
    'tag' => null,
    'description' => null,
    'href' => null,
    'state' => null,
    'index' => null,
    'image' => null,
    'mockup' => null,
    'meta' => [],
    'featured' => false,
])

@php
    // Detail / case-study pages exist for in-dev items too, so a card is
    // clickable whenever it has an internal href. The "Launching soon" chip
    // conveys status; only the EXTERNAL "visit live" link is withheld (§7.4).
    $isInDev = $state === 'in-dev';
    $clickable = (bool) $href;
    $el = $clickable ? 'a' : 'article';
    $classes = 'card' . ($featured ? ' is-featured' : '');
@endphp

<{{ $el }}
    @if ($clickable) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}>

    @if ($image)
        {{-- Real screenshot (live products/work) --}}
        <div class="card-media">
            <img src="{{ asset(ltrim($image, '/')) }}" alt="{{ $title }} screenshot" loading="lazy" decoding="async" width="640" height="400">
        </div>
    @elseif ($mockup)
        {{-- Branded UI mockup (in-development items with no live site yet) --}}
        <div class="card-media"><x-mockup :for="$mockup" :label="$title" /></div>
    @endif

    <div class="card-body">
        <div class="card-top">
            @if ($index)
                <span class="card-index">{{ $index }}</span>
            @elseif ($tag)
                <span class="chip">{{ $tag }}</span>
            @endif

            @if ($state === 'live')
                <span class="chip chip-live">Live</span>
            @elseif ($isInDev)
                <span class="chip chip-accent">Launching soon</span>
            @endif
        </div>

        <h3 class="card-title">{{ $title }}</h3>

        @if ($description)
            <p class="card-desc">{{ $description }}</p>
        @endif

        @if (! empty($meta))
            <div class="flex flex-wrap gap-2 pt-1">
                @foreach ($meta as $m)
                    <span class="chip">{{ $m }}</span>
                @endforeach
            </div>
        @endif

        @if ($clickable)
            <span class="card-arrow">
                {{ $variant === 'work' ? 'View case study' : ($variant === 'product' ? 'Explore product' : 'Learn more') }}
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        @elseif ($isInDev)
            <span class="card-soft">In development</span>
        @endif
    </div>
</{{ $el }}>
