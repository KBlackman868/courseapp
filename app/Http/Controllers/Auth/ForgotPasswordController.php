<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // Display the form to request a password reset link
    public function showLinkRequestForm()
    {
        return view('pages.forgot_password');
    }

    // Handle sending the password reset link email.
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email input
        $request->validate(['email' => 'required|email']);

        // Try to send the password reset link.
        $status = Password::sendResetLink($request->only('email'));

        // Return with a success or error message:
        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
