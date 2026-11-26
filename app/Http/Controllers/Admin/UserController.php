<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Show the form for creating a new account.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::cases(),
        ]);
    }

    /**
     * Store a new account. An account made by an admin can log in right away.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            // The "hashed" cast of the User model hashes the password before saving it.
            'password' => $request->validated('password'),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "The account of {$user->name} has been created.");
    }

    /**
     * Show one user, with the tickets they reported and the tickets they handle.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user,
            'tickets' => $user->tickets()->with('category')->latest()->get(),
            'assignedTickets' => $user->assignedTickets()->with('category')->latest()->get(),
        ]);
    }

    /**
     * Show the form for changing the name, the email address and the role of a user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::cases(),
        ]);
    }

    /**
     * Save the name, the email address and the role of a user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Admins cannot change their own role, so there is always at least one admin left.
        if ($user->is($request->user()) && $request->validated('role') !== $user->role->value) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'You cannot change your own role.');
        }

        $user->update($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', "The account of {$user->name} has been updated. Role: {$user->role->label()}.");
    }

    /**
     * Delete an account, for example somebody who registered by mistake.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Admins delete their own account on their profile page, not here.
        if ($user->is($request->user())) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account here. Use your profile page.');
        }

        // The tickets and replies of a user stay, so the history of the HelpDesk is complete.
        // That is why a user who still has tickets or replies cannot be deleted.
        if ($user->tickets()->exists() || $user->replies()->exists()) {
            return redirect()->route('admin.users.index')
                ->with('error', "{$user->name} still has tickets or replies, so the account cannot be deleted.");
        }

        // An admin may still handle tickets: those links in the pivot table are removed first.
        $user->assignedTickets()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "The account of {$user->name} has been deleted.");
    }

    /**
     * Approve an account that registered without a student or teacher address,
     * and give it a role at the same time.
     */
    public function approve(ApproveUserRequest $request, User $user): RedirectResponse
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
