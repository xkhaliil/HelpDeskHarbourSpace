<?php

use App\Models\User;

test('guests are redirected to the login page from the admin panel', function () {
    $this->get('/admin')->assertRedirect('/login');
});

test('students and teachers cannot open the admin panel', function (string $role) {
    $user = User::factory()->{$role}()->create();

    $this->actingAs($user)->get('/admin')->assertForbidden();
})->with(['student', 'teacher']);

test('admins can open the admin panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin')->assertOk();
});

test('admins are sent from the user dashboard to the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin');
});
