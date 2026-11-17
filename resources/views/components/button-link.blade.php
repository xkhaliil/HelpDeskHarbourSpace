@props(['variant' => 'primary'])

@php
$variantClasses = match ($variant) {
    'secondary' => 'border border-harbour bg-white text-harbour hover:bg-harbour-light',
    'light' => 'bg-white text-harbour hover:bg-harbour-light',
    default => 'bg-harbour text-white hover:bg-harbour-dark',
};
@endphp

<a {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5 text-xs font-bold uppercase tracking-widest transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour '.$variantClasses]) }}>
    {{ $slot }}
</a>
