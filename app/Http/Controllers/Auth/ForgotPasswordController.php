<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Mail\CustomResetPasswordEmail;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('pages.forgot_password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Send password reset link
        $status = Password::sendResetLink($request->only('email'));

        // Log the password reset attempt
        \Log::info('Password reset requested', ['email' => $request->email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => 'We have emailed your password reset link! Please check your inbox.'])
            : back()->withErrors(['email' => 'We couldn\'t find an account with that email address.']);
    }
}