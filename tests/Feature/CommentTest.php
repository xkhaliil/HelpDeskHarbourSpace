<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

test('an admin can manage any comment', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);
    $admin = User::factory()->admin()->create();

    expect($comment->canManage($admin))->toBeTrue();
});

test('the article author can manage comments on the article', function () {
    $author = User::factory()->nonAdmin()->create();
    $article = Article::factory()->create(['author_id' => $author->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);

    expect($comment->canManage($author))->toBeTrue();
});

test('an unrelated user cannot manage a comment', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);
    $other = User::factory()->nonAdmin()->create();

    expect($comment->canManage($other))->toBeFalse();
});

test('the comment author can view an unapproved comment without managing it', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $commenter = User::factory()->nonAdmin()->create();
    $comment = Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
    ]);

    expect($comment->canView($commenter))->toBeTrue()
        ->and($comment->canManage($commenter))->toBeFalse();
});

test('an admin can view an unapproved comment', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);
    $admin = User::factory()->admin()->create();

    expect($comment->canView($admin))->toBeTrue();
});

test('guests cannot view an unapproved comment', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $comment = Comment::factory()->unapproved()->create(['article_id' => $article->id]);

    expect($comment->canView(null))->toBeFalse();
});

test('everyone can view an approved comment', function () {
    $comment = Comment::factory()->create();

    expect($comment->canView(null))->toBeTrue();
});
