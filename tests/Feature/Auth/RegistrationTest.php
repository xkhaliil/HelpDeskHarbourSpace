<?php

use App\Enums\Role;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register with a university email address', function (string $email) {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
})->with([
    'main domain' => 'test@harbour.space',
    'subdomain' => 'test@student.harbour.space',
]);

test('new users always get the student role', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@harbour.space',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ]);

    expect(User::where('email', 'test@harbour.space')->first()->role)->toBe(Role::Student);
});

test('registration is refused for an email address outside the university', function (string $email) {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors([
        'email' => 'Please use your university email address (ending in @harbour.space).',
    ]);
})->with([
    'other domain' => 'test@example.com',
    'look-alike domain' => 'test@fake-harbour.space',
    'domain as subdomain' => 'test@harbour.space.example.com',
]);

test('the university domain comes from the helpdesk config', function () {
    config(['helpdesk.email_domain' => 'example.edu']);

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.edu',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
});
