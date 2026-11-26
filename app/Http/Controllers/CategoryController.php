<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Category;
use Illuminate\View\View;

/**
 * The public category pages: everybody can see what can be reported, also without logging in.
 * The admin manages the categories in App\Http\Controllers\Admin\CategoryController.
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        // withCount adds a "tickets_count" to every category, with one extra query.
        $categories = Category::withCount('tickets')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Display the category, with the number of tickets per status.
     */
    public function show(Category $category): View
    {
        // Only the numbers are public: the tickets themselves are private.
        // For example: ['open' => 3, 'in_progress' => 1, 'solved' => 5, 'closed' => 2].
        $countsPerStatus = [];
        foreach (TicketStatus::cases() as $status) {
            $countsPerStatus[$status->value] = $category->tickets()->where('status', $status)->count();
        }

        return view('categories.show', [
            'category' => $category,
            'statuses' => TicketStatus::cases(),
            'countsPerStatus' => $countsPerStatus,
        ]);
    }
}
