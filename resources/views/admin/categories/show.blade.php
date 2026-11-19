<x-app-layout :title="$category->name">
    <x-slot name="actions">
        <x-button-link :href="route('admin.categories.edit', $category)">Edit</x-button-link>
        <x-button-link :href="route('admin.categories.index')" variant="secondary">All categories</x-button-link>
    </x-slot>

    <h2 class="mb-4 text-xl font-black uppercase tracking-tight">Tickets in this category ({{ $tickets->total() }})</h2>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-widest text-gray-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Title</th>
                    <th scope="col" class="px-4 py-3">Reported by</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3">Priority</th>
                    <th scope="col" class="px-4 py-3">Reported</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $ticket->title }}</td>
                        <td class="px-4 py-3">{{ $ticket->user->name }}</td>
                        <td class="px-4 py-3"><x-badge :value="$ticket->status" /></td>
                        <td class="px-4 py-3"><x-badge :value="$ticket->priority" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $ticket->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No tickets in this category yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</x-app-layout>
