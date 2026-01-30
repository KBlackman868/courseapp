@extends('components.layouts')

@section('title', 'My Learning')

@php
    $heading = 'My Learning';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Search and Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <form action="{{ route('my-learning.index') }}" method="GET" class="flex-1 max-w-md">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="join w-full">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Search your courses..."
                       class="input input-bordered join-item flex-1">
                <button type="submit" class="btn btn-primary join-item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Tabs -->
    <div class="tabs tabs-boxed bg-base-200/50 p-1 rounded-xl inline-flex">
        <a href="{{ route('my-learning.index', ['status' => 'all', 'search' => $search]) }}"
           class="tab {{ $status === 'all' ? 'tab-active bg-primary text-primary-content' : '' }}">
            All Courses
            <span class="ml-2 badge badge-sm {{ $status === 'all' ? 'badge-primary-content' : 'badge-ghost' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('my-learning.index', ['status' => 'approved', 'search' => $search]) }}"
           class="tab {{ $status === 'approved' ? 'tab-active bg-success text-success-content' : '' }}">
            Active
            <span class="ml-2 badge badge-sm {{ $status === 'approved' ? 'badge-success-content' : 'badge-success' }}">{{ $counts['approved'] }}</span>
        </a>
        <a href="{{ route('my-learning.index', ['status' => 'pending', 'search' => $search]) }}"
           class="tab {{ $status === 'pending' ? 'tab-active bg-warning text-warning-content' : '' }}">
            Pending
            <span class="ml-2 badge badge-sm {{ $status === 'pending' ? 'badge-warning-content' : 'badge-warning' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('my-learning.index', ['status' => 'completed', 'search' => $search]) }}"
           class="tab {{ $status === 'completed' ? 'tab-active bg-info text-info-content' : '' }}">
            Completed
            <span class="ml-2 badge badge-sm {{ $status === 'completed' ? 'badge-info-content' : 'badge-info' }}">{{ $counts['completed'] }}</span>
        </a>
    </div>

    <!-- Courses Grid -->
    @if($enrollments->isEmpty())
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body text-center py-16">
                <div class="mx-auto w-16 h-16 bg-base-200 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-base-content">
                    @if($search)
                        No courses found for "{{ $search }}"
                    @elseif($status === 'pending')
                        No pending enrollments
                    @elseif($status === 'completed')
                        No completed courses yet
                    @else
                        Start Your Learning Journey
                    @endif
                </h3>
                <p class="text-base-content/70 mt-2">
                    @if($search)
                        Try a different search term or browse all courses.
                    @elseif($status === 'pending')
                        All your enrollment requests have been processed.
                    @elseif($status === 'completed')
                        Keep learning! Complete a course to see it here.
                    @else
                        Browse our course catalog and enroll in courses that interest you.
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($enrollments as $enrollment)
                <div class="card bg-base-100 shadow-md hover:shadow-lg transition-all hover:-translate-y-1">
                    <!-- Course Image -->
                    <figure class="relative h-40 bg-gradient-to-br from-indigo-500 to-purple-600">
                        @if($enrollment->course->image)
                            <img src="{{ Storage::url($enrollment->course->image) }}"
                                 alt="{{ $enrollment->course->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            @if($enrollment->status === 'approved')
                                <span class="badge badge-success">Active</span>
                            @elseif($enrollment->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($enrollment->status === 'completed')
                                <span class="badge badge-info">Completed</span>
                            @elseif($enrollment->status === 'denied')
                                <span class="badge badge-error">Denied</span>
                            @endif
                        </div>

                        <!-- Audience Badge -->
                        <div class="absolute bottom-3 left-3">
                            <span class="badge badge-{{ $enrollment->course->audience_color ?? 'blue' }} badge-outline bg-base-100/90">
                                {{ $enrollment->course->audience_label ?? 'All Users' }}
                            </span>
                        </div>
                    </figure>

                    <div class="card-body">
                        <h3 class="card-title text-base-content line-clamp-2">
                            {{ $enrollment->course->title }}
                        </h3>

                        <p class="text-sm text-base-content/70 line-clamp-2 mt-1">
                            {{ Str::limit($enrollment->course->description, 100) }}
                        </p>

                        <!-- Enrollment Info -->
                        <div class="flex items-center gap-2 text-xs text-base-content/50 mt-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Enrolled {{ $enrollment->created_at->diffForHumans() }}
                        </div>

                        <!-- Progress Bar (if applicable) -->
                        @if($enrollment->status === 'approved' && isset($enrollment->progress))
                            <div class="mt-3">
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Progress</span>
                                    <span>{{ $enrollment->progress ?? 0 }}%</span>
                                </div>
                                <progress class="progress progress-primary w-full" value="{{ $enrollment->progress ?? 0 }}" max="100"></progress>
                            </div>
                        @endif

                        <!-- Action Button -->
                        <div class="card-actions justify-end mt-4">
                            @if($enrollment->status === 'approved')
                                @if($enrollment->course->hasMoodleIntegration())
                                    <a href="{{ route('courses.access', $enrollment->course) }}"
                                       class="btn btn-primary btn-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Continue
                                    </a>
                                @else
                                    <a href="{{ route('courses.show', $enrollment->course) }}"
                                       class="btn btn-primary btn-sm">
                                        View Course
                                    </a>
                                @endif
                            @elseif($enrollment->status === 'pending')
                                <button class="btn btn-ghost btn-sm" disabled>
                                    <svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Awaiting Approval
                                </button>
                            @elseif($enrollment->status === 'completed')
                                <a href="{{ route('courses.show', $enrollment->course) }}"
                                   class="btn btn-outline btn-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    View Certificate
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
            {{ $enrollments->appends(['status' => $status, 'search' => $search])->links() }}
        </div>
    @endif
</div>
@endsection
