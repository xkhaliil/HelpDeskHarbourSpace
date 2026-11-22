<?php

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;

test('the seeder creates the three demo accounts', function () {
    $this->seed();

    expect(User::where('email', 'admin@admin.com')->first())
        ->name->toBe('Admin')
        ->role->toBe(Role::Admin)
        ->and(User::where('email', 'teacher@harbour.space')->first()->role)->toBe(Role::Teacher)
        ->and(User::where('email', 'student@harbour.space')->first()->role)->toBe(Role::Student);

    $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
    $this->assertAuthenticated();
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

    expect(User::count())->toBe(13)
        ->and(Ticket::count())->toBe(40)
        ->and(Ticket::distinct()->pluck('status')->sort()->values()->all())->toHaveCount(count(TicketStatus::cases()))
        ->and(Ticket::whereHas('user', fn ($query) => $query->where('role', Role::Admin))->count())->toBe(0);
});
