<x-app-layout title="Categories">
    <x-slot name="actions">
        <x-button-link :href="route('admin.categories.create')">New category</x-button-link>
    </x-slot>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-widest text-gray-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Name</th>
                    <th scope="col" class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-semibold">
                            <a href="{{ route('admin.categories.show', $category) }}" class="hover:text-harbour hover:underline">{{ $category->name }}</a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4 text-xs font-bold uppercase tracking-widest">
                                <a href="{{ route('admin.categories.show', $category) }}" class="text-gray-600 hover:text-ink">View</a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-harbour hover:text-harbour-dark">Edit</a>

                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-bold uppercase tracking-widest text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-8 text-center text-gray-500">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
