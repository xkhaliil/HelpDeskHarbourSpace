<x-site-layout title="Authors overview">

<section class="max-w-4xl">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">BLAUG</p>
    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Authors</h1>
    <p class="mt-2 text-sm text-slate-500">{{ $authors->count() }} authors</p>
</section>

<div class="mt-10 max-w-4xl divide-y divide-blue-100 border-t border-blue-100">
    @forelse($authors as $author)
        <article class="flex items-center justify-between gap-10 py-7">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    <a href="{{ route('authors.show', $author) }}" class="hover:text-blue-700">{{ $author->name }}</a>
                </h2>

                <p class="mt-1 text-xs uppercase tracking-wider text-blue-600">
                    {{ $author->articles_count }} {{ Str::plural('article', $author->articles_count) }}
                </p>
            </div>

            <p class="shrink-0 text-xs text-slate-400">
                Member since {{ $author->created_at->format('M j, Y') }}
            </p>
        </article>
    @empty
        <p class="py-7 text-sm text-slate-500">No authors yet.</p>
    @endforelse
</div>

</x-site-layout>
