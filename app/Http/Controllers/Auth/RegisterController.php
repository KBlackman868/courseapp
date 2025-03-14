<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Where to redirect after registration.
    protected $redirectTo = '/home';

    // Show the registration form.
    public function showRegistrationForm()
    {
        return view('pages.home_register');
    }

    // Handle the registration request.
    public function register(Request $request)
    {
        // Validate input.
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'department' => 'required|string|max:255',
        ]);

        // Create the user.
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name'  => $validatedData['last_name'],
            'email'      => $validatedData['email'],
            'password'   => Hash::make($validatedData['password']),
            'department' => $validatedData['department'],
        ]);

        // Log the user in.
        Auth::login($user);

        // Flash a success message.
        session()->flash('success', "You've successfully registered!");

        // Redirect to the dashboard.
        return redirect($this->redirectTo);
    }
}
