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
    <label for="{{ $id }}" class="block text-xs font-semibold">{{ $label }}@if($required)<span class="ml-1" aria-hidden="true">*</span>@endif</label>
    <select id="{{ $id }}" name="{{ $name }}" class="block w-full rounded-md border shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has($name) ? 'border-red-500' : 'border-gray-400' }}" @required($required)>
        @if($placeholder !== '')
            <option value="" disabled @selected(old($name, $value) === '' || old($name, $value) === null)>{{ $placeholder }}</option>
        @endif
        @foreach($options as $option)
            <option value="{{ $option['key'] }}" @selected((string) old($name, $value) === (string) $option['key'])>{{ $option['value'] }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
