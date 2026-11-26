{{-- One user: the account details, the tickets they reported and (for admins) the tickets they handle. --}}
<x-app-layout :title="$user->name">
    <x-slot name="actions">
        <x-button-link :href="route('admin.users.edit', $user)">Edit</x-button-link>
        <x-button-link :href="route('admin.users.index')" variant="secondary">All users</x-button-link>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <aside>
            <x-card>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Email</dt>
                        <dd class="mt-1">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Role</dt>
                        <dd class="mt-1"><x-badge :value="$user->role" /></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Account</dt>
                        <dd class="mt-1">{{ $user->isApproved() ? 'Approved' : 'Waiting for approval' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Registered</dt>
                        <dd class="mt-1">{{ $user->created_at->format('d M Y') }}</dd>
                    </div>
                </dl>
            </x-card>
        </aside>

        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="text-xl font-black uppercase tracking-tight">Reported tickets ({{ $tickets->count() }})</h2>
                @include('admin.users.partials.tickets', ['tickets' => $tickets, 'emptyText' => 'No tickets reported.'])
            </x-card>

            @if ($user->isAdmin())
                <x-card>
                    <h2 class="text-xl font-black uppercase tracking-tight">Handles ({{ $assignedTickets->count() }})</h2>
                    @include('admin.users.partials.tickets', ['tickets' => $assignedTickets, 'emptyText' => 'No tickets assigned.'])
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
