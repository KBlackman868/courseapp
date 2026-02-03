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

  <!-- Inline toast styles (lighter than toastr CDN) -->
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
<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">

  {{-- ============================================================
       MINIMAL TOP BAR - Slim header with menu trigger
       ============================================================ --}}
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-sticky shadow-sm">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
      <div class="flex items-center justify-between h-14">

      <!-- Logo - shrink-0 prevents compression -->
      <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
        <img src="{{ asset('images/moh_logo.jpg') }}" class="h-8 w-8 rounded-full object-cover" alt="MOH Logo" onerror="this.src='https://ui-avatars.com/api/?name=MOH&background=6366f1&color=fff'" />
        <span class="text-xl font-semibold whitespace-nowrap text-gray-900 dark:text-white hidden sm:inline">MOH Learning</span>
      </a>

      <!-- Desktop Navigation - Center with overflow handling -->
      <div class="hidden lg:flex items-center justify-center flex-1 min-w-0 mx-4">
        <ul class="flex items-center gap-x-1 xl:gap-x-2">

          <!-- Home Link -->
          <li class="shrink-0">
            <a href="{{ route('home') }}" class="block py-2 px-3 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('home') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
              Home
            </a>
          </li>

          @auth
            @if(auth()->user()->hasRole(['admin', 'superadmin', 'course_admin']))
              <!-- Admin Dropdown -->
              <li class="relative shrink-0" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-1 py-2 px-3 rounded-lg text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                  <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-medium px-1.5 py-0.5 rounded">
                    @if(auth()->user()->hasRole('superadmin'))SA
                    @elseif(auth()->user()->hasRole('course_admin'))CA
                    @else A @endif
                  </span>
                  <span>Menu</span>
                  <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                  </svg>
                </button>

                <!-- Admin Dropdown Menu - positioned outside overflow context -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="absolute left-0 top-full mt-1 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-[200] max-h-[calc(100vh-100px)] overflow-y-auto">
                  <ul class="p-2 text-sm text-gray-700 dark:text-gray-300 font-medium">

                    @if(auth()->user()->hasRole('superadmin'))
                      <li>
                        <a href="{{ route('dashboard.superadmin') }}" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded {{ request()->routeIs('dashboard.superadmin') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : '' }}">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                          Dashboard
                        </a>
                      </li>
                    @endif

                    @if(auth()->user()->hasRole(['superadmin', 'admin']))
                      <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                          Users
                        </a>
                      </li>
                      @if(auth()->user()->hasRole('superadmin'))
                        <li>
                          <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Roles
                          </a>
                        </li>
                      @endif
                    @endif

                    <li class="border-t border-gray-200 dark:border-gray-700 my-1"></li>

                    <li>
                      <a href="{{ route('courses.index') }}" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Courses
                      </a>
                    </li>
                    <li>
                      <a href="{{ route('courses.create') }}" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Course
                      </a>
                    </li>

                    <li class="border-t border-gray-200 dark:border-gray-700 my-1"></li>

                    <li>
                      <a href="{{ route('admin.account-requests.index') }}" class="flex items-center justify-between w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                        <span class="flex items-center gap-2">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                          Account Requests
                        </span>
                        @php $accountPending = \App\Models\AccountRequest::pending()->count(); @endphp
                        @if($accountPending > 0)<span class="badge badge-warning badge-sm">{{ $accountPending }}</span>@endif
                      </a></li>
                      <li><a href="{{ route('admin.course-access-requests.index') }}">
                        Course Access
                        @php $coursePending = \App\Models\CourseAccessRequest::pending()->count(); @endphp
                        @if($coursePending > 0)<span class="badge badge-warning badge-sm">{{ $coursePending }}</span>@endif
                      </a></li>
                      <li><a href="{{ route('admin.enrollment-requests.index') }}">
                        Enrollment Requests
                        @php $enrollmentPending = \App\Models\EnrollmentRequest::pending()->count(); @endphp
                        @if($enrollmentPending > 0)<span class="badge badge-warning badge-sm">{{ $enrollmentPending }}</span>@endif
                      </a></li>
                      @if(auth()->user()->hasRole('superadmin'))
                        <div class="divider my-0"></div>
                        <li><a href="{{ route('admin.moodle.status') }}" class="{{ request()->routeIs('admin.moodle.*') ? 'active' : '' }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                          Moodle Status
                        </a>
                      </li>
                      <li>
                        <a href="{{ route('moodle.sso') }}" target="_blank" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-indigo-600 dark:text-indigo-400 rounded">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                          Open Moodle
                        </a>
                      </li>
                      <li>
                        <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                          Activity Logs
                        </a>
                      </li>
                    @endif
                  </ul>
                </div>
              </li>
            @else
              <!-- Regular User Links -->
              <li class="shrink-0">
                <a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                  Dashboard
                </a>
              </li>
              <li class="shrink-0">
                <a href="{{ route('mycourses') }}" class="block py-2 px-3 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('mycourses') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                  My Courses
                </a>
              </li>
            @endif
          @else
            <!-- Guest Links -->
            <li class="shrink-0">
              <a href="{{ route('login') }}" class="block py-2 px-3 rounded-lg text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                Login
              </a>
            </li>
            <li class="shrink-0">
              <a href="{{ route('register') }}" class="block py-2 px-4 rounded-full text-sm font-medium whitespace-nowrap text-white bg-indigo-600 hover:bg-indigo-700">
                Register
              </a>
            </li>
          @endauth
        </ul>
      </div>

      <!-- Right Side Items -->
      <div class="flex items-center gap-1 shrink-0">
        @auth
        <div class="flex-none flex items-center gap-2">
          <!-- Notifications Dropdown -->
          @php
            $unreadCount = auth()->user()->systemNotifications()->unread()->count();
            $recentNotifications = auth()->user()->systemNotifications()->latest()->take(5)->get();
          @endphp
          <div class="dropdown dropdown-end dropdown-hover">
            <label tabindex="0" class="btn btn-ghost btn-circle">
              <div class="indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($unreadCount > 0)
                  <span class="badge badge-xs badge-error indicator-item">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
              </div>
            </label>
            <div tabindex="0" class="dropdown-content bg-base-100 rounded-box w-72 shadow-xl z-[100] mt-1">
              <div class="p-3 border-b border-base-200">
                <span class="font-semibold">Notifications</span>
                @if($unreadCount > 0)
                  <span class="badge badge-error badge-sm ml-2">{{ $unreadCount }}</span>
                @endif
              </div>
              <ul class="menu menu-sm p-2 max-h-64 overflow-y-auto">
                @forelse($recentNotifications as $notification)
                  <li>
                    <div class="flex flex-col items-start {{ $notification->read_at ? 'opacity-60' : '' }}">
                      <span class="text-sm font-medium">{{ Str::limit($notification->title, 35) }}</span>
                      <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                  </li>
                @empty
                  <li class="text-center py-3 text-gray-500 text-sm">No notifications</li>
                @endforelse
              </ul>
              <div class="p-2 border-t border-base-200">
                <a href="{{ route('notifications.index') }}" class="btn btn-ghost btn-sm btn-block">View All</a>
              </div>
            </div>
          </div>

          <!-- Theme Toggle -->
          <button @click="toggleDarkMode()" class="btn btn-ghost btn-circle">
            <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </button>

          <!-- User Avatar Dropdown -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
              <span class="sr-only">Open user menu</span>
              @php $photo = auth()->user()->profile_photo; @endphp
              <img class="w-8 h-8 rounded-full object-cover"
                   src="{{ $photo ? Storage::url($photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . '+' . auth()->user()->last_name) . '&background=6366f1&color=fff' }}"
                   alt="{{ auth()->user()->first_name }}"
                   onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name) }}&background=6366f1&color=fff'" />
              <span class="hidden md:block text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->first_name }}</span>
              <svg class="w-4 h-4 text-gray-500 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
              </svg>
            </button>

            <!-- Avatar Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 top-full mt-1 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-[200]">
              <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                  <img class="w-10 h-10 rounded-full object-cover"
                       src="{{ $photo ? Storage::url($photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . '+' . auth()->user()->last_name) . '&background=6366f1&color=fff' }}"
                       alt="{{ auth()->user()->first_name }}"
                       onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name) }}&background=6366f1&color=fff'" />
                  <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</div>
                  </div>
                </div>
              </div>
              <ul class="p-2 text-sm text-gray-700 dark:text-gray-300">
                <li>
                  <a href="{{ route('profile.show') }}" class="flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                  </a>
                </li>
                <li>
                  <a href="{{ route('profile.settings') }}" class="flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                  </a>
                </li>
                <li class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 w-full p-2 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <button x-data @click="$dispatch('toggle-mobile-menu')" type="button" class="lg:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
          <span class="sr-only">Open main menu</span>
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
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
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $heading }}</h1>
          @elseif(View::hasSection('title'))
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">@yield('title')</h1>
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
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
          <div class="p-4 sm:p-6">
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
      <footer class="footer footer-center p-4 bg-base-300 text-base-content">
        <p>&copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago. All rights reserved.</p>
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
            <li><a href="{{ route('admin.enrollment-requests.index') }}">
              Enrollment Requests
              @php $ep = \App\Models\EnrollmentRequest::pending()->count(); @endphp
              @if($ep > 0)<span class="badge badge-warning badge-sm">{{ $ep }}</span>@endif
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
<<<<<<< HEAD
        darkMode: false,
        scrolled: false,
        mobileMenuOpen: false,

        init() {
          if (this.darkMode) {
            document.documentElement.classList.add('dark');
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
