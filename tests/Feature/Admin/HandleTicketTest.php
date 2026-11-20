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

test('admins are sent from the user ticket page to the admin ticket page', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get("/tickets/{$ticket->id}")
        ->assertRedirect("/admin/tickets/{$ticket->id}");
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
