<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use App\Models\Reply;
use App\Models\Ticket;
use App\Notifications\TicketReplied;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The replies in the conversation of a ticket.
 * There is no index or show page: the replies are read on the ticket page.
 */
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

    /**
     * Show the form for changing a reply.
     */
    public function edit(Reply $reply): View
    {
        Gate::authorize('update', $reply);

        return view('replies.edit', compact('reply'));
    }

    /**
     * Save the changed message of a reply.
     */
    public function update(UpdateReplyRequest $request, Reply $reply): RedirectResponse
    {
        Gate::authorize('update', $reply);

        $reply->update(['message' => $request->validated('message')]);

        // Back to the ticket page. Admins are sent on to their own ticket page by TicketController.
        return redirect()->route('tickets.show', $reply->ticket_id)
            ->with('success', 'Your reply has been updated.');
    }

    /**
     * Delete a reply.
     */
    public function destroy(Reply $reply): RedirectResponse
    {
        Gate::authorize('delete', $reply);

        $reply->delete();

        return redirect()->route('tickets.show', $reply->ticket_id)
            ->with('success', 'Your reply has been deleted.');
    }
}
