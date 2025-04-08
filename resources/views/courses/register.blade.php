<x-layouts>
    <x-slot:heading>
        Registration Page
    </x-slot:heading>

    @php
        // Check enrollment status for this course only when the user is logged in.
        $isEnrolled = false;
        if(Auth::check()){
            $isEnrolled = \App\Models\Enrollment::where('user_id', Auth::id())
                            ->where('course_id', $course->id)
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();
        }
    @endphp

    <!-- Header Section -->
    <header class="bg-gradient-to-r from-blue-800 to-blue-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('{{ asset('images/hero-bg.jpg') }}');"></div>
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h1 class="text-6xl md:text-7xl font-extrabold mb-4 tracking-wide drop-shadow-lg animate-fadeInDown">
                Master Your Skills with Our Course
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-200 animate-fadeInUp">
                Unlock new opportunities and become a pro in your field with expert guidance.
            </p>
            @if(!$isEnrolled)
                <!-- Enrollment form -->
                <form action="{{ route('courses.enroll.store', ['course' => $course->id]) }}" method="POST" class="animate-fadeInUp">
                    @csrf
                    <button type="submit" class="inline-block bg-white text-blue-700 px-10 py-4 rounded-full font-semibold text-lg shadow-lg transition transform duration-300 hover:scale-105 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Enroll Now
                    </button>
                </form>
            @else
                <span class="inline-block bg-gray-500 text-white px-10 py-4 rounded-full font-semibold text-lg shadow-lg animate-fadeInUp">
                    Already Enrolled
                </span>
            @endif
        </div>
    </header>

    <!-- What You Will Learn Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">What You Will Learn</h2>
            <div class="grid md:grid-cols-2 gap-12">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-700">Core Concepts</h3>
                        <p class="mt-2 text-gray-600">
                            Build a strong foundation by understanding essential principles and industry standards.
                        </p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-700">Practical Projects</h3>
                        <p class="mt-2 text-gray-600">
                            Gain hands-on experience with real-life projects and interactive assignments.
                        </p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-700">Advanced Techniques</h3>
                        <p class="mt-2 text-gray-600">
                            Learn innovative methods and tools to advance your skills beyond the basics.
                        </p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-700">Problem Solving</h3>
                        <p class="mt-2 text-gray-600">
                            Develop critical thinking and problem-solving skills to tackle real-world challenges.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What To Expect Section -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">What To Expect</h2>
            <div class="md:flex md:space-x-12">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <p class="text-gray-700 text-lg leading-relaxed">
                        Immerse yourself in an engaging learning environment designed to propel you forward. Our course offers a blend of theory, interactive exercises, and real-world case studies that bring the content to life.
                    </p>
                    <p class="text-gray-700 text-lg leading-relaxed mt-4">
                        Expect in-depth tutorials, actionable insights, and a learning community that motivates and challenges you to grow. From step-by-step guides to hands-on projects, every module is crafted to ensure you gain practical skills that are immediately applicable.
                    </p>
                    <p class="text-gray-700 text-lg leading-relaxed mt-4">
                        We provide a variety of resources—including downloadable materials, comprehensive notes, and live Q&A sessions—to ensure you overcome any obstacles and fully grasp every concept.
                    </p>
                </div>
                <div class="md:w-1/2">
                    <img class="rounded-lg shadow-md" src="{{ asset('images/learning-materials.jpg') }}" alt="Learning Materials" />
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Section (Replacement for Video Introduction) -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">Real Success Stories</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-2xl transition duration-300">
                    <p class="text-gray-600 italic mb-4">"This course transformed my career. I went from a beginner to an expert in no time!"</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full mr-4" src="{{ asset('images/testimonial1.jpg') }}" alt="Alex Johnson">
                        <div>
                            <p class="text-gray-900 font-bold">Alex Johnson</p>
                            <p class="text-gray-600 text-sm">Software Developer</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-2xl transition duration-300">
                    <p class="text-gray-600 italic mb-4">"The hands-on projects and expert guidance gave me the skills I needed to excel."</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full mr-4" src="{{ asset('images/testimonial2.jpg') }}" alt="Maria Lopez">
                        <div>
                            <p class="text-gray-900 font-bold">Maria Lopez</p>
                            <p class="text-gray-600 text-sm">Data Analyst</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 3 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-2xl transition duration-300">
                    <p class="text-gray-600 italic mb-4">"This thing rel bad. Use it!"</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full mr-4" src="{{ asset('varma.png') }}" alt="Varma">
                        <div>
                            <p class="text-gray-900 font-bold">Varma Maharaj</p>
                            <p class="text-gray-600 text-sm">Solutions Manager</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enrollment Call-to-Action Section -->
    <section id="enroll" class="py-16">
        <div class="max-w-full bg-blue-700 text-white px-6 py-12 text-center">
            <h2 class="text-4xl font-bold mb-6">Ready to Get Started?</h2>
            <p class="text-xl mb-8">
                Take the next step towards a brighter future. Enroll now and transform your career!
            </p>
            <!-- Enrollment form -->
            <form action="{{ route('courses.enroll.store', ['course' => $course->id]) }}" method="POST">
                @csrf
                <button type="submit" class="inline-block bg-white text-blue-700 px-10 py-4 rounded-full text-lg font-semibold shadow hover:bg-gray-100 transition transform duration-300 hover:scale-105">
                    Enroll Now
                </button>
            </form>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-gray-900 text-white py-6 w-full">
        <div class="px-6 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} Ministry of Health. All rights reserved.</p>
        </div>
    </footer>
</x-layouts>
