@props([
    'name',
    'label',
    'placeholder' => '',
    'value' => '',
    'id' => $name,
    'required' => false,
    'rows' => 4,
])

<div class="space-y-1">
    <label for="{{ $id }}" class="block text-xs font-semibold">{{ $label }}@if($required)<span class="ml-1" aria-hidden="true">*</span>@endif</label>
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" class="block w-full rounded-md border shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has($name) ? 'border-red-500' : 'border-gray-400' }}" placeholder="{{ $placeholder }}" @required($required)>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
