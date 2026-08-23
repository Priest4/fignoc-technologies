{{--
  Landing-page footer. Deliberately thin: the legal links Google Ads requires
  on a landing page, the physical address that answers "are you real", and one
  route back to the main site. No sitemap of exits.
--}}
@php $brand = config('fignoc.brand'); @endphp

<footer class="lp-foot">
    <div class="lp-foot-in">
        <span>&copy; {{ now()->year }} {{ $brand['name'] }}</span>
        <span class="lp-foot-links">
            <a href="mailto:{{ $brand['email'] }}">{{ $brand['email'] }}</a>
            <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy</a>
            <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms</a>
            <a href="{{ route('home') }}" target="_blank" rel="noopener">Main site</a>
        </span>
    </div>
</footer>
