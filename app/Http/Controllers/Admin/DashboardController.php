<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard: tickets per status and the latest important tickets.
     */
    public function index(): View
    {
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
            'urgentTickets' => $urgentTickets,
            'highTickets' => $highTickets,
        ]);
    }
}
