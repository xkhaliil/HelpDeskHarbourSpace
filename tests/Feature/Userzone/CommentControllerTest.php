<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

test('guests are redirected from the comment moderation pages', function () {
    $this->get('/user/comments')->assertRedirect('/login');
});

test('admins see all comments in the moderation index', function () {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'A comment from another author',
    ]);

    $this->actingAs($admin)
        ->get('/user/comments')
        ->assertOk()
        ->assertSee('A comment from another author');
});

test('article authors only see comments on their own articles', function () {
    $author = User::factory()->nonAdmin()->create();
    $ownArticle = Article::factory()->create(['author_id' => $author->id]);
    $otherArticle = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    Comment::factory()->create([
        'article_id' => $ownArticle->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'Comment on my article',
    ]);
    Comment::factory()->create([
        'article_id' => $otherArticle->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'Comment on someone else article',
    ]);

    $this->actingAs($author)
        ->get('/user/comments')
        ->assertOk()
        ->assertSee('Comment on my article')
        ->assertDontSee('Comment on someone else article');
});

test('the unmoderated filter only shows comments awaiting validation', function () {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'An approved comment',
    ]);
    Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'A waiting comment',
    ]);

    $this->actingAs($admin)
        ->get('/user/comments?unmoderated=1')
        ->assertOk()
        ->assertSee('A waiting comment')
        ->assertDontSee('An approved comment');
});

test('an admin can view the comment detail page', function () {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'The comment detail content',
    ]);

    $this->actingAs($admin)
        ->get("/user/comments/{$comment->id}")
        ->assertOk()
        ->assertSee('The comment detail content');
});

test('an unrelated user cannot view the comment detail page', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);
    $other = User::factory()->nonAdmin()->create();

    $this->actingAs($other)->get("/user/comments/{$comment->id}")->assertForbidden();
});

test('an article author can approve a comment', function () {
    $author = User::factory()->nonAdmin()->create();
    $article = Article::factory()->create(['author_id' => $author->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);

    $response = $this->actingAs($author)->put("/user/comments/{$comment->id}", [
        'content' => $comment->content,
        'approved' => 1,
    ]);

    $response->assertRedirect('/user/comments');
    expect($comment->fresh()->approved_at)->not->toBeNull();
});

test('an unrelated user cannot approve a comment', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);
    $other = User::factory()->nonAdmin()->create();

    $this->actingAs($other)->put("/user/comments/{$comment->id}", [
        'content' => $comment->content,
        'approved' => 1,
    ])->assertForbidden();

    expect($comment->fresh()->approved_at)->toBeNull();
});

test('an article author can delete a comment', function () {
    $author = User::factory()->nonAdmin()->create();
    $article = Article::factory()->create(['author_id' => $author->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);

    $response = $this->actingAs($author)->delete("/user/comments/{$comment->id}");

    $response->assertRedirect('/user/comments');
    $this->assertModelMissing($comment);
});

test('an unrelated user cannot delete a comment', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);
    $other = User::factory()->nonAdmin()->create();

    $this->actingAs($other)->delete("/user/comments/{$comment->id}")->assertForbidden();

    $this->assertModelExists($comment);
});

test('updating a comment requires content', function () {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);

    $this->actingAs($admin)->put("/user/comments/{$comment->id}", [
        'content' => '',
        'approved' => 1,
    ])->assertSessionHasErrors('content');

    expect($comment->fresh()->approved_at)->toBeNull();
});
