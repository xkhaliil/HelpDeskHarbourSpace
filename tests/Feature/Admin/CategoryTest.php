<?php

use App\Models\Category;
use App\Models\User;

test('admins see all categories', function () {
    Category::factory()->create(['name' => 'Wi-Fi & Network']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/categories')
        ->assertOk()
        ->assertSee('Wi-Fi &amp; Network', escape: false);
});

test('students cannot manage categories', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get('/admin/categories')
        ->assertForbidden();
});

test('admins can open the create, show and edit pages', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create(['name' => 'Printing & Scanning']);

    $this->actingAs($admin)->get('/admin/categories/create')->assertOk();
    $this->actingAs($admin)->get("/admin/categories/{$category->id}")->assertOk()->assertSee('Printing &amp; Scanning', escape: false);
    $this->actingAs($admin)->get("/admin/categories/{$category->id}/edit")->assertOk();
});

test('admins can create a category', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/categories', ['name' => 'Audio & Video'])
        ->assertRedirect('/admin/categories')
        ->assertSessionHas('success', 'The category has been created.');

    $this->assertDatabaseHas('categories', ['name' => 'Audio & Video']);
});

test('a category needs a name', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/categories', ['name' => ''])
        ->assertSessionHasErrors(['name' => 'The name field is required.']);
});

test('a category name must be unique', function () {
    Category::factory()->create(['name' => 'Audio & Video']);

    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/categories', ['name' => 'Audio & Video'])
        ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
});

test('admins can rename a category and keep its own name', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create(['name' => 'Audio']);

    $this->actingAs($admin)
        ->put("/admin/categories/{$category->id}", ['name' => 'Audio'])
        ->assertSessionHasNoErrors();

    $this->actingAs($admin)
        ->put("/admin/categories/{$category->id}", ['name' => 'Audio & Video'])
        ->assertRedirect('/admin/categories')
        ->assertSessionHas('success', 'The category has been updated.');

    expect($category->refresh()->name)->toBe('Audio & Video');
});

test('admins can delete a category', function () {
    $category = Category::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete("/admin/categories/{$category->id}")
        ->assertRedirect('/admin/categories')
        ->assertSessionHas('success', 'The category has been deleted.');

    $this->assertModelMissing($category);
});
