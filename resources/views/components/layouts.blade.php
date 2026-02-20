<!DOCTYPE html>
<html lang="en" data-theme="light" x-data="layoutData()" :class="{ 'dark': darkMode }" :data-theme="darkMode ? 'dark' : 'light'" class="scroll-smooth">
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
    /* Sidebar transitions */
    .sidebar-nav { transition: transform 0.3s ease, width 0.3s ease; }
    .main-content-shift { transition: margin-left 0.3s ease; }
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
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col sidebar-nav"
         :class="collapsed ? 'lg:w-20' : 'lg:w-64'">
      <div class="flex h-full flex-col bg-indigo-600 px-6 pb-4 overflow-y-auto"
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
    <div class="main-content-shift lg:ml-64" :class="collapsed ? 'lg:ml-20' : 'lg:ml-64'">
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
        <p>&copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago. All rights reserved.</p>
      </footer>
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
            document.documentElement.setAttribute('data-theme', 'dark');
          } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-theme', 'light');
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

    function sidebarData() {
      return {
        mobileOpen: false,
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleCollapsed() {
          this.collapsed = !this.collapsed;
          localStorage.setItem('sidebarCollapsed', this.collapsed.toString());
        }
      }
    }
  </script>
  @stack('scripts')
</body>

@else
{{-- ============================================================ --}}
{{-- GUEST LAYOUT: Simple top navbar (no sidebar) --}}
{{-- ============================================================ --}}
<body class="min-h-screen bg-base-200">
  <div class="flex flex-col min-h-screen">
    {{-- Navbar --}}
    <div class="navbar bg-base-100 shadow-lg sticky top-0 z-50">
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
      <div class="flex-none hidden lg:flex">
        <ul class="menu menu-horizontal px-1 gap-1">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('login') }}">Login</a></li>
          <li><a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a></li>
        </ul>
      </div>
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

    {{-- Footer --}}
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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
