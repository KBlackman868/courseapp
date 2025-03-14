    <x-layouts : course="$course">
        <x-slot:heading>
            Registration Page
        </x-slot:heading>
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Master Your Skills Course</title>
        <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-50">

        <!-- Header Section -->
        <header class="bg-blue-700 text-white py-16">
            <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold mb-4">Master Your Skills with Our Course</h1>
            <p class="text-xl mb-8">
                Unlock new opportunities and become a pro in your field with expert guidance.
            </p>
            {{-- <!-- <form action="{{ route('enroll.store', $course->id) }}" method="POST"> --> --}}
                {{-- @csrf --}}
                <button type="submit" class="inline-block bg-blue-700 text-white px-10 py-4 rounded-full text-lg font-semibold shadow hover:bg-blue-800 transition">
                    Enroll Now
                </button>
            {{-- </form> --}}

        </header>

        <!-- What You Will Learn Section -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">What You Will Learn</h2>
            <div class="grid md:grid-cols-2 gap-12">
                <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-gray-700">Core Concepts</h3>
                    <p class="text-gray-600 mt-2">
                    Build a strong foundation by understanding essential principles and industry standards.
                    </p>
                </div>
                </div>
                <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12l9-5-9-5-9 5 9 5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-gray-700">Practical Projects</h3>
                    <p class="text-gray-600 mt-2">
                    Gain hands-on experience with real-life projects and interactive assignments.
                    </p>
                </div>
                </div>
                <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-gray-700">Advanced Techniques</h3>
                    <p class="text-gray-600 mt-2">
                    Learn innovative methods and tools to advance your skills beyond the basics.
                    </p>
                </div>
                </div>
                <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-gray-700">Problem Solving</h3>
                    <p class="text-gray-600 mt-2">
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
                <img class="rounded-lg shadow-md" src="{{ asset('images/interactive_learning.jpg') }}" alt="Interactive Learning in Action" />
                </div>
            </div>
            </div>
        </section>

        <!-- Video Template Section -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">Course Introduction</h2>
            <div class="flex justify-center">
                <div class="relative w-full max-w-3xl">
                <a href="https://www.youtube.com/watch?v=XGD0eGfKwlE" target="_blank">
                    <img class="w-full rounded-lg shadow-xl" src="https://img.youtube.com/vi/XGD0eGfKwlE/hqdefault.jpg" alt="Course Introduction Video Thumbnail">
                    <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white opacity-85" fill="currentColor" viewBox="0 0 84 84">
                        <circle cx="42" cy="42" r="42" fill="currentColor"/>
                        <polygon fill="white" points="33,28 33,56 57,42"/>
                    </svg>
                    </div>
                </a>
                </div>
            </div>
            <p class="text-center text-gray-600 mt-6">
                Watch this captivating introduction video to get a sneak peek into the transformative journey that awaits you.
            </p>
            <p class="text-center text-gray-600 mt-2">
                Discover what makes our course unique, from expert-led sessions to immersive, real-world applications that ensure lasting impact.
            </p>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="bg-blue-50 py-16">
            <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">Why Apply for This Course?</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                <svg class="mx-auto h-10 w-10 text-blue-700 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2 text-gray-700">Expert Guidance</h3>
                <p class="text-gray-600">
                    Learn from industry experts who provide real-world insights and hands-on training.
                </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                <svg class="mx-auto h-10 w-10 text-blue-700 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 11V3a1 1 0 011-1h0a1 1 0 011 1v8m-2 0h.01M12 11a9 9 0 100 18 9 9 0 000-18z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2 text-gray-700">Career Advancement</h3>
                <p class="text-gray-600">
                    Boost your resume and stand out in the job market with in-demand skills.
                </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                <svg class="mx-auto h-10 w-10 text-blue-700 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2 text-gray-700">Interactive Community</h3>
                <p class="text-gray-600">
                    Join a vibrant network of learners and professionals for support and collaboration.
                </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                <svg class="mx-auto h-10 w-10 text-blue-700 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <h3 class="text-xl font-semibold mb-2 text-gray-700">Flexible Learning</h3>
                <p class="text-gray-600">
                    Study at your own pace with lifetime access to course materials and updates.
                </p>
                </div>
            </div>
            </div>
        </section>

        <!-- Enrollment Call-to-Action Section -->
        <section id="enroll" class="py-16">
            <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">Ready to Get Started?</h2>
            <p class="text-xl text-gray-700 mb-8">
                Take the next step towards a brighter future. Enroll now and transform your career!
            </p>
            {{-- <form action="{{ route('enroll.store', $course->id) }}" method="POST"> --}}
                {{-- @csrf --}}
                <button type="submit" class="inline-block bg-blue-700 text-white px-10 py-4 rounded-full text-lg font-semibold shadow hover:bg-blue-800 transition">
                    Enroll Now
                </button>
            {{-- </form> --}}

            </div>
        </section>

        <!-- Footer Section -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm">&copy; Ministry of Health.</p>
            </div>
        </footer>

        </body>
        </html>
    </x-layouts>
