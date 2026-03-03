<!DOCTYPE html>
<html lang="en" data-theme="light" x-data="layoutData()" :class="{ 'dark': darkMode }" :data-theme="darkMode ? 'dark' : 'light'" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Ministry of Health') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.jsx'])
  <link rel="stylesheet" href="{{ asset('build/assets/app-Dbqh7ppH.css') }}"/>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

  <style>
    html, body { overflow-x: hidden; max-width: 100vw; }
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    /* Sidebar layout - plain CSS so it works without Tailwind/Alpine */
    @media (min-width: 1024px) {
      .sidebar-fixed {
        position: fixed; top: 0; bottom: 0; left: 0;
        width: 16rem; z-index: 50;
        display: flex; flex-direction: column;
      }
      .sidebar-fixed.collapsed { width: 5rem; }
      .content-offset { padding-left: 16rem; }
      .content-offset.content-collapsed { padding-left: 5rem; }
    }
    .sidebar-fixed { transition: width 0.3s ease; }
    .content-offset { transition: padding-left 0.3s ease; }
    .sidebar-overlay { transition: opacity 0.3s ease; }
    [x-cloak] { display: none !important; }
  </style>
</head>

@auth
{{-- ============================================================ --}}
{{-- AUTHENTICATED LAYOUT: Sidebar + Top bar (matches AdminLayout.jsx) --}}
{{-- ============================================================ --}}
<body class="min-h-screen bg-gray-50">
  <div x-data="sidebarData()">

    {{-- Mobile sidebar overlay --}}
    <div x-show="mobileOpen" x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" @click="mobileOpen = false">
    </div>

    {{-- Mobile sidebar panel --}}
    <div x-show="mobileOpen" x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-64 lg:hidden">
      <div class="flex h-full flex-col bg-indigo-600 px-6 pb-4 overflow-y-auto">
        {{-- Close button --}}
        <div class="flex h-16 items-center justify-between">
          <a href="/" class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center">
              <span class="text-white font-bold text-sm">MOH</span>
            </div>
            <span class="text-white font-semibold">Admin Panel</span>
          </a>
          <button @click="mobileOpen = false" class="text-indigo-200 hover:text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        @include('components.sidebar-nav')
      </div>
    </div>

    {{-- Desktop sidebar --}}
    <div class="sidebar-fixed hidden lg:flex bg-indigo-600"
         :class="collapsed && 'collapsed'">
      <div class="flex h-full w-full flex-col bg-indigo-600 px-6 pb-4 overflow-y-auto"
           :class="collapsed ? 'px-3 pb-4' : 'px-6 pb-4'">
        {{-- Logo --}}
        <div class="flex h-16 shrink-0 items-center">
          <a href="/" class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
              <span class="text-white font-bold text-sm">MOH</span>
            </div>
            <span x-show="!collapsed" x-transition class="text-white font-semibold whitespace-nowrap">Admin Panel</span>
          </a>
        </div>
        <div x-show="!collapsed">
          @include('components.sidebar-nav')
        </div>
        {{-- Collapsed icon-only nav --}}
        <div x-show="collapsed" x-cloak class="mt-4 space-y-1">
          @include('components.sidebar-nav-icons')
        </div>
      </div>
    </div>

    {{-- Main content area --}}
    <div class="content-offset" :class="collapsed && 'content-collapsed'">
      {{-- Top bar --}}
      <div class="sticky top-0 z-40 flex h-16 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        {{-- Mobile hamburger --}}
        <button @click="mobileOpen = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

        {{-- Desktop collapse toggle --}}
        <button @click="toggleCollapsed()" class="hidden lg:block -m-2.5 p-2.5 text-gray-700 hover:text-indigo-600">
          <svg x-show="!collapsed" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
          <svg x-show="collapsed" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

        <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
          {{-- Page title --}}
          <div class="flex flex-1 items-center">
            @if(isset($heading))
              <h1 class="text-lg font-semibold text-gray-900">{{ $heading }}</h1>
            @elseif(View::hasSection('title'))
              <h1 class="text-lg font-semibold text-gray-900">@yield('title')</h1>
            @endif
          </div>

          {{-- Right side actions --}}
          <div class="flex items-center gap-x-4 lg:gap-x-6">
            {{-- Notifications --}}
            <a href="{{ route('notifications.index') }}" class="relative -m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
              </svg>
              @php $unreadCount = auth()->user()->systemNotifications()->unread()->count(); @endphp
              @if($unreadCount > 0)
                <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
              @endif
            </a>

            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ profileOpen: false }">
              <button @click="profileOpen = !profileOpen" class="-m-1.5 flex items-center p-1.5">
                <img class="h-8 w-8 rounded-full bg-gray-50 object-cover"
                     src="{{ auth()->user()->profile_photo ? Storage::url(auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name) . '&background=6366f1&color=fff' }}"
                     alt="">
                <span class="hidden lg:flex lg:items-center">
                  <span class="ml-4 text-sm font-semibold text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                  <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                  </svg>
                </span>
              </button>
              <div x-show="profileOpen" @click.away="profileOpen = false"
                   x-transition:enter="transition ease-out duration-100"
                   x-transition:enter-start="transform opacity-0 scale-95"
                   x-transition:enter-end="transform opacity-100 scale-100"
                   x-transition:leave="transition ease-in duration-75"
                   x-transition:leave-start="transform opacity-100 scale-100"
                   x-transition:leave-end="transform opacity-0 scale-95"
                   class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5">
                <a href="{{ route('profile.show') }}" class="block px-3 py-1 text-sm text-gray-900 hover:bg-gray-50">Your profile</a>
                <a href="/" class="block px-3 py-1 text-sm text-gray-900 hover:bg-gray-50">Back to site</a>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="block w-full text-left px-3 py-1 text-sm text-red-600 hover:bg-gray-50">Sign out</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Main content --}}
      <main class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
          @if(isset($slot))
            {{ $slot }}
          @else
            @yield('content')
          @endif
        </div>
      </main>

      {{-- Footer --}}
      <footer class="border-t border-gray-200 py-4 text-center text-sm text-gray-500">
        <div class="flex flex-wrap justify-center gap-4 mb-2">
          <a href="{{ route('terms') }}" class="hover:text-gray-700">Terms and Conditions</a>
          <a href="{{ route('privacy-policy') }}" class="hover:text-gray-700">Privacy Policy</a>
        </div>

        <!-- Logo -->
        <div class="flex-1">
          <a href="{{ route('home') }}" class="btn btn-ghost normal-case text-xl gap-2">
            <div class="avatar">
              <div class="w-8 rounded-full ring ring-primary ring-offset-base-100 ring-offset-1">
                <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH" onerror="this.src='https://ui-avatars.com/api/?name=MOH&background=6366f1&color=fff'" />
              </div>
            </div>
            <span class="hidden sm:inline gradient-text font-bold">MOH Learning</span>
          </a>
        </div>

        <!-- Desktop Navigation -->
        <div class="flex-none hidden lg:flex">
          <ul class="menu menu-horizontal px-1 gap-1">
            @auth
              @if(auth()->user()->hasRole(['admin', 'superadmin', 'course_admin']))
                <!-- Admin Navigation with Dropdown -->
                <li>
                  <details>
                    <summary class="font-medium">
                      <div class="badge badge-primary badge-sm">
                        @if(auth()->user()->hasRole('superadmin'))
                          Super Admin
                        @elseif(auth()->user()->hasRole('course_admin'))
                          Course Admin
                        @else
                          Admin
                        @endif
                      </div>
                      Menu
                    </summary>
                    <ul class="bg-base-100 rounded-box w-52 shadow-xl z-50">
                      @if(auth()->user()->hasRole('superadmin'))
                        <li><a href="{{ route('dashboard.superadmin') }}" class="{{ request()->routeIs('dashboard.superadmin') ? 'active' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                          Dashboard
                        </a></li>
                        <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                          Users
                        </a></li>
                        <li><a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                          Roles
                        </a></li>
                      @elseif(auth()->user()->hasRole('admin'))
                        <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                          Users
                        </a></li>
                      @endif
                      <li><a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.index') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Courses
                      </a></li>
                      <li><a href="{{ route('courses.create') }}" class="{{ request()->routeIs('courses.create') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Course
                      </a></li>
                      <div class="divider my-0"></div>
                      <li><a href="{{ route('admin.account-requests.index') }}">
                        Account Requests
                        @php $accountPending = \App\Models\AccountRequest::pending()->count(); @endphp
                        @if($accountPending > 0)<span class="badge badge-warning badge-sm">{{ $accountPending }}</span>@endif
                      </a></li>
                      <li><a href="{{ route('admin.course-access-requests.index') }}">
                        Course Access
                        @php $coursePending = \App\Models\CourseAccessRequest::pending()->count(); @endphp
                        @if($coursePending > 0)<span class="badge badge-warning badge-sm">{{ $coursePending }}</span>@endif
                      </a></li>
                      @if(auth()->user()->hasRole('superadmin'))
                        <div class="divider my-0"></div>
                        <li><a href="{{ route('admin.moodle.status') }}" class="{{ request()->routeIs('admin.moodle.*') ? 'active' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                          Moodle Status
                        </a></li>
                        <li><a href="{{ route('moodle.sso') }}" target="_blank" class="text-secondary">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                          Open Moodle
                        </a></li>
                        <li><a href="{{ route('admin.activity-logs.index') }}" class="{{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                          Activity Logs
                        </a></li>
                      @endif
                    </ul>
                  </details>
                </li>
              @else
                <!-- Regular User Navigation -->
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('dashboard.learner') }}" class="{{ request()->routeIs('dashboard.learner') ? 'active' : '' }}">Courses</a></li>
                <li><a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'active' : '' }}">Course Catalog</a></li>
                <li><a href="{{ route('my-learning.index') }}" class="{{ request()->routeIs('my-learning.*') ? 'active' : '' }}">My Learning</a></li>
                <li><a href="{{ route('mycourses') }}" class="{{ request()->routeIs('mycourses') ? 'active' : '' }}">My Courses</a></li>
              @endif
            @else
              <!-- Guest Navigation -->
              <li><a href="{{ route('home') }}">Home</a></li>
              <li><a href="{{ route('login') }}">Login</a></li>
              <li><a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a></li>
            @endauth
          </ul>
        </div>

        <!-- Right side - Notifications, Theme, Profile -->
        @auth
        <div class="flex-none gap-2">
          <!-- Notifications -->
          <a href="{{ route('notifications.index') }}" class="btn btn-ghost btn-circle">
            <div class="indicator">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              @php $unreadCount = auth()->user()->systemNotifications()->unread()->count(); @endphp
              @if($unreadCount > 0)
                <span class="badge badge-xs badge-error indicator-item">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
              @endif
            </div>
          </a>

          <!-- Theme Toggle -->
          <button @click="toggleDarkMode()" class="btn btn-ghost btn-circle">
            <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </button>

          <!-- Profile Dropdown -->
          <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle avatar">
              <div class="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                @php $photo = auth()->user()->profile_photo; @endphp
                <img src="{{ $photo ? Storage::url($photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . '+' . auth()->user()->last_name) . '&background=6366f1&color=fff' }}"
                     alt="Avatar"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name) }}&background=6366f1&color=fff'" />
              </div>
            </label>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box w-64 shadow-xl z-50">
              <!-- User Info -->
              <li class="menu-title bg-primary text-primary-content rounded-t-box px-4 py-3">
                <div>
                  <p class="font-semibold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                  <p class="text-xs opacity-80">{{ auth()->user()->email }}</p>
                  @if(auth()->user()->getRoleNames()->isNotEmpty())
                    @php $role = auth()->user()->getRoleNames()->first(); @endphp
                    <span class="badge badge-sm mt-1 bg-white/20 border-0">{{ ucwords(str_replace('_', ' ', $role)) }}</span>
                  @endif
                </div>
              </li>
              <li><a href="{{ route('profile.show') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profile
              </a></li>
              <li><a href="{{ route('profile.settings') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
              </a></li>
              <div class="divider my-0"></div>
              <li>
                <form method="POST" action="{{ route('logout') }}" class="p-0">
                  @csrf
                  <button type="submit" class="text-error w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Sign Out
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
        @endauth
      </div>

    {{-- Page Content --}}
    <main class="flex-1">
      @if(!request()->routeIs('home') && !request()->routeIs('welcome'))
        <div class="bg-gradient-to-r from-primary/10 to-secondary/10 py-6">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(isset($heading))
              <h1 class="text-3xl font-bold text-base-content">{{ $heading }}</h1>
            @elseif(View::hasSection('title'))
              <h1 class="text-3xl font-bold text-base-content">@yield('title')</h1>
            @endif
          </div>
        </div>
      @endif

      <div class="{{ request()->routeIs('home') || request()->routeIs('welcome') ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6' }}">
        @if(request()->routeIs('home') || request()->routeIs('welcome'))
          {{ $slot ?? '' }}
          @yield('content')
        @else
          <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
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

      <!-- Footer -->
      <footer class="footer footer-center p-6 bg-base-300 text-base-content">
        <div>
          <div class="flex flex-wrap justify-center gap-4 mb-2 text-sm">
            <a href="{{ route('terms') }}" class="link link-hover">Terms and Conditions</a>
            <a href="{{ route('privacy-policy') }}" class="link link-hover">Privacy Policy</a>
          </div>
          <p>&copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago. All rights reserved.</p>
        </div>
      </footer>
    </div>

    <!-- Mobile Drawer Sidebar -->
    <div class="drawer-side z-50">
      <label for="main-drawer" class="drawer-overlay" aria-label="close sidebar"></label>
      <ul class="menu p-4 w-80 min-h-full bg-base-100" onclick="document.getElementById('main-drawer').checked = false;">
        <!-- Close button -->
        <li class="flex justify-end mb-2">
          <label for="main-drawer" class="btn btn-sm btn-circle btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </label>
        </li>
        <!-- Logo in sidebar -->
        <li class="menu-title">
          <div class="flex items-center gap-2">
            <div class="avatar">
              <div class="w-8 rounded-full">
                <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH" onerror="this.src='https://ui-avatars.com/api/?name=MOH&background=6366f1&color=fff'" />
              </div>
            </div>
            <span class="font-bold">MOH Learning</span>
          </div>
        </li>

        @auth
          @if(auth()->user()->hasRole(['admin', 'superadmin', 'course_admin']))
            <!-- Admin Role Badge -->
            <li class="my-2">
              <div class="badge badge-primary">
                @if(auth()->user()->hasRole('superadmin'))
                  Super Admin
                @elseif(auth()->user()->hasRole('course_admin'))
                  Course Admin
                @else
                  Admin
                @endif
              </div>
            </li>

            @if(auth()->user()->hasRole('superadmin'))
              <li><a href="{{ route('dashboard.superadmin') }}">Dashboard</a></li>
              <li><a href="{{ route('admin.users.index') }}">Users</a></li>
              <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
            @elseif(auth()->user()->hasRole('admin'))
              <li><a href="{{ route('admin.users.index') }}">Users</a></li>
            @endif

            <li><a href="{{ route('courses.index') }}">Courses</a></li>
            <li><a href="{{ route('courses.create') }}">Create Course</a></li>

            <div class="divider">Pending</div>
            <li><a href="{{ route('admin.account-requests.index') }}">
              Account Requests
              @php $ap = \App\Models\AccountRequest::pending()->count(); @endphp
              @if($ap > 0)<span class="badge badge-warning badge-sm">{{ $ap }}</span>@endif
            </a></li>
            <li><a href="{{ route('admin.course-access-requests.index') }}">
              Course Access
              @php $cp = \App\Models\CourseAccessRequest::pending()->count(); @endphp
              @if($cp > 0)<span class="badge badge-warning badge-sm">{{ $cp }}</span>@endif
            </a></li>

            @if(auth()->user()->hasRole('superadmin'))
              <div class="divider">Moodle</div>
              <li><a href="{{ route('admin.moodle.status') }}">Moodle Status</a></li>
              <li><a href="{{ route('moodle.sso') }}" target="_blank" class="text-secondary">Open Moodle</a></li>
              <li><a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a></li>
            @endif
          @else
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('dashboard.learner') }}">Courses</a></li>
            <li><a href="{{ route('catalog.index') }}">Course Catalog</a></li>
            <li><a href="{{ route('my-learning.index') }}">My Learning</a></li>
            <li><a href="{{ route('mycourses') }}">My Courses</a></li>
          @endif

          <div class="divider">Account</div>
          <li><a href="{{ route('profile.show') }}">Profile</a></li>
          <li><a href="{{ route('profile.settings') }}">Settings</a></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="text-error">Sign Out</button>
            </form>
          </li>
        @else
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('login') }}">Login</a></li>
          <li><a href="{{ route('register') }}">Register</a></li>
        @endauth
      </ul>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
  <script>
    function layoutData() {
      return {
        darkMode: localStorage.getItem('darkMode') === 'true',
        init() {
          if (this.darkMode) {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-theme', 'dark');
          }
          toastr.options = {
            closeButton: true, progressBar: true,
            positionClass: "toast-top-right", timeOut: "3000"
          };
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
@endauth
</html>