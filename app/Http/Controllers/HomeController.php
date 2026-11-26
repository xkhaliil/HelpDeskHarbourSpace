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
        // The cache only keeps simple values: for safety, Laravel does not rebuild objects from the
        // cache (see "serializable_classes" in config/cache.php). That is why the categories are
        // kept as plain arrays with only the 3 columns the page needs, not as Category models.
        $categories = Cache::remember(Category::HOME_PAGE_CACHE_KEY, now()->addHour(), function () {
            return Category::orderBy('name')->get(['id', 'name', 'description'])->toArray();
        });

        return view('home', [
            'categories' => $categories,
            'statuses' => TicketStatus::cases(),
        ]);
    }
}
