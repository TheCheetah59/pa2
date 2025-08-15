<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActivated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->is_activated) {
            if ($request->expectsJson()) {
                abort(403, 'Account inactive.');
            }

            return redirect('/');
        }

        return $next($request);
    }
}