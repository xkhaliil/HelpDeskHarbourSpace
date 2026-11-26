<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;

test('admins can open any ticket with its conversation', function () {
    $ticket = Ticket::factory()->create(['title' => 'Projector does not turn on']);
    Reply::factory()->for($ticket)->for($ticket->user)->create(['message' => 'It is still broken.']);

    $this->actingAs(User::factory()->admin()->create())
        ->get("/admin/tickets/{$ticket->id}")
        ->assertOk()
        ->assertSee('Projector does not turn on')
        ->assertSee('It is still broken.')
        ->assertSee($ticket->user->email);
});

test('admins are sent from the user ticket pages to the admin panel', function () {
    $ticket = Ticket::factory()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/tickets')->assertRedirect('/admin/tickets');
    $this->actingAs($admin)->get("/tickets/{$ticket->id}")->assertRedirect("/admin/tickets/{$ticket->id}");
    $this->actingAs($admin)->get("/tickets/{$ticket->id}/edit")->assertRedirect("/admin/tickets/{$ticket->id}");
});

test('admins can change the status and the priority', function () {
    $ticket = Ticket::factory()->open()->create(['priority' => TicketPriority::Normal]);

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/tickets/{$ticket->id}", ['status' => 'in_progress', 'priority' => 'urgent'])
        ->assertRedirect("/admin/tickets/{$ticket->id}")
        ->assertSessionHas('success', 'The ticket has been updated.');

    $ticket->refresh();
    expect($ticket->status)->toBe(TicketStatus::InProgress)
        ->and($ticket->priority)->toBe(TicketPriority::Urgent);
});

test('the status and the priority must be known values', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/tickets/{$ticket->id}", ['status' => 'forgotten', 'priority' => ''])
        ->assertSessionHasErrors([
            'status' => 'The selected status is invalid.',
            'priority' => 'The priority field is required.',
        ]);
});

test('students cannot use the admin ticket page', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->actingAs($ticket->user)->get("/admin/tickets/{$ticket->id}")->assertForbidden();
    $this->actingAs($ticket->user)
        ->put("/admin/tickets/{$ticket->id}", ['status' => 'solved', 'priority' => 'urgent'])
        ->assertForbidden();

    expect($ticket->refresh()->status)->toBe(TicketStatus::Open);
});

test('admins can choose which admins handle a ticket', function () {
    $ticket = Ticket::factory()->open()->create();
    $admin = User::factory()->admin()->create();
    $colleague = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put("/admin/tickets/{$ticket->id}", [
            'status' => 'in_progress',
            'priority' => 'normal',
            'assignees' => [$admin->id, $colleague->id],
        ])
        ->assertSessionHasNoErrors();

    expect($ticket->assignees()->pluck('users.id')->sort()->values()->all())->toBe([$admin->id, $colleague->id]);
});

test('unchecking every admin removes the admins from the ticket', function () {
    $ticket = Ticket::factory()->inProgress()->create();
    $admin = User::factory()->admin()->create();
    $ticket->assignees()->attach($admin);

    $this->actingAs($admin)
        ->put("/admin/tickets/{$ticket->id}", ['status' => 'in_progress', 'priority' => 'normal'])
        ->assertSessionHasNoErrors();

    expect($ticket->assignees()->count())->toBe(0);
});

test('only admins can handle a ticket', function () {
    $ticket = Ticket::factory()->open()->create();
    $student = User::factory()->student()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/tickets/{$ticket->id}", [
            'status' => 'in_progress',
            'priority' => 'normal',
            'assignees' => [$student->id],
        ])
        ->assertSessionHasErrors(['assignees.0' => 'Only members of the IT staff (admins) can handle a ticket.']);

    expect($ticket->assignees()->count())->toBe(0);
});

test('the admin ticket page shows who handles the ticket', function () {
    $ticket = Ticket::factory()->inProgress()->create();
    $ticket->assignees()->attach(User::factory()->admin()->create(['name' => 'Marc Soler']));

    $this->actingAs(User::factory()->admin()->create())
        ->get("/admin/tickets/{$ticket->id}")
        ->assertOk()
        ->assertSeeInOrder(['Handled by', 'Marc Soler']);
});
