<?php

use App\Enums\TicketPriority;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;

test('only teachers see the priority field', function () {
    $this->actingAs(User::factory()->teacher()->create())
        ->get('/tickets/create')
        ->assertSee('name="priority"', escape: false);

    $this->actingAs(User::factory()->student()->create())
        ->get('/tickets/create')
        ->assertDontSee('name="priority"', escape: false);
});

test('teachers can choose the priority of a new ticket', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)->post('/tickets', [
        'title' => 'Projector does not turn on',
        'category_id' => Category::factory()->create()->id,
        'location' => 'Lecture Hall A',
        'description' => 'My lecture starts in ten minutes.',
        'priority' => 'urgent',
    ])->assertSessionHasNoErrors();

    expect(Ticket::firstOrFail()->priority)->toBe(TicketPriority::Urgent);
});

test('a student ticket is always normal, even when a priority is sent', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)->post('/tickets', [
        'title' => 'Projector does not turn on',
        'category_id' => Category::factory()->create()->id,
        'location' => 'Lecture Hall A',
        'description' => 'The light on the projector stays red.',
        'priority' => 'urgent',
    ])->assertSessionHasNoErrors();

    expect(Ticket::firstOrFail()->priority)->toBe(TicketPriority::Normal);
});

test('the priority must be one of the known values', function () {
    $this->actingAs(User::factory()->teacher()->create())
        ->post('/tickets', [
            'title' => 'Projector does not turn on',
            'category_id' => Category::factory()->create()->id,
            'location' => 'Lecture Hall A',
            'description' => 'The light on the projector stays red.',
            'priority' => 'very-urgent',
        ])
        ->assertSessionHasErrors(['priority' => 'The selected priority is invalid.']);
});

test('teachers can change the priority when they edit a ticket', function () {
    $teacher = User::factory()->teacher()->create();
    $ticket = Ticket::factory()->for($teacher)->open()->create(['priority' => TicketPriority::Normal]);

    $this->actingAs($teacher)->put("/tickets/{$ticket->id}", [
        'title' => $ticket->title,
        'category_id' => $ticket->category_id,
        'location' => $ticket->location,
        'description' => $ticket->description,
        'priority' => 'high',
    ])->assertSessionHasNoErrors();

    expect($ticket->refresh()->priority)->toBe(TicketPriority::High);
});

test('students cannot change the priority when they edit a ticket', function () {
    $student = User::factory()->student()->create();
    $ticket = Ticket::factory()->for($student)->open()->create(['priority' => TicketPriority::Normal]);

    $this->actingAs($student)->put("/tickets/{$ticket->id}", [
        'title' => $ticket->title,
        'category_id' => $ticket->category_id,
        'location' => $ticket->location,
        'description' => $ticket->description,
        'priority' => 'urgent',
    ])->assertSessionHasNoErrors();

    expect($ticket->refresh()->priority)->toBe(TicketPriority::Normal);
});
