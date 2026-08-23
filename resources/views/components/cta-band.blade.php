{{--
  Reusable pre-footer CTA band (brief §4). Used on Home + every service /
  product / work page. Primary = "Start a project" → contact; ghost = work.
  Carries a real person photo (site-wide human warmth to balance the mockups).
  TODO (brief §11): swap stock for a real Fignoc client / lifestyle photo.
--}}
@props([
    'heading' => 'Ready to get found and win more customers?',
    'text' => "Tell us what you're building — or where you want to be found. Free consultation, honest scope, and you own everything we ship. We reply within one business day.",
    'primaryLabel' => 'Start a project',
    'secondaryLabel' => 'See the work',
    'image' => 'images/people/team-office.jpg',
])

<section aria-labelledby="cta-heading" class="cta-band">
    <div class="container-x section">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr] lg:items-center reveal">
            <div>
                <span class="eyebrow" style="color: color-mix(in srgb, var(--color-on-dark) 70%, transparent);">Start a project</span>
                <h2 id="cta-heading" class="display mt-4" style="color: var(--color-paper); max-width: 20ch;">{{ $heading }}</h2>
                <p class="mt-5 max-w-xl leading-7" style="color: color-mix(in srgb, var(--color-on-dark) 72%, transparent);">{{ $text }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('contact') }}" class="btn btn-primary">
                        {{ $primaryLabel }}
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="{{ route('work') }}" class="btn btn-outline-light">
                        {{ $secondaryLabel }}
                    </a>
                </div>
            </div>
            @if ($image)
                <div class="hidden md:block">
                    <img src="{{ asset(ltrim($image, '/')) }}" alt="" aria-hidden="true"
                         loading="lazy" decoding="async" width="640" height="426"
                         style="width: 100%; aspect-ratio: 3/2; object-fit: cover; object-position: center 30%; border-radius: 16px; box-shadow: 0 40px 80px -40px rgba(0,0,0,0.6);">
                </div>
            @endif
        </div>
    </div>
</section>
