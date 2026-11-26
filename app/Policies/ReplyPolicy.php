<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Reply;
use App\Models\User;

/**
 * Who may change or delete a reply. Laravel finds this policy by itself,
 * because it is called "ReplyPolicy" and lives in app/Policies.
 */
class ReplyPolicy
{
    /**
     * Only the person who wrote the reply can change it,
     * and only while the ticket is not closed (a closed conversation stays as it is).
     */
    public function update(User $user, Reply $reply): bool
    {
        return $reply->user_id === $user->id && $reply->ticket->status !== TicketStatus::Closed;
    }

    /**
     * Deleting a reply follows the same rule as changing it.
     */
    public function delete(User $user, Reply $reply): bool
    {
        return $this->update($user, $reply);
    }
}
