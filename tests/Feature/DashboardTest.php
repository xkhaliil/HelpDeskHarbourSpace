<?php

use App\Models\Ticket;
use App\Models\User;

test('the dashboard greets the user with their name and role', function () {
    $teacher = User::factory()->teacher()->create(['name' => 'Marta Vidal']);

    $this->actingAs($teacher)
        ->get('/dashboard')
        ->assertOk()
        ->assertSeeInOrder(['Hello', 'Marta Vidal', 'Teacher']);
});

test('the dashboard counts only the tickets of the user', function () {
    $student = User::factory()->student()->create();
    Ticket::factory()->count(2)->for($student)->open()->create();
    Ticket::factory()->for($student)->inProgress()->create();
    Ticket::factory()->count(3)->for($student)->solved()->create();
    Ticket::factory()->count(4)->open()->create();

    $this->actingAs($student)
        ->get('/dashboard')
        ->assertViewHas('openCount', 2)
        ->assertViewHas('inProgressCount', 1)
        ->assertViewHas('solvedCount', 3);
});

test('the dashboard shows the latest tickets of the user', function () {
    $student = User::factory()->student()->create();
    Ticket::factory()->for($student)->create(['title' => 'Projector does not turn on']);
    Ticket::factory()->create(['title' => 'Somebody else has no Wi-Fi']);

    $this->actingAs($student)
        ->get('/dashboard')
        ->assertSee('Projector does not turn on')
        ->assertDontSee('Somebody else has no Wi-Fi');
});
