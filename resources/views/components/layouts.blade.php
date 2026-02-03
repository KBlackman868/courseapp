<!DOCTYPE html>
<html lang="en" x-data="layoutData()" :class="{ 'dark': darkMode }" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Ministry of Health') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.jsx'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

  <style>
    html, body { overflow-x: hidden; max-width: 100vw; }
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    /* Toast styles */
    .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
    .toast { padding: 1rem 1.5rem; border-radius: 0.5rem; color: white; font-size: 0.875rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); animation: slideIn 0.3s ease; }
    .toast-success { background: #10b981; }
    .toast-error { background: #ef4444; }
    .toast-warning { background: #f59e0b; }
    .toast-info { background: #3b82f6; }
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
  </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">

  <div class="drawer">
    <input id="main-drawer" type="checkbox" class="drawer-toggle" />

    <div class="drawer-content flex flex-col">
      <!-- Navbar - z-[100] to ensure it's always above page content -->
      <div class="navbar bg-base-100 shadow-lg sticky top-0 z-[100]">
        <!-- Mobile menu button -->
        <div class="flex-none lg:hidden">
          <label for="main-drawer" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </label>
        </div>

  <!-- Mobile Sidebar -->
  <div x-show="mobileMenuOpen"
       x-transition:enter="transition ease-in-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 z-50 w-80 bg-white dark:bg-gray-800 shadow-xl lg:hidden"
       style="display: none;">

    <div class="flex flex-col h-full">
      <!-- Sidebar Header -->
      <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
          <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH" class="w-8 h-8 rounded-full" onerror="this.src='https://ui-avatars.com/api/?name=MOH&background=6366f1&color=fff'" />
          <span class="font-bold text-gray-900 dark:text-white">MOH Learning</span>
        </div>
        <button @click="mobileMenuOpen = false" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
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
                        <a href="{{ route('dashboard.superadmin') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('dashboard.superadmin') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                          Dashboard
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                          Users
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('admin.roles.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                          Roles
                        </a>
                      @elseif(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                          Users
                        </a>
                      @endif
                      <a href="{{ route('courses.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('courses.index') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Courses
                      </a>
                      <a href="{{ route('courses.create') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('courses.create') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Course
                      </a>
                      <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                      <a href="{{ route('admin.account-requests.index') }}" class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        Account Requests
                        @php $accountPending = \App\Models\AccountRequest::pending()->count(); @endphp
                        @if($accountPending > 0)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $accountPending }}</span>@endif
                      </a>
                      <a href="{{ route('admin.course-access-requests.index') }}" class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        Course Access
                        @php $coursePending = \App\Models\CourseAccessRequest::pending()->count(); @endphp
                        @if($coursePending > 0)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $coursePending }}</span>@endif
                      </a>
                      @if(auth()->user()->hasRole('superadmin'))
                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        <a href="{{ route('admin.moodle.status') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                          Moodle Status
                        </a>
                        <a href="{{ route('moodle.sso') }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                          Open Moodle
                        </a>
                        <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                          Activity Logs
                        </a>
                      @endif
                    </div>
                  </div>
                @else
                  <!-- Regular User Nav -->
                  <a href="{{ route('home') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">Home</a>
                  <a href="{{ route('mycourses') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('mycourses') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">My Courses</a>
                @endif
              @else
                <!-- Guest Nav -->
                <a href="{{ route('home') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Home</a>
                <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Login</a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm">Register</a>
              @endauth
            </div>
          </div>

          <!-- Right: Notifications + Theme + Profile -->
          @auth
          <div class="flex items-center gap-2">
            <!-- Notifications -->
            <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              @php $unreadCount = auth()->user()->systemNotifications()->unread()->count(); @endphp
              @if($unreadCount > 0)
                <span class="absolute top-1 right-1 block h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center ring-2 ring-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
              @endif
            </a>

            <!-- Theme Toggle -->
            <button @click="toggleDarkMode()" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700 transition-colors">
              <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
              </svg>
              <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </button>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
              <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                @php $photo = auth()->user()->profile_photo; @endphp
                <img src="{{ $photo ? Storage::url($photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . '+' . auth()->user()->last_name) . '&background=6366f1&color=fff' }}"
                     alt="Avatar"
                     class="w-8 h-8 rounded-full ring-2 ring-indigo-500 ring-offset-2 object-cover"
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
                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs font-medium bg-white/20 text-white">{{ ucwords(str_replace('_', ' ', $role)) }}</span>
                  @endif
                </div>
                <div class="py-1">
                  <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profile
                  </a>
                  <a href="{{ route('profile.settings') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                  </a>
                  <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                      Sign Out
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          @endauth
        </div>
      </div>
    </nav>

      <!-- Page Content - z-0 ensures it stays below navbar (z-50) -->
      <main class="flex-1 relative z-0">
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
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
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
    <footer class="bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-sm text-gray-600 dark:text-gray-400">&copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago. All rights reserved.</p>
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
  <script>
    function layoutData() {
      return {
        darkMode: localStorage.getItem('darkMode') === 'true',
        mobileMenuOpen: false,

        init() {
          if (this.darkMode) {
            document.documentElement.classList.add('dark');
          }
          // Show session toasts
          @if(session('success'))
            this.showToast('success', "{{ session('success') }}");
          @endif
          @if(session('error'))
            this.showToast('error', "{{ session('error') }}");
          @endif
          @if(session('warning'))
            this.showToast('warning', "{{ session('warning') }}");
          @endif
          @if(session('info'))
            this.showToast('info', "{{ session('info') }}");
          @endif
        },

        toggleDarkMode() {
          this.darkMode = !this.darkMode;
          document.documentElement.classList.toggle('dark', this.darkMode);
          localStorage.setItem('darkMode', this.darkMode.toString());
        },

        showToast(type, message) {
          const container = document.getElementById('toast-container');
          const toast = document.createElement('div');
          toast.className = `toast toast-${type}`;
          toast.textContent = message;
          container.appendChild(toast);
          setTimeout(() => toast.remove(), 4000);
        }
      }
    }
  </script>
  @stack('scripts')
</body>
</html>
