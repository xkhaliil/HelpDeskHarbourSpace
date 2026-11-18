<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
 * Public Website routes
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
 * Authentication routes
 */
require __DIR__.'/auth.php';

/*
 * Routes for logged-in users
 */
Route::middleware('auth')->group(function () {
    // For the user's dashboard (after login)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // For the user's profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
 * Admin routes: only for users with the admin role (see App\Http\Middleware\IsAdmin)
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', Admin\CategoryController::class);
});
