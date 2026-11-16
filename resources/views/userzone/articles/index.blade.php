<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Articles (admin)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">All articles</h3>

                    <a href="{{ route('userzone.articles.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
                        New article
                    </a>
                </div>

                <table class="w-full text-left text-sm text-gray-900">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Title</th>
                            <th scope="col" class="px-6 py-3 font-medium">Author</th>
                            <th scope="col" class="px-6 py-3 font-medium">Publish date</th>
                            <th scope="col" class="px-6 py-3 font-medium">Categories</th>
                            <th scope="col" class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($articles as $article)
                            <tr>
                                <td class="px-6 py-4">{{ $article->title }}</td>
                                <td class="px-6 py-4">{{ $article->author->name }}</td>
                                <td class="px-6 py-4">{{ $article->publish_date->format('M j, Y') }}</td>
                                <td class="px-6 py-4">
                                    {{ $article->categories->pluck('name')->join(', ') ?: 'None' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4 text-xs font-semibold uppercase tracking-widest">
                                        <a href="{{ route('articles.show', $article->id) }}" class="text-gray-600 hover:text-gray-800">View</a>

                                        <a href="{{ route('userzone.articles.edit', $article->id) }}" class="text-gray-800 hover:text-gray-600">Edit</a>

                                        <form action="{{ route('userzone.articles.destroy', $article->id) }}" method="post">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-500">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No articles yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
