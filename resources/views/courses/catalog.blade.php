<x-layouts>
    <x-slot:heading>
        Course Catalog
    </x-slot:heading>

    <div class="max-w-7xl mx-auto">
        <!-- Header with filters -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-gray-600 dark:text-gray-400">
                    Browse available courses
                    @if(auth()->user()->isInternal())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 ml-2">
                            MOH Staff
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 ml-2">
                            External User
                        </span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $courses->count() }} courses available</span>
            </div>
        </div>

        @if($courses->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No courses available</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Check back later for new courses.</p>
            </div>
        @else
            <!-- Course Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                        <!-- Course Image -->
                        <div class="relative h-48 bg-gradient-to-br from-indigo-500 to-purple-600">
                            @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="h-20 w-20 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Enrollment Badge -->
                            <div class="absolute top-3 right-3">
                                @if($course->is_free)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500 text-white shadow-lg">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        OPEN
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-lg">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        APPROVAL REQUIRED
                                    </span>
                                @endif
                            </div>

                            <!-- Category Badge -->
                            @if($course->category)
                                <div class="absolute bottom-3 left-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-black/50 text-white backdrop-blur-sm">
                                        {{ $course->category->name }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Course Content -->
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 line-clamp-2">
                                {{ $course->title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                                {{ Str::limit($course->description, 120) }}
                            </p>

                            <!-- Course Meta -->
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-4">
                                @if($course->creator)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $course->creator->first_name }}
                                    </span>
                                @endif
                                @if($course->hasMoodleIntegration())
                                    <span class="flex items-center text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Moodle
                                    </span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <div class="mt-auto">
                                @switch($course->user_enrollment_status)
                                    @case('open')
                                    @case('enrolled')
                                    @case('approved')
                                        <a href="{{ route('courses.access-moodle', $course) }}"
                                           class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg>
                                                Enter Course
                                            </span>
                                        </a>
                                        @break

                                    @case('pending')
                                        <button disabled
                                                class="block w-full text-center px-4 py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-xl font-semibold cursor-not-allowed">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Pending Approval
                                            </span>
                                        </button>
                                        @break

                                    @case('denied')
                                        <form action="{{ route('courses.request-access', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="block w-full text-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                    Resubmit Request
                                                </span>
                                            </button>
                                        </form>
                                        @break

                                    @case('can_request')
                                    @default
                                        <form action="{{ route('courses.request-access', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                    Request Access
                                                </span>
                                            </button>
                                        </form>
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
