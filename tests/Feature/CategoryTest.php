<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

test('categories page lists only categories that have articles', function () {
    $category = Category::factory()->create(['name' => 'Laravel']);
    $emptyCategory = Category::factory()->create(['name' => 'React']);

    Article::factory()->create()->categories()->attach($category);

    $response = $this->get('/categories');

    $response
        ->assertOk()
        ->assertSee('Laravel')
        ->assertDontSee('React');
});

test('categories page renders when there are no categories', function () {
    Category::factory()->create();

    $this->get('/categories')
        ->assertOk()
        ->assertSee('No categories yet.');
});

test('category page shows the articles of that category', function () {
    $author = User::factory()->create();

    $category = Category::factory()->create(['name' => 'Laravel']);
    $otherCategory = Category::factory()->create(['name' => 'React']);

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'title' => 'An article about Laravel',
    ]);
    $article->categories()->attach($category);

    $otherArticle = Article::factory()->create([
        'author_id' => $author->id,
        'title' => 'An article about React',
    ]);
    $otherArticle->categories()->attach($otherCategory);

    $response = $this->get("/categories/{$category->id}");

    $response
        ->assertOk()
        ->assertSee('Laravel')
        ->assertSee('An article about Laravel')
        ->assertDontSee('An article about React');
});

test('category page returns not found for a category without articles', function () {
    $category = Category::factory()->create();

    $this->get("/categories/{$category->id}")->assertNotFound();
});
