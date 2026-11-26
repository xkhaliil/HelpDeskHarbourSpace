{{-- The form to report a new problem (the fields are in tickets/partials/form). --}}
<x-app-layout title="New ticket">
    <x-card class="max-w-2xl sm:p-8">
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @include('tickets.partials.form', ['ticket' => null])

            <div class="flex items-center gap-3">
                <x-breeze.primary-button>Send ticket</x-breeze.primary-button>
                <x-button-link :href="route('tickets.index')" variant="secondary">Cancel</x-button-link>
            </div>
        </form>
    </x-card>
</x-app-layout>
