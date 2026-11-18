<x-app-layout :title="$ticket->title">
    <x-slot name="actions">
        @can('update', $ticket)
            <x-button-link :href="route('tickets.edit', $ticket)" variant="secondary">Edit</x-button-link>
        @endcan

        @can('delete', $ticket)
            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Delete this ticket?')">
                @csrf
                @method('DELETE')
                <x-breeze.danger-button>Delete</x-breeze.danger-button>
            </form>
        @endcan
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-500">Description</h2>
                <p class="mt-3 whitespace-pre-line">{{ $ticket->description }}</p>
            </x-card>
        </div>

        <aside>
            <x-card>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Status</dt>
                        <dd class="mt-1"><x-badge :value="$ticket->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Priority</dt>
                        <dd class="mt-1"><x-badge :value="$ticket->priority" /></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Category</dt>
                        <dd class="mt-1">{{ $ticket->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Location</dt>
                        <dd class="mt-1">{{ $ticket->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Reported</dt>
                        <dd class="mt-1">{{ $ticket->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Last update</dt>
                        <dd class="mt-1">{{ $ticket->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>

                @cannot('update', $ticket)
                    <p class="mt-6 rounded-md bg-gray-50 p-3 text-xs text-gray-600">
                        A ticket can only be edited or deleted while its status is open.
                    </p>
                @endcannot
            </x-card>
        </aside>
    </div>
</x-app-layout>
