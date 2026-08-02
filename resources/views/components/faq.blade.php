{{--
  FAQ accordion (brief §4). Accessible: real <button> toggles with aria-expanded
  + aria-controls, keyboard operable. Height animates via grid-template-rows
  0fr→1fr. Emits FAQPage JSON-LD to @stack('head') (brief §6).

  Props:
    items    array of ['q' => question, 'a' => answer(HTML allowed)]
    heading  section heading
    eyebrow  small label above heading
    intro    optional one-line support under the heading
--}}
@props([
    'items' => [],
    'heading' => 'Frequently asked questions',
    'eyebrow' => 'FAQ',
    'intro' => 'Straight answers — click a question to open it.',
])

@if (! empty($items))
<section class="faq-section section" aria-labelledby="faq-heading">
    <div class="container-x faq-layout">
        <div class="faq-intro reveal">
            @if ($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
            <h2 id="faq-heading" class="faq-heading">{{ $heading }}</h2>
            @if ($intro)
                <p class="faq-lede">{{ $intro }}</p>
            @endif
        </div>

        <div x-data="{ open: 0 }" class="faq-list reveal">
            @foreach ($items as $i => $item)
                <div class="faq-item" :class="{ 'is-open': open === {{ $i }} }">
                    <h3 class="faq-item-title">
                        <button type="button" class="faq-q"
                                :aria-expanded="(open === {{ $i }}).toString()"
                                aria-controls="faq-panel-{{ $i }}"
                                @click="open = (open === {{ $i }} ? null : {{ $i }})">
                            <span class="faq-q-index" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="faq-q-text">{{ $item['q'] }}</span>
                            <span class="faq-q-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                        </button>
                    </h3>
                    <div id="faq-panel-{{ $i }}" class="faq-panel" :class="{ 'open': open === {{ $i }} }" role="region">
                        <div class="faq-a-inner">
                            <div class="faq-a">{!! $item['a'] !!}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($items)->map(fn ($item) => [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => trim(strip_tags($item['a'])),
        ],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@endif
