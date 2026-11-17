<x-app-layout title="Dashboard">
    <x-card>
        <p class="flex items-center gap-2">
            Hello {{ auth()->user()->name }}, you're logged in as
            <x-badge :value="auth()->user()->role" />
        </p>
    </x-card>
</x-app-layout>
