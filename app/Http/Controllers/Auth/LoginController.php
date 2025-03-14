<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Where to redirect users after login.
    protected $redirectTo = '/home';

    // Display the custom login form
    public function showLoginForm()
    {
        return view('pages.login');
    }

    // Handle a login request
    public function login(Request $request)
    {
        // Validate the login credentials
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate the session to prevent fixation
            $request->session()->regenerate();

            // Redirect the user to the intended page or dashboard
            return redirect()->intended($this->redirectTo);
        }

        // If authentication fails, redirect back with errors
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    // Logout the user
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
