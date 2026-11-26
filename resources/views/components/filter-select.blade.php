{{-- A small dropdown for the filters above a ticket list. --}}
@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
])

{{-- A dropdown for a filter form. The first option ("All") means: do not filter. --}}
<div class="space-y-1">
    <label for="{{ $name }}" class="block text-xs font-bold uppercase tracking-widest text-gray-500">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" class="block w-full rounded-md border px-3 py-2 text-sm shadow-xs focus:border-harbour focus:ring-harbour {{ $errors->has($name) ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
        <option value="">All</option>
        @foreach ($options as $option)
            <option value="{{ $option['key'] }}" @selected((string) $selected === (string) $option['key'])>{{ $option['value'] }}</option>
        @endforeach
    </select>
</div>
