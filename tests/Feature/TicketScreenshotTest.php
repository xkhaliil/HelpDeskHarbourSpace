<?php

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('a screenshot can be uploaded with a new ticket', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)->post('/tickets', [
        'title' => 'Projector does not turn on',
        'category_id' => Category::factory()->create()->id,
        'location' => 'Room 2.03',
        'description' => 'The light on the projector stays red.',
        'screenshot' => UploadedFile::fake()->image('projector.png'),
    ])->assertSessionHasNoErrors();

    $ticket = Ticket::firstOrFail();
    expect($ticket->screenshot)->toStartWith('screenshots/');
    Storage::disk('public')->assertExists($ticket->screenshot);

    $this->actingAs($student)
        ->get("/tickets/{$ticket->id}")
        ->assertSee('storage/'.$ticket->screenshot);
});

test('the screenshot must be an image of at most 2 MB', function (UploadedFile $file, string $message) {
    $this->actingAs(User::factory()->create())
        ->post('/tickets', [
            'title' => 'Projector does not turn on',
            'category_id' => Category::factory()->create()->id,
            'location' => 'Room 2.03',
            'description' => 'The light on the projector stays red.',
            'screenshot' => $file,
        ])
        ->assertSessionHasErrors(['screenshot' => $message]);
})->with([
    'not an image' => fn () => [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'), 'The screenshot field must be an image.'],
    'too large' => fn () => [UploadedFile::fake()->image('big.png')->size(3000), 'The screenshot field must not be greater than 2048 kilobytes.'],
]);

test('a new screenshot replaces the old file', function () {
    $oldFile = UploadedFile::fake()->image('old.png')->store('screenshots', 'public');
    $ticket = Ticket::factory()->open()->create(['screenshot' => $oldFile]);

    $this->actingAs($ticket->user)->put("/tickets/{$ticket->id}", [
        'title' => $ticket->title,
        'category_id' => $ticket->category_id,
        'location' => $ticket->location,
        'description' => $ticket->description,
        'screenshot' => UploadedFile::fake()->image('new.png'),
    ])->assertSessionHasNoErrors();

    Storage::disk('public')->assertMissing($oldFile);
    Storage::disk('public')->assertExists($ticket->refresh()->screenshot);
});

test('deleting a ticket removes its screenshot', function () {
    $file = UploadedFile::fake()->image('screen.png')->store('screenshots', 'public');
    $ticket = Ticket::factory()->open()->create(['screenshot' => $file]);

    $this->actingAs($ticket->user)->delete("/tickets/{$ticket->id}");

    Storage::disk('public')->assertMissing($file);
});

test('the ticket page still works when the screenshot file is missing', function () {
    $ticket = Ticket::factory()->create(['screenshot' => 'screenshots/missing.png']);

    $this->actingAs($ticket->user)
        ->get("/tickets/{$ticket->id}")
        ->assertOk()
        ->assertDontSee('screenshots/missing.png');
});
