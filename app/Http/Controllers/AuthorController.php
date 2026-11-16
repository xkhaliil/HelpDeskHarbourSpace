<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class AuthorController extends Controller
{
    public function index(): View
    {
        $authors = User::query()
            ->whereHas('articles')
            ->withCount('articles')
            ->orderBy('name') // Todo: tbd if this is the right order
            ->get();

        return view('authors.index', compact('authors'));
    }

    public function show(int $id): View
    {
        $author = User::query()
            ->whereHas('articles')
            ->findOrFail($id);

        return view('authors.show', compact('author'));
    }
}
