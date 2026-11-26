<?php

use App\Models\Ticket;
use Illuminate\Database\Eloquent\MissingAttributeException;
use Illuminate\Database\LazyLoadingViolationException;

test('loading a relationship inside a loop throws an error outside production', function () {
    Ticket::factory()->count(2)->create();
    $tickets = Ticket::all();

    // The users were not loaded with with('user'), so this would be an N+1 problem.
    expect(fn () => $tickets->first()->user)->toThrow(LazyLoadingViolationException::class);
});

test('using a column that was not loaded throws an error outside production', function () {
    Ticket::factory()->create();
    $ticket = Ticket::with('user:id,name')->firstOrFail();

    expect(fn () => $ticket->user->email)->toThrow(MissingAttributeException::class);
});
