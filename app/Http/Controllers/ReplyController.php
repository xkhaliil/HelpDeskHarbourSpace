<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReplyRequest;
use App\Models\Ticket;
use App\Notifications\TicketReplied;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ReplyController extends Controller
{
    /**
     * Add a reply to the conversation of a ticket.
     * Used by the ticket owner and by the admins.
     */
    public function store(StoreReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('reply', $ticket);

        $reply = $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
        ]);

        // When somebody of the IT staff replies, the owner of the ticket gets an email.
        // The owner does not get an email about their own reply.
        if ($request->user()->isAdmin()) {
            $ticket->user->notify(new TicketReplied($ticket, $reply));
        }

        return back()->with('success', 'Your reply has been sent.');
    }
}
