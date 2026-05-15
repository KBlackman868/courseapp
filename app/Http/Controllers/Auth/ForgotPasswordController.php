<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use App\Mail\CustomResetPasswordEmail;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Send password reset link (silently succeeds even if email not found)
        Password::sendResetLink($request->only('email'));

        // Log the password reset attempt
        \Log::info('Password reset requested', ['email' => $request->email]);

        // Always return the same response to prevent account enumeration
        return back()->with(['status' => 'If an account exists with that email, a password reset link has been sent. Please check your inbox.']);
    }
}