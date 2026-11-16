<?php

use App\Models\Article;
use App\Models\User;

test('authors page lists only users that have articles', function () {
    $author = User::factory()->create(['name' => 'Ada Author']);
    $reader = User::factory()->create(['name' => 'Rita Reader']);

    Article::factory()->create(['author_id' => $author->id]);

    $response = $this->get('/authors');

    $response
        ->assertOk()
        ->assertSee('Ada Author')
        ->assertDontSee('Rita Reader');
});

test('authors page renders when there are no authors', function () {
    User::factory()->create();

    $this->get('/authors')
        ->assertOk()
        ->assertSee('No authors yet.');
});

test('author page shows the articles of that author', function () {
    $author = User::factory()->create(['name' => 'Ada Author']);
    $otherAuthor = User::factory()->create(['name' => 'Bob Builder']);

    Article::factory()->create([
        'author_id' => $author->id,
        'title' => 'An article by Ada',
    ]);

    Article::factory()->create([
        'author_id' => $otherAuthor->id,
        'title' => 'An article by Bob',
    ]);

    $response = $this->get("/authors/{$author->id}");

    $response
        ->assertOk()
        ->assertSee('Ada Author')
        ->assertSee('An article by Ada')
        ->assertDontSee('An article by Bob');
});

test('author page returns not found for a user without articles', function () {
    $reader = User::factory()->create();

    $this->get("/authors/{$reader->id}")->assertNotFound();
});
