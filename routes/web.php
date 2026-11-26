<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
 * Public Website routes
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

// Everybody can see the categories, to know what can be reported
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

/*
 * Authentication routes
 */
require __DIR__.'/auth.php';

/*
 * Routes for logged-in users
 */
Route::middleware('auth')->group(function () {
    // For the user's dashboard (after login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students and teachers manage their own tickets (see App\Policies\TicketPolicy)
    Route::resource('tickets', TicketController::class);
    Route::patch('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
    Route::post('/tickets/{ticket}/replies', [ReplyController::class, 'store'])->name('tickets.replies.store');

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

    Route::resource('tickets', Admin\TicketController::class)->only(['index', 'show', 'update']);

    Route::resource('categories', Admin\CategoryController::class);

    Route::resource('users', Admin\UserController::class)->only(['index', 'update']);
    Route::patch('/users/{user}/approve', [Admin\UserController::class, 'approve'])->name('users.approve');
});
