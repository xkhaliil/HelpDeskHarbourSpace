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

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->validated('search').'%');
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
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('role', $request->validated('role'));
            });
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
        $ticket->load(['user', 'category', 'replies.user']);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::cases(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    /**
     * Change the status and the priority of a ticket.
     */
    public function update(AdminUpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->validated());

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'The ticket has been updated.');
    }
}
