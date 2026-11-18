<x-app-layout title="New category">
    <x-card class="max-w-xl">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <x-form-textinput name="name" label="Name" placeholder="For example: Wi-Fi & Network" required />

            <div class="flex items-center gap-3">
                <x-breeze.primary-button>Create category</x-breeze.primary-button>
                <x-button-link :href="route('admin.categories.index')" variant="secondary">Cancel</x-button-link>
            </div>
        </form>
    </x-card>
</x-app-layout>
