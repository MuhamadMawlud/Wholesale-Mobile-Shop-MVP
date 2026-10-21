<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the wholesale customer registration form.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Always sets status to 'pending' and is_admin to false on the backend.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->company_name = $validated['company_name'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->password = Hash::make($validated['password']);

        // Explicitly set in code - never taken from user input
        $user->status = 'pending';
        $user->is_admin = false;
        $user->save();

        // Do NOT log the user in; redirect to pending notice page
        return redirect()->route('register.pending')->with(
            'status',
            'Your registration was submitted successfully! Your account is currently waiting for administrator approval.'
        );
    }

    /**
     * Show the registration pending confirmation notice.
     */
    public function pending(): View
    {
        return view('auth.pending');
    }
}
