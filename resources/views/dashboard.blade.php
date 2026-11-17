<x-app-layout title="Dashboard">
    <x-card>
        <p>Hello {{ auth()->user()->name }}, you're logged in!</p>
    </x-card>
</x-app-layout>
