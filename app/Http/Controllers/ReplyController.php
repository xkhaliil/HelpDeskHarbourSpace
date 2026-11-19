<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReplyRequest;
use App\Models\Ticket;
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

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
        ]);

        return back()->with('success', 'Your reply has been sent.');
    }
}
