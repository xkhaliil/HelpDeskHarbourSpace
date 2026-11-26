<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFilterTicketsRequest;
use App\Http\Requests\AdminUpdateTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * All tickets, with a search on the title and filters.
     */
    public function index(AdminFilterTicketsRequest $request): View
    {
        $query = Ticket::with(['user', 'category'])->latest();

        // whereLike ignores upper and lower case on every database (SQLite, MySQL and Postgres).
        if ($request->filled('search')) {
            $query->whereLike('title', '%'.$request->validated('search').'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->validated('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->validated('category_id'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->validated('priority'));
        }

        // The role of the person who created the ticket lives in the users table.
        if ($request->filled('role')) {
            $query->whereRelation('user', 'role', $request->validated('role'));
        }

        return view('admin.tickets.index', [
            'tickets' => $query->paginate(15)->withQueryString(),
            'filters' => $request->validated(),
            'categories' => Category::orderBy('name')->get(),
            'statuses' => TicketStatus::cases(),
            'priorities' => TicketPriority::cases(),
            'roles' => Role::cases(),
        ]);
    }

    /**
     * Show one ticket so an admin can handle it.
     */
    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'category', 'replies.user', 'assignees']);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::cases(),
            'priorities' => TicketPriority::cases(),
            // All admins, for the "Handled by" checkboxes.
            'admins' => User::where('role', Role::Admin)->orderBy('name')->get(),
        ]);
    }

    /**
     * Change the status, the priority and the admins who handle a ticket.
     */
    public function update(AdminUpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->safe()->only(['status', 'priority']));

        // sync() saves exactly the checked admins in the pivot table:
        // new ones are added, admins who are no longer checked are removed.
        // When no checkbox is checked, the form sends nothing, so the list is empty.
        $ticket->assignees()->sync($request->validated('assignees', []));

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'The ticket has been updated.');
    }
}
