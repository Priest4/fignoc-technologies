{{-- Work case study (brief §7.5). CreativeWork JSON-LD + cross-link to product. --}}
@php
    $d = $work->detail ?? [];
    $origin = 'https://' . ltrim(config('fignoc.brand.domain'), '/');
@endphp
<x-layout
    {{-- "{Name} case study", not just "{Name}": /products/{slug} covers the
         same platform, and identical titles had the two competing. --}}
    :title="$work->name . ' case study'"
    :description="\Illuminate\Support\Str::limit(trim(($work->summary ? $work->summary . ' ' : '') . $work->description), 152)"
    ogType="article"
    :canonical="route('work.show', $work)"
    :og-image="$work->image_path">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Work', 'url' => route('work')],
        ['label' => $work->name, 'url' => route('work.show', $work)],
    ]" />

    <header class="section container-x">
        <div class="flex items-center gap-3 reveal">
            <span class="eyebrow" style="margin:0;">{{ $work->type }}</span>
            <span class="chip {{ $work->isLive() ? 'chip-live' : 'chip-accent' }}">{{ $work->isLive() ? 'Live' : 'Launching soon' }}</span>
        </div>
        <h1 class="display mt-5 reveal" style="max-width: 18ch;">{{ $work->name }}</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 reveal" style="color: var(--color-body);">{{ $work->summary ?? $work->description }}</p>

        <div class="mt-8 flex flex-wrap items-center gap-3 reveal">
            @if ($work->isLive() && $work->project_url)
                <a href="{{ $work->project_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    Visit {{ $work->name }}
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3h7v7M13 3 4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @else
                <span class="chip chip-accent">Launching soon</span>
            @endif
            @if ($product)
                <a href="{{ route('products.show', $product) }}" class="btn btn-ghost">See the product</a>
            @endif
        </div>
    </header>

    <div class="container-x reveal">
        <div class="surface-media" style="max-width: 940px; aspect-ratio: 16/10;">
            @if ($work->image_path)
                <img src="{{ asset(ltrim($work->image_path, '/')) }}" alt="{{ $work->name }} screenshot"
                     fetchpriority="high" decoding="async" width="1280" height="800"
                     style="width:100%; height:100%; object-fit: cover; object-position: top; display:block;">
            @else
                <x-mockup :for="$work->slug" :label="$work->name" />
            @endif
        </div>
    </div>

    <div class="container-x section">
        <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr]">
            <div class="space-y-10">
                @if (! empty($d['challenge']))
                    <section class="reveal">
                        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>The challenge</h2>
                        <p class="mt-3 leading-8" style="color: var(--color-body); max-width: 60ch;">{{ $d['challenge'] }}</p>
                    </section>
                @endif

                @if (! empty($d['built']))
                    <section class="reveal">
                        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>What we built</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach ($d['built'] as $b)
                                <li class="flex items-start gap-3">
                                    <span class="shrink-0" style="margin-top:1px; width:24px; height:24px; border-radius:7px; background:var(--color-brand-tint); color:var(--color-navy); display:grid; place-items:center;"><svg width="13" height="13" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8.5l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                    <span style="color: var(--color-body); line-height: 1.5;">{{ $b }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if (! empty($d['outcome']))
                    <section class="reveal">
                        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 0.65rem;"><span style="width: 22px; height: 3px; border-radius: 2px; background: var(--color-accent); flex: none;"></span>Outcome</h2>
                        <p class="mt-3 leading-8" style="color: var(--color-body); max-width: 60ch;">{{ $d['outcome'] }}</p>
                    </section>
                @endif
            </div>

            <aside class="reveal">
                <div class="surface surface-hover" style="padding: 1.75rem;">
                    <h2 class="eyebrow" style="margin:0;">Tech stack</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach (($work->technologies ?? []) as $tech)
                            <span class="chip">{{ $tech }}</span>
                        @endforeach
                    </div>
                    <p class="mt-6 text-sm" style="color: var(--color-muted);">Status</p>
                    <p class="mt-1 font-semibold" style="color: var(--color-heading);">{{ $work->isLive() ? 'Live' : 'In development' }}</p>
                </div>
            </aside>
        </div>
    </div>

    <x-cta-band />

    @push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $work->name,
        'about' => $work->type,
        'creator' => ['@type' => 'Organization', 'name' => config('fignoc.brand.name'), '@id' => $origin . '/#organization'],
        'url' => route('work.show', $work),
    ] + ($work->isLive() && $work->project_url ? ['sameAs' => $work->project_url] : []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layout>
