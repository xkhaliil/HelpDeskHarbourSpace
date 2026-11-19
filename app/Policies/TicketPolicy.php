<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Students and teachers can only see their own tickets.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id === $user->id;
    }

    /**
     * The owner can only edit a ticket while it is still open,
     * so nothing changes after the IT team has started working on it.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id === $user->id && $ticket->status === TicketStatus::Open;
    }

    /**
     * Same rule as editing: only the owner, and only while the ticket is open.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id === $user->id && $ticket->status === TicketStatus::Open;
    }

    /**
     * The owner and the admins can talk about a ticket until it is closed.
     */
    public function reply(User $user, Ticket $ticket): bool
    {
        if ($ticket->status === TicketStatus::Closed) {
            return false;
        }

        return $ticket->user_id === $user->id || $user->isAdmin();
    }
}
