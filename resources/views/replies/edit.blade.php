{{-- The form to change your own reply. Only the author can open it (see App\Policies\ReplyPolicy). --}}
<x-app-layout title="Edit reply">
    <x-card class="max-w-2xl sm:p-8">
        <p class="mb-6 text-sm text-gray-600">
            On the ticket <span class="font-bold">{{ $reply->ticket->title }}</span>
        </p>

        <form action="{{ route('replies.update', $reply) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <x-form-textarea name="message" label="Your reply" rows="5" :value="$reply->message" required />

            <div class="flex items-center gap-3">
                <x-breeze.primary-button>Save changes</x-breeze.primary-button>
                <x-button-link :href="route('tickets.show', $reply->ticket_id)" variant="secondary">Cancel</x-button-link>
            </div>
        </form>
    </x-card>
</x-app-layout>
