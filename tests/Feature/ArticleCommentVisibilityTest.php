<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

test('guests only see approved comments', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'An approved comment',
    ]);
    Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'A hidden waiting comment',
    ]);

    $this->get(route('articles.show', $article->id))
        ->assertOk()
        ->assertSee('An approved comment')
        ->assertDontSee('A hidden waiting comment');
});

test('the comment author sees the awaiting validation message on their own comment', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    $commenter = User::factory()->nonAdmin()->create();
    Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'content' => 'My own waiting comment',
    ]);

    $this->actingAs($commenter)
        ->get(route('articles.show', $article->id))
        ->assertOk()
        ->assertSee('My own waiting comment')
        ->assertSee("This comment is awaiting validation. You're the only one to see it.", false);
});

test('the article author sees an awaiting validation badge on unapproved comments', function () {
    $author = User::factory()->nonAdmin()->create();
    $article = Article::factory()->create(['author_id' => $author->id]);
    Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'A comment waiting for the author',
    ]);

    $this->actingAs($author)
        ->get(route('articles.show', $article->id))
        ->assertOk()
        ->assertSee('A comment waiting for the author')
        ->assertSee('Awaiting validation');
});

test('an admin sees an awaiting validation badge on unapproved comments', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'A comment waiting for an admin',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('articles.show', $article->id))
        ->assertOk()
        ->assertSee('A comment waiting for an admin')
        ->assertSee('Awaiting validation');
});

test('an unrelated user does not see unapproved comments', function () {
    $article = Article::factory()->create(['author_id' => User::factory()->nonAdmin()->create()->id]);
    Comment::factory()->unapproved()->create([
        'article_id' => $article->id,
        'user_id' => User::factory()->nonAdmin()->create()->id,
        'content' => 'A waiting comment from someone else',
    ]);

    $this->actingAs(User::factory()->nonAdmin()->create())
        ->get(route('articles.show', $article->id))
        ->assertOk()
        ->assertDontSee('A waiting comment from someone else');
});
