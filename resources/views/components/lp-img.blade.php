{{--
  Responsive image for the landing pages.

  Emits <picture> with a WebP source and a progressive-JPEG fallback, both
  driven by the ladder in public/images/opt/manifest.json. Falls back to the
  untouched original when the optimiser has not generated variants yet.

  Props:
    src    path relative to /public/images, e.g. 'proof/seo-results.jpg'
    alt    alt text ('' for decorative)
    sizes  the CSS box this fills, so the browser picks the right rung
    eager  true for the LCP image only — everything else stays lazy
--}}
@props([
    'src',
    'alt' => '',
    'sizes' => '100vw',
    'eager' => false,
])

@php
    $set = \App\Support\ImageSet::for($src);
@endphp

@if ($set)
    <picture>
        <source type="image/webp" srcset="{{ $set['webp'] }}" sizes="{{ $sizes }}">
        <img
            {{ $attributes }}
            src="{{ $set['fallback'] }}"
            srcset="{{ $set['jpg'] }}"
            sizes="{{ $sizes }}"
            width="{{ $set['width'] }}"
            height="{{ $set['height'] }}"
            alt="{{ $alt }}"
            loading="{{ $eager ? 'eager' : 'lazy' }}"
            @if ($eager) fetchpriority="high" @endif
            decoding="async">
    </picture>
@else
    {{-- No variants on disk: ship the original rather than nothing. Run
         `node tools/optimise-images.mjs` to generate the ladder. --}}
    <img
        {{ $attributes }}
        src="{{ asset('images/' . $src) }}"
        alt="{{ $alt }}"
        loading="{{ $eager ? 'eager' : 'lazy' }}"
        @if ($eager) fetchpriority="high" @endif
        decoding="async">
@endif
