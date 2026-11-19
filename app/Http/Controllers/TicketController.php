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
    public function index(FilterTicketsRequest $request): View
    {
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
        $data = $request->safe()->except(['screenshot', 'priority']);

        // Only teachers may choose a priority. For students it is always "normal",
        // even when somebody adds a priority field to the request by hand.
        if ($request->user()->isTeacher()) {
            $data['priority'] = $request->validated('priority') ?? TicketPriority::Normal->value;
        } else {
            $data['priority'] = TicketPriority::Normal->value;
        }

        if ($request->hasFile('screenshot')) {
            // Saved in storage/app/public/screenshots, visible after "php artisan storage:link".
            $data['screenshot'] = $request->file('screenshot')->store('screenshots', 'public');
        }

        $ticket = $request->user()->tickets()->create($data);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Your ticket has been created. The IT and campus team will look at it soon.');
    }

    /**
     * Show one ticket of the logged-in user.
     */
    public function show(Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load('category');

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form to edit an open ticket.
     */
    public function edit(Ticket $ticket): View
    {
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

        $data = $request->safe()->except(['screenshot', 'priority']);

        // Teachers may change the priority. For students it stays what it was.
        if ($request->user()->isTeacher() && $request->filled('priority')) {
            $data['priority'] = $request->validated('priority');
        }

        if ($request->hasFile('screenshot')) {
            // A new image replaces the old one.
            $ticket->deleteScreenshot();
            $data['screenshot'] = $request->file('screenshot')->store('screenshots', 'public');
        }

        $ticket->update($data);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Your ticket has been updated.');
    }

    /**
     * Delete an open ticket.
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('delete', $ticket);

        $ticket->deleteScreenshot();
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Your ticket has been deleted.');
    }
}
