@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-md bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800']) }}>
        {{ $status }}
    </div>
@endif
