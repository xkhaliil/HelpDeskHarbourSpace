<?php

use App\Enums\Role;
use App\Models\Ticket;
use App\Models\User;

test('admins see all users with their role and number of tickets', function () {
    $teacher = User::factory()->teacher()->create(['name' => 'Marta Vidal']);
    Ticket::factory()->count(2)->for($teacher)->create();

    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/users');

    $response->assertOk()->assertSeeInOrder(['Marta Vidal', 'Teacher', '2']);
});

test('teachers cannot open the users page', function () {
    $this->actingAs(User::factory()->teacher()->create())
        ->get('/admin/users')
        ->assertForbidden();
});

test('admins can change the role of a user', function () {
    $student = User::factory()->student()->create(['name' => 'Lucas Moreau']);

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/users/{$student->id}", ['role' => 'teacher'])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success', 'The role of Lucas Moreau is now: Teacher.');

    expect($student->refresh()->role)->toBe(Role::Teacher);
});

test('the role must be a known role', function () {
    $student = User::factory()->student()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/users/{$student->id}", ['role' => 'superhero'])
        ->assertSessionHasErrors(['role' => 'The selected role is invalid.']);

    expect($student->refresh()->role)->toBe(Role::Student);
});

test('admins cannot change their own role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put("/admin/users/{$admin->id}", ['role' => 'student'])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('error', 'You cannot change your own role.');

    expect($admin->refresh()->role)->toBe(Role::Admin);
});
