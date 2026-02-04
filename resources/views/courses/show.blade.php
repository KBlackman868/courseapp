<x-layouts>
    <x-slot:heading>
        {{ $course->title }}
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            {{-- Course Image Header --}}
            <div class="relative">
                @if($course->image)
                    <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-full h-72 object-cover">
                @else
                    <div class="w-full h-72 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                @endif

                {{-- Status Badge --}}
                <div class="absolute top-4 right-4">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </div>

                {{-- Moodle Sync Badge --}}
                @if($course->moodle_course_id)
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Moodle Integrated
                        </span>
                    </div>
                @endif
            </div>

            {{-- Course Content --}}
            <div class="p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>
                <p class="text-gray-700 mb-6 leading-relaxed">{{ $course->description }}</p>

                {{-- Access Level Status Banner --}}
                <div class="mb-6 p-4 rounded-xl
                    @if($accessLevel['level'] === 'enrolled') bg-green-50 border border-green-200
                    @elseif($accessLevel['level'] === 'pending') bg-yellow-50 border border-yellow-200
                    @elseif($accessLevel['level'] === 'denied') bg-red-50 border border-red-200
                    @elseif($accessLevel['level'] === 'request_access') bg-blue-50 border border-blue-200
                    @else bg-indigo-50 border border-indigo-200
                    @endif">
                    <div class="flex items-center">
                        @if($accessLevel['level'] === 'enrolled')
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-800 font-medium">{{ $accessLevel['message'] }}</span>
                        @elseif($accessLevel['level'] === 'pending')
                            <svg class="w-5 h-5 text-yellow-600 mr-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-yellow-800 font-medium">{{ $accessLevel['message'] }}</span>
                        @elseif($accessLevel['level'] === 'denied')
                            <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-800 font-medium">{{ $accessLevel['message'] }}</span>
                        @elseif($accessLevel['level'] === 'request_access')
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-blue-800 font-medium">{{ $accessLevel['message'] }}</span>
                        @else
                            <svg class="w-5 h-5 text-indigo-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-indigo-800 font-medium">{{ $accessLevel['message'] }}</span>
                        @endif
                    </div>
                </div>

                {{-- User Type Badge (for clarity) --}}
                @auth
                    <div class="mb-6 flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Your access type:</span>
                        @if(auth()->user()->isInternal())
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                Ministry of Health Staff
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                External User
                            </span>
                        @endif
                    </div>
                @endauth

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-4">
                    @if($accessLevel['level'] === 'enrolled')
                        {{-- Enrolled users: Show Access Course button --}}
                        @if($accessLevel['can_access_moodle'])
                            <a href="{{ route('courses.access-moodle', $course) }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Access Course in Moodle
                            </a>
                        @else
                            <span class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-600 font-semibold rounded-xl cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Enrolled - Moodle sync in progress
                            </span>
                        @endif

                    @elseif($accessLevel['level'] === 'pending')
                        {{-- Pending: Show waiting status --}}
                        <span class="inline-flex items-center px-6 py-3 bg-yellow-100 text-yellow-800 font-semibold rounded-xl cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Awaiting Approval
                        </span>

                    @elseif($accessLevel['level'] === 'denied')
                        {{-- Denied: Show contact option --}}
                        <span class="inline-flex items-center px-6 py-3 bg-red-100 text-red-800 font-semibold rounded-xl cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Access Denied
                        </span>

                    @elseif($accessLevel['level'] === 'request_access')
                        {{-- External users without enrollment: Request Access --}}
                        {{-- Posts to CourseAccessRequestController@store so admins can see/manage requests --}}
                        <form action="{{ route('courses.request-access', ['course' => $course->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Request Access
                            </button>
                        </form>
                        <p class="w-full text-sm text-gray-500 mt-2">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Your request will be reviewed by a course administrator. You'll be notified once approved.
                        </p>

                    @else
                        {{-- Internal users without enrollment: Direct Enroll --}}
                        <form action="{{ route('courses.enroll.store', ['course' => $course->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Enroll Now
                            </button>
                        </form>
                    @endif

                    {{-- Back to courses --}}
                    <a href="{{ route('courses.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Courses
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts>
