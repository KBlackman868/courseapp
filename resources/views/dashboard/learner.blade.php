<x-layouts>
    <x-slot:heading>
        Course Catalog
    </x-slot:heading>

    <div class="max-w-7xl mx-auto">
        {{-- Onboarding Welcome Banner --}}
        @if($showOnboarding)
            <div class="alert alert-info shadow-lg mb-6" id="onboarding-banner">
                <div class="flex items-start w-full">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="ml-3 flex-1">
                        <h3 class="font-bold">Welcome to the Learning Portal, {{ $user->first_name }}!</h3>
                        @if($user->isMohStaff())
                            <p class="text-sm mt-1">
                                As an MOH Staff member, you have access to both MOH and external courses.
                                <br>
                                <strong>OPEN_ENROLLMENT</strong> courses can be accessed immediately.
                                <strong>APPROVAL_REQUIRED</strong> courses need admin approval first.
                            </p>
                        @else
                            <p class="text-sm mt-1">
                                Browse available courses in the External Courses tab.
                                <br>
                                <strong>Enroll</strong> in open courses immediately, or <strong>Request Access</strong> for courses that require approval.
                            </p>
                        @endif
                    </div>
                    <form action="{{ route('dashboard.complete-onboarding') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-ghost">
                            Got it
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- DaisyUI Tabs --}}
        <div class="tabs tabs-boxed bg-base-200 p-1 mb-6">
            @if($canAccessMohTab)
                <a href="{{ route('dashboard.learner', ['tab' => 'moh']) }}"
                   class="tab tab-lg @if($activeTab === 'moh') tab-active @endif">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    MOH Courses
                </a>
            @else
                <div class="tab tab-lg tab-disabled tooltip" data-tip="Only available to MOH Staff">
                    <svg class="w-5 h-5 mr-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    MOH Courses
                </div>
            @endif

            @if($canAccessExternalTab)
                <a href="{{ route('dashboard.learner', ['tab' => 'external']) }}"
                   class="tab tab-lg @if($activeTab === 'external') tab-active @endif">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    External Courses
                </a>
            @endif
        </div>

        {{-- Search and Filters --}}
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body py-4">
                <form action="{{ route('dashboard.learner') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    {{-- Search Input --}}
                    <div class="form-control flex-1">
                        <div class="input-group">
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Search courses..."
                                   class="input input-bordered w-full" />
                            <button type="submit" class="btn btn-square btn-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Category Filter --}}
                    <div class="form-control w-full md:w-48">
                        <select name="category" class="select select-bordered" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" @if($category == $id) selected @endif>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Enrollment Type Filter --}}
                    <div class="form-control w-full md:w-48">
                        <select name="enrollment_type" class="select select-bordered" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="OPEN_ENROLLMENT" @if($enrollmentType === 'OPEN_ENROLLMENT') selected @endif>Open Enrollment</option>
                            <option value="APPROVAL_REQUIRED" @if($enrollmentType === 'APPROVAL_REQUIRED') selected @endif>Approval Required</option>
                        </select>
                    </div>

                    {{-- Clear Filters --}}
                    @if($search || $category || $enrollmentType)
                        <a href="{{ route('dashboard.learner', ['tab' => $activeTab]) }}" class="btn btn-ghost">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Tab Content Description --}}
        <div class="mb-6">
            @if($activeTab === 'moh')
                <p class="text-gray-600 dark:text-gray-400">
                    Courses available to MOH Staff members. These include internal training and shared programs.
                </p>
            @else
                <p class="text-gray-600 dark:text-gray-400">
                    Courses available to external partners and the public.
                </p>
            @endif
        </div>

        {{-- Course Grid --}}
        @if($courses->isEmpty())
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">No courses found</h3>
                    <p class="text-gray-500 mt-2">
                        @if($search || $category || $enrollmentType)
                            Try adjusting your search or filters.
                        @else
                            No courses are available in this category yet.
                        @endif
                    </p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                    @php
                        $status = $userCourseStatuses[$course->id] ?? ['status' => 'available', 'cta' => null, 'action' => null];
                    @endphp
                    <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                        {{-- Course Image --}}
                        <figure class="relative">
                            @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="h-48 w-full object-cover" />
                            @else
                                <div class="h-48 w-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            <div class="absolute top-2 right-2 flex gap-1">
                                @if($course->isOpenEnrollment())
                                    <span class="badge badge-success">Open</span>
                                @else
                                    <span class="badge badge-warning">Approval Required</span>
                                @endif
                            </div>

                            {{-- Moodle Badge --}}
                            @if($course->hasMoodleIntegration())
                                <div class="absolute top-2 left-2">
                                    <span class="badge badge-info badge-sm">Moodle</span>
                                </div>
                            @endif
                        </figure>

                        <div class="card-body">
                            {{-- Category --}}
                            @if($course->category)
                                <div class="badge badge-outline badge-sm mb-2">{{ $course->category->name }}</div>
                            @endif

                            {{-- Title --}}
                            <h2 class="card-title text-lg">
                                {{ Str::limit($course->title, 50) }}
                            </h2>

                            {{-- Description --}}
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                {{ Str::limit($course->description, 100) }}
                            </p>

                            {{-- Action Button --}}
                            <div class="card-actions justify-end mt-4">
                                @switch($status['status'])
                                    @case('enrolled')
                                        <a href="{{ route('courses.access-moodle', $course) }}" class="btn btn-primary btn-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                            Go to Course
                                        </a>
                                        @break

                                    @case('pending')
                                        <button class="btn btn-disabled btn-sm" disabled>
                                            <svg class="w-4 h-4 mr-1 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pending Approval
                                        </button>
                                        @break

                                    @case('syncing')
                                        <button class="btn btn-info btn-sm" disabled>
                                            <span class="loading loading-spinner loading-xs"></span>
                                            Setting up...
                                        </button>
                                        @break

                                    @case('sync_failed')
                                        <div class="tooltip tooltip-left" data-tip="Admin has been notified">
                                            <button class="btn btn-error btn-sm" disabled>
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>
                                                </svg>
                                                Setup Failed
                                            </button>
                                        </div>
                                        @break

                                    @case('rejected')
                                        <form action="{{ route('courses.request-access', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Request Again
                                            </button>
                                        </form>
                                        @break

                                    @default
                                        @if($course->isOpenEnrollment())
                                            <a href="{{ route('courses.access-moodle', $course) }}" class="btn btn-success btn-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg>
                                                Enroll
                                            </a>
                                        @else
                                            <form action="{{ route('courses.request-access', $course) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            {{-- Pagination --}}
            <div class="mt-8 flex justify-center">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</x-layouts>
