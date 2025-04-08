    <!DOCTYPE html>
    <html lang="en" class="h-full bg-gray-100">
    <head>
    <link rel="canonical" href="https://demo.themesberg.com/landwind/" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry of Health X Moodle</title>

    <!-- Meta SEO -->
    <meta name="title" content="Ministry of Health X Moodle">
    <meta name="description" content="Experience a modern, secure, and customizable learning management system powered by Moodle.">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Kyle Blackman">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom Animations -->
    <style>
        @keyframes fadeInDown {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInDown {
        animation: fadeInDown 0.6s ease-out forwards;
        }
        @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
        }
    </style>
    </head>
    <body class="h-full">
    <!-- Navigation -->
    <header class="fixed w-full z-50">
        <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex items-center justify-between mx-auto p-4">
            <!-- Brand / Logo -->
            <a href="#" class="flex items-center group" title="Developed by Kyle Blackman">
            <img src="{{ asset('images/moh_logo.jpg') }}" class="h-10 mr-3" alt="Ministry of Health Logo" />
            <span class="self-center text-2xl font-semibold dark:text-white group-hover:underline">
                Ministry of Health X Moodle
            </span>
            </a>
            <!-- Mobile Menu Button -->
            <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
            </button>
            <!-- Login Button -->
            <div class="flex items-center md:order-2">
            <a href="{{ route('login') }}" class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                Login
            </a>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="md:hidden" id="navbar-default">
            <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
            <a href="#" class="block py-2 px-3 text-gray-900 bg-blue-700 rounded dark:text-white">Home</a>
            <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">About</a>
            <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Features</a>
            <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Contact</a>
            </div>
            <div class="border-t border-gray-200 pt-4 pb-3">
            <div class="flex items-center px-5">
                <img class="w-10 h-10 rounded-full" src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health Logo">
                <div class="ml-3">
                <div class="text-base font-medium text-gray-900 dark:text-white">Ministry of Health X Moodle</div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Developed by Kyle Blackman</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Your Profile</a>
                <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Settings</a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">
                    Logout
                </button>
                </form>
            </div>
            </div>
        </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="pt-20">
        <!-- Hero Section with Carousel -->
        <section class="bg-gray-50 dark:bg-gray-800">
        <div id="animation-carousel" class="relative w-full" data-carousel="slide">
            <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
            <div class="hidden duration-200 ease-linear" data-carousel-item>
                <img src="{{ asset('images/carousel-1.jpg') }}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="Slide 1">
            </div>
            <div class="hidden duration-200 ease-linear" data-carousel-item>
                <img src="{{ asset('images/carousel-2.jpg') }}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="Slide 2">
            </div>
            <div class="hidden duration-200 ease-linear" data-carousel-item="active">
                <img src="{{ asset('images/carousel-3.jpg') }}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="Slide 3">
            </div>
            <div class="hidden duration-200 ease-linear" data-carousel-item>
                <img src="{{ asset('images/carousel-4.jpg') }}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="Slide 4">
            </div>
            <div class="hidden duration-200 ease-linear" data-carousel-item>
                <img src="{{ asset('images/carousel-5.jpg') }}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="Slide 5">
            </div>
            </div>
            <button type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70">
                <svg class="w-4 h-4 text-white dark:text-gray-800" fill="none" viewBox="0 0 6 10" xmlns="http://www.w3.org/2000/svg">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1L1 5l4 4"/>
                </svg>
                <span class="sr-only">Previous</span>
            </span>
            </button>
            <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70">
                <svg class="w-4 h-4 text-white dark:text-gray-800" fill="none" viewBox="0 0 6 10" xmlns="http://www.w3.org/2000/svg">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
                <span class="sr-only">Next</span>
            </span>
            </button>
        </div>
        </section>

        <!-- Moodle Features Section -->
        <section class="bg-white dark:bg-gray-900 py-16">
        <div class="max-w-screen-xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-10">Why Choose Moodle?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg transform transition hover:scale-105">
                <svg class="w-12 h-12 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5z"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Customizable</h3>
                <p class="text-gray-600 dark:text-gray-400">Tailor Moodle to your needs with powerful customization options.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg transform transition hover:scale-105">
                <svg class="w-12 h-12 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Scalable</h3>
                <p class="text-gray-600 dark:text-gray-400">Grow seamlessly with a platform that scales with your organization.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg transform transition hover:scale-105">
                <svg class="w-12 h-12 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Secure</h3>
                <p class="text-gray-600 dark:text-gray-400">Robust security measures protect your sensitive data.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg transform transition hover:scale-105">
                <svg class="w-12 h-12 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 6 9-6"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">User Friendly</h3>
                <p class="text-gray-600 dark:text-gray-400">Intuitive design for an exceptional learning experience.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg transform transition hover:scale-105">
                <svg class="w-12 h-12 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Interactive</h3>
                <p class="text-gray-600 dark:text-gray-400">Engage with dynamic content and collaborative tools.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg transform transition hover:scale-105">
                <svg class="w-12 h-12 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Comprehensive</h3>
                <p class="text-gray-600 dark:text-gray-400">A complete suite of tools for managing your courses and learning content.</p>
            </div>
            </div>
        </div>
        </section>

        <!-- FAQ Section: Moodle Offerings -->
        <section class="bg-white dark:bg-gray-900 py-16">
        <div class="max-w-screen-xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 dark:text-white mb-10">What Can Moodle Offer?</h2>
            <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-white dark:bg-gray-900 text-gray-900 dark:text-white" data-inactive-classes="text-gray-500 dark:text-gray-400">
            <!-- FAQ Item 1 -->
            <h3 id="accordion-flush-heading-1">
                <button type="button" class="flex items-center justify-between w-full py-5 font-medium text-left text-gray-800 bg-white border-b border-gray-200 rounded-t-lg dark:bg-gray-900 dark:text-white dark:border-gray-700" data-accordion-target="#accordion-flush-body-1" aria-expanded="true" aria-controls="accordion-flush-body-1">
                <span>How customizable is Moodle?</span>
                <svg data-accordion-icon class="w-6 h-6 rotate-180 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-1" class="" aria-labelledby="accordion-flush-heading-1">
                <div class="py-5 border-b border-gray-200 dark:border-gray-700">
                <p class="mb-2 text-gray-500 dark:text-gray-400">Moodle is highly customizable. You can modify themes, integrate plugins, and tailor features to meet your institution’s needs.</p>
                </div>
            </div>
            <!-- FAQ Item 2 -->
            <h3 id="accordion-flush-heading-2">
                <button type="button" class="flex items-center justify-between w-full py-5 font-medium text-left text-gray-800 bg-white border-b border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700" data-accordion-target="#accordion-flush-body-2" aria-expanded="false" aria-controls="accordion-flush-body-2">
                <span>What tools does Moodle offer?</span>
                <svg data-accordion-icon class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-2" class="hidden" aria-labelledby="accordion-flush-heading-2">
                <div class="py-5 border-b border-gray-200 dark:border-gray-700">
                <p class="mb-2 text-gray-500 dark:text-gray-400">Moodle provides course management, collaboration tools, dashboards, multimedia support, and detailed reporting features.</p>
                </div>
            </div>
            <!-- FAQ Item 3 -->
            <h3 id="accordion-flush-heading-3">
                <button type="button" class="flex items-center justify-between w-full py-5 font-medium text-left text-gray-800 bg-white border-b border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700" data-accordion-target="#accordion-flush-body-3" aria-expanded="false" aria-controls="accordion-flush-body-3">
                <span>How secure is Moodle?</span>
                <svg data-accordion-icon class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-3" class="hidden" aria-labelledby="accordion-flush-heading-3">
                <div class="py-5 border-b border-gray-200 dark:border-gray-700">
                <p class="mb-2 text-gray-500 dark:text-gray-400">Moodle is built with strong security features, including regular updates, data encryption, and advanced user management protocols.</p>
                </div>
            </div>
            <!-- FAQ Item 4 -->
            <h3 id="accordion-flush-heading-4">
                <button type="button" class="flex items-center justify-between w-full py-5 font-medium text-left text-gray-800 bg-white border-b border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700" data-accordion-target="#accordion-flush-body-4" aria-expanded="false" aria-controls="accordion-flush-body-4">
                <span>How does Moodle support interactive learning?</span>
                <svg data-accordion-icon class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-4" class="hidden" aria-labelledby="accordion-flush-heading-4">
                <div class="py-5 border-b border-gray-200 dark:border-gray-700">
                <p class="mb-2 text-gray-500 dark:text-gray-400">Moodle supports interactive learning through forums, quizzes, real-time collaboration tools, and customizable dashboards.</p>
                </div>
            </div>
            </div>
        </div>
        </section>
    <!-- Footer Section -->
    <footer class="bg-gray-900 text-white py-6 w-full">
        <div class="max-w-screen-xl mx-auto px-6 text-center">
        <div class="flex flex-col items-center justify-center space-y-2">
            <a href="#" class="flex items-center justify-center group" title="Developed by Kyle Blackman">
            <img src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health Logo" class="h-10 mr-3" />
            <span class="text-2xl font-semibold dark:text-white group-hover:underline">
                Ministry of Health X Moodle
            </span>
            </a>
            <p class="text-sm mt-2">
            © {{ date('Y') }} Ministry of Health. All Rights Reserved.
            </p>
        </div>
        </div>
    </footer>

    <script src="https://unpkg.com/flowbite@1.4.1/dist/flowbite.js"></script>
    </body>
    </html>
