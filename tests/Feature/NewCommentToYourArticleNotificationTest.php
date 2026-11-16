<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentToYourArticleNotification;
use Illuminate\Support\Facades\Notification;

test('the author notification links to comment moderation', function () {
    Notification::fake();

    $author = User::factory()->nonAdmin()->create();
    $article = Article::factory()->create(['author_id' => $author->id]);
    $commenter = User::factory()->nonAdmin()->create();

    $this->actingAs($commenter)->post(route('articles.comments.store', $article->id), [
        'content' => 'A comment that needs moderation',
    ]);

    $comment = Comment::where('content', 'A comment that needs moderation')->firstOrFail();

    Notification::assertSentTo($author, NewCommentToYourArticleNotification::class, function (NewCommentToYourArticleNotification $notification) use ($author, $comment) {
        $mail = $notification->toMail($author);

        return $mail->actionUrl === route('userzone.comments.show', $comment->id)
            && str_contains(implode(' ', $mail->introLines), 'awaiting validation');
    });
    Notification::assertNotSentTo($commenter, NewCommentToYourArticleNotification::class);
});

test('the author notification escapes the comment content', function () {
    Notification::fake();

    $author = User::factory()->nonAdmin()->create();
    $article = Article::factory()->create(['author_id' => $author->id]);
    $content = "<script>alert('xss')</script>";

    $this->actingAs(User::factory()->nonAdmin()->create())->post(route('articles.comments.store', $article->id), [
        'content' => $content,
    ]);

    Notification::assertSentTo($author, NewCommentToYourArticleNotification::class, function (NewCommentToYourArticleNotification $notification) use ($author, $content) {
        $rendered = $notification->toMail($author)->render();

        return str_contains($rendered, '&lt;script&gt;')
            && ! str_contains($rendered, $content);
    });
});
