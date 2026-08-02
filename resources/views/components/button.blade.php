@props(['href' => null, 'variant' => 'primary'])

@php
    $variantClass = match ($variant) {
        'ghost'   => 'btn-ghost',
        'on-dark' => 'btn-on-dark',
        default   => 'btn-primary',
    };
@endphp

@if ($href)
    <a {{ $attributes->merge(['href' => $href, 'class' => "btn {$variantClass}"]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => "btn {$variantClass}"]) }}>{{ $slot }}</button>
@endif
