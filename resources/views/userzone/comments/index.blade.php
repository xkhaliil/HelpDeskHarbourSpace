<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Comments
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Moderate comments</h3>

                    <div class="flex items-center gap-4 text-xs font-semibold uppercase tracking-widest">
                        <a href="{{ route('userzone.comments.index') }}" class="text-gray-600 hover:text-gray-800 @if(! request()->boolean('unmoderated')) underline @endif">All</a>
                        <a href="{{ route('userzone.comments.index', ['unmoderated' => 1]) }}" class="text-gray-600 hover:text-gray-800 @if(request()->boolean('unmoderated')) underline @endif">Unmoderated</a>
                    </div>
                </div>

                <table class="w-full text-left text-sm text-gray-900">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Article</th>
                            <th scope="col" class="px-6 py-3 font-medium">Author</th>
                            <th scope="col" class="px-6 py-3 font-medium">Comment</th>
                            <th scope="col" class="px-6 py-3 font-medium">Status</th>
                            <th scope="col" class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($comments as $comment)
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('articles.show', $comment->article_id) }}" class="text-blue-600 hover:text-blue-800">{{ $comment->article->title }}</a>
                                </td>
                                <td class="px-6 py-4">{{ $comment->user->name }}</td>
                                <td class="px-6 py-4 max-w-md">{{ $comment->content }}</td>
                                <td class="px-6 py-4">
                                    @if($comment->approved_at)
                                        Approved
                                    @else
                                        <span class="text-amber-600">Awaiting validation</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4 text-xs font-semibold uppercase tracking-widest">
                                        <a href="{{ route('userzone.comments.show', $comment->id) }}" class="text-gray-600 hover:text-gray-800">View</a>

                                        <a href="{{ route('userzone.comments.edit', $comment->id) }}" class="text-gray-800 hover:text-gray-600">Edit</a>

                                        <form action="{{ route('userzone.comments.destroy', $comment->id) }}" method="post">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-500">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No comments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
