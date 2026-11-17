<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard of a student or teacher.
     */
    public function __invoke(): View|RedirectResponse
    {
        // Admins have their own dashboard in the admin panel.
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard');
    }
}
