<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // The email address decides the role (see config/helpdesk.php).
        // Students and teachers with their university address can start right away,
        // every other account has to be approved by an admin first.
        if (Str::endsWith($request->email, '@'.config('helpdesk.teacher_email_domain'))) {
            $role = Role::Teacher;
            $approvedAt = now();
        } elseif (Str::endsWith($request->email, '@'.config('helpdesk.student_email_domain'))) {
            $role = Role::Student;
            $approvedAt = now();
        } else {
            $role = Role::Student;
            $approvedAt = null;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'approved_at' => $approvedAt,
        ]);

        event(new Registered($user));

        if (! $user->isApproved()) {
            return redirect()->route('login')
                ->with('status', 'Your account has been created. An administrator has to approve it before you can log in.');
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
