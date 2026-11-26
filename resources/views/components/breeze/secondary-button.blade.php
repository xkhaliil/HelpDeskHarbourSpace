{{-- A white button for less important actions, like cancel (from Laravel Breeze). --}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full border border-harbour bg-white px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-harbour transition hover:bg-harbour-light focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour disabled:opacity-50']) }}>
    {{ $slot }}
</button>
