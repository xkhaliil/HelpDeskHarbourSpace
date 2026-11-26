{{-- The admin makes a new account. It is approved right away (see Admin\UserController@store). --}}
<x-app-layout title="New user">
    <x-card class="max-w-xl">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <x-form-textinput name="name" label="Name" placeholder="For example: Marta Vidal" required />

            <x-form-emailinput name="email" label="Email" placeholder="name@teacher.harbour.space" required />

            <x-form-select name="role" label="Role" value="student" required
                           :options="collect($roles)->map(fn ($role) => ['key' => $role->value, 'value' => $role->label()])" />

            <x-form-passwordinput name="password" label="Password" required />

            <x-form-passwordinput name="password_confirmation" label="Confirm password" required />

            <div class="flex items-center gap-3">
                <x-breeze.primary-button>Create account</x-breeze.primary-button>
                <x-button-link :href="route('admin.users.index')" variant="secondary">Cancel</x-button-link>
            </div>
        </form>
    </x-card>
</x-app-layout>
