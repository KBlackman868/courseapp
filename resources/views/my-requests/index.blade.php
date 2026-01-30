@extends('components.layouts')

@section('title', 'My Requests')

@php
    $heading = 'My Requests';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Tabs -->
    <div class="tabs tabs-boxed bg-base-200/50 p-1 rounded-xl inline-flex">
        <a href="{{ route('my-requests.index', ['status' => 'all']) }}"
           class="tab {{ $status === 'all' ? 'tab-active bg-primary text-primary-content' : '' }}">
            All
            <span class="ml-2 badge badge-sm {{ $status === 'all' ? 'badge-primary-content' : 'badge-ghost' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('my-requests.index', ['status' => 'pending']) }}"
           class="tab {{ $status === 'pending' ? 'tab-active bg-warning text-warning-content' : '' }}">
            Pending
            <span class="ml-2 badge badge-sm {{ $status === 'pending' ? 'badge-warning-content' : 'badge-warning' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('my-requests.index', ['status' => 'approved']) }}"
           class="tab {{ $status === 'approved' ? 'tab-active bg-success text-success-content' : '' }}">
            Approved
            <span class="ml-2 badge badge-sm {{ $status === 'approved' ? 'badge-success-content' : 'badge-success' }}">{{ $counts['approved'] }}</span>
        </a>
        <a href="{{ route('my-requests.index', ['status' => 'rejected']) }}"
           class="tab {{ $status === 'rejected' ? 'tab-active bg-error text-error-content' : '' }}">
            Rejected
            <span class="ml-2 badge badge-sm {{ $status === 'rejected' ? 'badge-error-content' : 'badge-error' }}">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    <!-- Requests List -->
    @if($requests->isEmpty())
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body text-center py-16">
                <div class="mx-auto w-16 h-16 bg-base-200 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-base-content">No requests found</h3>
                <p class="text-base-content/70 mt-2">
                    @if($status === 'pending')
                        You don't have any pending course access requests.
                    @elseif($status === 'approved')
                        None of your requests have been approved yet.
                    @elseif($status === 'rejected')
                        None of your requests have been rejected.
                    @else
                        You haven't made any course access requests yet.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('dashboard.learner') }}" class="btn btn-primary">
                        Browse Courses
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($requests as $request)
                <div class="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
                    <div class="card-body">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <!-- Course Image -->
                                    <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex-shrink-0 flex items-center justify-center">
                                        @if($request->course->image)
                                            <img src="{{ Storage::url($request->course->image) }}" alt="{{ $request->course->title }}" class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        @endif
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content">
                                            {{ $request->course->title }}
                                        </h3>
                                        <p class="text-sm text-base-content/70 mt-1">
                                            Requested {{ $request->requested_at->diffForHumans() }}
                                        </p>

                                        @if($request->request_reason)
                                            <p class="text-sm text-base-content/60 mt-2 line-clamp-2">
                                                "{{ $request->request_reason }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <!-- Status Badge -->
                                @if($request->status === 'pending')
                                    <div class="badge badge-warning gap-2">
                                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Pending Review
                                    </div>
                                @elseif($request->status === 'approved')
                                    <div class="badge badge-success gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approved
                                    </div>
                                    @if($request->approved_at)
                                        <span class="text-xs text-base-content/50">
                                            {{ $request->approved_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                @elseif($request->status === 'rejected')
                                    <div class="badge badge-error gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Rejected
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex gap-2 mt-2">
                                    @if($request->status === 'approved' && $request->moodle_sync_status === 'synced')
                                        <a href="{{ route('courses.show', $request->course) }}" class="btn btn-primary btn-sm">
                                            Go to Course
                                        </a>
                                    @elseif($request->status === 'rejected')
                                        <form action="{{ route('course-access-requests.store', $request->course) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="request_reason" value="{{ $request->request_reason }}">
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                Request Again
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Rejection Reason -->
                        @if($request->status === 'rejected' && $request->rejection_reason)
                            <div class="mt-4 p-4 bg-error/10 rounded-lg border border-error/20">
                                <p class="text-sm text-error font-medium">Rejection Reason:</p>
                                <p class="text-sm text-base-content/80 mt-1">{{ $request->rejection_reason }}</p>
                            </div>
                        @endif

                        <!-- Moodle Sync Status -->
                        @if($request->status === 'approved')
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                @if($request->moodle_sync_status === 'synced')
                                    <span class="text-success flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Course access ready
                                    </span>
                                @elseif($request->moodle_sync_status === 'syncing')
                                    <span class="text-info flex items-center gap-1">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Setting up your access...
                                    </span>
                                @elseif($request->moodle_sync_status === 'failed')
                                    <span class="text-error flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Setup failed - admin notified
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6">
            {{ $requests->appends(['status' => $status])->links() }}
        </div>
    @endif
</div>
@endsection
