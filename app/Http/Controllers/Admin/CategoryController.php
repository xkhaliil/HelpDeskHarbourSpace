<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('tickets')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        // The welcome page has to show the new category, so its cached list is removed.
        Cache::forget(Category::HOME_PAGE_CACHE_KEY);

        return redirect()->route('admin.categories.index')
            ->with('success', 'The category has been created.');
    }

    /**
     * Display the category.
     */
    public function show(Category $category): View
    {
        $tickets = $category->tickets()->with('user')->latest()->paginate(10);

        return view('admin.categories.show', compact('category', 'tickets'));
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        // The welcome page has to show the new name and description.
        Cache::forget(Category::HOME_PAGE_CACHE_KEY);

        return redirect()->route('admin.categories.index')
            ->with('success', 'The category has been updated.');
    }

    /**
     * Remove the category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // A category that is still used by tickets cannot be deleted,
        // otherwise those tickets would point to a category that no longer exists.
        if ($category->tickets()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', "The category \"{$category->name}\" still has tickets, so it cannot be deleted.");
        }

        $category->delete();

        // The welcome page should no longer show the deleted category.
        Cache::forget(Category::HOME_PAGE_CACHE_KEY);

        return redirect()->route('admin.categories.index')
            ->with('success', 'The category has been deleted.');
    }
}
