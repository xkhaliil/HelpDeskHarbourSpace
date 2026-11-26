<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\FilterTicketsRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Show the tickets of the logged-in user, optionally filtered by status.
     */
    public function index(FilterTicketsRequest $request): View|RedirectResponse
    {
        // Admins see all tickets in the admin panel.
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.tickets.index');
        }

        $query = $request->user()->tickets()->with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->validated('status'));
        }

        return view('tickets.index', [
            'tickets' => $query->paginate(10)->withQueryString(),
            'statuses' => TicketStatus::cases(),
            'currentStatus' => $request->validated('status'),
        ]);
    }

    /**
     * Show the form to report a new problem.
     */
    public function create(): View
    {
        return view('tickets.create', [
            'categories' => Category::orderBy('name')->get(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    /**
     * Store a new ticket for the logged-in user.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Only teachers may choose a priority. For students it is always "normal",
        // even when somebody adds a priority field to the request by hand.
        if ($request->user()->isTeacher()) {
            $data['priority'] = $request->validated('priority') ?? TicketPriority::Normal->value;
        } else {
            $data['priority'] = TicketPriority::Normal->value;
        }

        $ticket = $request->user()->tickets()->create($data);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Your ticket has been created. The IT and campus team will look at it soon.');
    }

    /**
     * Show one ticket of the logged-in user.
     */
    public function show(Ticket $ticket): View|RedirectResponse
    {
        // Admins handle tickets on their own page in the admin panel.
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.tickets.show', $ticket);
        }

        Gate::authorize('view', $ticket);

        $ticket->load(['category', 'replies.user']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form to edit an open ticket.
     */
    public function edit(Ticket $ticket): View|RedirectResponse
    {
        // Admins handle tickets on their own page in the admin panel.
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.tickets.show', $ticket);
        }

        Gate::authorize('update', $ticket);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'categories' => Category::orderBy('name')->get(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    /**
     * Update an open ticket.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('update', $ticket);

        $data = $request->safe()->except(['priority']);

        // Teachers may change the priority. For students it stays what it was.
        if ($request->user()->isTeacher() && $request->filled('priority')) {
            $data['priority'] = $request->validated('priority');
        }

        $ticket->update($data);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Your ticket has been updated.');
    }

    /**
     * Close a solved ticket: the owner confirms that the problem is gone.
     */
    public function close(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('close', $ticket);

        $ticket->update(['status' => TicketStatus::Closed]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Thanks for confirming! The ticket is now closed.');
    }

    /**
     * Delete an open ticket.
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Your ticket has been deleted.');
    }
}
