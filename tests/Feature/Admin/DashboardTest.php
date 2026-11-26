<?php

use App\Enums\TicketPriority;
use App\Models\Ticket;
use App\Models\User;

test('the admin dashboard counts the tickets per status', function () {
    Ticket::factory()->count(3)->open()->create();
    Ticket::factory()->count(2)->inProgress()->create();
    Ticket::factory()->solved()->create();

    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin');

    $response->assertOk()->assertViewHas('countsPerStatus', [
        'open' => 3,
        'in_progress' => 2,
        'solved' => 1,
        'closed' => 0,
    ]);
});

test('the admin dashboard lists the latest urgent and high tickets', function () {
    Ticket::factory()->urgent()->create(['title' => 'Lecture hall has no power']);
    Ticket::factory()->high()->create(['title' => 'Projector does not turn on']);
    Ticket::factory()->create(['title' => 'Broken chair', 'priority' => 'normal']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertSeeInOrder(['Lecture hall has no power', 'Projector does not turn on'])
        ->assertDontSee('Broken chair');
});

test('the admin dashboard lists the unsolved tickets the admin handles', function () {
    $admin = User::factory()->admin()->create();
    Ticket::factory()->inProgress()->create(['title' => 'Wi-Fi drops in the library', 'priority' => TicketPriority::Normal])->assignees()->attach($admin);
    Ticket::factory()->solved()->create(['title' => 'Printer is out of toner', 'priority' => TicketPriority::Normal])->assignees()->attach($admin);
    Ticket::factory()->open()->create(['title' => 'Projector does not turn on', 'priority' => TicketPriority::Normal])->assignees()->attach(User::factory()->admin()->create());

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Wi-Fi drops in the library')
        ->assertDontSee('Printer is out of toner')
        ->assertDontSee('Projector does not turn on');
});
