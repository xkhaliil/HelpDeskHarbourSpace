{{-- A list of tickets on the user page. Needs $tickets (with their category) and $emptyText. --}}
<ul class="mt-2 divide-y divide-gray-200">
    @forelse ($tickets as $ticket)
        <li class="flex items-center justify-between gap-3 py-3">
            <div>
                <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-semibold hover:text-harbour hover:underline">{{ $ticket->title }}</a>
                <div class="text-xs text-gray-500">{{ $ticket->category->name }} &middot; {{ $ticket->created_at->format('d M Y') }}</div>
            </div>
            <x-badge :value="$ticket->status" />
        </li>
    @empty
        <li class="py-3 text-sm text-gray-500">{{ $emptyText }}</li>
    @endforelse
</ul>
