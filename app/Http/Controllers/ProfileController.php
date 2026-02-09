<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Enrollment;                      // ← fix this
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;             // ← need this
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;   // ← and this
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

        return view('profile.show', compact('user', 'enrollments'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048',
        ]);
    
        $user = $request->user();
        $file = $request->file('profile_photo');
        $ext  = $file->extension();
        $filename = $user->id . '.' . $ext;
    
        // will go to storage/app/public/avatars/{id}.{ext}
        $file->storeAs('avatars', $filename, 'public');
    
        // save *relative* path in DB
        $user->profile_photo = "avatars/{$filename}";
        $user->save();
    
        return back()->with('success','Profile photo updated.');
    }
   

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // High-risk accounts (admin, superadmin, course_admin, MOH staff): 14 chars
        // Standard accounts (external users): 12 chars
        $isHighRisk = $user->hasRole(['superadmin', 'admin', 'course_admin', 'moh_staff']);
        $minLength = $isHighRisk ? 14 : 12;

        $data = $request->validate([
            'current_password' => 'required',
            'password'         => "required|min:{$minLength}|confirmed",
        ], [
            'password.min' => "Password must be at least {$minLength} characters for " . ($isHighRisk ? 'high-risk' : 'standard') . ' accounts.',
        ]);

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
        return view('profile.settings', ['user' => $request->user()]);
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
