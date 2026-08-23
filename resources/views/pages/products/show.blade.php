{{-- Product detail (brief §7.7). SoftwareApplication JSON-LD + cross-link to case study. --}}
@php
    $long = data_get($product->detail, 'long', $product->description);
@endphp
<x-layout
    {{-- The tag distinguishes this from /work/{slug} for the same platform. --}}
    :title="$product->name . ($product->tag ? ' — ' . $product->tag : ' — our product')"
    :description="\Illuminate\Support\Str::limit(trim(($product->headline ? $product->headline . ' ' : '') . $product->description), 152)"
    :canonical="route('products.show', $product)"
    :og-image="$product->screenshot_path">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Products', 'url' => route('products')],
        ['label' => $product->name, 'url' => route('products.show', $product)],
    ]" />

    <header class="section container-x">
        <div class="flex items-center gap-3 reveal">
            <span class="eyebrow" style="margin:0;">{{ $product->tag }}</span>
            <span class="chip {{ $product->isLive() ? 'chip-live' : 'chip-accent' }}">{{ $product->isLive() ? 'Live' : 'Launching soon' }}</span>
        </div>
        <h1 class="display mt-5 reveal" style="max-width: 16ch;">{{ $product->name }}</h1>
        @if ($product->headline)
            <p class="mt-6 max-w-2xl text-lg leading-8 reveal" style="color: var(--color-body);">{{ $product->headline }}</p>
        @endif

        <div class="mt-8 flex flex-wrap items-center gap-3 reveal">
            @if ($product->isLive() && $product->external_url)
                <a href="{{ $product->external_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    Visit {{ $product->name }}
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3h7v7M13 3 4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @else
                <a href="{{ route('contact') }}" class="btn btn-primary">Get notified at launch</a>
            @endif
            @if ($work)
                <a href="{{ route('work.show', $work) }}" class="btn btn-ghost">See how we built it</a>
            @endif
        </div>
    </header>

    <div class="container-x reveal">
        <div class="surface-media" style="max-width: 940px; aspect-ratio: 16/10;">
            @if ($product->screenshot_path)
                <img src="{{ asset(ltrim($product->screenshot_path, '/')) }}" alt="{{ $product->name }} screenshot"
                     fetchpriority="high" decoding="async" width="1280" height="800"
                     style="width:100%; height:100%; object-fit: cover; object-position: top; display:block;">
            @else
                <x-mockup :for="$product->slug" :label="$product->name" />
            @endif
        </div>
    </div>

    <div class="container-x section" style="padding-top: 3rem; border-top: 1px solid var(--color-line-soft); margin-top: 3rem;">
        <div class="grid gap-12 pt-12 lg:grid-cols-[1.3fr_1fr]">
            <div class="space-y-10">
                <section class="reveal">
                    <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>What it is</h2>
                    <p class="mt-3 leading-8" style="color: var(--color-body); max-width: 60ch;">{{ $long }}</p>
                </section>

                @if (! empty($product->features))
                    <section class="reveal">
                        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>Features</h2>
                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($product->features as $f)
                                <li class="flex items-start gap-3">
                                    <span class="shrink-0" style="margin-top:1px; width:24px; height:24px; border-radius:7px; background:var(--color-brand-tint); color:var(--color-navy); display:grid; place-items:center;"><svg width="13" height="13" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8.5l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                    <span style="color: var(--color-body); line-height: 1.5;">{{ $f }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>

            @if (! empty($product->who_for))
                <aside class="reveal">
                    <div class="surface surface-hover" style="padding: 1.75rem;">
                        <h2 class="eyebrow" style="margin:0;">Who it's for</h2>
                        <ul class="mt-5 space-y-3">
                            @foreach ($product->who_for as $w)
                                <li class="flex items-start gap-3">
                                    <span class="mt-2 shrink-0" style="width:6px;height:6px;border-radius:999px;background:var(--color-accent);"></span>
                                    <span style="color: var(--color-body); line-height: 1.5;">{{ $w }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            @endif
        </div>
    </div>

    <x-cta-band />

    @push('head')
    @php
        $productLd = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $product->name,
            'applicationCategory' => $product->tag,
            'operatingSystem' => 'Web',
            'description' => $product->headline ?? $product->description,
            'url' => $product->isLive() && $product->external_url ? $product->external_url : route('products.show', $product),
            'image' => $product->screenshot_path ? asset(ltrim($product->screenshot_path, '/')) : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('fignoc.brand.name'),
                '@id' => 'https://' . ltrim(config('fignoc.brand.domain'), '/') . '/#organization',
            ],
            'sameAs' => $product->isLive() && $product->external_url ? $product->external_url : null,
        ], fn ($v) => $v !== null);
    @endphp
    <script type="application/ld+json">{!! json_encode($productLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layout>
