<?php

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;

test('guests can see the list of categories with their description', function () {
    $category = Category::factory()->create([
        'name' => 'Classroom Equipment',
        'description' => 'Projectors, screens and speakers in the classrooms.',
    ]);
    Ticket::factory()->count(2)->for($category)->create();

    $this->get('/categories')
        ->assertOk()
        ->assertSee('Classroom Equipment')
        ->assertSee('Projectors, screens and speakers in the classrooms.')
        ->assertSee('2 tickets reported')
        ->assertSee(route('categories.show', $category));
});

test('guests can see a category with the number of tickets per status', function () {
    $category = Category::factory()->create(['name' => 'Wi-Fi & Network']);
    Ticket::factory()->count(3)->for($category)->create(['status' => TicketStatus::Open]);
    Ticket::factory()->for($category)->create(['status' => TicketStatus::Solved]);

    $this->get("/categories/{$category->id}")
        ->assertOk()
        ->assertSee('Wi-Fi &amp; Network', escape: false)
        ->assertViewHas('countsPerStatus', ['open' => 3, 'in_progress' => 0, 'solved' => 1, 'closed' => 0])
        ->assertSee('Log in to report a problem');
});

test('the category page does not show the private tickets', function () {
    $category = Category::factory()->create();
    Ticket::factory()->for($category)->create(['title' => 'My laptop was stolen in room 3.05']);

    $this->get("/categories/{$category->id}")
        ->assertOk()
        ->assertDontSee('My laptop was stolen in room 3.05');
});

test('logged-in users can report a problem from the category page', function () {
    $category = Category::factory()->create();

    $this->actingAs(User::factory()->student()->create())
        ->get("/categories/{$category->id}")
        ->assertSee(route('tickets.create'));
});

test('a category that does not exist gives a 404 page', function () {
    $this->get('/categories/999')->assertNotFound();
});
