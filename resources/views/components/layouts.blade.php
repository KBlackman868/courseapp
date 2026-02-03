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
    /* DO NOT use overflow-x: hidden on html/body - it clips dropdowns */
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    /* Navbar height: p-4 (16px) * 2 + content (~40px) = ~72px, use pt-[72px] or pt-20 (80px) for safety */
  </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">

  <!-- Navbar - Fixed at top with explicit height -->
  <nav class="bg-white dark:bg-gray-800 fixed w-full z-[100] top-0 start-0 border-b border-gray-200 dark:border-gray-700 shadow-sm h-[72px]">
    <div class="max-w-screen-xl h-full flex items-center justify-between mx-auto px-4">

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
                        @if($accountPending > 0)
                          <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded">{{ $accountPending }}</span>
                        @endif
                      </a>
                    </li>
                    <li>
                      <a href="{{ route('admin.course-access-requests.index') }}" class="flex items-center justify-between w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                        <span class="flex items-center gap-2">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
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
                      <li>
                        <a href="{{ route('admin.moodle.status') }}" class="flex items-center gap-2 w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
          <!-- Notification Bell -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button" class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
              <span class="sr-only">View notifications</span>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.292-.538 1.292H5.538C5 18 5 17.301 5 16.708c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26 1.33-2.112h-6.92Z"/>
              </svg>
              @php $unreadCount = auth()->user()->systemNotifications()->unread()->count(); @endphp
              @if($unreadCount > 0)
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
              @endif
            </button>

            <!-- Notification Dropdown -->
            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 top-full mt-1 w-80 bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700 rounded-lg shadow-lg z-[200] border border-gray-200 dark:border-gray-700">
              <div class="px-4 py-2 font-medium text-center text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-t-lg">
                Notifications
                @if($unreadCount > 0)
                  <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">{{ $unreadCount }}</span>
                @endif
              </div>
              <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto">
                @php $recentNotifications = auth()->user()->systemNotifications()->latest()->take(5)->get(); @endphp
                @forelse($recentNotifications as $notification)
                  <a href="{{ route('notifications.index') }}" class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="text-gray-600 dark:text-gray-300 text-sm mb-1 {{ $notification->read_at ? '' : 'font-semibold' }}">
                      {{ Str::limit($notification->message, 60) }}
                    </div>
                    <div class="text-xs text-indigo-600 dark:text-indigo-400">{{ $notification->created_at->diffForHumans() }}</div>
                  </a>
                @empty
                  <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">No notifications</div>
                @endforelse
              </div>
              <a href="{{ route('notifications.index') }}" class="block py-2 text-sm font-medium text-center text-gray-700 dark:text-gray-300 rounded-b-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                View all
              </a>
            </div>
          </a>

          <!-- Dark Mode Toggle -->
          <button @click="toggleDarkMode()" type="button" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Mobile Navigation Menu -->
    <div x-data="{ mobileOpen: false }" @toggle-mobile-menu.window="mobileOpen = !mobileOpen"
         x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 max-h-[calc(100vh-72px)] overflow-y-auto">
      <ul class="p-4 space-y-1">
        <li>
          <a href="{{ route('home') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('home') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Home
          </a>
        </li>

        @auth
          @if(auth()->user()->hasRole(['admin', 'superadmin', 'course_admin']))
            <li class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
              <span class="block px-3 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Admin</span>
            </li>
            @if(auth()->user()->hasRole('superadmin'))
              <li><a href="{{ route('dashboard.superadmin') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a></li>
            @endif
            @if(auth()->user()->hasRole(['superadmin', 'admin']))
              <li><a href="{{ route('admin.users.index') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Users</a></li>
              @if(auth()->user()->hasRole('superadmin'))
                <li><a href="{{ route('admin.roles.index') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Roles</a></li>
              @endif
            @endif
            <li><a href="{{ route('courses.index') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Courses</a></li>
            <li><a href="{{ route('courses.create') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Create Course</a></li>
            <li>
              <a href="{{ route('admin.account-requests.index') }}" class="flex items-center justify-between py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                Account Requests
                @if($accountPending > 0)<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded">{{ $accountPending }}</span>@endif
              </a>
            </li>
            <li>
              <a href="{{ route('admin.course-access-requests.index') }}" class="flex items-center justify-between py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                Course Access
                @if($coursePending > 0)<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded">{{ $coursePending }}</span>@endif
              </a>
            </li>
            @if(auth()->user()->hasRole('superadmin'))
              <li><a href="{{ route('admin.moodle.status') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Moodle Status</a></li>
              <li><a href="{{ route('admin.activity-logs.index') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Activity Logs</a></li>
            @endif
          @else
            <li><a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('dashboard*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">Dashboard</a></li>
            <li><a href="{{ route('mycourses') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('mycourses') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">My Courses</a></li>
          @endif
        @else
          <li><a href="{{ route('login') }}" class="block py-2 px-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Login</a></li>
          <li><a href="{{ route('register') }}" class="block py-2 px-3 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 text-center">Register</a></li>
        @endauth
      </ul>
    </div>
  </nav>

  <!-- Main Content - Proper spacing for fixed navbar -->
  <main class="flex-1 pt-[72px]">
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
  <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shrink-0">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
      <p class="text-center text-gray-500 dark:text-gray-400 text-sm">
        &copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago. All rights reserved.
      </p>
    </div>
  </footer>

  {{-- Scripts --}}
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
