<!DOCTYPE html>
<html lang="en" x-data="layoutData()" :class="{ 'dark': darkMode }" :data-theme="darkMode ? 'dark' : 'light'" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Ministry of Health') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.jsx'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

  <style>
    /* Z-Index Strategy - Using high values to ensure menu stays on top of DaisyUI components */
    .z-content { z-index: 1; }
    .z-sticky { z-index: 100; }
    .z-dropdown { z-index: 200; }
    .z-overlay { z-index: 9999; }
    .z-modal { z-index: 10000; }
  </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

  {{-- ============================================================
       MINIMAL TOP BAR - Slim header with menu trigger
       ============================================================ --}}
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-sticky">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
      <div class="flex items-center justify-between h-14">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
          <img src="{{ asset('images/moh_logo.jpg') }}" class="h-7 w-7 rounded-full object-cover" alt="MOH" onerror="this.src='https://ui-avatars.com/api/?name=MOH&background=6366f1&color=fff&size=32'" />
          <span class="text-base font-semibold text-gray-900 dark:text-white hidden sm:block">MOH Learning</span>
        </a>

        {{-- Right Actions --}}
        <div class="flex items-center gap-1">
          @auth
            {{-- Notifications --}}
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @php $unreadCount = auth()->user()->systemNotifications()->unread()->count(); @endphp
                @if($unreadCount > 0)
                  <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
              </button>
              {{-- Notification dropdown --}}
              <div x-show="open" @click.away="open = false" x-transition
                   class="absolute right-0 mt-1 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-dropdown">
                <div class="p-2 border-b border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                  Notifications @if($unreadCount > 0)<span class="ml-1 text-xs bg-red-500 text-white px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>@endif
                </div>
                <div class="max-h-48 overflow-y-auto">
                  @php $recentNotifications = auth()->user()->systemNotifications()->latest()->take(5)->get(); @endphp
                  @forelse($recentNotifications as $notification)
                    <a href="{{ route('notifications.index') }}" class="block px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 {{ $notification->read_at ? 'text-gray-500' : 'text-gray-900 dark:text-white font-medium' }}">
                      {{ Str::limit($notification->message, 50) }}
                    </a>
                  @empty
                    <p class="px-3 py-2 text-sm text-gray-500">No notifications</p>
                  @endforelse
                </div>
                <a href="{{ route('notifications.index') }}" class="block p-2 text-center text-xs text-indigo-600 dark:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-t border-gray-200 dark:border-gray-700">View all</a>
              </div>
            </div>

            {{-- Dark Mode --}}
            <button @click="toggleDarkMode()" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
              <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
              <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>

            {{-- Profile --}}
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" class="flex items-center gap-1.5 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                @php $photo = auth()->user()->profile_photo; @endphp
                <img class="w-7 h-7 rounded-full object-cover" src="{{ $photo ? Storage::url($photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name) . '&background=6366f1&color=fff&size=32' }}" alt="" onerror="this.src='https://ui-avatars.com/api/?name=U&background=6366f1&color=fff&size=32'" />
                <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
              </button>
              <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-dropdown py-1">
                <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('profile.show') }}" class="block px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Profile</a>
                <a href="{{ route('profile.settings') }}" class="block px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Settings</a>
                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200 dark:border-gray-700 mt-1 pt-1">
                  @csrf
                  <button type="submit" class="block w-full text-left px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">Sign out</button>
                </form>
              </div>
            </div>
          @else
            <a href="{{ route('login') }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Login</a>
            <a href="{{ route('register') }}" class="px-3 py-1.5 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md">Register</a>
          @endauth

          {{-- Menu Trigger (hover on desktop, click on mobile) --}}
          <div class="relative ml-1"
               x-data="{ menuOpen: false, isMobile: window.innerWidth < 768 }"
               x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 768 })"
               @mouseenter="if(!isMobile) menuOpen = true"
               @mouseleave="if(!isMobile) menuOpen = false"
               @keydown.escape.window="menuOpen = false">
            <button @click="if(isMobile) menuOpen = !menuOpen"
                    class="flex items-center gap-1 px-2.5 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md"
                    :class="{ 'bg-gray-100 dark:bg-gray-700': menuOpen }">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
              <span class="hidden sm:inline">Menu</span>
            </button>

            {{-- Overlay Navigation Menu --}}
            <div x-show="menuOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 @click.away="menuOpen = false"
                 class="absolute right-0 mt-1 w-64 sm:w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-overlay max-h-[calc(100vh-80px)] overflow-y-auto">

              {{-- Quick Links --}}
              <div class="p-2">
                <a href="{{ route('home') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md {{ request()->routeIs('home') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                  Home
                </a>

                @auth
                  <a href="{{ route('dashboard') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md {{ request()->routeIs('dashboard*') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    Dashboard
                  </a>
                  <a href="{{ route('mycourses') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md {{ request()->routeIs('mycourses') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    My Courses
                  </a>
                  <a href="{{ route('courses.index') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Browse Courses
                  </a>

                  @if(auth()->user()->hasRole(['admin', 'superadmin', 'course_admin']))
                    {{-- Admin Section --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 my-2 pt-2">
                      <p class="px-2.5 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        @if(auth()->user()->hasRole('superadmin'))Super Admin
                        @elseif(auth()->user()->hasRole('course_admin'))Course Admin
                        @else Admin @endif
                      </p>

                      @if(auth()->user()->hasRole('superadmin'))
                        <a href="{{ route('dashboard.superadmin') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                          Analytics
                        </a>
                      @endif

                      @if(auth()->user()->hasRole(['superadmin', 'admin']))
                        <a href="{{ route('admin.users.index') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                          Users
                        </a>
                        @if(auth()->user()->hasRole('superadmin'))
                          <a href="{{ route('admin.roles.index') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Roles
                          </a>
                        @endif
                      @endif

                      <a href="{{ route('courses.create') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Course
                      </a>

                      @php $accountPending = \App\Models\AccountRequest::pending()->count(); @endphp
                      <a href="{{ route('admin.account-requests.index') }}" @click="menuOpen = false" class="flex items-center justify-between px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <span class="flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                          Account Requests
                        </span>
                        @if($accountPending > 0)<span class="text-xs bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded">{{ $accountPending }}</span>@endif
                      </a>

                      @php $coursePending = \App\Models\CourseAccessRequest::pending()->count(); @endphp
                      <a href="{{ route('admin.course-access-requests.index') }}" @click="menuOpen = false" class="flex items-center justify-between px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <span class="flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                          Course Access
                        </span>
                        @if($coursePending > 0)<span class="text-xs bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded">{{ $coursePending }}</span>@endif
                      </a>

                      @if(auth()->user()->hasRole('superadmin'))
                        <a href="{{ route('admin.moodle.status') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                          Moodle Status
                        </a>
                        <a href="{{ route('admin.activity-logs.index') }}" @click="menuOpen = false" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                          Activity Logs
                        </a>
                        <a href="{{ route('moodle.sso') }}" target="_blank" class="flex items-center gap-2 px-2.5 py-2 text-sm rounded-md text-indigo-600 dark:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                          Open Moodle
                        </a>
                      @endif
                    </div>
                  @endif
                @endauth
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  {{-- ============================================================
       MAIN CONTENT AREA - z-content ensures it stays below header/menu
       ============================================================ --}}
  <main class="flex-1 relative z-content isolate">
    {{-- Page Header (for non-landing pages) --}}
    @if(!request()->routeIs('home') && !request()->routeIs('welcome'))
      <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
          @if(isset($heading))
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $heading }}</h1>
          @elseif(View::hasSection('title'))
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">@yield('title')</h1>
          @endif
        </div>
      </div>
    @endif

    {{-- Content Area --}}
    <div class="{{ request()->routeIs('home') || request()->routeIs('welcome') ? '' : 'max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6' }}">
      @if(request()->routeIs('home') || request()->routeIs('welcome'))
        {{ $slot ?? '' }}
        @yield('content')
      @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="p-3 sm:p-4 lg:p-6">
            @if(isset($slot))
              {{ $slot }}
            @else
              @yield('content')
            @endif
          </div>
        </div>
      @endif
    </div>
  </main>

  {{-- ============================================================
       FOOTER - Always at bottom
       ============================================================ --}}
  <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-3">
      <p class="text-center text-xs sm:text-sm text-gray-500 dark:text-gray-400">
        &copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago
      </p>
    </div>
  </footer>

  {{-- Scripts --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    function layoutData() {
      return {
        darkMode: localStorage.getItem('darkMode') === 'true',
        init() {
          if (this.darkMode) document.documentElement.classList.add('dark');
          toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: "3000" };
          @if(session('success')) toastr.success("{{ session('success') }}"); @endif
          @if(session('error')) toastr.error("{{ session('error') }}"); @endif
          @if(session('warning')) toastr.warning("{{ session('warning') }}"); @endif
          @if(session('info')) toastr.info("{{ session('info') }}"); @endif
        },
        toggleDarkMode() {
          this.darkMode = !this.darkMode;
          document.documentElement.classList.toggle('dark', this.darkMode);
          localStorage.setItem('darkMode', this.darkMode.toString());
        }
      }
    }
  </script>
  @stack('scripts')
</body>
</html>
