<x-app-layout :title="$category->name">
    <x-slot name="actions">
        <x-button-link :href="route('admin.categories.edit', $category)">Edit</x-button-link>
        <x-button-link :href="route('admin.categories.index')" variant="secondary">All categories</x-button-link>
    </x-slot>

    <x-card>
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Created</dt>
                <dd class="mt-1">{{ $category->created_at->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-widest text-gray-500">Last updated</dt>
                <dd class="mt-1">{{ $category->updated_at->format('d M Y') }}</dd>
            </div>
        </dl>
    </x-card>
</x-app-layout>
