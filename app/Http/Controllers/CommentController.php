<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Notifications\NewCommentToYourArticleNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $id)
    {
        // step 0a - validation
        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        // step 1 - load necessary data
        $article = Article::findOrFail($id);

        // step 2a - create the comment
        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'article_id' => $id,
            'content' => $validated['content'],
        ]);

        $article->author->notify(new NewCommentToYourArticleNotification($comment));

        // step 3 - return a response (view, redirect)
        return redirect()->back();
    }
}
