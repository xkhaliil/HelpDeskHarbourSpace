@props(['value'])

{{-- Coloured badge for a Role, TicketStatus or TicketPriority value. The colours live in the enum. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide '.$value->badgeClasses()]) }}>
    {{ $value->label() }}
</span>
