<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ArticleController extends Controller
{
    public function index()
    {
        if(auth()->user()->is_admin) {
            $articles = Article::query()
                ->with(['author', 'categories'])
                ->orderByDesc('publish_date')
                ->get();
        } else {
            $articles = Article::query()
                ->where('author_id', auth()->user()->id)
                ->with(['author', 'categories'])
                ->orderByDesc('publish_date')
                ->get();
        }

        return view('userzone.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('userzone.articles.create', [
            'authors' => $this->authorOptions(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:3'],
            'author_id' => ['required', 'exists:users,id'],
            'publish_date' => ['required', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:8192'],
        ]);

        $article = Article::create(Arr::except($validated, ['categories','image']));

        $article->categories()->sync($validated['categories'] ?? []);

        if($request->has('image')) {
            $article->addMediaFromRequest('image')->toMediaCollection('headers');
        }

        return redirect()->route('userzone.articles.index');
    }

    public function edit(int $id)
    {
        $article = Article::findOrFail($id);

        abort_unless($article->canManage(auth()->user()), 403);

        return view('userzone.articles.edit', [
            'article' => $article,
            'authors' => $this->authorOptions(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:3'],
            'author_id' => ['required', 'exists:users,id'],
            'publish_date' => ['required', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:8192'],
        ]);

        $article = Article::findOrFail($id);

        abort_unless($article->canManage(auth()->user()), 403);

        $article->update(Arr::except($validated, ['categories','image']));

        $article->categories()->sync($validated['categories'] ?? []);

        if($request->has('image')) {
            $article->addMediaFromRequest('image')->toMediaCollection('headers');
        }

        return redirect()->route('userzone.articles.index');
    }

    public function destroy(int $id)
    {
        $article = Article::findOrFail($id);

        abort_unless($article->canManage(auth()->user()), 403);

        $article->categories()->detach();
        $article->delete();

        return redirect()->route('userzone.articles.index');
    }

    /**
     * @return array<int, array{key: int, value: string}>
     */
    private function authorOptions(): array
    {
        return User::query()
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'key' => $user->id,
                'value' => $user->name,
            ])
            ->all();
    }

    /**
     * @return array<int, array{key: int, value: string}>
     */
    private function categoryOptions(): array
    {
        return Category::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => [
                'key' => $category->id,
                'value' => $category->name,
            ])
            ->all();
    }

}
