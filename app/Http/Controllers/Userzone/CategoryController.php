<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return view('userzone.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('userzone.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('userzone.categories.index');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('userzone.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $category = Category::findOrFail($id);


        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('userzone.categories.index');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return redirect()->route('userzone.categories.index');
    }


}
