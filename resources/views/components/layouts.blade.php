<!DOCTYPE html>
<html lang="en" x-data="layout()" x-bind:class="{ 'dark': dark }" class="h-full bg-gray-100 dark:bg-gray-900">
<head>
  <meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Ministry of Health') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
</head>
<body class="h-full font-sans antialiased text-gray-900 dark:text-gray-100">

  <div class="min-h-full flex flex-col">
    {{-- NAVBAR --}}
    <nav class="fixed w-full z-50 bg-green-200/80 backdrop-blur-md border-b border-green-300 dark:bg-green-800/80 dark:border-green-700 transition-colors">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          {{-- Logo & Links --}}
          <div class="flex items-center">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
              <img src="{{ asset('images/moh_logo.jpg') }}" class="h-8 w-8" alt="MOH Logo">
              <span class="font-bold text-xl text-black dark:text-white">MOH × Moodle</span>
            </a>
            <div class="hidden md:flex md:space-x-6 md:ml-10">
              @role('user')
                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')"
                  class="text-black dark:text-white hover:-translate-y-1 hover:scale-105 transition">
                  Home
                </x-nav-link>
                <x-nav-link href="{{ route('mycourses') }}" :active="request()->routeIs('mycourses')"
                  class="text-black dark:text-white hover:-translate-y-1 hover:scale-105 transition">
                  My Courses
                </x-nav-link>
              @endrole
              @role('admin|superadmin')
                <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')"
                  class="text-black dark:text-white hover:-translate-y-1 hover:scale-105 transition">
                  Users
                </x-nav-link>
                <x-nav-link href="{{ route('admin.enrollments.index') }}" :active="request()->routeIs('admin.enrollments.*')"
                  class="text-black dark:text-white hover:-translate-y-1 hover:scale-105 transition">
                  Pending
                </x-nav-link>
                <x-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')"
                  class="text-black dark:text-white hover:-translate-y-1 hover:scale-105 transition">
                  Create
                </x-nav-link>
              @endrole
            </div>
          </div>

          {{-- Toggles & Profile --}}
          <div class="flex items-center space-x-4">
            {{-- Dark Mode --}}
            <button @click="toggleDark()"
              class="p-2 rounded-full hover:-translate-y-1 hover:scale-105 transition bg-white/60 dark:bg-black/60">
              <template x-if="!dark">
                <svg class="h-6 w-6 text-black" fill="currentColor"><path d="M12 3v2m0 14v2m9-9h-2M5 12H3"/></svg>
              </template>
              <template x-if="dark">
                <svg class="h-6 w-6 text-white" fill="currentColor"><path d="M21 12.79A9 9 0 1111.21 3"/></svg>
              </template>
            </button>

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open"
                class="flex items-center space-x-1 p-1 rounded-full hover:-translate-y-1 hover:scale-105 transition">
                @php
                $photo = auth()->user()->profile_photo;
                @endphp
                <img src="{{ $photo ? Storage::url($photo) : asset('images/default-avatar.png') }}" class="h-8 w-8 rounded-full object-cover" alt="Avatar">
                <svg class="h-4 w-4 text-black dark:text-white" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div x-show="open" @click.away="open=false"
                   class="origin-top-right absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm hover:bg-green-100 dark:hover:bg-green-900">Your Profile</a>
                <a href="{{ route('mycourses') }}"     class="block px-4 py-2 text-sm hover:bg-green-100 dark:hover:bg-green-900">Enrolled</a>
                <a href="{{ route('profile.settings') }}" class="block px-4 py-2 text-sm hover:bg-green-100 dark:hover:bg-green-900">Settings</a>
                <a href="{{ route('password.change') }}"   class="block px-4 py-2 text-sm hover:bg-green-100 dark:hover:bg-green-900">Change Password</a>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-green-100 dark:hover:bg-green-900">
                    Logout
                  </button>
                </form>
              </div>
            </div>

            {{-- Mobile toggle --}}
            <button @click="mobileOpen = !mobileOpen"
              class="md:hidden p-2 rounded-md hover:-translate-y-1 hover:scale-105 transition bg-white/60 dark:bg-black/60">
              <svg x-show="!mobileOpen" class="h-6 w-6 text-black dark:text-white" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
              <svg x-show="mobileOpen"  class="h-6 w-6 text-black dark:text-white" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </div>

      {{-- Mobile menu --}}
      <div x-show="mobileOpen" class="md:hidden bg-green-200 dark:bg-green-800">
        <div class="px-2 pt-2 pb-3 space-y-1">
          @role('user')
            <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')" class="text-black dark:text-white">Home</x-nav-link>
            <x-nav-link href="{{ route('mycourses') }}" :active="request()->routeIs('mycourses')" class="text-black dark:text-white">My Courses</x-nav-link>
          @endrole
          @role('admin|superadmin')
            <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')" class="text-black dark:text-white">Users</x-nav-link>
            <x-nav-link href="{{ route('admin.enrollments.index') }}" :active="request()->routeIs('admin.enrollments.*')" class="text-black dark:text-white">Pending</x-nav-link>
            <x-nav-link href="{{ route('courses.create') }}" :active="request()->routeIs('courses.create')" class="text-black dark:text-white">Create</x-nav-link>
          @endrole
        </div>
      </div>
    </nav>

    {{-- PAGE HEADER --}}
    <header class="pt-16 bg-gray-50 dark:bg-gray-900 shadow">
      <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold">{{ $heading }}</h1>
      </div>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 overflow-y-auto py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{ $slot }}
      </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-blue-200 dark:bg-blue-800 border-t border-blue-300 dark:border-blue-700 py-6 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-800 dark:text-gray-200">
        &copy; {{ date('Y') }} Ministry of Health. All rights reserved.
      </div>
    </footer>
  </div>

  <!-- Toastr -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    function layout() {
      return {
        dark: JSON.parse(localStorage.getItem('dark') || 'false'),
        mobileOpen: false,
        toggleDark() {
          this.dark = !this.dark;
          localStorage.setItem('dark', this.dark);
        }
      }
    }

    @if(session('success'))
      toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
      toastr.error("{{ session('error') }}");
    @endif
  </script>
</body>
</html>
