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
                               class="block w-full bg-gradient-to-r from-red-500 via-red-600 to-yellow-500 text-white font-semibold rounded-full text-lg px-5 py-3 shadow-lg transition-all hover:shadow-2xl focus:ring-4 focus:outline-none focus:ring-red-200">
                                Enroll Now
                            </a>
                        @else
                            <span class="block w-full bg-gray-500 text-white font-semibold rounded-full text-lg px-5 py-3 text-center">
                                Already Enrolled
                            </span>
                        @endif

                        @hasanyrole('admin|superadmin')
                        <!-- Delete Course Button: visible to admins and superadmins -->
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to delete this course?');">
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

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">&copy; Ministry of Health. All rights reserved.</p>
        </div>
    </footer>
</x-layouts>
