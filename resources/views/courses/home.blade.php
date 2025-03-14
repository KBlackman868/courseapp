    <x-layouts>
        <x-slot:heading>
            Home Page
        </x-slot:heading>

        <!-- Hero Section -->
        <header class="bg-blue-600 text-white py-20">
            <div class="max-w-7xl mx-auto px-4">
                <h1 class="text-4xl font-bold mb-4">Learn New Skills Online</h1>
                <p class="text-lg mb-6">
                    Join millions of students from around the world already learning on our platform.
                </p>
                <a href="#"
                class="inline-block bg-white text-blue-600 px-6 py-3 rounded font-semibold hover:bg-gray-100">
                    Get Started
                </a>
            </div>
        </header>

        <!-- Courses Grid -->
        <main class="max-w-7xl mx-auto px-4 py-10">
            <h2 class="text-3xl font-bold mb-8 text-gray-800">Featured Courses</h2>
            <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Card 1: Microsoft Word -->
                <div class="max-w-sm bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('images/microsoft_word.jpg') }}" alt="Microsoft Word" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                            Microsoft Word
                        </h5>
                        <p class="mb-4 text-gray-700">
                            Learn how to create professional documents with Microsoft Word, mastering formatting, templates, and advanced editing tools.
                        </p>
                        <a href="{{ route('courses.register') }}"
                        class="block w-full text-gray-900 bg-gradient-to-r from-red-200 via-red-300 to-yellow-200 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-red-100 dark:focus:ring-red-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                            Sign Up Now
                        </a>
                    </div>
                </div>

                <!-- Card 2: Microsoft PowerPoint -->
                <div class="max-w-sm bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('images/powerpoint.png') }}" alt="Microsoft PowerPoint" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                            Microsoft PowerPoint
                        </h5>
                        <p class="mb-4 text-gray-700">
                            Dive into Microsoft PowerPoint and learn how to design engaging presentations with dynamic slides and multimedia integration.
                        </p>
                        <a href="{{ route('courses.register' , ['id']) }}"
                        class="block w-full text-gray-900 bg-gradient-to-r from-red-200 via-red-300 to-yellow-200 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-red-100 dark:focus:ring-red-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                            Sign Up Now
                        </a>
                    </div>
                </div>

                <!-- Card 3: Microsoft Excel -->
                <div class="max-w-sm bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('images/microsoft_excel.jpg') }}" alt="Microsoft Excel" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                            Microsoft Excel
                        </h5>
                        <p class="mb-4 text-gray-700">
                            Learn how to analyze data, build spreadsheets, and create complex formulas with Microsoft Excel to excel in data management.
                        </p>
                        <a href="{{ route('courses.register') }}"
                        class="block w-full text-gray-900 bg-gradient-to-r from-red-200 via-red-300 to-yellow-200 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-red-100 dark:focus:ring-red-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                            Sign Up Now
                        </a>
                    </div>
                </div>

                <!-- Card 4: Microsoft Publisher -->
                <div class="max-w-sm bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('images/microsoft_publisher.jpg') }}" alt="Microsoft Publisher" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                            Microsoft Publisher
                        </h5>
                        <p class="mb-4 text-gray-700">
                            Learn how to design brochures, newsletters, and flyers with Microsoft Publisher's easy-to-use layout tools.
                        </p>
                        <a href="{{ route('courses.register') }}"
                        class="block w-full text-gray-900 bg-gradient-to-r from-red-200 via-red-300 to-yellow-200 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-red-100 dark:focus:ring-red-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                            Sign Up Now
                        </a>
                    </div>
                </div>

                <!-- Card 5: Microsoft Outlook -->
                <div class="max-w-sm bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('images/microsoft_outlook.jpg') }}" alt="Microsoft Outlook" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                            Microsoft Outlook
                        </h5>
                        <p class="mb-4 text-gray-700">
                            Master email management, scheduling, and contact organization with Microsoft Outlook to boost your productivity.
                        </p>
                        <a href="{{ route('courses.register') }}"
                        class="block w-full text-gray-900 bg-gradient-to-r from-red-200 via-red-300 to-yellow-200 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-red-100 dark:focus:ring-red-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                            Sign Up Now
                        </a>
                    </div>
                </div>

                <!-- Card 6: Microsoft Access -->
                <div class="max-w-sm bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('images/microsoft_access.jpg') }}" alt="Microsoft Access" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                            Microsoft Access
                        </h5>
                        <p class="mb-4 text-gray-700">
                            Discover how to create and manage databases effectively with Microsoft Access, streamlining your business data solutions.
                        </p>
                        <a href="{{ route('courses.register') }}"
                        class="block w-full text-gray-900 bg-gradient-to-r from-red-200 via-red-300 to-yellow-200 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-red-100 dark:focus:ring-red-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                            Sign Up Now
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </x-layouts>
