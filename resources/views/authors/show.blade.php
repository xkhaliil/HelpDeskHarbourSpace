<x-site-layout title="{{ $author->name }}">

<a href="{{ route('authors.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">&larr; Back to authors</a>

<section class="mt-6 max-w-3xl">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Author</p>

    <h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">{{ $author->name }}</h1>

    <p class="mt-4 text-sm text-slate-500">
        Member since {{ $author->created_at->format('M j, Y') }}
        &middot;
        {{ $author->articles->count() }} {{ Str::plural('article', $author->articles->count()) }}
    </p>
</section>

<section class="mt-10 max-w-3xl">
    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Articles</h2>

    <div class="mt-4 divide-y divide-blue-100 border-t border-blue-100">
        @forelse($author->articles as $article)
            <article class="py-5">
                <h3 class="text-lg font-semibold text-slate-900">
                    <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-700">{{ $article->title }}</a>
                </h3>

                <p class="mt-1 text-xs uppercase tracking-wider text-blue-600">
                    {{ \Illuminate\Support\Carbon::parse($article->publish_date)->format('M j, Y') }}
                    &middot;
                    {{ $article->comments->count() }} {{ Str::plural('comment', $article->comments->count()) }}
                </p>

                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600">
                    {{ Str::limit($article->content, 180) }}
                </p>
            </article>
        @empty
            <p class="py-5 text-sm text-slate-500">No articles yet.</p>
        @endforelse
    </div>
</section>

</x-site-layout>
