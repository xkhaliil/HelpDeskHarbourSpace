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

test('accounts waiting for approval are listed separately', function () {
    User::factory()->unapproved()->create(['name' => 'Jordi Puig', 'email' => 'jordi.puig@gmail.com']);

    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/users');

    $response->assertOk()
        ->assertSeeInOrder(['Waiting for approval (1)', 'Jordi Puig', 'jordi.puig@gmail.com', 'All users']);
    expect($response->viewData('users')->pluck('name'))->not->toContain('Jordi Puig');
});

test('admins can approve an account and choose its role', function () {
    $pendingUser = User::factory()->unapproved()->create(['name' => 'Jordi Puig']);

    $this->actingAs(User::factory()->admin()->create())
        ->patch("/admin/users/{$pendingUser->id}/approve", ['role' => 'teacher'])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success', 'The account of Jordi Puig is approved as Teacher. This person can log in now.');

    $pendingUser->refresh();
    expect($pendingUser->isApproved())->toBeTrue()
        ->and($pendingUser->role)->toBe(Role::Teacher);

    $this->post('/logout');
    $this->post('/login', ['email' => $pendingUser->email, 'password' => 'password']);
    $this->assertAuthenticatedAs($pendingUser);
});

test('approving needs a known role', function () {
    $pendingUser = User::factory()->unapproved()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->patch("/admin/users/{$pendingUser->id}/approve", ['role' => 'guest'])
        ->assertSessionHasErrors(['role' => 'The selected role is invalid.']);

    expect($pendingUser->refresh()->isApproved())->toBeFalse();
});

test('an account that is already approved cannot be approved again', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch("/admin/users/{$admin->id}/approve", ['role' => 'student'])
        ->assertSessionHas('error');

    expect($admin->refresh()->role)->toBe(Role::Admin);
});

test('only admins can approve accounts', function () {
    $pendingUser = User::factory()->unapproved()->create();

    $this->actingAs(User::factory()->teacher()->create())
        ->patch("/admin/users/{$pendingUser->id}/approve", ['role' => 'teacher'])
        ->assertForbidden();

    expect($pendingUser->refresh()->isApproved())->toBeFalse();
});

test('the admin dashboard shows how many accounts are waiting', function () {
    User::factory()->count(2)->unapproved()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertSee('2 accounts waiting for your approval.');
});

test('admins cannot change their own role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put("/admin/users/{$admin->id}", ['role' => 'student'])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('error', 'You cannot change your own role.');

    expect($admin->refresh()->role)->toBe(Role::Admin);
});
