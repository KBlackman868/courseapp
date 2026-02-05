<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleManagementController extends Controller
{
    /**
     * SECURITY: Only SuperAdmins can access role management
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('superadmin')) {
                abort(403, 'Only SuperAdmins can manage roles.');
            }
            return $next($request);
        });
    }

    /**
     * Display role assignments page
     * Returns ALL users for client-side filtering (no server pagination)
     */
    public function index()
    {
        // Load all users with roles for client-side filtering
        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::all();

        return Inertia::render('Admin/RoleAssignmentsIndex', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function assignRole(Request $request, User $user)
    {
        // SECURITY: Double-check SuperAdmin authorization (defense in depth)
        if (!auth()->user()->hasRole('superadmin')) {
            ActivityLogger::logSystem('role_escalation_blocked',
                'Unauthorized role assignment attempt blocked',
                [
                    'attempted_by' => auth()->user()->email,
                    'target_user' => $user->email,
                    'requested_role' => $request->role
                ],
                'failed',
                'critical'
            );
            abort(403, 'Only SuperAdmins can assign roles.');
        }

        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        // Prevent self-demotion from superadmin
        if ($user->id === auth()->id() && $user->hasRole('superadmin') && $request->role !== 'superadmin') {
            return back()->with('error', 'You cannot remove your own superadmin role.');
        }

        $oldRoles = $user->getRoleNames()->toArray();
        $user->syncRoles([$request->role]);

        // Log the role change
        ActivityLogger::logSystem('role_assigned',
            "Role changed for {$user->email}: " . implode(',', $oldRoles) . " -> {$request->role}",
            [
                'target_user' => $user->email,
                'old_roles' => $oldRoles,
                'new_role' => $request->role,
                'assigned_by' => auth()->user()->email
            ]
        );

        return back()->with('success', 'Role updated successfully for ' . $user->email);
    }

    public function bulkAssignRoles(Request $request)
    {
        // SECURITY: Double-check SuperAdmin authorization (defense in depth)
        if (!auth()->user()->hasRole('superadmin')) {
            ActivityLogger::logSystem('bulk_role_escalation_blocked',
                'Unauthorized bulk role assignment attempt blocked',
                ['attempted_by' => auth()->user()->email],
                'failed',
                'critical'
            );
            abort(403, 'Only SuperAdmins can assign roles.');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|exists:roles,name'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            // Skip self-role-change
            if ($user->id === auth()->id() && $user->hasRole('superadmin') && $request->role !== 'superadmin') {
                $skippedCount++;
                continue;
            }

            $oldRoles = $user->getRoleNames()->toArray();
            $user->syncRoles([$request->role]);
            $updatedCount++;

            ActivityLogger::logSystem('role_assigned',
                "Bulk role change for {$user->email}: " . implode(',', $oldRoles) . " -> {$request->role}",
                [
                    'target_user' => $user->email,
                    'old_roles' => $oldRoles,
                    'new_role' => $request->role,
                    'assigned_by' => auth()->user()->email,
                    'bulk_operation' => true
                ]
            );
        }

        $message = "Role updated for {$updatedCount} users.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} users.";
        }

        return back()->with('success', $message);
    }
}