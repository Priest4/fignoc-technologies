{{--
  Testimonials strip (audit P0 — social proof). One card per quote: star rating,
  the quote, and an initials avatar (no stock faces). Content comes from
  config('fignoc.testimonials') — currently PLACEHOLDER copy to be swapped for
  real, attributable client quotes before launch.

  Props:
    items    array of ['quote' => .., 'name' => .., 'role' => .., 'company' => ..]
    eyebrow  small label
    heading  section heading
--}}
@props([
    'items' => [],
    'eyebrow' => 'Testimonials',
    'heading' => "What clients say.",
])

@if (! empty($items))
<section class="section" style="border-top: 1px solid var(--color-line-soft);" aria-labelledby="testimonials-heading">
    <div class="container-x">
        <div class="reveal" style="max-width: 44rem;">
            <span class="eyebrow">{{ $eyebrow }}</span>
            <h2 id="testimonials-heading" class="display mt-4" style="font-size: 2.2rem;">{{ $heading }}</h2>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3" data-reveal-group>
            @foreach ($items as $t)
                @php
                    $initials = \Illuminate\Support\Str::of($t['name'] ?? '')
                        ->explode(' ')->filter()
                        ->map(fn ($w) => \Illuminate\Support\Str::substr($w, 0, 1))
                        ->take(2)->implode('');
                @endphp
                <figure class="reveal card" style="padding: 1.75rem; gap: 1rem;">
                    <div style="display: flex; gap: 0.1rem; color: var(--color-accent);" aria-hidden="true">
                        @for ($i = 0; $i < 5; $i++)
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.6l2.55 5.17 5.7.83-4.13 4.02.98 5.68L10 14.98l-5.08 2.72.98-5.68L1.75 7.6l5.7-.83z"/></svg>
                        @endfor
                    </div>
                    <blockquote style="font-size: 1rem; line-height: 1.65; color: var(--color-body);">"{{ $t['quote'] }}"</blockquote>
                    <figcaption class="flex items-center gap-3" style="margin-top: auto; padding-top: 0.5rem;">
                        <span aria-hidden="true" style="width: 42px; height: 42px; border-radius: 999px; background: var(--color-brand-tint); color: var(--color-navy); display: grid; place-items: center; font-weight: 700; font-size: 0.9rem; flex: none;">{{ $initials }}</span>
                        <span>
                            <span style="display: block; font-weight: 600; color: var(--color-heading);">{{ $t['name'] }}</span>
                            <span style="display: block; font-size: 0.85rem; color: var(--color-muted);">{{ $t['role'] }}@if (! empty($t['company'])), {{ $t['company'] }}@endif</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
