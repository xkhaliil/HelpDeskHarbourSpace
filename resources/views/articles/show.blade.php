<x-site-layout title="{{ $article->title }}">

<a href="{{ route('articles.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">&larr; Back to articles</a>

<article class="mt-6 max-w-3xl">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Article</p>

    <h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">{{ $article->title }}</h1>

    <div>
        @foreach($article->categories as $category)
            <span class="bg-gray-200 px-4 py-0.5 rounded-full">{{$category->name}}</span>
        @endforeach
    </div>

    <p class="mt-4 text-sm text-slate-500">
        By <span class="font-medium text-blue-700">{{ $article->author->name }}</span>
        &middot;
        {{ \Illuminate\Support\Carbon::parse($article->publish_date)->format('M j, Y') }}
    </p>

    <img src="{{$article->getImagePath() }}" />

    <div class="mt-8 whitespace-pre-line border-t border-blue-100 pt-8 text-[15px] leading-7 text-slate-700">
        {{ $article->content }}
    </div>
</article>

<section class="mt-12 max-w-3xl">
    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">
        Comments ({{ $comments->count() }})
    </h2>

    <x-article-comment-form :article="$article" />

    <div class="mt-4 divide-y divide-blue-100 border-t border-blue-100">
        @forelse($comments as $comment)
            <div class="py-4">
                <p class="text-xs text-slate-400">{{ $comment->created_at->format('M j, Y') }} - {{ $comment->user->name }}</p>

                @if($comment->approved_at === null)
                    @if(auth()->id() === $comment->user_id)
                        <p class="mt-1 text-sm text-amber-700">This comment is awaiting validation. You're the only one to see it.</p>
                    @else
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-amber-600">Awaiting validation</p>
                    @endif
                @endif

                <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $comment->content }}</p>
            </div>
        @empty
            <p class="py-4 text-sm text-slate-500">No comments yet.</p>
        @endforelse
    </div>
</section>

</x-site-layout>
