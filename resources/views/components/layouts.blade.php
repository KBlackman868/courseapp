<!DOCTYPE html>
<html lang="en" x-data="layout()" :class="{ 'dark': dark }" class="h-full bg-gray-50 dark:bg-gray-900">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Ministry of Health') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

  <style>
    /* Smooth fade-in */
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
    @keyframes fadeIn { to { opacity: 1; } }
    /* Nav shadow transition */
    .page-transition { transition: background-color 0.3s, border-color 0.3s; }
  </style>
</head>
<body class="h-full font-sans antialiased text-gray-900 dark:text-gray-100" x-init="init()">
  <div class="min-h-full flex flex-col">

    <!-- NAVBAR -->
    <nav class="fixed w-full z-50 backdrop-blur bg-white/70 dark:bg-gray-800/70 page-transition"
         :class="{
           'border-b border-indigo-600 bg-gradient-to-r from-indigo-500 to-blue-500': scrolled,
           'border-b border-transparent': !scrolled
         }"
         @scroll.window="scrolled = window.pageYOffset > 10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

          <!-- Logo -->
          <a href="{{ route('home') }}" class="flex items-center space-x-2 transform hover:scale-110 transition" >
            <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH Logo" class="h-8 w-8 rounded-full shadow-lg" />
            <span class="text-2xl font-extrabold text-indigo-700 dark:text-indigo-300">MOH × Moodle</span>
          </a>

          <!-- Desktop Links -->
          <div class="hidden md:flex space-x-6">
            @role('user')
              <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')"
                class="px-3 py-2 text-sm font-medium hover:text-white hover:bg-indigo-600 rounded-lg transition"
                active-class="bg-indigo-700 text-white">Home</x-nav-link>
              <x-nav-link href="{{ route('mycourses') }}" :active="request()->routeIs('mycourses')"
                class="px-3 py-2 text-sm font-medium hover:text-white hover:bg-indigo-600 rounded-lg transition"
                active-class="bg-indigo-700 text-white">My Courses</x-nav-link>
            @endrole
            @role('admin|superadmin')
              <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')"
                class="px-3 py-2 text-sm font-medium hover:text-white hover:bg-indigo-600 rounded-lg transition"
                active-class="bg-indigo-700 text-white">Users</x-nav-link>
              <x-nav-link href="{{ route('admin.enrollments.index') }}" :active="request()->routeIs('admin.enrollments.*')"
                class="px-3 py-2 text-sm font-medium hover:text-white hover:bg-indigo-600 rounded-lg transition"
                active-class="bg-indigo-700 text-white">Pending</x-nav-link>
              <x-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')"
                class="px-3 py-2 text-sm font-medium hover:text-white hover:bg-indigo-600 rounded-lg transition"
                active-class="bg-indigo-700 text-white">Create</x-nav-link>
            @endrole
          </div>

          <!-- Actions -->
          <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="p-2 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-700 transition relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032…"/></svg>
              <span class="absolute top-0 right-0 inline-block h-2 w-2 bg-red-500 rounded-full animate-ping"></span>
            </button>
            <!-- Dark Mode Toggle -->
            <button @click="toggleDark()" class="p-2 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-700 transition">
              <template x-if="!dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 16v1…"/></svg>
              </template>
              <template x-if="dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354…"/></svg>
              </template>
            </button>
            <!-- Profile -->
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" class="flex items-center space-x-2 p-1 rounded-full hover:scale-105 transition">
                @php $photo = auth()->user()->profile_photo; @endphp
                <img src="{{ $photo ? Storage::url($photo) : asset('images/default-avatar.png') }}" class="h-8 w-8 rounded-full border-2 border-indigo-600 dark:border-indigo-300" alt="Avatar">
                <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div x-show="open" @click.away="open=false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg divide-y divide-gray-200 dark:divide-gray-700">
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-700">Your Profile</a>
                <a href="{{ route('mycourses') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-700">Enrolled</a>
                <a href="{{ route('profile.settings') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-700">Settings</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900">Logout</button>
                </form>
              </div>
            </div>
            <!-- Mobile Menu Toggle -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-700 transition">
              <svg x-show="!mobileOpen" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
              <svg x-show="mobileOpen" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div x-show="mobileOpen" x-transition class="md:hidden bg-white dark:bg-gray-800 shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1">
          @role('user')
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-700">Home</a>
            <a href="{{ route('mycourses') }}" class="block px-3 py-2 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-700">My Courses</a>
          @endrole
          @role('admin|superadmin')
            <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-md font-medium hover:bg-indigo-100 dark:hover:bg-indigo-700">Users</a>
            <a href="{{ route('admin.enrollments.index') }}" class="block px-3 py-2 rounded-md font-medium hover:bg-indigo-100 dark:hover:bg-indigo-700">Pending</a>
            <a href="{{ route('courses.create') }}" class="block px-3 py-2 rounded-md font-medium hover:bg-indigo-100 dark:hover:bg-indigo-700">Create</a>
          @endrole
        </div>
      </div>
    </nav>

    <!-- HEADER -->
    <header class="pt-16 bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900 shadow animate-fade-in">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-indigo-800 dark:text-indigo-300 tracking-tight transform hover:scale-105 transition">{{ $heading }}</h1>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 overflow-y-auto py-6 animate-fade-in">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{ $slot }}
      </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6 animate-fade-in">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600 dark:text-gray-400">
        &copy; {{ date('Y') }} Ministry of Health. All rights reserved.
      </div>
    </footer>
  </div>

  <script>
    function layout() {
      return {
        dark: JSON.parse(localStorage.getItem('dark')||'false'),
        scrolled: false,
        mobileOpen: false,
        time: '',
        init() {
          this.scrolled = window.pageYOffset > 10;
          window.addEventListener('scroll', () => this.scrolled = window.pageYOffset > 10);
          setInterval(() => { this.time = new Date().toLocaleTimeString(); }, 1000);
        },
        toggleDark() { this.dark = !this.dark; localStorage.setItem('dark', this.dark); }
      }
    }
  </script>
</body>
</html>
