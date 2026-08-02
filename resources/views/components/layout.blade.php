@props([
    'title' => null,
    'description' => 'Fignoc Technologies is a Harare software company. We build custom software, web systems, apps and ecommerce for Zimbabwe — and run the digital growth (SEO, AEO/GEO, ads) that gets you found.',
    'canonical' => null,
    'ogImage' => null,
    'ogType' => 'website',
])
@php
    $brand = config('fignoc.brand');
    $siteName = $brand['name'];
    $fullTitle = $title
        ? $title . ' — ' . $siteName
        : $siteName . ' — Zimbabwe software company: custom software, web & digital growth';
    // Prefer an explicit canonical; otherwise strip query strings from the current URL.
    // Production: set APP_URL=https://www.fignoc.co.zw so asset()/route() URLs are absolute HTTPS.
    $canonicalUrl = $canonical ?? strtok(url()->current(), '?');
    $origin = 'https://' . ltrim($brand['domain'], '/');
    $ogImageUrl = $ogImage
        ? (str_starts_with($ogImage, 'http') ? $ogImage : asset(ltrim($ogImage, '/')))
        : asset(ltrim($brand['logo_path'] ?? 'images/og-default.jpg', '/'));
    // Force absolute OG/schema image URLs on the brand origin when asset() is relative/local.
    if (! str_starts_with($ogImageUrl, 'http')) {
        $ogImageUrl = $origin . '/' . ltrim($ogImageUrl, '/');
    }

    $logoPath = ltrim($brand['logo_path'] ?? 'images/og-default.jpg', '/');
    $logoUrl = $origin . '/' . $logoPath;
    $sameAs = array_values(array_filter($brand['same_as'] ?? []));
    $waDigits = preg_replace('/\D+/', '', $brand['whatsapp'] ?? '');
    $telephone = $waDigits ? ('+' . $waDigits) : ($brand['phone'] ?? null);

    $organization = [
        '@type' => 'Organization',
        '@id' => $origin . '/#organization',
        'name' => $siteName,
        'url' => $origin,
        'email' => $brand['email'],
        'telephone' => $telephone,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $logoUrl,
        ],
        'image' => $logoUrl,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '811 Waterfall',
            'addressLocality' => 'Harare',
            'addressCountry' => 'ZW',
        ],
        'areaServed' => ['@type' => 'Country', 'name' => 'Zimbabwe'],
    ];
    if ($sameAs !== []) {
        $organization['sameAs'] = $sameAs;
    }

    $localBusiness = [
        '@type' => ['ProfessionalService', 'LocalBusiness'],
        '@id' => $origin . '/#localbusiness',
        'name' => $siteName,
        'url' => $origin,
        'email' => $brand['email'],
        'telephone' => $telephone,
        'image' => $logoUrl,
        'logo' => $logoUrl,
        'parentOrganization' => ['@id' => $origin . '/#organization'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '811 Waterfall',
            'addressLocality' => 'Harare',
            'addressRegion' => 'Harare',
            'addressCountry' => 'ZW',
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Harare'],
            ['@type' => 'Country', 'name' => 'Zimbabwe'],
        ],
    ];

    // Organization + WebSite + LocalBusiness JSON-LD, sitewide (brief §6).
    $orgLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $organization,
            $localBusiness,
            [
                '@type' => 'WebSite',
                '@id' => $origin . '/#website',
                'url' => $origin,
                'name' => $siteName,
                'publisher' => ['@id' => $origin . '/#organization'],
                'inLanguage' => 'en',
            ],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    @if ($sc = config('fignoc.analytics.search_console_verification'))
        <meta name="google-site-verification" content="{{ $sc }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:locale" content="en_ZW">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">

    {{-- Icons --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    {{-- Preload the self-hosted variable font (LCP text) --}}
    <link rel="preload" href="{{ asset('fonts/satoshi-variable.woff2') }}" as="font" type="font/woff2" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Sitewide Organization + LocalBusiness + WebSite structured data --}}
    <script type="application/ld+json">{!! json_encode($orgLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    {{-- Per-page structured data / meta (BreadcrumbList, FAQPage, Article…) --}}
    @stack('head')

    {{-- GA4 — only loads when config('fignoc.analytics.ga4') is a real Measurement ID. --}}
    @php $ga4 = config('fignoc.analytics.ga4'); @endphp
    @if ($ga4)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){ dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '{{ $ga4 }}');
        document.addEventListener('click', function (e) {
            var el = e.target.closest && e.target.closest('a, button');
            if (!el) return;
            var href = el.getAttribute('href') || '';
            if (href.indexOf('wa.me') !== -1) {
                gtag('event', 'whatsapp_click', { link_url: href });
            } else if (/\/contact\b/.test(href)) {
                gtag('event', 'cta_contact_click', { link_text: (el.textContent || '').trim().slice(0, 60) });
            }
        }, { passive: true, capture: true });
        document.addEventListener('submit', function (e) {
            if (e.target.matches('form[action*="contact"]')) gtag('event', 'contact_form_submit');
        }, true);
    </script>
    @endif
</head>
<body class="font-sans antialiased">
    <a href="#main" class="skip-link">Skip to content</a>

    <x-navbar />

    <main id="main">{{ $slot }}</main>

    <x-footer />

    @php $waNum = preg_replace('/\D+/', '', $brand['whatsapp'] ?? ''); @endphp
    @if ($waNum)
        <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener noreferrer" class="wa-float" aria-label="Chat with Fignoc on WhatsApp">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 2.1.55 4.05 1.6 5.77L2 22l4.45-1.17a9.86 9.86 0 0 0 5.59 1.72h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-2.64.69.7-2.57-.19-.31a8.18 8.18 0 0 1-1.26-4.4c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.2 8.2 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43-.14 0-.31-.01-.47-.01-.16 0-.43.06-.65.31-.22.25-.86.84-.86 2.05 0 1.21.88 2.38 1 2.54.12.16 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.51.59.19 1.12.16 1.54.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.22-.17-.47-.29Z"/></svg>
        </a>
    @endif

    @stack('scripts')
</body>
</html>
