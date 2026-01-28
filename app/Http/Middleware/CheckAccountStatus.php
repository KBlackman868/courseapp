<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     * Check if the user's account is active before allowing access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user has pending account status
            if ($user->account_status === User::STATUS_PENDING) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error',
                    'Your account is pending approval. Please wait for an administrator to review your registration.'
                );
            }

            // Check if user has inactive account status
            if ($user->account_status === User::STATUS_INACTIVE) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error',
                    'Your account has been deactivated. Please contact an administrator for assistance.'
                );
            }
        }

        return $next($request);
    }
}
