<x-layouts>
    <x-slot:heading>
        Course Catalog
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-base-content">Available Courses</h1>
                    <p class="text-base-content/70 mt-1">
                        Browse and enroll in courses
                        @if(auth()->user()->isInternal())
                            <span class="badge badge-info badge-sm ml-2">MOH Staff</span>
                        @else
                            <span class="badge badge-secondary badge-sm ml-2">External User</span>
                        @endif
                    </p>
                </div>
                <div class="stats stats-horizontal shadow bg-base-100">
                    <div class="stat py-2 px-4">
                        <div class="stat-title text-xs">Courses</div>
                        <div class="stat-value text-lg text-primary">{{ $courses->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($courses->isEmpty())
            <!-- Empty State -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body items-center text-center py-16">
                    <svg class="w-16 h-16 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h2 class="card-title mt-4">No courses available</h2>
                    <p class="text-base-content/60">Check back later for new courses.</p>
                </div>
            </div>
        @else
            <!-- Course Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                    <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                        <!-- Course Image -->
                        <figure class="relative h-48 bg-gradient-to-br from-primary to-secondary">
                            @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="flex items-center justify-center w-full h-full">
                                    <svg class="w-20 h-20 text-primary-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Enrollment Badge -->
                            <div class="absolute top-3 right-3">
                                @if($course->is_free)
                                    <div class="badge badge-success gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        OPEN
                                    </div>
                                @else
                                    <div class="badge badge-warning gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        APPROVAL
                                    </div>
                                @endif
                            </div>

                            <!-- Category Badge -->
                            @if($course->category)
                                <div class="absolute bottom-3 left-3">
                                    <div class="badge badge-neutral badge-sm">{{ $course->category->name }}</div>
                                </div>
                            @endif
                        </figure>

                        <div class="card-body p-5">
                            <h2 class="card-title text-base line-clamp-2">{{ $course->title }}</h2>
                            <p class="text-sm text-base-content/70 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>

                            <!-- Course Meta -->
                            <div class="flex items-center gap-3 text-xs text-base-content/60 mt-2">
                                @if($course->creator)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $course->creator->first_name }}
                                    </span>
                                @endif
                                @if($course->hasMoodleIntegration())
                                    <span class="flex items-center gap-1 text-success">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Moodle
                                    </span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <div class="card-actions mt-4">
                                @switch($course->user_enrollment_status)
                                    @case('open')
                                    @case('enrolled')
                                    @case('approved')
                                        <a href="{{ route('courses.access-moodle', $course) }}" class="btn btn-primary btn-block gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                            Enter Course
                                        </a>
                                        @break

                                    @case('syncing')
                                        <button disabled class="btn btn-info btn-block gap-2">
                                            <span class="loading loading-spinner loading-sm"></span>
                                            Setting up access...
                                        </button>
                                        @break

                                    @case('sync_failed')
                                        <button disabled class="btn btn-error btn-block gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            Setup failed
                                        </button>
                                        @break

                                    @case('pending')
                                        <button disabled class="btn btn-ghost btn-block gap-2">
                                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pending Approval
                                        </button>
                                        @break

                                    @case('denied')
                                        <form action="{{ route('courses.request-access', $course) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-block gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Resubmit Request
                                            </button>
                                        </form>
                                        @break

                                    @case('can_request')
                                    @default
                                        @if($course->isOpenEnrollment())
                                            <a href="{{ route('courses.access-moodle', $course) }}" class="btn btn-success btn-block gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg>
                                                Enroll Now
                                            </a>
                                        @else
                                            <form action="{{ route('courses.request-access', $course) }}" method="POST" class="w-full">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-block gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                    Request Access
                                                </button>
                                            </form>
                                        @endif
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts>
