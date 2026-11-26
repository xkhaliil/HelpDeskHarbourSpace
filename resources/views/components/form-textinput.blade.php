{{-- A text field with its label and its validation error. --}}
@props([
    'name',
    'label',
    'placeholder' => '',
    'value' => '',
    'id' => $name,
    'required' => false,
])

<div class="space-y-1">
    <label for="{{ $id }}" class="block text-sm font-semibold text-ink">{{ $label }}@if($required)<span class="ml-1 text-harbour" aria-hidden="true">*</span>@endif</label>
    <input type="text" id="{{ $id }}" name="{{ $name }}" class="block w-full rounded-md border px-3 py-2 shadow-xs focus:border-harbour focus:ring-harbour {{ $errors->has($name) ? 'border-red-500 bg-red-50' : 'border-gray-300' }}" placeholder="{{ $placeholder }}" value="{{ old($name, $value) }}" @required($required) @if($errors->has($name)) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
    @error($name)
        <p id="{{ $id }}-error" class="text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
