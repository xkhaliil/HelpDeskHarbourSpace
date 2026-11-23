<x-app-layout title="Admin dashboard">
    <x-slot name="actions">
        <x-button-link :href="route('admin.tickets.index')">All tickets</x-button-link>
    </x-slot>

    <p class="mb-8 text-lg">
        Welcome back, <span class="font-bold">{{ auth()->user()->name }}</span>.
        Here is what is happening on the HelpDesk.
    </p>

    @if ($pendingUsersCount > 0)
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-amber-300 bg-amber-50 p-4">
            <p class="text-sm font-medium text-amber-900">
                {{ $pendingUsersCount }} {{ Str::plural('account', $pendingUsersCount) }} waiting for your approval.
            </p>
            <x-button-link :href="route('admin.users.index')">Review accounts</x-button-link>
        </div>
    @endif

    <!-- Number of tickets per status -->
    <h2 class="mb-4 text-xl font-black uppercase tracking-tight">Tickets per status</h2>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($statuses as $status)
            <a href="{{ route('admin.tickets.index', ['status' => $status->value]) }}"
               class="group rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-harbour focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour">
                <x-badge :value="$status" />
                <p class="mt-4 text-5xl font-black tracking-tight">{{ $countsPerStatus[$status->value] }}</p>
                <p class="mt-2 text-xs font-bold uppercase tracking-widest text-gray-500 group-hover:text-harbour">View tickets &rarr;</p>
            </a>
        @endforeach
    </div>

    <!-- Latest important tickets -->
    <div class="mt-10 grid gap-6 lg:grid-cols-2">
        <x-card>
            <h2 class="text-xl font-black uppercase tracking-tight">Latest <span class="text-red-600">urgent</span> tickets</h2>
            <div class="mt-2">
                @include('admin.partials.ticket-list', ['tickets' => $urgentTickets, 'emptyText' => 'No urgent tickets.'])
            </div>
        </x-card>

        <x-card>
            <h2 class="text-xl font-black uppercase tracking-tight">Latest <span class="text-orange-600">high priority</span> tickets</h2>
            <div class="mt-2">
                @include('admin.partials.ticket-list', ['tickets' => $highTickets, 'emptyText' => 'No high priority tickets.'])
            </div>
        </x-card>
    </div>
</x-app-layout>
