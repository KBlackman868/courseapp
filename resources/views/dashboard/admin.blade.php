<x-layouts>
    <x-slot:heading>
        Admin Dashboard
    </x-slot:heading>

    <div class="max-w-7xl mx-auto">
        {{-- Welcome Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Welcome back, {{ $user->first_name }}
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Here's what's happening with your platform today.
            </p>
        </div>

        {{-- Stats Cards (DaisyUI) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Users --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_users']) }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-primary/10">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-sm">
                        <span class="badge badge-info badge-sm">{{ $stats['moh_staff'] }} MOH</span>
                        <span class="badge badge-success badge-sm ml-2">{{ $stats['external_users'] }} External</span>
                    </div>
                </div>
            </div>

            {{-- Pending Requests --}}
            <div class="card bg-base-100 shadow-xl @if($stats['pending_total'] > 0) border-2 border-warning @endif">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pending Requests</p>
                            <p class="text-3xl font-bold @if($stats['pending_total'] > 0) text-warning @else text-gray-900 dark:text-gray-100 @endif">
                                {{ $stats['pending_total'] }}
                            </p>
                        </div>
                        <div class="p-3 rounded-full @if($stats['pending_total'] > 0) bg-warning/20 @else bg-gray-100 dark:bg-gray-700 @endif">
                            <svg class="w-8 h-8 @if($stats['pending_total'] > 0) text-warning @else text-gray-400 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-sm">
                        <span class="badge badge-warning badge-sm">{{ $pendingAccountRequests }} Accounts</span>
                        <span class="badge badge-info badge-sm ml-2">{{ $pendingCourseRequests }} Courses</span>
                    </div>
                </div>
            </div>

            {{-- Active Courses --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Active Courses</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['active_courses']) }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-success/10">
                            <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Failed Syncs --}}
            <div class="card bg-base-100 shadow-xl @if($stats['failed_syncs'] > 0) border-2 border-error @endif">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Failed Syncs</p>
                            <p class="text-3xl font-bold @if($stats['failed_syncs'] > 0) text-error @else text-gray-900 dark:text-gray-100 @endif">
                                {{ $stats['failed_syncs'] }}
                            </p>
                        </div>
                        <div class="p-3 rounded-full @if($stats['failed_syncs'] > 0) bg-error/20 @else bg-gray-100 dark:bg-gray-700 @endif">
                            <svg class="w-8 h-8 @if($stats['failed_syncs'] > 0) text-error @else text-gray-400 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    @if($stats['failed_syncs'] > 0)
                        <a href="{{ route('admin.course-access-requests.index', ['status' => 'failed']) }}" class="mt-2 text-sm text-error hover:underline">
                            View failed syncs &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Pending Account Requests --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-lg">
                            Pending Account Requests
                            @if($pendingAccountRequests > 0)
                                <span class="badge badge-warning">{{ $pendingAccountRequests }}</span>
                            @endif
                        </h2>
                        <a href="{{ route('admin.account-requests.index') }}" class="btn btn-ghost btn-sm">
                            View All
                        </a>
                    </div>

                    @if($recentAccountRequests->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>No pending account requests</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Department</th>
                                        <th>Requested</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAccountRequests as $request)
                                        <tr>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-primary text-primary-content rounded-full w-8">
                                                            <span class="text-xs">{{ strtoupper(substr($request->first_name, 0, 1) . substr($request->last_name, 0, 1)) }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-sm">{{ $request->first_name }} {{ $request->last_name }}</div>
                                                        <div class="text-xs opacity-50">{{ $request->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-sm">{{ $request->department ?? '-' }}</td>
                                            <td class="text-sm">{{ $request->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('admin.account-requests.index') }}" class="btn btn-ghost btn-xs">
                                                    Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pending Course Access Requests --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-lg">
                            Pending Course Requests
                            @if($pendingCourseRequests > 0)
                                <span class="badge badge-info">{{ $pendingCourseRequests }}</span>
                            @endif
                        </h2>
                        <a href="{{ route('admin.course-access-requests.index') }}" class="btn btn-ghost btn-sm">
                            View All
                        </a>
                    </div>

                    @if($recentCourseRequests->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>No pending course requests</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Course</th>
                                        <th>Requested</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCourseRequests as $request)
                                        <tr>
                                            <td>
                                                <div class="font-bold text-sm">{{ $request->user->first_name }} {{ $request->user->last_name }}</div>
                                                <div class="text-xs opacity-50">{{ $request->user->email }}</div>
                                            </td>
                                            <td class="text-sm">{{ Str::limit($request->course->title, 25) }}</td>
                                            <td class="text-sm">{{ $request->requested_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('admin.course-access-requests.index') }}" class="btn btn-ghost btn-xs">
                                                    Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Failed Moodle Syncs Section --}}
        @if($failedMoodleSyncs->isNotEmpty())
            <div class="mt-8">
                <div class="card bg-base-100 shadow-xl border-2 border-error">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="card-title text-lg text-error">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Failed Moodle Syncs
                                <span class="badge badge-error">{{ $stats['failed_syncs'] }}</span>
                            </h2>
                            <a href="{{ route('admin.course-access-requests.index', ['status' => 'failed']) }}" class="btn btn-error btn-sm">
                                View All Failed
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Course</th>
                                        <th>Error</th>
                                        <th>Failed At</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($failedMoodleSyncs as $sync)
                                        <tr>
                                            <td>
                                                <div class="font-bold text-sm">{{ $sync->user->first_name }} {{ $sync->user->last_name }}</div>
                                            </td>
                                            <td class="text-sm">{{ Str::limit($sync->course->title, 30) }}</td>
                                            <td>
                                                <div class="tooltip tooltip-left" data-tip="{{ $sync->moodle_sync_error }}">
                                                    <span class="text-xs text-error">{{ Str::limit($sync->moodle_sync_error, 30) }}</span>
                                                </div>
                                            </td>
                                            <td class="text-sm">{{ $sync->updated_at->diffForHumans() }}</td>
                                            <td>
                                                <form action="{{ route('admin.course-access-requests.retrySync', $sync) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-xs">
                                                        Retry
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Recent Notifications --}}
        <div class="mt-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-lg">Recent Notifications</h2>
                        <a href="{{ route('notifications.index') }}" class="btn btn-ghost btn-sm">
                            View All
                        </a>
                    </div>

                    @if($recentNotifications->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p>No notifications</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentNotifications as $notification)
                                <div class="flex items-start gap-4 p-3 rounded-lg @if(!$notification->is_read) bg-primary/5 @else bg-base-200 @endif">
                                    <div class="flex-shrink-0">
                                        @if($notification->type === 'success')
                                            <div class="p-2 rounded-full bg-success/20">
                                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @elseif($notification->type === 'warning')
                                            <div class="p-2 rounded-full bg-warning/20">
                                                <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>
                                                </svg>
                                            </div>
                                        @elseif($notification->type === 'error')
                                            <div class="p-2 rounded-full bg-error/20">
                                                <svg class="w-4 h-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="p-2 rounded-full bg-info/20">
                                                <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $notification->title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($notification->message, 80) }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notification->is_read)
                                        <span class="flex-shrink-0 w-2 h-2 bg-primary rounded-full"></span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts>
