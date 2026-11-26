<?php

use App\Enums\TicketStatus;
use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\ReplyPolicy;

test('authors can change and delete their reply while the ticket is not closed', function (TicketStatus $status) {
    $reply = Reply::factory()->for(Ticket::factory()->create(['status' => $status]))->create();

    expect((new ReplyPolicy)->update($reply->user, $reply))->toBeTrue()
        ->and((new ReplyPolicy)->delete($reply->user, $reply))->toBeTrue();
})->with([
    'open' => TicketStatus::Open,
    'in progress' => TicketStatus::InProgress,
    'solved' => TicketStatus::Solved,
]);

test('authors cannot change or delete their reply on a closed ticket', function () {
    $reply = Reply::factory()->for(Ticket::factory()->closed()->create())->create();

    expect((new ReplyPolicy)->update($reply->user, $reply))->toBeFalse()
        ->and((new ReplyPolicy)->delete($reply->user, $reply))->toBeFalse();
});

test('nobody else can change or delete a reply, not even an admin', function (string $role) {
    $reply = Reply::factory()->for(Ticket::factory()->open()->create())->create();
    $otherUser = User::factory()->{$role}()->create();

    expect((new ReplyPolicy)->update($otherUser, $reply))->toBeFalse()
        ->and((new ReplyPolicy)->delete($otherUser, $reply))->toBeFalse();
})->with(['student', 'teacher', 'admin']);
