<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Enforces:
     * 1. Credentials must be valid. If invalid (wrong password or non-existent email),
     *    return standard credential error without revealing whether the email exists.
     * 2. If credentials match, evaluate account status BEFORE creating a session.
     *    - 'pending': refuse login with clear message, NO session created.
     *    - 'rejected': refuse login with clear message, NO session created.
     *    - 'approved': log user in, regenerate session, and redirect to catalog.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Step 1: Validate credentials without creating a session
        if (! Auth::validate($credentials)) {
            // Do not reveal whether the email exists
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Step 2: Credentials are valid. Retrieve user to check approval status
        $user = User::where('email', $credentials['email'])->first();

        // Step 3: Check account status before establishing session
        if ($user->isPending()) {
            throw ValidationException::withMessages([
                'email' => 'Your account is pending administrator approval.',
            ]);
        }

        if ($user->isRejected()) {
            throw ValidationException::withMessages([
                'email' => 'Your account registration has been rejected. Please contact support.',
            ]);
        }

        if (! $user->isApproved()) {
            throw ValidationException::withMessages([
                'email' => 'Your account is not approved to access the wholesale platform.',
            ]);
        }

        // Step 4: User is approved. Log in, regenerate session
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('catalog.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog.index');
    }
}
