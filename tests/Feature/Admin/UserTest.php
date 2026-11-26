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

test('admins can change the name, the email address and the role of a user', function () {
    $student = User::factory()->student()->create(['name' => 'Lucas Moreau']);

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/users/{$student->id}", [
            'name' => 'Lucas Moreau',
            'email' => 'lucas.moreau@teacher.harbour.space',
            'role' => 'teacher',
        ])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success', 'The account of Lucas Moreau has been updated. Role: Teacher.');

    $student->refresh();
    expect($student->role)->toBe(Role::Teacher)
        ->and($student->email)->toBe('lucas.moreau@teacher.harbour.space');
});

test('the role must be a known role', function () {
    $student = User::factory()->student()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/users/{$student->id}", ['name' => $student->name, 'email' => $student->email, 'role' => 'superhero'])
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
        ->put("/admin/users/{$admin->id}", ['name' => $admin->name, 'email' => $admin->email, 'role' => 'student'])
        ->assertRedirect("/admin/users/{$admin->id}/edit")
        ->assertSessionHas('error', 'You cannot change your own role.');

    expect($admin->refresh()->role)->toBe(Role::Admin);
});

test('admins can change their own name and keep their role', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin']);

    $this->actingAs($admin)
        ->put("/admin/users/{$admin->id}", ['name' => 'Head of IT', 'email' => $admin->email, 'role' => 'admin'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/users');

    expect($admin->refresh()->name)->toBe('Head of IT');
});

test('the email address of a user must stay unique', function () {
    User::factory()->create(['email' => 'taken@harbour.space']);
    $student = User::factory()->student()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/users/{$student->id}", ['name' => $student->name, 'email' => 'taken@harbour.space', 'role' => 'student'])
        ->assertSessionHasErrors(['email' => 'The email has already been taken.']);
});

test('admins can open the create and edit pages of a user', function () {
    $admin = User::factory()->admin()->create();
    $student = User::factory()->student()->create(['email' => 'lucas@student.harbour.space']);

    $this->actingAs($admin)->get('/admin/users/create')->assertOk();
    $this->actingAs($admin)->get("/admin/users/{$student->id}/edit")->assertOk()->assertSee('lucas@student.harbour.space');
});

test('the user page shows the reported tickets and, for admins, the handled tickets', function () {
    $student = User::factory()->student()->create();
    Ticket::factory()->for($student)->create(['title' => 'Projector does not turn on']);
    $colleague = User::factory()->admin()->create();
    Ticket::factory()->inProgress()->create(['title' => 'Wi-Fi drops in the library'])->assignees()->attach($colleague);
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get("/admin/users/{$student->id}")
        ->assertOk()
        ->assertSee('Projector does not turn on');

    $this->actingAs($admin)->get("/admin/users/{$colleague->id}")
        ->assertOk()
        ->assertSee('Wi-Fi drops in the library');
});

test('admins can create an account that can log in right away', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/users', [
            'name' => 'Laia Ferrer',
            'email' => 'laia.ferrer@example.com',
            'role' => 'admin',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success', 'The account of Laia Ferrer has been created.');

    $user = User::where('email', 'laia.ferrer@example.com')->firstOrFail();
    expect($user->role)->toBe(Role::Admin)
        ->and($user->isApproved())->toBeTrue();

    $this->post('/logout');
    $this->post('/login', ['email' => 'laia.ferrer@example.com', 'password' => 'a-strong-password']);
    $this->assertAuthenticatedAs($user);
});

test('a new account needs a unique email address', function () {
    User::factory()->create(['email' => 'taken@harbour.space']);

    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/users', [
            'name' => 'Somebody',
            'email' => 'taken@harbour.space',
            'role' => 'student',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])
        ->assertSessionHasErrors(['email' => 'The email has already been taken.']);
});

test('the password of a new account must be confirmed', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/users', [
            'name' => 'Somebody',
            'email' => 'somebody@harbour.space',
            'role' => 'student',
            'password' => 'a-strong-password',
            'password_confirmation' => 'another-password',
        ])
        ->assertSessionHasErrors(['password' => 'The password field confirmation does not match.']);
});

test('students cannot create accounts', function () {
    $this->actingAs(User::factory()->student()->create())
        ->post('/admin/users', ['name' => 'Somebody', 'email' => 'somebody@harbour.space', 'role' => 'admin'])
        ->assertForbidden();

    expect(User::where('email', 'somebody@harbour.space')->exists())->toBeFalse();
});

test('admins can delete an account without tickets or replies', function () {
    $pendingUser = User::factory()->unapproved()->create(['name' => 'Jordi Puig']);

    $this->actingAs(User::factory()->admin()->create())
        ->delete("/admin/users/{$pendingUser->id}")
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success', 'The account of Jordi Puig has been deleted.');

    $this->assertModelMissing($pendingUser);
});

test('an admin who handles tickets can be deleted', function () {
    $colleague = User::factory()->admin()->create();
    Ticket::factory()->inProgress()->create()->assignees()->attach($colleague);

    $this->actingAs(User::factory()->admin()->create())
        ->delete("/admin/users/{$colleague->id}")
        ->assertSessionHas('success');

    $this->assertModelMissing($colleague);
    $this->assertDatabaseCount('ticket_user', 0);
});

test('an account with tickets cannot be deleted', function () {
    $student = User::factory()->student()->create(['name' => 'Lucas Moreau']);
    Ticket::factory()->for($student)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete("/admin/users/{$student->id}")
        ->assertRedirect('/admin/users')
        ->assertSessionHas('error', 'Lucas Moreau still has tickets or replies, so the account cannot be deleted.');

    $this->assertModelExists($student);
});

test('admins cannot delete their own account in the admin panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete("/admin/users/{$admin->id}")
        ->assertSessionHas('error', 'You cannot delete your own account here. Use your profile page.');

    $this->assertModelExists($admin);
});
