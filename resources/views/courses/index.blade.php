<x-layouts>
    <x-slot:heading>
        Home Page
    </x-slot:heading>

    <!-- Hero Section -->
    <header class="relative h-[70vh] bg-gradient-to-r from-blue-800 to-blue-600 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('{{ asset('images/hero-bg.jpg') }}');"></div>
        <div class="relative z-10 text-center px-4">
            <h1 class="text-6xl md:text-7xl font-extrabold text-white tracking-wide drop-shadow-lg animate-fadeInDown">
                Learn New Skills Online
            </h1>
            <p class="mt-4 text-xl md:text-2xl text-gray-200 max-w-2xl mx-auto animate-fadeInUp">
                Join millions of students from around the world already learning on our platform.
            </p>
            <a href="{{ route('home') }}"
               class="mt-8 inline-block bg-white text-blue-700 px-10 py-4 rounded-full font-semibold text-lg shadow-lg transform transition duration-300 hover:scale-105 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-300 animate-fadeInUp">
                Get Started
            </a>
        </div>
    </header>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Courses Grid -->
    <main class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-4xl font-bold mb-10 text-gray-800 text-center">Featured Courses</h2>
        <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($courses as $course)
                @php
                    // Check if the authenticated user is enrolled in this course (pending or approved)
                    $isEnrolled = false;
                    if(Auth::check()){
                        $isEnrolled = \App\Models\Enrollment::where('user_id', Auth::id())
                                        ->where('course_id', $course->id)
                                        ->whereIn('status', ['pending', 'approved'])
                                        ->exists();
                    }
                @endphp

                <div class="relative bg-white rounded-2xl shadow-xl overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-2xl {{ $isEnrolled ? 'opacity-50 pointer-events-none' : '' }}">
                    {{-- Moodle Sync Badge --}}
                    @if($course->moodle_course_id)
                        <div class="absolute top-2 right-2 z-20">
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                Moodle Synced
                            </span>
                        </div>
                    @endif

                    <div class="relative">
                        <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover">
                        @if($isEnrolled)
                            <!-- Overlay for enrolled courses -->
                            <div class="absolute inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                                <span class="text-xl text-white font-bold">Enrolled</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="mb-3 text-2xl font-bold text-gray-900 tracking-tight">{{ $course->title }}</h3>
                        <p class="mb-4 text-gray-700 text-sm">{{ Str::limit($course->description, 150) }}</p>
                        
                        @if(!$isEnrolled)
                            <a href="{{ route('courses.register', ['course' => $course->id]) }}"
                               class="block w-full bg-gradient-to-r from-red-500 via-red-600 to-yellow-500 text-white font-semibold rounded-full text-lg px-5 py-3 shadow-lg transition-all hover:shadow-2xl focus:ring-4 focus:outline-none focus:ring-red-200 text-center">
                                Enroll Now
                            </a>
                        @else
                            <span class="block w-full bg-gray-500 text-white font-semibold rounded-full text-lg px-5 py-3 text-center">
                                Already Enrolled
                            </span>
                        @endif

                        @hasanyrole('admin|superadmin')
                            {{-- Moodle Sync Button --}}
                            @if(!$course->moodle_course_id)
                                <button type="button" 
                                        onclick="openSyncModal({{ $course->id }}, '{{ addslashes($course->title) }}')"
                                        class="w-full mt-3 bg-blue-600 text-white font-semibold rounded-full text-lg px-5 py-3 shadow hover:bg-blue-700 transition-all">
                                    Sync to Moodle
                                </button>
                            @endif

                            {{-- Edit Course Button --}}
                            <a href="{{ route('courses.edit', $course->id) }}" 
                               class="block w-full mt-3 bg-yellow-500 text-white font-semibold rounded-full text-lg px-5 py-3 text-center shadow hover:bg-yellow-600 transition-all">
                                Edit Course
                            </a>

                            <!-- Delete Course Button -->
                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="mt-3" 
                                  onsubmit="return confirm('Are you sure you want to delete this course?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white font-semibold rounded-full text-lg px-5 py-3 shadow hover:bg-red-700 transition-all">
                                    Delete Course
                                </button>
                            </form>
                        @endhasanyrole
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    {{-- Moodle Sync Modal --}}
    @hasanyrole('admin|superadmin')
    <div id="syncModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Sync Course to Moodle</h3>
                <form id="syncForm" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Course:</label>
                        <p id="courseName" class="text-gray-600"></p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="modal_moodle_course_shortname" class="block text-gray-700 font-bold mb-2">
                            Moodle Short Name: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="moodle_course_shortname" id="modal_moodle_course_shortname" 
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" 
                               required placeholder="e.g., CS101_2025">
                        <p class="text-gray-500 text-sm mt-1">Unique identifier (no spaces)</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="modal_moodle_category_id" class="block text-gray-700 font-bold mb-2">
                            Moodle Category: <span class="text-red-500">*</span>
                        </label>
                        <select name="moodle_category_id" id="modal_moodle_category_id" 
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" 
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
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeSyncModal()" 
                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
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
            
            // Generate suggested shortname
            const suggestedShortname = courseTitle.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 20) + '_' + new Date().getFullYear();
            document.getElementById('modal_moodle_course_shortname').placeholder = suggestedShortname;
        }
        
        function closeSyncModal() {
            document.getElementById('syncModal').classList.add('hidden');
            document.getElementById('syncForm').reset();
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('syncModal');
            if (event.target == modal) {
                closeSyncModal();
            }
        }
    </script>
    <script>
        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
        
        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif
        
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
        @endif
    </script>

    @endhasanyrole
</x-layouts>