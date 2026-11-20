<?php

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;

test('students cannot open the list of all tickets', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get('/admin/tickets')
        ->assertForbidden();
});

test('admins see the tickets of everybody', function () {
    Ticket::factory()->for(User::factory()->student())->create(['title' => 'A student ticket']);
    Ticket::factory()->for(User::factory()->teacher())->create(['title' => 'A teacher ticket']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/tickets')
        ->assertOk()
        ->assertSee('A student ticket')
        ->assertSee('A teacher ticket');
});

test('admins can search tickets by title', function () {
    Ticket::factory()->create(['title' => 'Projector does not turn on']);
    Ticket::factory()->create(['title' => 'Wi-Fi keeps disconnecting']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/tickets?search=projector')
        ->assertOk()
        ->assertSee('Projector does not turn on')
        ->assertDontSee('Wi-Fi keeps disconnecting');
});

test('admins can filter tickets', function (array $matching, array $other, array $filter) {
    Ticket::factory()->create(['title' => 'The matching ticket', ...$matching]);
    Ticket::factory()->create(['title' => 'The other ticket', ...$other]);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/tickets?'.http_build_query($filter))
        ->assertOk()
        ->assertSee('The matching ticket')
        ->assertDontSee('The other ticket');
})->with([
    'by status' => fn () => [['status' => 'solved'], ['status' => 'open'], ['status' => 'solved']],
    'by priority' => fn () => [['priority' => 'urgent'], ['priority' => 'normal'], ['priority' => 'urgent']],
]);

test('admins can filter tickets by category', function () {
    $network = Category::factory()->create(['name' => 'Wi-Fi & Network']);
    $building = Category::factory()->create(['name' => 'Building & Facilities']);
    Ticket::factory()->for($network)->create(['title' => 'No internet on the 3rd floor']);
    Ticket::factory()->for($building)->create(['title' => 'Broken chair']);

    $this->actingAs(User::factory()->admin()->create())
        ->get("/admin/tickets?category_id={$network->id}")
        ->assertSee('No internet on the 3rd floor')
        ->assertDontSee('Broken chair');
});

test('admins can filter tickets by the role of the creator', function () {
    Ticket::factory()->for(User::factory()->student())->create(['title' => 'A student ticket']);
    Ticket::factory()->for(User::factory()->teacher())->create(['title' => 'A teacher ticket']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/tickets?role=teacher')
        ->assertSee('A teacher ticket')
        ->assertDontSee('A student ticket');
});

test('the list of all tickets is paginated', function () {
    Ticket::factory()->count(16)->create();

    $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/tickets');

    expect($response->viewData('tickets')->count())->toBe(15)
        ->and($response->viewData('tickets')->total())->toBe(16);
});

test('an invalid filter is refused', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/tickets?priority=whenever')
        ->assertRedirect('/admin/tickets')
        ->assertSessionHasErrors(['priority' => 'The selected priority is invalid.']);
});
