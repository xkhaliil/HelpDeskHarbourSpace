{{-- The admin changes the name, the email address and the role of a user. --}}
<x-app-layout title="Edit user">
    <x-card class="max-w-xl">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <x-form-textinput name="name" label="Name" :value="$user->name" required />

            <x-form-emailinput name="email" label="Email" :value="$user->email" required />

            <x-form-select name="role" label="Role" :value="$user->role->value" required
                           :options="collect($roles)->map(fn ($role) => ['key' => $role->value, 'value' => $role->label()])" />

            @if ($user->is(auth()->user()))
                <p class="text-sm text-gray-500">This is your own account: you cannot change your own role.</p>
            @endif

            <div class="flex items-center gap-3">
                <x-breeze.primary-button>Save changes</x-breeze.primary-button>
                <x-button-link :href="route('admin.users.show', $user)" variant="secondary">Cancel</x-button-link>
            </div>
        </form>
    </x-card>
</x-app-layout>
