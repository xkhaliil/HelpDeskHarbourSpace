<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

test('guests are redirected from the article admin pages', function () {
    $this->get('/user/articles')->assertRedirect('/login');
});

test('article admin page lists all articles', function () {
    $author = User::factory()->create(['name' => 'Ada Author']);
    Article::factory()->create([
        'author_id' => $author->id,
        'title' => 'An article about Laravel',
    ]);

    $response = $this->actingAs(User::factory()->create())
        ->get('/user/articles');

    $response
        ->assertOk()
        ->assertSee('An article about Laravel')
        ->assertSee('Ada Author');
});

test('article create page renders the form', function () {
    User::factory()->create();
    Category::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get('/user/articles/create')
        ->assertOk()
        ->assertSee('Create a new article');
});

test('article edit page renders the current article', function () {
    $article = Article::factory()->create(['title' => 'An existing article']);

    $this->actingAs(User::factory()->create())
        ->get("/user/articles/{$article->id}/edit")
        ->assertOk()
        ->assertSee('An existing article');
});

test('an article can be created with categories', function () {
    $author = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs(User::factory()->create())
        ->post('/user/articles', [
            'title' => 'A new article',
            'content' => 'The content of the new article.',
            'author_id' => $author->id,
            'publish_date' => '2026-11-17T10:30',
            'categories' => [$category->id],
        ]);

    $response->assertRedirect('/user/articles');

    $article = Article::where('title', 'A new article')->firstOrFail();

    expect($article->author_id)->toBe($author->id)
        ->and($article->categories->pluck('id')->all())->toBe([$category->id]);
});

test('an article can be created without categories', function () {
    $author = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->post('/user/articles', [
            'title' => 'A categoryless article',
            'content' => 'The content of the categoryless article.',
            'author_id' => $author->id,
            'publish_date' => '2026-11-17T10:30',
        ])
        ->assertRedirect('/user/articles');

    expect(Article::where('title', 'A categoryless article')->exists())->toBeTrue();
});

test('creating an article requires a title, content, author and publish date', function () {
    $this->actingAs(User::factory()->create())
        ->post('/user/articles', [])
        ->assertSessionHasErrors(['title', 'content', 'author_id', 'publish_date']);
});

test('creating an article rejects an unknown author or category', function () {
    $this->actingAs(User::factory()->create())
        ->post('/user/articles', [
            'title' => 'An invalid article',
            'content' => 'The content of the invalid article.',
            'author_id' => 9999,
            'publish_date' => '2026-11-17T10:30',
            'categories' => [9999],
        ])
        ->assertSessionHasErrors(['author_id', 'categories.0']);
});

test('an article can be updated and its categories synced', function () {
    $article = Article::factory()->create();
    $oldCategory = Category::factory()->create();
    $newCategory = Category::factory()->create();
    $article->categories()->attach($oldCategory);

    $newAuthor = User::factory()->create();

    $response = $this->actingAs(User::factory()->create())
        ->put("/user/articles/{$article->id}", [
            'title' => 'An updated article',
            'content' => 'The updated content of the article.',
            'author_id' => $newAuthor->id,
            'publish_date' => '2026-12-01T09:00',
            'categories' => [$newCategory->id],
        ]);

    $response->assertRedirect('/user/articles');

    $article->refresh();

    expect($article->title)->toBe('An updated article')
        ->and($article->author_id)->toBe($newAuthor->id)
        ->and($article->categories->pluck('id')->all())->toBe([$newCategory->id]);
});

test('an article can be deleted', function () {
    $article = Article::factory()->create();
    $article->categories()->attach(Category::factory()->create());

    $response = $this->actingAs(User::factory()->create())
        ->delete("/user/articles/{$article->id}");

    $response->assertRedirect('/user/articles');

    $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    $this->assertDatabaseMissing('article_category', ['article_id' => $article->id]);
});
