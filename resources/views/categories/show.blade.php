{{-- Public page: one category, with the number of tickets per status (the tickets themselves are private). --}}
<x-app-layout :title="$category->name">
    <x-slot name="actions">
        <x-button-link :href="route('categories.index')" variant="secondary">All categories</x-button-link>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h2 class="text-sm font-black uppercase tracking-widest text-gray-500">What belongs here</h2>
            <p class="mt-3 text-lg">{{ $category->description ?? 'No description yet.' }}</p>

            {{-- Logged-in users can report a problem right away, visitors have to log in first --}}
            <div class="mt-6">
                @auth
                    <x-button-link :href="route('tickets.create')">Report a problem</x-button-link>
                @else
                    <x-button-link :href="route('login')">Log in to report a problem</x-button-link>
                @endauth
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-black uppercase tracking-widest text-gray-500">Tickets in this category</h2>

            <ul class="mt-4 space-y-3">
                @foreach ($statuses as $status)
                    <li class="flex items-center justify-between">
                        <x-badge :value="$status" />
                        <span class="text-xl font-black">{{ $countsPerStatus[$status->value] }}</span>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
</x-app-layout>
