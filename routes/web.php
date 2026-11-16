<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Userzone\DashboardController;
use App\Http\Controllers\Userzone\ProfileController;
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
 * Userzone routes
 */
Route::middleware('auth')->group(function () {
    // For the user's dashboard (after login)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // For the user's profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
