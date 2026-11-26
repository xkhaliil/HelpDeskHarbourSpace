<?php

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;

test('the seeder creates the three approved demo accounts', function () {
    $this->seed();

    $admin = User::where('email', 'admin@example.com')->first();
    $teacher = User::where('email', 'teacher@harbour.space')->first();
    $student = User::where('email', 'student@harbour.space')->first();

    expect($admin->name)->toBe('Admin')
        ->and($admin->role)->toBe(Role::Admin)
        ->and($teacher->role)->toBe(Role::Teacher)
        ->and($student->role)->toBe(Role::Student)
        ->and($admin->isApproved() && $teacher->isApproved() && $student->isApproved())->toBeTrue();

    $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password']);
    $this->assertAuthenticatedAs($admin);
});

test('the seeder creates two accounts that wait for approval', function () {
    $this->seed();

    expect(User::whereNull('approved_at')->pluck('email')->all())
        ->toBe(['jordi.puig@gmail.com', 'sofia.martin@harbour.space']);
});

test('the seeder creates the categories, tickets and replies', function () {
    $this->seed();

    expect(Category::pluck('name')->all())->toBe([
        'Wi-Fi & Network',
        'Accounts & Portal',
        'Classroom Equipment',
        'Laptop & Software',
        'Building & Facilities',
    ]);

    expect(User::count())->toBe(17)
        ->and(Ticket::count())->toBe(40)
        ->and(Ticket::distinct()->pluck('status')->all())->toHaveCount(count(TicketStatus::cases()))
        ->and(Ticket::whereHas('user', fn ($query) => $query->where('role', Role::Admin))->count())->toBe(0)
        ->and(Ticket::whereHas('user', fn ($query) => $query->whereNull('approved_at'))->count())->toBe(0);
});

test('the seeder lets the IT staff handle every ticket that is not open', function () {
    $this->seed();

    expect(User::where('role', Role::Admin)->count())->toBe(3)
        ->and(Ticket::where('status', TicketStatus::Open)->has('assignees')->count())->toBe(0)
        ->and(Ticket::where('status', '!=', TicketStatus::Open)->doesntHave('assignees')->count())->toBe(0)
        ->and(User::where('role', '!=', Role::Admin)->has('assignedTickets')->count())->toBe(0);
});
