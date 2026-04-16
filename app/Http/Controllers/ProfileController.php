<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Enrollment;                      // ← fix this
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Rules\PasswordRules;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Eager-load approved enrollments + their courses
        $enrollments = Enrollment::with('course')
                         ->where('user_id', $user->id)
                         ->where('status', 'approved')
                         ->get();

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'enrollments' => $enrollments,
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $user = $request->user();

        // Delete old photo if it exists
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $file = $request->file('profile_photo');
        $ext  = $file->extension();
        $filename = $user->id . '.' . $ext;

        $file->storeAs('avatars', $filename, 'public');

        $user->profile_photo = "avatars/{$filename}";
        $user->save();

        return back()->with('success', 'Profile photo updated.');
    }

    /**
     * Serve a user's profile photo (avoids IIS symlink issues).
     */
    public function servePhoto(User $user)
    {
        if (!$user->profile_photo || !Storage::disk('public')->exists($user->profile_photo)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($user->profile_photo));
    }
   

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required',
            'password'         => PasswordRules::rules($request),
        ], PasswordRules::messages());

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return back()->with('success','Password changed.');
    }

    public function settings(Request $request)
    {
        return Inertia::render('Profile/Settings', [
            'user' => $request->user(),
        ]);
    }

    public function changePassword(Request $request)
    {
        return Inertia::render('Profile/ChangePassword');
    }

    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status'          => session('status'),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
