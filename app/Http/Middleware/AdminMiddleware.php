<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Enforces:
     * - Guests are redirected to the login page.
     * - Authenticated non-admins receive a 403 Forbidden error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        if (! Auth::user()->is_admin) {
            abort(403, 'Unauthorized. Administrator access required.');
        }

        return $next($request);
    }
}
