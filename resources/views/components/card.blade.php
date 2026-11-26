{{-- A white box with a border, used around most content blocks. --}}
<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white p-6 shadow-sm']) }}>
    {{ $slot }}
</div>
