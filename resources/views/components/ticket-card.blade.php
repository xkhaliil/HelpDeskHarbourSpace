@props(['ticket'])

<a href="{{ route('tickets.show', $ticket) }}"
   class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-harbour hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour">
    <div class="flex flex-wrap gap-2">
        <x-badge :value="$ticket->status" />
        <x-badge :value="$ticket->priority" />
    </div>

    <h3 class="mt-3 font-bold">{{ $ticket->title }}</h3>
    <p class="mt-1 text-sm text-gray-600">{{ $ticket->category->name }} &middot; {{ $ticket->location }}</p>

    <p class="mt-auto pt-4 text-xs text-gray-500">Reported {{ $ticket->created_at->diffForHumans() }}</p>
</a>
