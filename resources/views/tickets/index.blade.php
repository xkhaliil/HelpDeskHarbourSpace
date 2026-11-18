<x-app-layout title="My tickets">
    <x-slot name="actions">
        <x-button-link :href="route('tickets.create')">New ticket</x-button-link>
    </x-slot>

    <!-- Filter by status -->
    <nav class="mb-6 flex flex-wrap gap-2" aria-label="Filter by status">
        <a href="{{ route('tickets.index') }}"
           @class([
               'rounded-full px-4 py-2 text-xs font-bold uppercase tracking-widest transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour',
               'bg-harbour text-white' => $currentStatus === null,
               'border border-gray-300 bg-white text-ink hover:border-harbour hover:text-harbour' => $currentStatus !== null,
           ])>
            All
        </a>

        @foreach ($statuses as $status)
            <a href="{{ route('tickets.index', ['status' => $status->value]) }}"
               @class([
                   'rounded-full px-4 py-2 text-xs font-bold uppercase tracking-widest transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour',
                   'bg-harbour text-white' => $currentStatus === $status->value,
                   'border border-gray-300 bg-white text-ink hover:border-harbour hover:text-harbour' => $currentStatus !== $status->value,
               ])>
                {{ $status->label() }}
            </a>
        @endforeach
    </nav>

    @error('status')
        <p role="alert" class="mb-4 text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-widest text-gray-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Title</th>
                    <th scope="col" class="px-4 py-3">Category</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3">Priority</th>
                    <th scope="col" class="px-4 py-3">Reported</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="px-4 py-3 font-semibold">
                            <a href="{{ route('tickets.show', $ticket) }}" class="hover:text-harbour hover:underline">{{ $ticket->title }}</a>
                            <div class="text-xs font-normal text-gray-500">{{ $ticket->location }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $ticket->category->name }}</td>
                        <td class="px-4 py-3"><x-badge :value="$ticket->status" /></td>
                        <td class="px-4 py-3"><x-badge :value="$ticket->priority" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $ticket->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                            No tickets here yet.
                            <a href="{{ route('tickets.create') }}" class="font-semibold text-harbour hover:underline">Report a problem</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</x-app-layout>
