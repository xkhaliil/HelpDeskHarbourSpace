<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the public welcome page.
     */
    public function index(): View
    {
        return view('home', [
            'categories' => Category::orderBy('name')->get(),
            'statuses' => TicketStatus::cases(),
        ]);
    }
}
