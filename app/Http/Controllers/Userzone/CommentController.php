<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::query()
            ->with(['article.author', 'user'])
            ->when(! auth()->user()->is_admin, fn ($query) => $query->whereHas('article', fn ($query) => $query->where('author_id', auth()->id())))
            ->when($request->boolean('unmoderated'), fn ($query) => $query->whereNull('approved_at'))
            ->orderByDesc('created_at')
            ->get();

        return view('userzone.comments.index', compact('comments'));
    }

    public function show(int $id)
    {
        $comment = Comment::findOrFail($id);

        abort_unless($comment->canManage(auth()->user()), 403);

        return view('userzone.comments.show', compact('comment'));
    }

    public function edit(int $id)
    {
        $comment = Comment::findOrFail($id);

        abort_unless($comment->canManage(auth()->user()), 403);

        return view('userzone.comments.edit', compact('comment'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:1000'],
            'approved' => ['nullable', 'boolean'],
        ]);

        $comment = Comment::findOrFail($id);

        abort_unless($comment->canManage(auth()->user()), 403);

        $comment->update([
            'content' => $validated['content'],
            'approved_at' => $request->boolean('approved') ? now() : null,
        ]);

        return redirect()->route('userzone.comments.index');
    }

    public function destroy(int $id)
    {
        $comment = Comment::findOrFail($id);

        abort_unless($comment->canManage(auth()->user()), 403);

        $comment->delete();

        return redirect()->route('userzone.comments.index');
    }
}
