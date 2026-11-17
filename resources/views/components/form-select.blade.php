@props([
    'name',
    'label',
    'options' => [],
    'placeholder' => '',
    'value' => '',
    'id' => $name,
    'required' => false,
])

<div class="space-y-1">
    <label for="{{ $id }}" class="block text-sm font-semibold text-ink">{{ $label }}@if($required)<span class="ml-1 text-harbour" aria-hidden="true">*</span>@endif</label>
    <select id="{{ $id }}" name="{{ $name }}" class="block w-full rounded-md border px-3 py-2 shadow-xs focus:border-harbour focus:ring-harbour {{ $errors->has($name) ? 'border-red-500 bg-red-50' : 'border-gray-300' }}" @required($required) @if($errors->has($name)) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
        @if($placeholder !== '')
            <option value="" disabled @selected(old($name, $value) === '' || old($name, $value) === null)>{{ $placeholder }}</option>
        @endif
        @foreach($options as $option)
            <option value="{{ $option['key'] }}" @selected((string) old($name, $value) === (string) $option['key'])>{{ $option['value'] }}</option>
        @endforeach
    </select>
    @error($name)
        <p id="{{ $id }}-error" class="text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
