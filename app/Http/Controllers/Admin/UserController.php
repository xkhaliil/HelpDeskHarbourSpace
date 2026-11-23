<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * The accounts waiting for approval, and all approved users
     * with their role and the number of tickets they reported.
     */
    public function index(): View
    {
        $pendingUsers = User::whereNull('approved_at')->oldest()->get();

        $users = User::whereNotNull('approved_at')
            ->withCount('tickets')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', [
            'pendingUsers' => $pendingUsers,
            'users' => $users,
            'roles' => Role::cases(),
        ]);
    }

    /**
     * Change the role of a user.
     */
    public function update(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        // Admins cannot change their own role, so there is always at least one admin left.
        if ($user->is($request->user())) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->validated('role')]);

        return redirect()->route('admin.users.index')
            ->with('success', "The role of {$user->name} is now: {$user->role->label()}.");
    }

    /**
     * Approve an account that registered without a student or teacher address,
     * and give it a role at the same time.
     */
    public function approve(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        if ($user->isApproved()) {
            return redirect()->route('admin.users.index')
                ->with('error', "The account of {$user->name} is already approved.");
        }

        $user->update([
            'role' => $request->validated('role'),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "The account of {$user->name} is approved as {$user->role->label()}. This person can log in now.");
    }
}
