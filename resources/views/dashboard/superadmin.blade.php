@extends('components.layouts')

@section('title', 'SuperAdmin Dashboard')

@php
    $heading = 'SuperAdmin Dashboard';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Security Notice -->
    <div class="alert alert-warning shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <h3 class="font-bold">SuperAdmin Access</h3>
            <div class="text-xs">You have full system privileges. All actions are logged.</div>
        </div>
    </div>

    <!-- Quick Action Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.roles.index') }}" class="card bg-gradient-to-br from-purple-500 to-indigo-600 text-white hover:shadow-xl transition-shadow">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Roles</p>
                        <p class="text-2xl font-bold">{{ $roleDistribution->count() }}</p>
                    </div>
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="card bg-gradient-to-br from-blue-500 to-cyan-600 text-white hover:shadow-xl transition-shadow">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Total Users</p>
                        <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
                    </div>
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.moodle.status') }}" class="card bg-gradient-to-br from-orange-500 to-red-600 text-white hover:shadow-xl transition-shadow">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Moodle Synced</p>
                        <p class="text-2xl font-bold">{{ $moodleHealth['courses_synced'] }}</p>
                    </div>
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.activity-logs.index') }}" class="card bg-gradient-to-br from-green-500 to-teal-600 text-white hover:shadow-xl transition-shadow">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Courses</p>
                        <p class="text-2xl font-bold">{{ $stats['total_courses'] }}</p>
                    </div>
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Role Distribution -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-base-content flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Role Distribution
                </h2>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th class="text-right">Users</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roleDistribution as $role)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        @if($role->name === 'superadmin')
                                            <span class="badge badge-sm bg-gradient-to-r from-purple-500 to-indigo-500 text-white border-0">
                                                {{ $role->display_name ?? ucfirst($role->name) }}
                                            </span>
                                        @elseif($role->name === 'admin')
                                            <span class="badge badge-sm badge-primary">{{ $role->display_name ?? ucfirst($role->name) }}</span>
                                        @elseif($role->name === 'course_admin')
                                            <span class="badge badge-sm badge-secondary">{{ $role->display_name ?? 'Course Admin' }}</span>
                                        @else
                                            <span class="badge badge-sm badge-ghost">{{ $role->display_name ?? ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-right font-semibold">{{ $role->users_count }}</td>
                                <td>
                                    <progress class="progress progress-primary w-20" value="{{ $role->users_count }}" max="{{ $stats['total_users'] }}"></progress>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-actions justify-end mt-4">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-primary btn-sm">
                        Manage Roles
                    </a>
                </div>
            </div>
        </div>

        <!-- Moodle Health -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-base-content flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    Moodle Integration Health
                </h2>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Courses Synced</div>
                        <div class="stat-value text-success">{{ $moodleHealth['courses_synced'] }}</div>
                    </div>
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Pending Sync</div>
                        <div class="stat-value text-warning">{{ $moodleHealth['courses_pending'] }}</div>
                    </div>
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Failed Syncs</div>
                        <div class="stat-value text-error">{{ $moodleHealth['failed_syncs'] }}</div>
                    </div>
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Users with Moodle</div>
                        <div class="stat-value text-info">{{ $moodleHealth['users_with_moodle'] }}</div>
                    </div>
                </div>

                <div class="card-actions justify-end mt-4">
                    <a href="{{ route('admin.moodle.status') }}" class="btn btn-warning btn-sm">
                        Moodle Status
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Workloads -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-base-content flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pending Workloads
                </h2>

                <div class="space-y-4 mt-4">
                    <div class="flex items-center justify-between p-4 bg-base-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-yellow-500 text-white rounded-full w-10">
                                    <span class="text-xl">{{ $pendingAccountRequests }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold">Account Requests</p>
                                <p class="text-sm text-base-content/70">Awaiting approval</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.account-requests.index') }}" class="btn btn-sm btn-outline">
                            Review
                        </a>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-base-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-blue-500 text-white rounded-full w-10">
                                    <span class="text-xl">{{ $pendingCourseRequests }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold">Course Access Requests</p>
                                <p class="text-sm text-base-content/70">Awaiting approval</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.course-access-requests.index') }}" class="btn btn-sm btn-outline">
                            Review
                        </a>
                    </div>

                    @if($failedSyncs > 0)
                    <div class="flex items-center justify-between p-4 bg-error/10 rounded-lg border border-error/30">
                        <div class="flex items-center gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-error text-white rounded-full w-10">
                                    <span class="text-xl">{{ $failedSyncs }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-error">Failed Moodle Syncs</p>
                                <p class="text-sm text-error/70">Requires attention</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.moodle.status') }}" class="btn btn-sm btn-error">
                            Fix Now
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-base-content flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Recent Users
                </h2>

                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $recentUser)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral-focus text-neutral-content rounded-full w-8">
                                                <span class="text-xs">{{ substr($recentUser->first_name, 0, 1) }}{{ substr($recentUser->last_name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-sm">{{ $recentUser->first_name }} {{ $recentUser->last_name }}</div>
                                            <div class="text-xs text-base-content/50">{{ $recentUser->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @foreach($recentUser->roles as $role)
                                        <span class="badge badge-xs badge-ghost">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                    @endforeach
                                </td>
                                <td class="text-xs text-base-content/70">{{ $recentUser->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-actions justify-end mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">
                        All Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-base-content flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Recent System Activity
            </h2>

            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivity as $activity)
                        <tr>
                            <td class="text-xs text-base-content/70">{{ $activity->created_at->diffForHumans() }}</td>
                            <td class="text-sm">{{ $activity->user?->email ?? 'System' }}</td>
                            <td>
                                <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded">
                                    {{ $activity->action }}
                                </span>
                            </td>
                            <td class="text-sm max-w-xs truncate">{{ Str::limit($activity->description, 50) }}</td>
                            <td>
                                @if($activity->status === 'success')
                                    <span class="badge badge-success badge-sm">Success</span>
                                @elseif($activity->status === 'failed')
                                    <span class="badge badge-error badge-sm">Failed</span>
                                @else
                                    <span class="badge badge-ghost badge-sm">{{ ucfirst($activity->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-base-content/50 py-8">
                                No recent activity logged
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-actions justify-end mt-4">
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline btn-sm">
                    View All Logs
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
