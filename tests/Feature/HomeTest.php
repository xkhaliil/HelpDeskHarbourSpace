<?php

use App\Models\Category;
use App\Models\User;

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
