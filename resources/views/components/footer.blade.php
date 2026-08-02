{{--
  Site footer (brief §4). Services list · Products list · Company · Contact.
  All links are real crawlable hrefs (brief §6). The retired sub-brand is not referenced.
--}}
@php
    $brand = config('fignoc.brand');
    $footerServices = [
        ['Web development', 'web-development'],
        ['Web systems & apps', 'web-systems'],
        ['Custom software', 'custom-software'],
        ['NGO information systems', 'ngo-systems'],
        ['Ecommerce', 'ecommerce'],
        ['SEO', 'seo'],
        ['AEO — Answer Engine Optimisation', 'aeo'],
        ['GEO — Generative Engine Optimisation', 'geo'],
        ['Content strategy & audit', 'content-strategy'],
        ['Google Ads', 'google-ads'],
        ['Social media ads', 'social-ads'],
        ['Customer journey optimisation', 'customer-journey-optimisation'],
    ];
    $footerProducts = [
        ['CV263', 'cv263'],
        ['Recruitment263', 'recruitment263'],
        ['Shop263', 'shop263'],
        ['NestZim', 'nestzim'],
        ['NiceJob', 'nicejob'],
    ];
@endphp

<footer class="site-footer">
    <div class="mx-auto max-w-[1280px] px-5 md:px-8 py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
            {{-- Brand + socials --}}
            <div>
                <a href="{{ route('home') }}" class="wordmark" style="color: var(--color-ink);">
                    Fignoc<span class="wordmark-dot">.</span>
                </a>
                <p class="mt-4 max-w-xs text-sm leading-7" style="color: var(--color-muted);">
                    A Harare digital product studio &amp; growth agency. We build our own platforms and rank businesses where Zimbabwe is searching — including AI answer engines.
                </p>
                <div class="mt-6">
                    <a href="mailto:{{ $brand['email'] }}" class="link-accent text-sm font-semibold" style="color: var(--color-ink);">{{ $brand['email'] }}</a>
                </div>
            </div>

            <div>
                <h4>Services</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($footerServices as [$label, $slug])
                        <li><a href="{{ route('services.show', $slug) }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4>Products</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($footerProducts as [$label, $slug])
                        <li><a href="{{ route('products.show', $slug) }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
                <h4 class="mt-8">Company</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('work') }}">Work</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('insights') }}">Insights</a></li>
                </ul>
            </div>

            <div>
                <h4>Contact</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="mailto:{{ $brand['email'] }}">{{ $brand['email'] }}</a></li>
                    <li><a href="tel:{{ str_replace(' ', '', $brand['phone']) }}">{{ $brand['phone'] }}</a></li>
                    <li><span style="color: var(--color-muted);">{{ $brand['address'] }}</span></li>
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t pt-6 sm:flex-row sm:items-center sm:justify-between"
             style="border-color: var(--color-line-soft);">
            <p class="text-xs" style="color: var(--color-muted);">
                &copy; {{ date('Y') }} Fignoc Technologies. Harare, Zimbabwe.
            </p>
            <div class="flex items-center gap-6 text-xs">
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
            </div>
        </div>
    </div>
</footer>
