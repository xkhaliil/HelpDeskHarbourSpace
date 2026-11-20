<x-app-layout title="All tickets">
    <!-- Search and filters -->
    <form action="{{ route('admin.tickets.index') }}" method="GET" class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="space-y-1 lg:col-span-2">
                <label for="search" class="block text-xs font-bold uppercase tracking-widest text-gray-500">Search by title</label>
                <input type="search" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="For example: projector"
                       class="block w-full rounded-md border px-3 py-2 text-sm shadow-xs focus:border-harbour focus:ring-harbour {{ $errors->has('search') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
            </div>

            <x-filter-select name="status" label="Status" :selected="$filters['status'] ?? null"
                             :options="collect($statuses)->map(fn ($status) => ['key' => $status->value, 'value' => $status->label()])" />

            <x-filter-select name="category_id" label="Category" :selected="$filters['category_id'] ?? null"
                             :options="$categories->map(fn ($category) => ['key' => $category->id, 'value' => $category->name])" />

            <x-filter-select name="priority" label="Priority" :selected="$filters['priority'] ?? null"
                             :options="collect($priorities)->map(fn ($priority) => ['key' => $priority->value, 'value' => $priority->label()])" />

            <x-filter-select name="role" label="Created by" :selected="$filters['role'] ?? null"
                             :options="collect($roles)->map(fn ($role) => ['key' => $role->value, 'value' => $role->label()])" />
        </div>

        @if ($errors->any())
            <ul role="alert" class="mt-4 space-y-1 text-sm font-medium text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-4 flex items-center gap-3">
            <x-breeze.primary-button>Filter</x-breeze.primary-button>
            <a href="{{ route('admin.tickets.index') }}" class="text-xs font-bold uppercase tracking-widest text-harbour hover:underline">Reset</a>
        </div>
    </form>

    <p class="mb-3 text-sm text-gray-600">{{ $tickets->total() }} {{ Str::plural('ticket', $tickets->total()) }} found</p>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-widest text-gray-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Title</th>
                    <th scope="col" class="px-4 py-3">Created by</th>
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
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="hover:text-harbour hover:underline">{{ $ticket->title }}</a>
                            <div class="text-xs font-normal text-gray-500">{{ $ticket->location }}</div>
                        </td>
                        <td class="px-4 py-3">
                            {{ $ticket->user->name }}
                            <div class="mt-1"><x-badge :value="$ticket->user->role" /></div>
                        </td>
                        <td class="px-4 py-3">{{ $ticket->category->name }}</td>
                        <td class="px-4 py-3"><x-badge :value="$ticket->status" /></td>
                        <td class="px-4 py-3"><x-badge :value="$ticket->priority" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $ticket->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">No tickets match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</x-app-layout>
