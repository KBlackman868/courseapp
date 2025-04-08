<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // Display a list of all users with role management
    public function index()
    {
        $users = User::all();
        return view('admin.users_lists', compact('users'));
    }

    // Update a user's role
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:superadmin,admin,user',
        ]);

        // Update the user roles (removing existing ones first)
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'User role updated successfully.');
    }
}
