<?php

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;

test('the ticket page shows the conversation', function () {
    $ticket = Ticket::factory()->create();
    $admin = User::factory()->admin()->create(['name' => 'Campus Admin']);
    Reply::factory()->for($ticket)->for($admin)->create(['message' => 'A technician is on the way.']);

    $this->actingAs($ticket->user)
        ->get("/tickets/{$ticket->id}")
        ->assertOk()
        ->assertSeeInOrder(['Campus Admin', 'A technician is on the way.']);
});

test('owners can reply to their ticket', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->actingAs($ticket->user)
        ->from("/tickets/{$ticket->id}")
        ->post("/tickets/{$ticket->id}/replies", ['message' => 'It happened again this morning.'])
        ->assertRedirect("/tickets/{$ticket->id}")
        ->assertSessionHas('success', 'Your reply has been sent.');

    $reply = $ticket->replies()->firstOrFail();
    expect($reply->message)->toBe('It happened again this morning.')
        ->and($reply->user_id)->toBe($ticket->user_id);
});

test('admins can reply to any ticket', function () {
    $ticket = Ticket::factory()->inProgress()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post("/tickets/{$ticket->id}/replies", ['message' => 'We are looking into it.'])
        ->assertSessionHasNoErrors();

    expect($ticket->replies()->firstOrFail()->user_id)->toBe($admin->id);
});

test('users cannot reply to the ticket of somebody else', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->actingAs(User::factory()->student()->create())
        ->post("/tickets/{$ticket->id}/replies", ['message' => 'Hello'])
        ->assertForbidden();

    expect($ticket->replies()->count())->toBe(0);
});

test('nobody can reply to a closed ticket', function () {
    $ticket = Ticket::factory()->closed()->create();

    $this->actingAs($ticket->user)
        ->post("/tickets/{$ticket->id}/replies", ['message' => 'One more thing'])
        ->assertForbidden();

    $this->actingAs($ticket->user)
        ->get("/tickets/{$ticket->id}")
        ->assertSee('This ticket is closed, so no new replies can be added.');
});

test('a reply needs a message', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->actingAs($ticket->user)
        ->post("/tickets/{$ticket->id}/replies", ['message' => ''])
        ->assertSessionHasErrors(['message' => 'The message field is required.']);
});
