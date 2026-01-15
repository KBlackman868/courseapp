<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$types  Allowed user types: 'internal', 'external', or 'any'
     */
    public function handle(Request $request, Closure $next, ...$types): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If 'any' is specified, allow all authenticated users
        if (in_array('any', $types)) {
            return $next($request);
        }

        // Check if user's type is in the allowed types
        if (!in_array($user->user_type, $types)) {
            abort(403, 'Access restricted to ' . implode(' or ', $types) . ' users only.');
        }

        return $next($request);
    }
}
