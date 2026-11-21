<x-app-layout title="Dashboard">
    <x-slot name="actions">
        <x-button-link :href="route('tickets.create')">Report a problem</x-button-link>
    </x-slot>

    <!-- Greeting with the logged-in user -->
    <p class="mb-8 flex flex-wrap items-center gap-2 text-lg">
        Hello <span class="font-bold">{{ auth()->user()->name }}</span>, you are logged in as
        <x-badge :value="auth()->user()->role" />
    </p>

    <!-- Number of own tickets per status -->
    <div class="grid gap-4 sm:grid-cols-3">
        <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="rounded-xl border-t-4 border-sky-500 bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-harbour focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Open</p>
            <p class="mt-2 text-5xl font-black tracking-tight">{{ $openCount }}</p>
        </a>
        <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="rounded-xl border-t-4 border-amber-500 bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-harbour focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500">In progress</p>
            <p class="mt-2 text-5xl font-black tracking-tight">{{ $inProgressCount }}</p>
        </a>
        <a href="{{ route('tickets.index', ['status' => 'solved']) }}" class="rounded-xl border-t-4 border-emerald-500 bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-harbour focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Solved</p>
            <p class="mt-2 text-5xl font-black tracking-tight">{{ $solvedCount }}</p>
        </a>
    </div>

    <!-- Latest tickets -->
    <div class="mt-10 flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-black uppercase tracking-tight">Your latest tickets</h2>
        <a href="{{ route('tickets.index') }}" class="text-xs font-bold uppercase tracking-widest text-harbour hover:underline">All my tickets &rarr;</a>
    </div>

    @if ($latestTickets->isEmpty())
        <x-card class="mt-4 text-center">
            <p class="text-gray-600">You have not reported any problem yet.</p>
            <x-button-link :href="route('tickets.create')" class="mt-4">Report a problem</x-button-link>
        </x-card>
    @else
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($latestTickets as $ticket)
                <x-ticket-card :ticket="$ticket" />
            @endforeach
        </div>
    @endif
</x-app-layout>
