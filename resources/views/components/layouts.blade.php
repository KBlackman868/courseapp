        <!DOCTYPE html>
        <html lang="en" class="h-full bg-gray-100">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title></title>
            <meta name="description" content="">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="">
            <script src="https://cdn.tailwindcss.com"></script>
            <!-- In the head section -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
        </head>
        <body class="h-full">
            <div class="min-h-full">
            <nav class="bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                    <!-- MOH LOGO Change
                    <div class="shrink-0">
                        <img class="size-8" src="{{ asset('images/moh_logo.jpg') }}" alt="Your Company">
                    </div>
                    -->
                    <div class="hidden md:block">
                        <x-nav-link href="/home" :active="request()->is('home')">Home</x-nav-link>
                        <x-nav-link href="/mycourses" :active="request()->is('mycourses')">Courses</x-nav-link>
                        @role('superadmin')
                        <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->is('admin/users*')">
                            Users
                        </x-nav-link>
                        @endrole
                        @hasanyrole('superadmin|admin')
                        <x-nav-link href="{{ route('admin.enrollments.index') }}" :active="request()->is('admin/listusers*')">
                            Pending Enrollments
                        </x-nav-link>
                        @endhasanyrole
                        @hasanyrole('superadmin|admin')
                        <x-nav-link href="{{ route('courses.create') }}" :active="request()->is('courses/create*')">
                            Create Courses
                        </x-nav-link>
                        @endhasanyrole
                    </div>

                    </div>
                    <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        </button>
                        <!-- Logout form for desktop -->
                        <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ml-4 px-3 py-2 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">
                            Logout
                        </button>
                        </form>
                    </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                    <!-- Mobile menu button -->
                    <button type="button" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <!-- Menu open: "hidden", Menu closed: "block" -->
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <!-- Menu open: "block", Menu closed: "hidden" -->
                        <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                    </div>
                </div>
                </div>

                <!-- Mobile menu -->
                <div class="md:hidden" id="mobile-menu">
                <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
                    <x-nav-link href="/home" :active="request()->is('home')">Home</x-nav-link>
                    <x-nav-link href="/course" :active="request()->is('course')">Courses</x-nav-link>
                    <x-nav-link href="/mycourses" :active="request()->is('mycourses')">My Courses</x-nav-link>
                    @if (['auth','role:superadmin'])->group(function(){
                    <x-nav-link href="/userlists" :active="request()->is('userlists')">List of Users</x-nav-link>
                    });
                    @endif
                </div>
                <div class="border-t border-gray-700 pt-4 pb-3">
                    <div class="flex items-center px-5">
                    <div class="shrink-0">
                        <img class="size-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="">
                    </div>
                    <div class="ml-3">
                        <div class="text-base/5 font-medium text-white">Tom Cook</div>
                        <div class="text-sm font-medium text-gray-400">tom@example.com</div>
                    </div>
                    <button type="button" class="relative ml-auto shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>
                    </div>
                    <div class="mt-3 space-y-1 px-2">
                    <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Your Profile</a>
                    <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Settings</a>
                    <!-- Logout form for mobile -->
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

            <header class="bg-white shadow-sm">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
                </div>
            </header>

            <main>
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
                </div>
            </main>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            @if(session('success'))
                <script>
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                    };
                    toastr.success("{{ session('success') }}");
                </script>
            @endif
            @if(session('error'))
                <script>
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                    };
                    toastr.error("{{ session('error') }}");
                </script>
            @endif
        </body>
        </html>
