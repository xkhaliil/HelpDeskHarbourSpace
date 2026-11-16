<x-site-layout title="Articles overview">

<section class="max-w-4xl">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">BLAUG</p>
    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Articles</h1>
    <p class="mt-2 text-sm text-slate-500">{{ $articles->count() }} articles</p>
</section>

<div class="mt-10 max-w-4xl divide-y divide-blue-100 border-t border-blue-100">
    @foreach($articles as $article)
        <article class="flex justify-between gap-10 py-7">
            <div class="w-12">
                <img src="{{$article->getImagePath() }}" class="w-12"/>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-700">{{ $article->title }}</a>
                </h2>

                <p class="mt-1 text-xs uppercase tracking-wider text-blue-600">By {{ $article->author->name }}</p>

                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600">
                    {{ Str::limit($article->content, 180) }}
                </p>

                <a href="{{ route('articles.show', $article) }}" class="mt-3 inline-block text-sm font-medium text-blue-600 hover:text-blue-800">
                    Read article
                </a>
            </div>

            <p class="shrink-0 text-xs text-slate-400">
                {{ \Illuminate\Support\Carbon::parse($article->publish_date)->format('M j, Y') }}
            </p>
        </article>
    @endforeach
</div>

</x-site-layout>
