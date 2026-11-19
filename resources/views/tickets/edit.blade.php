<x-app-layout title="Edit ticket">
    <x-card class="max-w-2xl sm:p-8">
        <form action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @include('tickets.partials.form', ['ticket' => $ticket])

            <div class="flex items-center gap-3">
                <x-breeze.primary-button>Save changes</x-breeze.primary-button>
                <x-button-link :href="route('tickets.show', $ticket)" variant="secondary">Cancel</x-button-link>
            </div>
        </form>
    </x-card>
</x-app-layout>
