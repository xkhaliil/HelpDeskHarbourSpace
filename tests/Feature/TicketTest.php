<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/tickets')->assertRedirect('/login');
});

test('users only see their own tickets', function () {
    $student = User::factory()->student()->create();
    Ticket::factory()->for($student)->create(['title' => 'My projector is broken']);
    Ticket::factory()->create(['title' => 'Somebody else has no Wi-Fi']);

    $this->actingAs($student)
        ->get('/tickets')
        ->assertOk()
        ->assertSee('My projector is broken')
        ->assertDontSee('Somebody else has no Wi-Fi');
});

test('users can filter their tickets by status', function () {
    $student = User::factory()->student()->create();
    Ticket::factory()->for($student)->open()->create(['title' => 'An open problem']);
    Ticket::factory()->for($student)->solved()->create(['title' => 'A solved problem']);

    $this->actingAs($student)
        ->get('/tickets?status=solved')
        ->assertOk()
        ->assertSee('A solved problem')
        ->assertDontSee('An open problem');
});

test('an unknown status filter is refused', function () {
    $this->actingAs(User::factory()->create())
        ->get('/tickets?status=lost')
        ->assertRedirect('/tickets')
        ->assertSessionHasErrors(['status' => 'The selected status is invalid.']);
});

test('the create page lists the categories', function () {
    Category::factory()->create(['name' => 'Classroom Equipment']);

    $this->actingAs(User::factory()->create())
        ->get('/tickets/create')
        ->assertOk()
        ->assertSee('Classroom Equipment');
});

test('users can report a problem', function () {
    $student = User::factory()->student()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($student)->post('/tickets', [
        'title' => 'Projector does not turn on',
        'category_id' => $category->id,
        'location' => 'Room 2.03',
        'description' => 'The light on the projector stays red.',
    ]);

    $ticket = Ticket::firstOrFail();
    $response->assertRedirect("/tickets/{$ticket->id}")
        ->assertSessionHas('success');

    $ticket->refresh();
    expect($ticket->user_id)->toBe($student->id)
        ->and($ticket->category_id)->toBe($category->id)
        ->and($ticket->status)->toBe(TicketStatus::Open)
        ->and($ticket->priority)->toBe(TicketPriority::Normal);
});

test('a ticket needs a title, category, location and description', function () {
    $this->actingAs(User::factory()->create())
        ->post('/tickets', [])
        ->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'category_id' => 'The category field is required.',
            'location' => 'The location field is required.',
            'description' => 'The description field is required.',
        ]);
});

test('a ticket needs an existing category', function () {
    $this->actingAs(User::factory()->create())
        ->post('/tickets', [
            'title' => 'Projector does not turn on',
            'category_id' => 999,
            'location' => 'Room 2.03',
            'description' => 'The light on the projector stays red.',
        ])
        ->assertSessionHasErrors(['category_id' => 'The selected category is invalid.']);
});

test('owners can view their ticket', function () {
    $ticket = Ticket::factory()->create(['title' => 'Broken chair']);

    $this->actingAs($ticket->user)
        ->get("/tickets/{$ticket->id}")
        ->assertOk()
        ->assertSee('Broken chair');
});

test('users cannot view, edit or delete the ticket of somebody else', function () {
    $ticket = Ticket::factory()->open()->create();
    $otherStudent = User::factory()->student()->create();

    $this->actingAs($otherStudent)->get("/tickets/{$ticket->id}")->assertForbidden();
    $this->actingAs($otherStudent)->get("/tickets/{$ticket->id}/edit")->assertForbidden();
    $this->actingAs($otherStudent)->delete("/tickets/{$ticket->id}")->assertForbidden();

    $this->assertModelExists($ticket);
});

test('owners can update an open ticket', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->actingAs($ticket->user)->get("/tickets/{$ticket->id}/edit")->assertOk();

    $this->actingAs($ticket->user)
        ->put("/tickets/{$ticket->id}", [
            'title' => 'Projector still does not turn on',
            'category_id' => $ticket->category_id,
            'location' => 'Room 3.05',
            'description' => 'We also tried another power socket.',
        ])
        ->assertRedirect("/tickets/{$ticket->id}")
        ->assertSessionHas('success', 'Your ticket has been updated.');

    expect($ticket->refresh()->title)->toBe('Projector still does not turn on')
        ->and($ticket->location)->toBe('Room 3.05');
});

test('owners can delete an open ticket', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->actingAs($ticket->user)
        ->delete("/tickets/{$ticket->id}")
        ->assertRedirect('/tickets')
        ->assertSessionHas('success', 'Your ticket has been deleted.');

    $this->assertModelMissing($ticket);
});

test('deleting a ticket also deletes its replies', function () {
    $ticket = Ticket::factory()->open()->create();
    $reply = Reply::factory()->for($ticket)->fromStaff()->create();

    $this->actingAs($ticket->user)->delete("/tickets/{$ticket->id}");

    $this->assertModelMissing($reply);
});

test('owners can close a solved ticket', function () {
    $ticket = Ticket::factory()->solved()->create();

    $this->actingAs($ticket->user)
        ->get("/tickets/{$ticket->id}")
        ->assertSee('Yes, close the ticket');

    $this->actingAs($ticket->user)
        ->patch("/tickets/{$ticket->id}/close")
        ->assertRedirect("/tickets/{$ticket->id}")
        ->assertSessionHas('success');

    expect($ticket->refresh()->status)->toBe(TicketStatus::Closed);
});

test('a ticket can only be closed when it is solved', function (string $status) {
    $ticket = Ticket::factory()->{$status}()->create();

    $this->actingAs($ticket->user)
        ->patch("/tickets/{$ticket->id}/close")
        ->assertForbidden();
})->with(['open', 'inProgress', 'closed']);

test('only the owner can close a ticket', function () {
    $ticket = Ticket::factory()->solved()->create();

    $this->actingAs(User::factory()->student()->create())
        ->patch("/tickets/{$ticket->id}/close")
        ->assertForbidden();

    expect($ticket->refresh()->status)->toBe(TicketStatus::Solved);
});

test('owners cannot edit or delete a ticket that is no longer open', function (string $status) {
    $ticket = Ticket::factory()->{$status}()->create();

    $this->actingAs($ticket->user)->get("/tickets/{$ticket->id}/edit")->assertForbidden();
    $this->actingAs($ticket->user)
        ->put("/tickets/{$ticket->id}", [
            'title' => 'A new title',
            'category_id' => $ticket->category_id,
            'location' => 'Room 3.05',
            'description' => 'A new description of the problem.',
        ])
        ->assertForbidden();
    $this->actingAs($ticket->user)->delete("/tickets/{$ticket->id}")->assertForbidden();

    $this->assertModelExists($ticket);
})->with(['inProgress', 'solved', 'closed']);
