<?php

use App\Enums\TicketStatus;
use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketReplied;
use Illuminate\Support\Facades\Notification;

test('the owner gets an email when an admin replies to the ticket', function () {
    $ticket = Ticket::factory()->inProgress()->create();
    $admin = User::factory()->admin()->create();
    Notification::fake();

    $this->actingAs($admin)
        ->post("/tickets/{$ticket->id}/replies", ['message' => 'A technician is on the way.'])
        ->assertSessionHasNoErrors();

    Notification::assertSentTo($ticket->user, TicketReplied::class, function (TicketReplied $notification) {
        return $notification->reply->message === 'A technician is on the way.';
    });
});

test('the owner does not get an email about their own reply', function () {
    $ticket = Ticket::factory()->open()->create();
    Notification::fake();

    $this->actingAs($ticket->user)
        ->post("/tickets/{$ticket->id}/replies", ['message' => 'It happened again this morning.'])
        ->assertSessionHasNoErrors();

    Notification::assertNothingSent();
});

test('the email contains the ticket, the reply and a link to the ticket', function () {
    $owner = User::factory()->student()->create(['name' => 'Lucas Moreau']);
    $ticket = Ticket::factory()->for($owner)->create([
        'title' => 'Projector does not turn on',
        'status' => TicketStatus::InProgress,
    ]);
    $admin = User::factory()->admin()->create(['name' => 'Campus Admin']);
    $reply = Reply::factory()->for($ticket)->for($admin)->create(['message' => 'A technician is on the way.']);

    $mail = (new TicketReplied($ticket, $reply))->toMail($owner);

    expect($mail->subject)->toBe('New reply on your ticket: Projector does not turn on')
        ->and($mail->greeting)->toBe('Hello Lucas Moreau,')
        ->and($mail->introLines)->toBe([
            'Campus Admin from the IT and campus team replied to your ticket "Projector does not turn on":',
            '"A technician is on the way."',
            'The status of your ticket is now: In progress.',
        ])
        ->and($mail->actionUrl)->toBe(route('tickets.show', $ticket));
});
