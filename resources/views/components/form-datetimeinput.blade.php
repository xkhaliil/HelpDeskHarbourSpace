@props([
    'name',
    'label',
    'value' => '',
    'id' => $name,
    'required' => false,
])

@php
    $currentValue = old($name, $value);
    $formattedValue = is_object($currentValue) && method_exists($currentValue, 'format')
        ? $currentValue->format('Y-m-d\TH:i')
        : $currentValue;
@endphp

<div class="space-y-1">
    <label for="{{ $id }}" class="block text-xs font-semibold">{{ $label }}@if($required)<span class="ml-1" aria-hidden="true">*</span>@endif</label>
    <input type="datetime-local" id="{{ $id }}" name="{{ $name }}" class="block w-full rounded-md border shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has($name) ? 'border-red-500' : 'border-gray-400' }}" value="{{ $formattedValue }}" @required($required)>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
