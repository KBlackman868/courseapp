<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        $roles = Role::all();
        
        return view('admin.role-management', compact('users', 'roles'));
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        // Prevent changing superadmin role unless current user is superadmin
        if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'Only superadmins can modify superadmin roles.');
        }

        // Prevent self-demotion from superadmin
        if ($user->id === auth()->id() && $user->hasRole('superadmin') && $request->role !== 'superadmin') {
            return back()->with('error', 'You cannot remove your own superadmin role.');
        }

        $user->syncRoles([$request->role]);

        return back()->with('success', 'Role updated successfully for ' . $user->email);
    }

    public function bulkAssignRoles(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|exists:roles,name'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $updatedCount = 0;

        foreach ($users as $user) {
            // Skip superadmins unless current user is superadmin
            if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
                continue;
            }

            $user->syncRoles([$request->role]);
            $updatedCount++;
        }

        return back()->with('success', "Role updated for {$updatedCount} users.");
    }
}