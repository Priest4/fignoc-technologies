{{--
  Landing-page header — the Starlink pattern: a floating translucent bar over
  the imagery carrying the wordmark, one persistent CTA, and a hamburger that
  is the only route to the rest of the company. The page has no inline
  navigation competing with the offer.

  Vanilla JS, no Alpine: this must be interactive before any bundle parses, and
  the landing page ships no other JavaScript.
--}}
@php
    $brand = config('fignoc.brand');
    $wa = preg_replace('/\D+/', '', $brand['whatsapp'] ?? '');
    $waHref = 'https://wa.me/' . $wa . '?text=' . rawurlencode("Hi Fignoc, I'd like the free 10-Minute Visibility Check for [website].");

    $mainLinks = [
        ['label' => 'Home',     'url' => route('home'),     'hint' => 'Fignoc Technologies'],
        ['label' => 'Services', 'url' => route('services'), 'hint' => 'Build · Rank · Grow'],
        ['label' => 'Our work', 'url' => route('work'),     'hint' => 'Platforms we have shipped'],
        ['label' => 'Products', 'url' => route('products'), 'hint' => 'Our own software'],
        ['label' => 'About',    'url' => route('about'),    'hint' => 'Who we are'],
        ['label' => 'Insights', 'url' => route('insights'), 'hint' => 'Search and AEO writing'],
        ['label' => 'Contact',  'url' => route('contact'),  'hint' => 'Start a project'],
    ];
@endphp

<header class="lp-head">
    <div class="lp-head-bar">
        <a href="{{ route('home') }}" class="lp-mark">Fignoc<b>.</b></a>

        @if ($wa)
            <button type="button" class="lp-btn lp-btn--primary lp-btn--sm" data-quote>
                Get started
            </button>
        @endif

        <button type="button" class="lp-burger" id="lp-burger"
                aria-expanded="false" aria-controls="lp-menu" aria-label="Open the main Fignoc site menu">
            <span class="lp-burger-box" aria-hidden="true"><i></i><i></i><i></i></span>
        </button>
    </div>

    <nav class="lp-menu" id="lp-menu" data-open="false" aria-label="Fignoc main site">
        <div class="lp-menu-in">
            <div>
                <p class="lp-menu-lab">The full Fignoc site</p>
                <div class="lp-menu-links">
                    @foreach ($mainLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener">
                            <span>{{ $link['label'] }}</span>
                            <small>{{ $link['hint'] }}</small>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="lp-menu-foot">
                <p class="lp-menu-lab">Talk to a person</p>
                <p style="margin-top:.7rem;">
                    <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer">WhatsApp {{ $brand['phone'] }}</a><br>
                    <a href="mailto:{{ $brand['email'] }}">{{ $brand['email'] }}</a>
                </p>
            </div>
        </div>
    </nav>
</header>
