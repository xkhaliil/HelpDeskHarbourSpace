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

test('guests do not see the links for logged-in users in the menu', function () {
    $this->get('/')
        ->assertOk()
        ->assertDontSee(route('dashboard'))
        ->assertDontSee(route('admin.dashboard'));
});

test('students see the links to their own tickets in the menu', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get('/profile')
        ->assertSee(route('tickets.index'))
        ->assertSee(route('tickets.create'))
        ->assertDontSee(route('admin.users.index'));
});

test('admins see the links of the admin panel in the menu', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/profile')
        ->assertSee(route('admin.tickets.index'))
        ->assertSee(route('admin.users.index'))
        ->assertDontSee(route('tickets.create'));
});

test('the footer shows the same menu as the header', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get('/profile')
        ->assertSeeInOrder(['<nav', route('tickets.create'), '<footer', route('tickets.create')], escape: false);
});
