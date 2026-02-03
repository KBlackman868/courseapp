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

  <!-- Navbar - Fixed at top -->
  <nav class="bg-white dark:bg-gray-800 fixed w-full z-[100] top-0 start-0 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">

      <!-- Logo -->
      <a href="{{ route('home') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
        <img src="{{ asset('images/moh_logo.jpg') }}" class="h-8 rounded-full" alt="MOH Logo" onerror="this.src='https://ui-avatars.com/api/?name=MOH&background=6366f1&color=fff'" />
        <span class="self-center text-xl font-semibold whitespace-nowrap text-gray-900 dark:text-white">MOH Learning</span>
      </a>

      <!-- Right Side Items (Notifications, Theme, Avatar) - Desktop -->
      <div class="flex items-center md:order-2 space-x-3 rtl:space-x-reverse">

        @auth
          <!-- Notification Bell -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button" class="relative inline-flex items-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600">
              <span class="sr-only">View notifications</span>
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.292-.538 1.292H5.538C5 18 5 17.301 5 16.708c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26 1.33-2.112h-6.92Z"/>
              </svg>
              @php $unreadCount = auth()->user()->systemNotifications()->unread()->count(); @endphp
              @if($unreadCount > 0)
                <div class="absolute block w-3 h-3 bg-red-500 border-2 border-white dark:border-gray-800 rounded-full top-1 end-1"></div>
              @endif
            </button>

            <!-- Notification Dropdown -->
            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-80 max-w-sm bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700 rounded-lg shadow-lg z-[200] border border-gray-200 dark:border-gray-700">
              <div class="block px-4 py-2 font-medium text-center text-gray-700 dark:text-gray-300 rounded-t-lg bg-gray-50 dark:bg-gray-700">
                Notifications
                @if($unreadCount > 0)
                  <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">{{ $unreadCount }}</span>
                @endif
              </div>
              <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto">
                @php $recentNotifications = auth()->user()->systemNotifications()->latest()->take(5)->get(); @endphp
                @forelse($recentNotifications as $notification)
                  <a href="{{ route('notifications.index') }}" class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="w-full">
                      <div class="text-gray-600 dark:text-gray-300 text-sm mb-1 {{ $notification->read_at ? '' : 'font-semibold' }}">
                        {{ Str::limit($notification->message, 60) }}
                      </div>
                      <div class="text-xs text-indigo-600 dark:text-indigo-400">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                  </a>
                @empty
                  <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">No notifications</div>
                @endforelse
              </div>
              <a href="{{ route('notifications.index') }}" class="block py-2 text-sm font-medium text-center text-gray-700 dark:text-gray-300 rounded-b-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                <div class="inline-flex items-center">
                  <svg class="w-4 h-4 me-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  View all
                </div>
              </a>
            </div>
          </div>

          <!-- Dark Mode Toggle -->
          <button @click="toggleDarkMode()" type="button" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600">
            <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </button>

          <!-- User Avatar Dropdown -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button" class="flex items-center text-sm pe-1 font-medium text-gray-900 dark:text-white rounded-full hover:text-indigo-600 dark:hover:text-indigo-400 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700">
              <span class="sr-only">Open user menu</span>
              @php $photo = auth()->user()->profile_photo; @endphp
              <img class="w-8 h-8 me-2 rounded-full object-cover"
                   src="{{ $photo ? Storage::url($photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . '+' . auth()->user()->last_name) . '&background=6366f1&color=fff' }}"
                   alt="{{ auth()->user()->first_name }}"
                   onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name) }}&background=6366f1&color=fff'" />
              <span class="hidden md:inline">{{ auth()->user()->first_name }}</span>
              <svg class="w-4 h-4 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
              </svg>
            </button>

            <!-- Avatar Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-[200]">
              <div class="p-2">
                <div class="flex items-center px-2.5 py-2 space-x-3 bg-gray-100 dark:bg-gray-700 rounded">
                  <img class="w-10 h-10 rounded-full object-cover"
                       src="{{ $photo ? Storage::url($photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . '+' . auth()->user()->last_name) . '&background=6366f1&color=fff' }}"
                       alt="{{ auth()->user()->first_name }}"
                       onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name) }}&background=6366f1&color=fff'" />
                  <div class="text-sm flex-1 min-w-0">
                    <div class="font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="truncate text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</div>
                  </div>
                  @if(auth()->user()->getRoleNames()->isNotEmpty())
                    @php $role = auth()->user()->getRoleNames()->first(); @endphp
                    <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-medium px-2 py-0.5 rounded">{{ ucfirst($role) }}</span>
                  @endif
                </div>
              </div>
              <ul class="px-2 pb-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                <li>
                  <a href="{{ route('profile.show') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                  </a>
                </li>
                <li>
                  <a href="{{ route('profile.settings') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                  </a>
                </li>
                <li>
                  <a href="{{ route('notifications.index') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notifications
                  </a>
                </li>
                <li class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center w-full p-2 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                      <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                      </svg>
                      Sign out
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        @endauth

        <!-- Mobile menu button -->
        <button x-data @click="$dispatch('toggle-mobile-menu')" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 dark:text-gray-400 rounded-lg md:hidden hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600">
          <span class="sr-only">Open main menu</span>
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <!-- Main Navigation -->
      <div x-data="{ mobileOpen: false }" @toggle-mobile-menu.window="mobileOpen = !mobileOpen"
           :class="{ 'hidden': !mobileOpen, 'block': mobileOpen }"
           class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-main">
        <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white md:dark:bg-gray-800">

          <!-- Home Link -->
          <li>
            <a href="{{ route('home') }}" class="block py-2 px-3 {{ request()->routeIs('home') ? 'text-white bg-indigo-600 rounded md:bg-transparent md:text-indigo-600 md:dark:text-indigo-400' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 md:hover:bg-transparent md:hover:text-indigo-600 md:dark:hover:text-indigo-400' }} md:p-0" aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">
              Home
            </a>
          </li>

          @auth
            @if(auth()->user()->hasRole(['admin', 'superadmin', 'course_admin']))
              <!-- Admin Dropdown -->
              <li class="relative" x-data="{ open: false, subOpen: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full py-2 px-3 font-medium text-gray-900 dark:text-white md:w-auto hover:bg-gray-100 dark:hover:bg-gray-700 md:hover:bg-transparent md:border-0 md:hover:text-indigo-600 md:dark:hover:text-indigo-400 md:p-0">
                  <span class="inline-flex items-center">
                    <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-medium px-2 py-0.5 rounded me-2">
                      @if(auth()->user()->hasRole('superadmin'))Super Admin
                      @elseif(auth()->user()->hasRole('course_admin'))Course Admin
                      @else Admin @endif
                    </span>
                    Menu
                  </span>
                  <svg class="w-4 h-4 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                  </svg>
                </button>

                <!-- Admin Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute left-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-[200]">
                  <ul class="p-2 text-sm text-gray-700 dark:text-gray-300 font-medium">

                    @if(auth()->user()->hasRole('superadmin'))
                      <li>
                        <a href="{{ route('dashboard.superadmin') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded {{ request()->routeIs('dashboard.superadmin') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : '' }}">
                          <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                          Dashboard
                        </a>
                      </li>
                    @endif

                    <!-- Users Sub-dropdown -->
                    @if(auth()->user()->hasRole(['superadmin', 'admin']))
                      <li class="relative" x-data="{ subOpen: false }">
                        <button @click="subOpen = !subOpen" @mouseenter="subOpen = true" class="inline-flex items-center justify-between w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                          <span class="inline-flex items-center">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            User Management
                          </span>
                          <svg class="w-4 h-4 ms-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                        </button>
                        <div x-show="subOpen" @click.away="subOpen = false" @mouseleave="subOpen = false" x-transition
                             class="absolute left-full top-0 ml-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-[210]">
                          <ul class="p-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                            <li><a href="{{ route('admin.users.index') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">All Users</a></li>
                            @if(auth()->user()->hasRole('superadmin'))
                              <li><a href="{{ route('admin.roles.index') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">Roles & Permissions</a></li>
                            @endif
                          </ul>
                        </div>
                      </li>
                    @endif

                    <!-- Courses Sub-dropdown -->
                    <li class="relative" x-data="{ subOpen: false }">
                      <button @click="subOpen = !subOpen" @mouseenter="subOpen = true" class="inline-flex items-center justify-between w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                        <span class="inline-flex items-center">
                          <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                          Courses
                        </span>
                        <svg class="w-4 h-4 ms-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                      </button>
                      <div x-show="subOpen" @click.away="subOpen = false" @mouseleave="subOpen = false" x-transition
                           class="absolute left-full top-0 ml-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-[210]">
                        <ul class="p-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                          <li><a href="{{ route('courses.index') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">All Courses</a></li>
                          <li><a href="{{ route('courses.create') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">Create Course</a></li>
                        </ul>
                      </div>
                    </li>

                    <li class="border-t border-gray-200 dark:border-gray-700 my-1"></li>

                    <!-- Requests -->
                    <li>
                      <a href="{{ route('admin.account-requests.index') }}" class="inline-flex items-center justify-between w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                        <span class="inline-flex items-center">
                          <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                          Account Requests
                        </span>
                        @php $accountPending = \App\Models\AccountRequest::pending()->count(); @endphp
                        @if($accountPending > 0)
                          <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded">{{ $accountPending }}</span>
                        @endif
                      </a>
                    </li>
                    <li>
                      <a href="{{ route('admin.course-access-requests.index') }}" class="inline-flex items-center justify-between w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                        <span class="inline-flex items-center">
                          <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                          Course Access
                        </span>
                        @php $coursePending = \App\Models\CourseAccessRequest::pending()->count(); @endphp
                        @if($coursePending > 0)
                          <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded">{{ $coursePending }}</span>
                        @endif
                      </a>
                    </li>

                    @if(auth()->user()->hasRole('superadmin'))
                      <li class="border-t border-gray-200 dark:border-gray-700 my-1"></li>

                      <!-- Moodle Sub-dropdown -->
                      <li class="relative" x-data="{ subOpen: false }">
                        <button @click="subOpen = !subOpen" @mouseenter="subOpen = true" class="inline-flex items-center justify-between w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                          <span class="inline-flex items-center">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Moodle
                          </span>
                          <svg class="w-4 h-4 ms-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                        </button>
                        <div x-show="subOpen" @click.away="subOpen = false" @mouseleave="subOpen = false" x-transition
                             class="absolute left-full top-0 ml-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-[210]">
                          <ul class="p-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                            <li><a href="{{ route('admin.moodle.status') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">Moodle Status</a></li>
                            <li><a href="{{ route('moodle.sso') }}" target="_blank" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-indigo-600 dark:text-indigo-400 rounded">Open Moodle <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg></a></li>
                          </ul>
                        </div>
                      </li>

                      <li>
                        <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                          <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                          Activity Logs
                        </a>
                      </li>
                    @endif
                  </ul>
                </div>
              </li>
            @else
              <!-- Regular User Links -->
              <li>
                <a href="{{ route('dashboard') }}" class="block py-2 px-3 {{ request()->routeIs('dashboard*') ? 'text-white bg-indigo-600 rounded md:bg-transparent md:text-indigo-600 md:dark:text-indigo-400' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 md:hover:bg-transparent md:hover:text-indigo-600 md:dark:hover:text-indigo-400' }} md:p-0">
                  Dashboard
                </a>
              </li>
              <li>
                <a href="{{ route('mycourses') }}" class="block py-2 px-3 {{ request()->routeIs('mycourses') ? 'text-white bg-indigo-600 rounded md:bg-transparent md:text-indigo-600 md:dark:text-indigo-400' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 md:hover:bg-transparent md:hover:text-indigo-600 md:dark:hover:text-indigo-400' }} md:p-0">
                  My Courses
                </a>
              </li>
            @endif
          @else
            <!-- Guest Links -->
            <li>
              <a href="{{ route('login') }}" class="block py-2 px-3 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 md:hover:bg-transparent md:hover:text-indigo-600 md:dark:hover:text-indigo-400 md:p-0">
                Login
              </a>
            </li>
            <li>
              <a href="{{ route('register') }}" class="block py-2 px-3 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg md:rounded-full md:px-4">
                Register
              </a>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content - Add padding-top for fixed navbar -->
  <main class="pt-20 min-h-screen">
    @if(!request()->routeIs('home') && !request()->routeIs('welcome'))
      <!-- Page Header -->
      <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 py-6 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          @if(isset($heading))
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $heading }}</h1>
          @elseif(View::hasSection('title'))
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">@yield('title')</h1>
          @endif
        </div>
      </div>
    @endif

    <!-- Main Content Area -->
    <div class="{{ request()->routeIs('home') || request()->routeIs('welcome') ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6' }}">
      @if(request()->routeIs('home') || request()->routeIs('welcome'))
        {{ $slot ?? '' }}
        @yield('content')
      @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
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
  <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <p class="text-center text-gray-500 dark:text-gray-400 text-sm">
        &copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago. All rights reserved.
      </p>
    </div>

    <!-- Mobile Drawer Sidebar -->
    <div class="drawer-side z-[150]">
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
