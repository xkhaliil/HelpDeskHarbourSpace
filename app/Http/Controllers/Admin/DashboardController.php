<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard: tickets per status, the tickets of this admin
     * and the latest important tickets.
     */
    public function index(Request $request): View
    {
        // The unsolved tickets that the logged-in admin handles
        // (the other side of the many-to-many relationship, see User::assignedTickets()).
        $assignedTickets = $request->user()->assignedTickets()
            ->with(['user', 'category'])
            ->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])
            ->latest()
            ->take(5)
            ->get();

        $countsPerStatus = [];
        foreach (TicketStatus::cases() as $status) {
            $countsPerStatus[$status->value] = Ticket::where('status', $status)->count();
        }

        $urgentTickets = Ticket::with(['user', 'category'])
            ->where('priority', TicketPriority::Urgent)
            ->latest()
            ->take(5)
            ->get();

        $highTickets = Ticket::with(['user', 'category'])
            ->where('priority', TicketPriority::High)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'pendingUsersCount' => User::whereNull('approved_at')->count(),
            'statuses' => TicketStatus::cases(),
            'countsPerStatus' => $countsPerStatus,
            'assignedTickets' => $assignedTickets,
            'urgentTickets' => $urgentTickets,
            'highTickets' => $highTickets,
        ]);
    }
}
