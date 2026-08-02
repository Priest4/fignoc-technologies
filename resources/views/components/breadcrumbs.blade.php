{{--
  Breadcrumbs (brief §6) — visible trail + BreadcrumbList JSON-LD on inner pages.
  Props: items = [ ['label' => 'Services', 'url' => route('services')], ... ]
  The last item is treated as the current page (no link).
--}}
@props(['items' => []])

@if (! empty($items))
<nav aria-label="Breadcrumb" class="container-x breadcrumbs">
    <ol class="flex flex-wrap items-center gap-2 text-sm" style="color: var(--color-muted);">
        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                @if (! $loop->last && ! empty($item['url']))
                    <a href="{{ $item['url'] }}" class="link-accent">{{ $item['label'] }}</a>
                    <span aria-hidden="true">/</span>
                @else
                    <span aria-current="page" style="color: var(--color-heading); font-weight: 500;">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($items)->values()->map(fn ($item, $i) => array_filter([
        '@type' => 'ListItem',
        'position' => $i + 1,
        'name' => $item['label'],
        'item' => $item['url'] ?? null,
    ]))->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@endif
