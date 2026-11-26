{{-- A link in the menu for small screens (from Laravel Breeze). --}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-white bg-white/10 py-2 pe-4 ps-3 text-start text-base font-semibold text-white transition'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-white/80 hover:border-white/40 hover:bg-white/5 hover:text-white transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
