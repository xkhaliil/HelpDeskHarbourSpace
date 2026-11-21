<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard of a student or teacher.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        // Admins have their own dashboard in the admin panel.
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard', [
            'openCount' => $user->tickets()->where('status', TicketStatus::Open)->count(),
            'inProgressCount' => $user->tickets()->where('status', TicketStatus::InProgress)->count(),
            'solvedCount' => $user->tickets()->where('status', TicketStatus::Solved)->count(),
            'latestTickets' => $user->tickets()->with('category')->latest()->take(6)->get(),
        ]);
    }
}
