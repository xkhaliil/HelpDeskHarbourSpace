<x-app-layout :title="$ticket->title">
    <x-slot name="actions">
        <x-button-link :href="route('admin.tickets.index')" variant="secondary">All tickets</x-button-link>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-500">Description</h2>
                <p class="mt-3 whitespace-pre-line">{{ $ticket->description }}</p>
            </x-card>

            @if ($ticket->screenshotUrl())
                <x-card>
                    <h2 class="text-sm font-black uppercase tracking-widest text-gray-500">Screenshot</h2>
                    <a href="{{ $ticket->screenshotUrl() }}" target="_blank" class="mt-3 block">
                        <img src="{{ $ticket->screenshotUrl() }}" alt="Screenshot of the problem" class="max-h-96 rounded-md border border-gray-200">
                    </a>
                </x-card>
            @endif

            @include('tickets.partials.conversation')
        </div>

        <aside class="space-y-6">
            <!-- Handle the ticket -->
            <x-card class="border-harbour/30">
                <h2 class="text-sm font-black uppercase tracking-widest text-harbour">Handle ticket</h2>

                <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <x-form-select name="status" label="Status" :value="$ticket->status->value" required
                                   :options="collect($statuses)->map(fn ($status) => ['key' => $status->value, 'value' => $status->label()])" />

                    <x-form-select name="priority" label="Priority" :value="$ticket->priority->value" required
                                   :options="collect($priorities)->map(fn ($priority) => ['key' => $priority->value, 'value' => $priority->label()])" />

                    {{-- One checkbox per admin. The checked ids are sent as the array "assignees[]". --}}
                    <fieldset class="space-y-2">
                        <legend class="block text-sm font-semibold text-ink">Handled by</legend>

                        @foreach ($admins as $admin)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="assignees[]" value="{{ $admin->id }}"
                                       class="rounded border-gray-300 text-harbour focus:ring-harbour"
                                       @checked(in_array($admin->id, old('assignees', $ticket->assignees->pluck('id')->all())))>
                                {{ $admin->name }}
                            </label>
                        @endforeach

                        @error('assignees.*')
                            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <x-breeze.primary-button>Save</x-breeze.primary-button>
                </form>
            </x-card>

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
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Created by</dt>
                        <dd class="mt-1">
                            {{ $ticket->user->name }} <x-badge :value="$ticket->user->role" />
                            <div class="text-gray-600">{{ $ticket->user->email }}</div>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Handled by</dt>
                        <dd class="mt-1">{{ $ticket->assignees->pluck('name')->join(', ') ?: 'Nobody yet' }}</dd>
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
            </x-card>
        </aside>
    </div>
</x-app-layout>
