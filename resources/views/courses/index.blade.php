<x-layouts>
    <x-slot:heading>
        Home Page
    </x-slot:heading>

    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #0ea5e9);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes pulse-glow {
            0%, 100% { 
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.5),
                           0 0 40px rgba(59, 130, 246, 0.3);
            }
            50% { 
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.8),
                           0 0 60px rgba(59, 130, 246, 0.5);
            }
        }

        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slideInLeft {
            animation: slideInLeft 1s ease-out;
        }

        .animate-slideInRight {
            animation: slideInRight 1s ease-out;
        }

        .animate-fadeInUp {
            animation: fadeInUp 1s ease-out;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
        }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            filter: blur(40px);
            animation: morph 8s ease-in-out infinite;
        }

        @keyframes morph {
            0%, 100% { 
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                transform: rotate(0deg);
            }
            50% { 
                border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
                transform: rotate(180deg);
            }
        }
    </style>

    <!-- Hero Section -->
    <header class="relative min-h-[85vh] animated-gradient flex items-center justify-center overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply opacity-30 blob"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply opacity-30 blob" style="animation-delay: 4s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-300 rounded-full mix-blend-multiply opacity-30 blob" style="animation-delay: 2s;"></div>
        </div>

        <!-- Floating particles -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-4 h-4 bg-white rounded-full opacity-60" style="animation: float 6s ease-in-out infinite;"></div>
            <div class="absolute top-3/4 right-1/3 w-3 h-3 bg-white rounded-full opacity-50" style="animation: float 6s ease-in-out infinite; animation-delay: 2s;"></div>
            <div class="absolute bottom-1/4 right-1/4 w-2 h-2 bg-white rounded-full opacity-40" style="animation: float 6s ease-in-out infinite; animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
            <div class="inline-block mb-6">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white text-blue-600 animate-fadeInUp">
                    <span class="w-2 h-2 bg-blue-600 rounded-full mr-2 animate-pulse"></span>
                    Ministry of Health Learning Management System
                </span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight drop-shadow-2xl animate-slideInLeft mb-6">
                Advance Your
                <span class="block mt-2 text-white">
                    Healthcare Career
                </span>
            </h1>
            
            <p class="mt-6 text-xl md:text-2xl text-white max-w-3xl mx-auto animate-slideInRight leading-relaxed drop-shadow-lg">
                Access professional development courses, clinical training, and continuous education designed for healthcare professionals.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center animate-fadeInUp">
                <a href="{{ route('courses.index') }}"
                   class="group relative inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 rounded-full font-bold text-lg shadow-2xl transform transition-all duration-300 hover:scale-105 hover:shadow-3xl overflow-hidden">
                    <span class="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    <span class="relative group-hover:text-white transition-colors duration-300">Explore Courses</span>
                    <svg class="relative w-5 h-5 ml-2 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                
                @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-md border-2 border-white text-white rounded-full font-bold text-lg transform transition-all duration-300 hover:bg-white hover:text-blue-600 hover:scale-105">
                    Get Started
                </a>
                @endguest
            </div>

            <!-- Simple Stats -->
            <div class="mt-16 flex justify-center items-center gap-8 text-white animate-fadeInUp" style="animation-delay: 0.5s;">
                <div class="text-center">
                    <div class="text-4xl font-bold">{{ $courses->count() }}+</div>
                    <div class="text-sm opacity-90">Courses Available</div>
                </div>
                <div class="hidden md:block w-px h-12 bg-white/30"></div>
                <div class="text-center">
                    <div class="text-4xl font-bold">24/7</div>
                    <div class="text-sm opacity-90">Online Access</div>
                </div>
                <div class="hidden md:block w-px h-12 bg-white/30"></div>
                <div class="text-center">
                    <div class="text-4xl font-bold">100%</div>
                    <div class="text-sm opacity-90">Digital Learning</div>
                </div>
            </div>
        </div>
    </header>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 animate-slideInRight">
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
                <button type="button" class="ml-4 text-white/80 hover:text-white" onclick="this.parentElement.parentElement.style.display='none';">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 z-50 animate-slideInRight">
            <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
                <button type="button" class="ml-4 text-white/80 hover:text-white" onclick="this.parentElement.parentElement.style.display='none';">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Courses Section -->
    <main class="relative py-20 bg-gray-50">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-50/30"></div>
        
        <div class="relative max-w-7xl mx-auto px-4">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Featured <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Training Modules</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Enhance your skills with our comprehensive healthcare training programs
                </p>
            </div>

            <!-- Courses Grid -->
            <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($courses as $course)
                    @php
                        $isEnrolled = false;
                        if(Auth::check()){
                            $isEnrolled = \App\Models\Enrollment::where('user_id', Auth::id())
                                            ->where('course_id', $course->id)
                                            ->whereIn('status', ['pending', 'approved'])
                                            ->exists();
                        }
                    @endphp

                    <div class="relative bg-white rounded-3xl shadow-xl overflow-hidden card-hover group {{ $isEnrolled ? 'opacity-75' : '' }}">
                        {{-- Moodle Sync Badge --}}
                        @if($course->moodle_course_id)
                            <div class="absolute top-4 right-4 z-20">
                                <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Moodle Synced
                                </span>
                            </div>
                        @endif

                        <!-- Course Image -->
                        <div class="relative h-56 overflow-hidden bg-gradient-to-br from-blue-400 to-indigo-600">
                            @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-20 h-20 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            @if($isEnrolled)
                                <!-- Enrolled Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 to-gray-900/50 flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-16 h-16 text-white mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xl text-white font-bold">Enrolled</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Category Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/20 backdrop-blur-md text-white text-xs font-semibold px-3 py-1.5 rounded-full border border-white/30">
                                    Healthcare Training
                                </span>
                            </div>
                        </div>

                        <!-- Course Content -->
                        <div class="p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $course->title }}
                            </h3>
                            
                            <p class="text-gray-600 mb-6 line-clamp-3">
                                {{ Str::limit($course->description, 150) }}
                            </p>

                            <!-- Course Meta -->
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Self-paced</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Certificate</span>
                                </div>
                            </div>
                            
                            @if(!$isEnrolled)
                                <a href="{{ route('courses.register', ['course' => $course->id]) }}"
                                   class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl text-center py-3 px-5 shadow-lg transition-all hover:shadow-2xl hover:scale-105 pulse-glow">
                                    Enroll Now
                                    <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            @else
                                <button disabled class="block w-full bg-gray-400 text-white font-bold rounded-xl text-center py-3 px-5 cursor-not-allowed">
                                    Already Enrolled
                                    <svg class="inline-block w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            @endif

                            @hasanyrole('admin|superadmin')
                                <div class="mt-4 space-y-2">
                                    {{-- Moodle Sync Button --}}
                                    @if(!$course->moodle_course_id)
                                        <button type="button" 
                                                onclick="openSyncModal({{ $course->id }}, '{{ addslashes($course->title) }}')"
                                                class="w-full bg-blue-100 text-blue-700 font-semibold rounded-xl py-2.5 px-4 hover:bg-blue-200 transition-all flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Sync to Moodle
                                        </button>
                                    @endif

                                    <div class="flex gap-2">
                                        {{-- Edit Button --}}
                                        <a href="{{ route('courses.edit', $course->id) }}" 
                                           class="flex-1 bg-yellow-100 text-yellow-700 font-semibold rounded-xl py-2.5 px-4 hover:bg-yellow-200 transition-all text-center">
                                            Edit
                                        </a>

                                        {{-- Delete Form --}}
                                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="flex-1" 
                                              onsubmit="return confirm('Are you sure you want to delete this course?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full bg-red-100 text-red-700 font-semibold rounded-xl py-2.5 px-4 hover:bg-red-200 transition-all">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endhasanyrole
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View All Courses Button -->
            @if($courses->count() >= 6)
            <div class="text-center mt-12">
                <a href="{{ route('courses.index') }}" 
                   class="inline-flex items-center px-8 py-3 bg-white text-blue-600 rounded-full font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all border-2 border-blue-100">
                    View All Courses
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </main>

    {{-- Moodle Sync Modal --}}
    @hasanyrole('admin|superadmin')
    <div id="syncModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 w-full max-w-md animate-fadeInUp">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
                    <h3 class="text-xl font-bold text-white">Sync Course to Moodle</h3>
                    <p class="text-blue-100 text-sm mt-1">Connect this course to your Moodle LMS</p>
                </div>
                
                <!-- Modal Body -->
                <form id="syncForm" method="POST" action="" class="p-6">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Course Name:</label>
                        <p id="courseName" class="text-gray-900 font-medium bg-gray-50 p-3 rounded-lg"></p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="modal_moodle_course_shortname" class="block text-gray-700 font-semibold mb-2">
                            Moodle Short Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="moodle_course_shortname" id="modal_moodle_course_shortname" 
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                               required placeholder="e.g., CS101_2025">
                        <p class="text-gray-500 text-sm mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Unique identifier (no spaces)
                        </p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="modal_moodle_category_id" class="block text-gray-700 font-semibold mb-2">
                            Moodle Category <span class="text-red-500">*</span>
                        </label>
                        <select name="moodle_category_id" id="modal_moodle_category_id" 
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                required>
                            <option value="">Select Category</option>
                            <option value="10">LMS Support</option>
                            <option value="14">Sandboxes</option>
                            <option value="27">Office Productivity</option>
                            <option value="2">HIV Related Training</option>
                            <option value="23">HIV Testing</option>
                            <option value="24">HCW Continuing Education</option>
                            <option value="22">Infection Prevention and Control (IPC)</option>
                            <option value="26">Monitoring and Evaluation Support Training</option>
                            <option value="25">Job Aids, Manuals, and SOPs</option>
                            <option value="18">Capacity Building</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeSyncModal()" 
                                class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
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
            
            const suggestedShortname = courseTitle.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 20) + '_' + new Date().getFullYear();
            document.getElementById('modal_moodle_course_shortname').placeholder = suggestedShortname;
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