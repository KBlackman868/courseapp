<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    protected $moodleService;
    protected $redirectTo = '/home';

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    // Show the registration form
    public function showRegistrationForm()
    {
        return view('pages.home_register'); // or 'auth.register' if you created it
    }

    // Handle the registration request
    public function register(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'department' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        
        try {
            // Create the user in Laravel
            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name'  => $validatedData['last_name'],
                'email'      => $validatedData['email'],
                'password'   => Hash::make($validatedData['password']),
                'department' => $validatedData['department'],
            ]);

            // Assign default role
            $user->assignRole('user');

            // Sync with Moodle
            $moodleUserId = $this->syncUserToMoodle($user, $validatedData['password']);
            
            if ($moodleUserId) {
                // Store the Moodle user ID in the users table
                $user->moodle_user_id = $moodleUserId;
                $user->save();
                
                Log::info('User registered and synced to Moodle', [
                    'user_id' => $user->id,
                    'moodle_user_id' => $moodleUserId
                ]);
            } else {
                // Log warning but don't fail registration
                Log::warning('User registered but Moodle sync failed', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            DB::commit();

            // Log the user in
            Auth::login($user);

            session()->flash('success', "You've successfully registered!");

            return redirect($this->redirectTo);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $validatedData['email']
            ]);
            
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Sync user to Moodle
     */
    private function syncUserToMoodle($user, $plainPassword)
    {
        try {
            // Prepare user data for Moodle
            $userData = [
                'username' => strtolower(str_replace('@', '_', $user->email)), // Replace @ with _ for Moodle username
                'password' => $plainPassword,
                'firstname' => $user->first_name,
                'lastname' => $user->last_name,
                'email' => $user->email,
                'auth' => 'manual', // Or 'email' if you want email-based auth
                'department' => $user->department,
                'description' => 'User registered from Ministry of Health platform',
                'city' => 'Trinidad and Tobago', // You can make this dynamic
                'country' => 'TT', // ISO country code
            ];

            // Use the createUser method from MoodleService
            $moodleUserId = $this->moodleService->createUser($userData);
            
            if ($moodleUserId) {
                Log::info('User successfully synced to Moodle', [
                    'user_id' => $user->id,
                    'moodle_user_id' => $moodleUserId
                ]);
            }
            
            return $moodleUserId;
            
        } catch (\Exception $e) {
            Log::error('Moodle user sync failed', [
                'error' => $e->getMessage(),
                'user_email' => $user->email
            ]);
            
            // Return null instead of throwing exception
            // This allows registration to continue even if Moodle sync fails
            return null;
        }
    }

    
}