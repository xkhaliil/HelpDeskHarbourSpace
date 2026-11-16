<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Userzone\DashboardController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
 * Public Website routes
 */
Route::get('/', WelcomeController::class)->name('welcome');
Route::resource('articles', ArticleController::class)->only(['index', 'show']);
Route::resource('authors', AuthorController::class)->only(['index', 'show']);
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

Route::post('articles/{id}/comments', [CommentController::class, 'store'])->middleware('auth')->name('articles.comments.store');

/*
 * Authentication routes
 */
require __DIR__.'/auth.php';

/*
 * Userzone routes
 */
Route::middleware('auth')->group(function () {
    // For the user's dashboard (after login)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Normal user routes here ------------------------------------------------------------------
    Route::prefix('user')->name('userzone.')->group(function () {
        Route::resource('articles', App\Http\Controllers\Userzone\ArticleController::class)->except('show');
        Route::resource('comments', App\Http\Controllers\Userzone\CommentController::class)->except(['create', 'store']);
    });

    // Admin user routes -------------------------------------------------------------------------
    Route::prefix('user')->name('userzone.')->middleware(\App\Http\Middleware\IsAdmin::class)->group(function () {
        Route::resource('categories', App\Http\Controllers\Userzone\CategoryController::class)->except('show');
    });

    // For the user's profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
