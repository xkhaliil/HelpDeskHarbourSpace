<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('the welcome page explains the HelpDesk to guests', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Harbour.Space HelpDesk')
        ->assertSee('How to report a problem')
        ->assertSee(route('login'))
        ->assertSee(route('register'));
});

test('the welcome page lists the categories', function () {
    Category::factory()->create(['name' => 'Classroom Equipment']);

    $this->get('/')->assertSee('Classroom Equipment');
});

test('logged-in users get a link to their dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertOk()
        ->assertSee('Go to your dashboard');
});

test('the welcome page keeps the categories in the cache', function () {
    Category::factory()->create(['name' => 'Classroom Equipment']);
    $this->get('/')->assertSee('Classroom Equipment');

    // Created without the admin panel, so the cached list is not removed.
    Category::factory()->create(['name' => 'Old Printers']);

    $this->get('/')->assertDontSee('Old Printers');
});

test('the welcome page still works when the categories come from the cache', function () {
    // The test cache normally keeps values as they are. The file and database caches
    // serialize them, so the test cache does the same here.
    config(['cache.stores.array.serialize' => true]);
    Cache::forgetDriver('array');
    Category::factory()->create(['name' => 'Classroom Equipment']);

    $this->get('/')->assertOk();

    $this->get('/')->assertOk()->assertSee('Classroom Equipment');
});
