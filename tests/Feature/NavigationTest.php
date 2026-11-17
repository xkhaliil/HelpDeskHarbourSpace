<?php

use App\Models\User;

test('the navigation shows the name and role of the logged-in user', function () {
    $teacher = User::factory()->teacher()->create(['name' => 'Marta Vidal']);

    $this->actingAs($teacher)
        ->get('/profile')
        ->assertOk()
        ->assertSeeInOrder(['Marta Vidal', 'Teacher']);
});

test('guests see the log in and register links', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('login'))
        ->assertSee(route('register'));
});
