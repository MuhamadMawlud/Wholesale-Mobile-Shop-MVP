<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerIsApproved
{
    /**
     * Handle an incoming request.
     * Enforces:
     * - Guests are redirected to the login page.
     * - Authenticated users must have status 'approved' or be an administrator.
     *   Unapproved (pending / rejected) users receive a 403 Forbidden response.
     * - Status is verified freshly from the database on every request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();
        if ($user && $user->exists) {
            $user = $user->fresh() ?? $user;
        }

        if (! $user || ! $user->canSeePrices()) {
            abort(403, 'Your account is not approved to access this wholesale feature.');
        }

        return $next($request);
    }
}
