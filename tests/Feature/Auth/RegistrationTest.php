<?php

use App\Enums\Role;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200)
        ->assertSee('placeholder="name@student.harbour.space"', escape: false);
});

test('a university address gets its role and can start right away', function (string $email, Role $role) {
    $response = $this->post('/register', [
        'name' => 'Khalil Ltaief',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', $email)->first();
    expect($user->role)->toBe($role)
        ->and($user->isApproved())->toBeTrue();
})->with([
    'student address' => ['khalil.ltaief@student.harbour.space', Role::Student],
    'teacher address' => ['khalil.ltaief@teacher.harbour.space', Role::Teacher],
]);

test('any other address has to wait for the approval of an admin', function (string $email) {
    $response = $this->post('/register', [
        'name' => 'Khalil Ltaief',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login', absolute: false))
        ->assertSessionHas('status', 'Your account has been created. An administrator has to approve it before you can log in.');

    $user = User::where('email', $email)->first();
    expect($user->role)->toBe(Role::Student)
        ->and($user->isApproved())->toBeFalse();
})->with([
    'personal address' => 'khalil.ltaief@gmail.com',
    'staff address' => 'khalil.ltaief@harbour.space',
    'look-alike domain' => 'khalil.ltaief@fake-student.harbour.space',
    'domain inside another domain' => 'khalil.ltaief@student.harbour.space.example.com',
]);

test('the role cannot be chosen in the registration form', function () {
    $this->post('/register', [
        'name' => 'Khalil Ltaief',
        'email' => 'khalil.ltaief@student.harbour.space',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ]);

    expect(User::where('email', 'khalil.ltaief@student.harbour.space')->first()->role)->toBe(Role::Student);
});

test('the university domains come from the helpdesk config', function () {
    config(['helpdesk.teacher_email_domain' => 'staff.example.edu']);

    $this->post('/register', [
        'name' => 'Khalil Ltaief',
        'email' => 'khalil@staff.example.edu',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    expect(User::where('email', 'khalil@staff.example.edu')->first()->role)->toBe(Role::Teacher);
});
