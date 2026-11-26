{{-- A link in the header menu, underlined when it is the current page (from Laravel Breeze). --}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-4 border-white px-1 pt-1 text-xs font-bold uppercase tracking-widest text-white focus-visible:outline-2 focus-visible:outline-white transition'
            : 'inline-flex items-center border-b-4 border-transparent px-1 pt-1 text-xs font-bold uppercase tracking-widest text-white/70 hover:border-white/40 hover:text-white focus-visible:outline-2 focus-visible:outline-white transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
