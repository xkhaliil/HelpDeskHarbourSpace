{{-- A short list of tickets for the admin dashboard. Needs $tickets and $emptyText. --}}
<ul class="divide-y divide-gray-200">
    @forelse ($tickets as $ticket)
        <li class="py-3">
            <div class="flex items-start justify-between gap-3">
                <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-semibold hover:text-harbour hover:underline">{{ $ticket->title }}</a>
                <x-badge :value="$ticket->status" />
            </div>
            <p class="mt-1 text-xs text-gray-500">
                {{ $ticket->user->name }} &middot; {{ $ticket->category->name }} &middot; {{ $ticket->created_at->diffForHumans() }}
            </p>
        </li>
    @empty
        <li class="py-3 text-sm text-gray-500">{{ $emptyText }}</li>
    @endforelse
</ul>
