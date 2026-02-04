<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\CreateMoodleUserWithPassword;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the user in Laravel
        $nameParts = explode(' ', trim($request->name), 2);
        $user = User::create([
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? $nameParts[0],
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => 'General',
            'organization' => '',
        ]);

        // MOODLE INTEGRATION START
        // Parse the name for Moodle
        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : $nameParts[0];
        
        // Generate username from email
        $emailPart = strtolower(explode('@', $request->email)[0]);
        $username = preg_replace('/[^a-z0-9]/', '', $emailPart);
        
        // Ensure username is at least 2 characters
        if (strlen($username) < 2) {
            $username = 'user' . $user->id;
        }
        
        Log::info('Dispatching Moodle user creation job', [
            'user_id' => $user->id,
            'username' => $username,
            'email' => $request->email
        ]);

        try {
            // Dispatch the job to create user in Moodle with the plain password
            CreateMoodleUserWithPassword::dispatch(
                $user,
                $username,
                $request->password, // Plain password before hashing
                $firstName,
                $lastName
            );
            
            Log::info('Moodle job dispatched successfully', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            // Log error but don't fail registration
            Log::error('Failed to dispatch Moodle job', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        // MOODLE INTEGRATION END

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}