{{-- Public page: the list of all categories, so visitors know what they can report. --}}
<x-app-layout title="Categories">
    <p class="max-w-2xl text-lg text-gray-600">
        Every ticket belongs to one of these categories. Choose the one that fits your problem best.
    </p>

    <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($categories as $category)
            <li>
                <a href="{{ route('categories.show', $category) }}" class="block h-full rounded-xl border-l-4 border-harbour bg-white p-5 shadow-sm transition hover:shadow-md">
                    <h2 class="text-lg font-black uppercase tracking-tight">{{ $category->name }}</h2>
                    <p class="mt-2 text-sm text-gray-600">{{ $category->description }}</p>
                    <p class="mt-4 text-xs font-bold uppercase tracking-widest text-harbour">
                        {{ $category->tickets_count }} {{ Str::plural('ticket', $category->tickets_count) }} reported
                    </p>
                </a>
            </li>
        @empty
            <li class="text-gray-500">There are no categories yet.</li>
        @endforelse
    </ul>
</x-app-layout>
