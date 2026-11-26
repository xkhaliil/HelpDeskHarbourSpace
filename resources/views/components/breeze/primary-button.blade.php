{{-- The main (blue) submit button of a form (from Laravel Breeze). --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full bg-harbour px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-harbour-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour disabled:opacity-50']) }}>
    {{ $slot }}
</button>
