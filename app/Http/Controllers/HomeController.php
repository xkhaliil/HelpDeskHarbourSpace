<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the public welcome page.
     */
    public function index(): View
    {
        // The categories rarely change, so they are kept in the cache for one hour.
        // Only the first visitor runs the query, the next visitors get the cached list.
        // When an admin changes a category, Admin\CategoryController removes the cached list.
        $categories = Cache::remember(Category::HOME_PAGE_CACHE_KEY, now()->addHour(), function () {
            return Category::orderBy('name')->get();
        });

        return view('home', [
            'categories' => $categories,
            'statuses' => TicketStatus::cases(),
        ]);
    }
}
