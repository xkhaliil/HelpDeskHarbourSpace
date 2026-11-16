<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Comment by {{ $comment->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Article</dt>
                            <dd class="mt-1">
                                <a href="{{ route('articles.show', $comment->article_id) }}" class="text-blue-600 hover:text-blue-800">{{ $comment->article->title }}</a>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($comment->approved_at)
                                    Approved on {{ $comment->approved_at->format('M j, Y H:i') }}
                                @else
                                    <span class="text-amber-600">Awaiting validation</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Posted</dt>
                            <dd class="mt-1">{{ $comment->created_at->format('M j, Y H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Content</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $comment->content }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex items-center gap-4 text-xs font-semibold uppercase tracking-widest">
                        <a href="{{ route('userzone.comments.edit', $comment->id) }}" class="text-gray-800 hover:text-gray-600">Edit</a>

                        <a href="{{ route('userzone.comments.index') }}" class="text-gray-600 hover:text-gray-800">Back to comments</a>

                        <form action="{{ route('userzone.comments.destroy', $comment->id) }}" method="post">
                            @method('delete')
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-500">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
