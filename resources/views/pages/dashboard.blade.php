<!-- resources/views/pages/dashboard.blade.php -->
<x-layouts>
    <x-slot:heading>
        Dashboard - Course Catalog
    </x-slot:heading>

    @php
        $courses = \App\Models\Course::where('status', 'active')->get();
    @endphp

    <style>
        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .badge-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
    </style>

    <!-- Dashboard Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-12 px-4 sm:px-6 lg:px-8 -mt-8 -mx-4 sm:-mx-6 lg:-mx-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-white">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">
                    Welcome back, {{ Auth::user()->first_name }}!
                </h1>
                <p class="text-blue-100 text-lg">
                    @if(Auth::user()->hasRole('superadmin'))
                        System Administrator Dashboard
                    @elseif(Auth::user()->hasRole('admin'))
                        Administrator Dashboard
                    @else
                        Student Learning Portal
                    @endif
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $enrolledCount = \App\Models\Enrollment::where('user_id', Auth::id())
                        ->where('status', 'approved')
                        ->count();
                    $pendingCount = \App\Models\Enrollment::where('user_id', Auth::id())
                        ->where('status', 'pending')
                        ->count();
                    $totalCourses = $courses->count();
                    $moodleSynced = $courses->where('moodle_course_id', '!=', null)->count();
                @endphp

                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-white">
                    <div class="text-2xl font-bold">{{ $totalCourses }}</div>
                    <div class="text-sm text-blue-100">Available Courses</div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-white">
                    <div class="text-2xl font-bold">{{ $enrolledCount }}</div>
                    <div class="text-sm text-blue-100">Enrolled Courses</div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-white">
                    <div class="text-2xl font-bold">{{ $pendingCount }}</div>
                    <div class="text-sm text-blue-100">Pending Approvals</div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-white">
                    <div class="text-2xl font-bold">{{ $moodleSynced }}</div>
                    <div class="text-sm text-blue-100">Moodle Synced</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 slide-in">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 slide-in">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Course Filters -->
        <div class="mb-8 bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-bold text-gray-900 mb-4 md:mb-0">All Available Courses</h2>
                <div class="flex flex-wrap gap-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        All Courses
                    </button>
                    <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        My Enrollments
                    </button>
                    <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Moodle Synced
                    </button>
                </div>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($courses as $course)
                @php
                    $enrollment = \App\Models\Enrollment::where('user_id', Auth::id())
                        ->where('course_id', $course->id)
                        ->first();
                    $isEnrolled = $enrollment && in_array($enrollment->status, ['pending', 'approved']);
                    $enrollmentStatus = $enrollment ? $enrollment->status : null;
                @endphp

                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover relative">
                    <!-- Status Badges -->
                    <div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
                        @if($course->moodle_course_id)
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full badge-pulse">
                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Moodle
                            </span>
                        @endif
                        
                        @if($isEnrolled)
                            @if($enrollmentStatus === 'approved')
                                <span class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    Enrolled
                                </span>
                            @elseif($enrollmentStatus === 'pending')
                                <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    Pending
                                </span>
                            @endif
                        @endif
                    </div>

                    <!-- Course Image -->
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 relative">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Course Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                            {{ $course->title }}
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ Str::limit($course->description, 100) }}
                        </p>

                        <!-- Course Meta -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Self-paced
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Certificate
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            @if(!$isEnrolled)
                                <form action="{{ route('courses.enroll.store', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg py-2.5 px-4 hover:from-blue-700 hover:to-indigo-700 transition-all flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Enroll Now
                                    </button>
                                </form>
                            @else
                                @if($enrollmentStatus === 'approved')
                                    <button disabled class="w-full bg-gray-100 text-gray-600 font-semibold rounded-lg py-2.5 px-4 cursor-not-allowed flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Already Enrolled
                                    </button>
                                @elseif($enrollmentStatus === 'pending')
                                    <button disabled class="w-full bg-yellow-100 text-yellow-700 font-semibold rounded-lg py-2.5 px-4 cursor-not-allowed flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Pending Approval
                                    </button>
                                @endif
                            @endif

                            <a href="{{ route('courses.show', $course) }}" 
                               class="w-full bg-gray-100 text-gray-700 font-semibold rounded-lg py-2.5 px-4 hover:bg-gray-200 transition-all text-center block">
                                View Details
                            </a>
                        </div>

                        <!-- Admin Actions -->
                        @hasanyrole('admin|superadmin')
                            <div class="mt-4 pt-4 border-t space-y-2">
                                @if(!$course->moodle_course_id)
                                    <button type="button" 
                                            onclick="openSyncModal({{ $course->id }}, '{{ addslashes($course->title) }}')"
                                            class="w-full bg-blue-50 text-blue-700 text-sm font-semibold rounded-lg py-2 px-3 hover:bg-blue-100 transition-all flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Sync to Moodle
                                    </button>
                                @endif

                                <div class="flex gap-2">
                                    <a href="{{ route('courses.edit', $course) }}" 
                                       class="flex-1 bg-yellow-50 text-yellow-700 text-sm font-semibold rounded-lg py-2 px-3 hover:bg-yellow-100 transition-all text-center">
                                        Edit
                                    </a>
                                    <form action="{{ route('courses.destroy', $course) }}" method="POST" class="flex-1" 
                                          onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-50 text-red-700 text-sm font-semibold rounded-lg py-2 px-3 hover:bg-red-100 transition-all">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endhasanyrole
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-gray-50 rounded-lg p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Courses Available</h3>
                        <p class="text-gray-600">Check back later for new courses.</p>
                        
                        @hasanyrole('admin|superadmin')
                            <div class="mt-4">
                                <a href="{{ route('courses.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Create First Course
                                </a>
                            </div>
                        @endhasanyrole
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Admin Quick Actions -->
        @hasanyrole('admin|superadmin')
            <div class="mt-12 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Admin Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('courses.create') }}" class="bg-white p-4 rounded-lg text-center hover:shadow-md transition">
                        <svg class="w-8 h-8 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="text-sm font-medium">Add Course</span>
                    </a>
                    
                    <a href="{{ route('admin.enrollments.index') }}" class="bg-white p-4 rounded-lg text-center hover:shadow-md transition">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Manage Enrollments</span>
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}" class="bg-white p-4 rounded-lg text-center hover:shadow-md transition">
                        <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Manage Users</span>
                    </a>
                    
                    <a href="{{ route('admin.moodle.courses.import') }}" class="bg-white p-4 rounded-lg text-center hover:shadow-md transition">
                        <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="text-sm font-medium">Sync Moodle</span>
                    </a>
                </div>
            </div>
        @endhasanyrole
    </div>

    {{-- Moodle Sync Modal (for admins) --}}
    @hasanyrole('admin|superadmin')
    <div id="syncModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
                    <h3 class="text-xl font-bold text-white">Sync Course to Moodle</h3>
                </div>
                
                <form id="syncForm" method="POST" action="" class="p-6">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Course Name:</label>
                        <p id="courseName" class="text-gray-900 font-medium"></p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="modal_moodle_course_shortname" class="block text-gray-700 font-semibold mb-2">
                            Moodle Short Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="moodle_course_shortname" id="modal_moodle_course_shortname" 
                               class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500" 
                               required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="modal_moodle_category_id" class="block text-gray-700 font-semibold mb-2">
                            Moodle Category <span class="text-red-500">*</span>
                        </label>
                        <select name="moodle_category_id" id="modal_moodle_category_id" 
                                class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500" 
                                required>
                            <option value="">Select Category</option>
                            <option value="10">Miscellaneous</option>
                            <!-- Add more categories as needed -->
                        </select>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeSyncModal()" 
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                            Sync to Moodle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openSyncModal(courseId, courseTitle) {
            document.getElementById('syncModal').classList.remove('hidden');
            document.getElementById('courseName').textContent = courseTitle;
            document.getElementById('syncForm').action = `/courses/${courseId}/sync-to-moodle`;
        }
        
        function closeSyncModal() {
            document.getElementById('syncModal').classList.add('hidden');
            document.getElementById('syncForm').reset();
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('syncModal');
            if (event.target == modal) {
                closeSyncModal();
            }
        }
    </script>
    @endhasanyrole
</x-layouts>