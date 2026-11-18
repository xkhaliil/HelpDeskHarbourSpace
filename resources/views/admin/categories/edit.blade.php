<x-app-layout title="Edit category">
    <x-card class="max-w-xl">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <x-form-textinput name="name" label="Name" :value="$category->name" required />

            <div class="flex items-center gap-3">
                <x-breeze.primary-button>Save changes</x-breeze.primary-button>
                <x-button-link :href="route('admin.categories.index')" variant="secondary">Cancel</x-button-link>
            </div>
        </form>
    </x-card>
</x-app-layout>
