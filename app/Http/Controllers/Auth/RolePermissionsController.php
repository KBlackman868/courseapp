<?php
// app/Http/Controllers/Admin/RolePermissionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin']);
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy('category');
        $users = User::with('roles')->paginate(20);
        
        return view('admin.roles.index', compact('roles', 'permissions', 'users'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('category');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'guard_name' => 'web'
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create role')->withInput();
        }
    }

    public function edit(Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()->route('admin.roles.index')
                ->with('warning', 'Superadmin role cannot be edited');
        }

        $permissions = Permission::all()->groupBy('category');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'superadmin') {
            return back()->with('error', 'Superadmin role cannot be modified');
        }

        $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'display_name' => $request->display_name,
                'description' => $request->description
            ]);

            $role->syncPermissions($request->permissions ?? []);

            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update role');
        }
    }

    public function destroy(Role $role)
    {
        $protectedRoles = ['superadmin', 'student', 'admin', 'user'];
        if (in_array($role->name, $protectedRoles)) {
            return back()->with('error', 'This role cannot be deleted');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users');
        }

        try {
            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role');
        }
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role');
        }

        if ($request->role === 'superadmin' && !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'Only superadmins can assign superadmin role');
        }

        try {
            $user->syncRoles([$request->role]);
            return back()->with('success', 'User role updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign role');
        }
    }

    public function showPermissions(Role $role)
    {
        $permissions = $role->permissions()->get()->groupBy('category');
        return view('admin.roles.permissions', compact('role', 'permissions'));
    }
}