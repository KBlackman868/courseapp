<!DOCTYPE html>
<html lang="en" x-data="layoutData()" :class="{ 'dark': darkMode }" :data-theme="darkMode ? 'dark' : 'light'" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Ministry of Health') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.jsx'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Inline toast styles (lighter than toastr CDN) -->
  <style>
    .toast-notification {
      position: fixed;
      top: 5rem;
      right: 1rem;
      padding: 1rem 1.5rem;
      border-radius: 0.5rem;
      color: white;
      font-weight: 500;
      z-index: 9999;
      animation: slideIn 0.3s ease-out;
      max-width: 400px;
    }
    .toast-success { background: linear-gradient(135deg, #10b981, #059669); }
    .toast-error { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .toast-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .toast-info { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
  </style>

  <style>
    /* Prevent horizontal scroll */
    html, body { overflow-x: hidden; max-width: 100vw; }
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
  </style>
</head>
<body class="min-h-screen bg-base-200">

  <div class="drawer">
    <input id="main-drawer" type="checkbox" class="drawer-toggle" />

    <div class="drawer-content flex flex-col">
      <!-- Navbar -->
      <div class="navbar bg-base-100 shadow-lg sticky top-0 z-50">
        <!-- Mobile menu button -->
        <div class="flex-none lg:hidden">
          <label for="main-drawer" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </label>
        </div>

        <!-- Logo -->
        <div class="flex-1">
          <a href="{{ route('home') }}" class="btn btn-ghost normal-case text-xl gap-2">
            <div class="avatar">
              <div class="w-8 rounded-full ring ring-primary ring-offset-base-100 ring-offset-1">
                <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH" onerror="this.src='https://ui-avatars.com/api/?name=MOH&background=6366f1&color=fff'"
                   loading="lazy" />
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
                        <li><a href="{{ config('moodle.base_url') }}" target="_blank" class="text-secondary">
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
              <div class="relative" x-data="{ profileOpen: false }">
                <button @click="profileOpen = !profileOpen"
                        class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300">
                  @php $photo = auth()->user()->profile_photo; @endphp
                  <img src="{{ $photo ? Storage::url($photo) : asset('images/default-avatar.png') }}"
                       class="h-8 w-8 rounded-full ring-2 ring-indigo-500/30 hover:ring-indigo-500/50 transition-all duration-300" alt="Avatar" loading="lazy">
                  <svg class="h-4 w-4 text-gray-600 dark:text-gray-400 transition-transform duration-300"
                       :class="{ 'rotate-180': profileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <!-- Profile Dropdown Menu -->
                <div x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     @click.away="profileOpen = false"
                     class="absolute right-0 mt-2 w-64 rounded-2xl glass dark:glass-dark shadow-xl overflow-hidden">

                  <!-- User Info Header -->
                  <div class="px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-500">
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                    <p class="text-xs text-indigo-100">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->getRoleNames()->isNotEmpty())
                      <div class="mt-1">
                        @php
                          $primaryRole = auth()->user()->getRoleNames()->first();
                          $roleColors = [
                            'superadmin' => 'bg-purple-200 text-purple-800',
                            'admin' => 'bg-indigo-200 text-indigo-800',
                            'course_admin' => 'bg-blue-200 text-blue-800',
                            'moh_staff' => 'bg-green-200 text-green-800',
                            'external_user' => 'bg-gray-200 text-gray-800'
                          ];
                          $roleDisplay = [
                            'superadmin' => 'Super Admin',
                            'admin' => 'Admin',
                            'course_admin' => 'Course Admin',
                            'moh_staff' => 'MOH Staff',
                            'external_user' => 'External User'
                          ];
                        @endphp
                        <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $roleColors[$primaryRole] ?? 'bg-gray-200 text-gray-800' }}">
                          {{ $roleDisplay[$primaryRole] ?? ucfirst(str_replace('_', ' ', $primaryRole)) }}
                        </span>
                      </div>
                    @endif
                  </div>

                  <!-- Menu Items -->
                  <div class="py-2">
                    <a href="{{ route('profile.show') }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                      <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      Your Profile
                    </a>

                    @if(!auth()->user()->hasRole(['admin', 'superadmin', 'course_admin']))
                      <a href="{{ route('mycourses') }}"
                         class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        My Courses
                      </a>
                    @endif

                    <a href="{{ route('profile.settings') }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                      <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      Settings
                    </a>

                    <hr class="my-2 border-gray-200 dark:border-gray-700">

                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit"
                              class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                        <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Sign Out
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            @endauth

            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300">
              <svg x-show="!mobileMenuOpen" class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
              <svg x-show="mobileMenuOpen" class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        @endauth
      </div>

      <!-- Page Content -->
      <main class="flex-1">
        @if(!request()->routeIs('home') && !request()->routeIs('welcome'))
          <!-- Page Header -->
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

        <!-- Main Content -->
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
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 text-white mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Brand Section -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
              <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH Logo" class="h-10 w-10 rounded-full" loading="lazy">
              <span class="text-xl font-bold">Ministry of Health Learning</span>
            </div>
            <p class="text-gray-400 text-sm max-w-md">
              Empowering healthcare professionals through continuous education and training excellence.
            </p>
            <div class="flex space-x-4 mt-4">
              <a href="#" class="text-gray-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                </svg>
              </a>
              <a href="#" class="text-gray-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path>
                </svg>
              </a>
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
              <li><a href="{{ config('moodle.base_url') }}" target="_blank" class="text-secondary">Open Moodle</a></li>
              <li><a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a></li>
            @endif
          @else
            <li><a href="{{ route('home') }}">Home</a></li>
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

  <script>
    // Lightweight toast function (no jQuery needed)
    function showToast(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `toast-notification toast-${type}`;
      toast.textContent = message;
      document.body.appendChild(toast);
      setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }

    function layoutData() {
      return {
        darkMode: localStorage.getItem('darkMode') === 'true',
        init() {
          if (this.darkMode) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }

          // Track scroll position
          window.addEventListener('scroll', () => {
            this.scrolled = window.pageYOffset > 20;
          });

          // Initialize toastr
          toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
          };

          // Display session messages
          @if(session('success'))
            toastr.success("{{ session('success') }}");
          @endif

          @if(session('error'))
            toastr.error("{{ session('error') }}");
          @endif

          @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
          @endif

          @if(session('info'))
            toastr.info("{{ session('info') }}");
          @endif
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
