{{-- A text field used on the profile and auth pages (from Laravel Breeze). --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border border-gray-300 px-3 py-2 shadow-xs focus:border-harbour focus:ring-harbour']) }}>
