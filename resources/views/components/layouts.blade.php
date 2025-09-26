<!DOCTYPE html>
<html lang="en" x-data="layoutData()" :class="{ 'dark': darkMode }" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Ministry of Health') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

  <style>
    /* Modern animations and effects */
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    @keyframes gradient-shift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    @keyframes pulse-glow {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
    
    /* Thanos Animation */
    @keyframes snap {
      0% { transform: rotate(0deg) scale(1); }
      25% { transform: rotate(-5deg) scale(1.05); }
      50% { transform: rotate(5deg) scale(1.1); filter: hue-rotate(180deg); }
      75% { transform: rotate(-3deg) scale(1.05); }
      100% { transform: rotate(0deg) scale(1); }
    }
    
    @keyframes dust {
      0% { opacity: 1; transform: translateY(0) rotateZ(0deg); }
      100% { opacity: 0; transform: translateY(-20px) rotateZ(720deg); filter: blur(4px); }
    }
    
    @keyframes infinity-glow {
      0%, 100% { 
        box-shadow: 0 0 20px rgba(147, 51, 234, 0.8), 
                    0 0 40px rgba(147, 51, 234, 0.6),
                    0 0 60px rgba(147, 51, 234, 0.4);
      }
      50% { 
        box-shadow: 0 0 30px rgba(236, 72, 153, 0.8), 
                    0 0 60px rgba(236, 72, 153, 0.6),
                    0 0 90px rgba(236, 72, 153, 0.4);
      }
    }
    
    /* Thanos Badge Animation */
    @keyframes disintegrate {
      0% { 
        opacity: 1; 
        transform: scale(1) rotate(0deg);
        filter: blur(0);
      }
      25% {
        opacity: 0.8;
        transform: scale(1.1) rotate(5deg);
      }
      50% {
        opacity: 0.5;
        transform: scale(0.9) rotate(-5deg);
        filter: blur(1px);
      }
      75% {
        opacity: 0.2;
        transform: scale(1.2) rotate(10deg) translateY(-10px);
        filter: blur(3px);
      }
      100% { 
        opacity: 0; 
        transform: scale(0.5) rotate(180deg) translateY(-30px);
        filter: blur(10px);
      }
    }
    
    @keyframes reassemble {
      0% { 
        opacity: 0; 
        transform: scale(0.5) rotate(180deg) translateY(-30px);
        filter: blur(10px);
      }
      50% {
        opacity: 0.5;
        transform: scale(1.2) rotate(90deg) translateY(-15px);
        filter: blur(5px);
      }
      100% { 
        opacity: 1; 
        transform: scale(1) rotate(0deg) translateY(0);
        filter: blur(0);
      }
    }
    
    @keyframes particle-float {
      0% {
        transform: translate(0, 0) scale(1);
        opacity: 1;
      }
      100% {
        transform: translate(var(--x), var(--y)) scale(0);
        opacity: 0;
      }
    }
    
    .thanos-badge {
      position: relative;
      cursor: pointer;
      transition: all 0.3s ease;
      background: linear-gradient(135deg, #9333ea, #7c3aed, #6366f1);
      background-size: 200% 200%;
      animation: gradient-shift 3s ease infinite;
    }
    
    .thanos-badge:hover {
      transform: scale(1.1);
      box-shadow: 0 0 30px rgba(147, 51, 234, 0.6);
    }
    
    .thanos-badge.snapped {
      animation: disintegrate 1.5s ease-out forwards;
      pointer-events: none;
    }
    
    .thanos-badge.reforming {
      animation: reassemble 1.5s ease-out forwards;
      pointer-events: none;
    }
    
    .thanos-badge::before {
      content: '💎';
      position: absolute;
      top: -15px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 16px;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .thanos-badge:hover::before {
      opacity: 1;
      animation: float 2s ease-in-out infinite;
    }
    
    /* Particle effects */
    .thanos-particle {
      position: absolute;
      width: 4px;
      height: 4px;
      background: linear-gradient(135deg, #9333ea, #ec4899);
      border-radius: 50%;
      pointer-events: none;
      animation: particle-float 1.5s ease-out forwards;
    }
    
    .thanos-hover {
      position: relative;
      transition: all 0.3s ease;
    }
    
    .thanos-hover:hover {
      animation: snap 0.6s ease-in-out;
      color: #9333ea;
    }
    
    .thanos-hover::before {
      content: '⚡';
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 20px;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .thanos-hover:hover::before {
      opacity: 1;
      animation: dust 1s ease-out forwards;
    }
    
    .thanos-hover::after {
      content: '';
      position: absolute;
      inset: -4px;
      border-radius: 12px;
      background: linear-gradient(45deg, #9333ea, #ec4899, #6366f1, #9333ea);
      opacity: 0;
      z-index: -1;
      transition: opacity 0.3s ease;
      background-size: 400% 400%;
    }
    
    .thanos-hover:hover::after {
      opacity: 0.3;
      animation: infinity-glow 2s ease-in-out infinite, gradient-shift 3s ease infinite;
    }
    
    .animate-float { animation: float 3s ease-in-out infinite; }
    .animate-gradient { 
      background-size: 200% 200%;
      animation: gradient-shift 8s ease infinite;
    }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    
    /* Glassmorphism */
    .glass {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .glass-dark {
      background: rgba(17, 24, 39, 0.8);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Smooth scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }
    
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, #6366f1, #8b5cf6);
      border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, #4f46e5, #7c3aed);
    }
    
    /* Dark mode scrollbar */
    .dark ::-webkit-scrollbar-track {
      background: #1f2937;
    }
    
    /* Hover lift effect */
    .hover-lift {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .hover-lift:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    /* Gradient text */
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    /* Smooth page transitions */
    .page-transition {
      animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Navigation active indicator */
    .nav-active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, #6366f1, #8b5cf6);
      border-radius: 2px;
    }
    
    /* Button shine effect */
    .btn-shine {
      position: relative;
      overflow: hidden;
    }
    
    .btn-shine::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease-in-out;
    }
    
    .btn-shine:hover::before {
      left: 100%;
    }

    /* Role badge styles */
    .role-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.125rem 0.5rem;
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 9999px;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
  </style>
</head>
<body class="min-h-screen font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
  
  <!-- Animated Background -->
  <div class="fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute top-20 -left-20 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 dark:opacity-10 animate-float"></div>
    <div class="absolute bottom-20 -right-20 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 dark:opacity-10 animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 dark:opacity-10 animate-float" style="animation-delay: 4s;"></div>
  </div>

  <div class="relative min-h-screen flex flex-col">
    
    <!-- Modern Navigation Bar -->
    <nav class="fixed w-full top-0 z-50 transition-all duration-300"
         :class="{
           'glass dark:glass-dark shadow-lg': scrolled,
           'bg-white/70 dark:bg-gray-900/70 backdrop-blur-md': !scrolled
         }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 md:h-20 transition-all duration-300" :class="{ 'md:h-16': scrolled }">
          
          <!-- Logo Section -->
          <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full blur-md opacity-60 group-hover:opacity-100 transition-opacity duration-300"></div>
              <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH Logo" 
                   class="relative h-10 w-10 md:h-12 md:w-12 rounded-full ring-2 ring-white/50 shadow-lg transition-transform duration-300 group-hover:scale-110" />
            </div>
            <div class="hidden md:block">
              <span class="text-xl font-bold gradient-text">MOH Learning</span>
              <p class="text-xs text-gray-500 dark:text-gray-400">Empowering Healthcare Excellence</p>
            </div>
          </a>

          <!-- Desktop Navigation Links -->
          <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
            
            @auth
              <!-- Home Link with Thanos effect -->
              <a href="{{ route('home') }}" 
                class="thanos-hover relative px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 {{ request()->routeIs('home') ? 'text-indigo-600 dark:text-indigo-400 nav-active' : 'text-gray-700 dark:text-gray-300' }}">
                <span class="relative z-10">Home</span>
              </a>

              <!-- My Courses - Only for non-admin users -->
              @if(!auth()->user()->hasRole(['admin', 'superadmin']))
                <a href="{{ route('mycourses') }}" 
                  class="relative px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 {{ request()->routeIs('mycourses') ? 'text-indigo-600 dark:text-indigo-400 nav-active' : 'text-gray-700 dark:text-gray-300' }}">
                  <span class="relative z-10">My Courses</span>
                </a>
              @endif

              <!-- Admin and Superadmin Section -->
              @if(auth()->user()->hasRole(['admin', 'superadmin']))
                <div class="flex items-center space-x-1 px-2 py-1 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20">
                  <span x-data="{ snapped: false }" 
                        @click="if(!snapped) { 
                          snapped = true; 
                          createParticles($el); 
                          setTimeout(() => snapped = false, 5000) 
                        }"
                        :class="{ 'snapped': snapped, 'reforming': !snapped && $el.classList.contains('snapped') }"
                        class="thanos-badge role-badge text-white mr-2">
                    @if(auth()->user()->hasRole('superadmin')) 
                      THANOS 💎
                    @else 
                      Admin 
                    @endif
                  </span>
                  
                  <a href="{{ route('admin.users.index') }}" 
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 hover:bg-white dark:hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-700 dark:text-gray-300' }}">
                    Users
                  </a>
                  
                  <a href="{{ route('admin.roles.index') }}" 
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 hover:bg-white dark:hover:bg-gray-800 {{ request()->routeIs('admin.roles.*') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-700 dark:text-gray-300' }}">
                    Roles
                  </a>
                  
                  <a href="{{ route('admin.enrollments.index') }}" 
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 hover:bg-white dark:hover:bg-gray-800 {{ request()->routeIs('admin.enrollments.*') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-700 dark:text-gray-300' }}">
                    Pending
                    @if(isset($pendingCount) && $pendingCount > 0)
                      <span class="ml-1 px-1.5 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $pendingCount }}</span>
                    @endif
                  </a>
                  
                  <a href="{{ route('courses.create') }}" 
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 hover:bg-white dark:hover:bg-gray-800 {{ request()->routeIs('courses.create') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-700 dark:text-gray-300' }}">
                    Create
                  </a>

                  @if(auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('admin.moodle.status') }}" 
                      class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 hover:bg-white dark:hover:bg-gray-800 {{ request()->routeIs('admin.moodle.*') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-700 dark:text-gray-300' }}">
                      Moodle
                    </a>
                  @endif
                </div>
              @endif
            @endauth

            <!-- Guest Links -->
            @guest
              <a href="{{ route('login') }}" 
                class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-gray-700 dark:text-gray-300">
                Login
              </a>
              <a href="{{ route('register') }}" 
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-medium transition-all duration-300 hover:from-indigo-700 hover:to-purple-700 btn-shine">
                Register
              </a>
            @endguest
          </div>

          <!-- Right Side Actions -->
          <div class="flex items-center space-x-2 md:space-x-3">
            
            @auth
              <!-- Notifications -->
              <button class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300 group">
                <svg class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if(isset($unreadNotifications) && $unreadNotifications > 0)
                  <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-red-500 rounded-full animate-pulse"></span>
                @endif
              </button>
              
              <!-- Dark Mode Toggle -->
              <button @click="toggleDarkMode()" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300">
                <svg x-show="!darkMode" class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg x-show="darkMode" class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </button>
              
              <!-- Profile Dropdown -->
              <div class="relative" x-data="{ profileOpen: false }">
                <button @click="profileOpen = !profileOpen" 
                        class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300">
                  @php $photo = auth()->user()->profile_photo; @endphp
                  <img src="{{ $photo ? Storage::url($photo) : asset('images/default-avatar.png') }}" 
                       class="h-8 w-8 rounded-full ring-2 ring-indigo-500/30 hover:ring-indigo-500/50 transition-all duration-300" alt="Avatar">
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
                            'superadmin' => 'thanos-badge text-white',
                            'admin' => 'bg-indigo-200 text-indigo-800',
                            'student' => 'bg-blue-200 text-blue-800',
                            'user' => 'bg-gray-200 text-gray-800'
                          ];
                          $roleDisplay = [
                            'superadmin' => 'THANOS 💎',
                            'admin' => 'Admin',
                            'student' => 'Student',
                            'user' => 'User'
                          ];
                        @endphp
                        <span x-data="{ snapped: false }" 
                              @click="if('{{ $primaryRole }}' === 'superadmin' && !snapped) { 
                                snapped = true; 
                                createParticles($el); 
                                setTimeout(() => snapped = false, 5000) 
                              }"
                              :class="{ 'snapped': snapped, 'reforming': !snapped && $el.classList.contains('snapped') }"
                              class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $roleColors[$primaryRole] ?? 'bg-gray-200 text-gray-800' }}">
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
                    
                    @if(!auth()->user()->hasRole(['admin', 'superadmin']))
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
      </div>

      <!-- Mobile Menu -->
      <div x-show="mobileMenuOpen" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 -translate-y-4"
          x-transition:enter-end="opacity-100 translate-y-0"
          @click.away="mobileMenuOpen = false"
          class="md:hidden glass dark:glass-dark border-t border-gray-200 dark:border-gray-700">
        <div class="px-4 pt-2 pb-3 space-y-1">
          
          @auth
            <!-- Display Current Role -->
            @if(auth()->user()->getRoleNames()->isNotEmpty())
              <div class="px-3 py-2">
                @php
                  $primaryRole = auth()->user()->getRoleNames()->first();
                  $roleDisplay = $primaryRole == 'superadmin' ? 'THANOS 💎' : ucfirst(str_replace('_', ' ', $primaryRole));
                @endphp
                <span x-data="{ snapped: false }"
                      @click="if('{{ $primaryRole }}' === 'superadmin' && !snapped) { 
                        snapped = true; 
                        createParticles($el); 
                        setTimeout(() => snapped = false, 5000) 
                      }"
                      :class="{ 'snapped': snapped, 'reforming': !snapped && $el.classList.contains('snapped') }"
                      class="role-badge 
                  @if($primaryRole == 'superadmin') thanos-badge text-white
                  @elseif($primaryRole == 'admin') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300
                  @else bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                  @endif">
                  {{ $roleDisplay }}
                </span>
              </div>
            @endif

            <!-- Home Link for all authenticated users -->
            <a href="{{ route('home') }}" 
              class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('home') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
              Home
            </a>
            
            <!-- My Courses only for non-admin users -->
            @if(!auth()->user()->hasRole(['admin', 'superadmin']))
              <a href="{{ route('mycourses') }}" 
                class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('mycourses') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                My Courses
              </a>
            @endif
            
            <!-- Admin Menu -->
            @if(auth()->user()->hasRole(['admin', 'superadmin']))
              <div class="pt-2 pb-1">
                <p class="px-3 text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Admin Menu</p>
              </div>
              <a href="{{ route('admin.users.index') }}" 
                class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('admin.users.*') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                Manage Users
              </a>
              
              <a href="{{ route('admin.roles.index') }}" 
                class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                Role Management
              </a>
              
              <a href="{{ route('admin.enrollments.index') }}" 
                class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('admin.enrollments.*') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                Pending Enrollments
                @if(isset($pendingCount) && $pendingCount > 0)
                  <span class="ml-2 px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $pendingCount }}</span>
                @endif
              </a>
              
              <a href="{{ route('courses.create') }}" 
                class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('courses.create') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                Create Course
              </a>
              
              @if(auth()->user()->hasRole('superadmin'))
                <a href="{{ route('admin.moodle.status') }}" 
                  class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('admin.moodle.*') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                  Moodle Integration
                </a>
              @endif
            @endif
          @else
            <!-- Guest Links -->
            <a href="{{ route('login') }}" 
              class="block px-3 py-2 rounded-xl text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
              Login
            </a>
            <a href="{{ route('register') }}" 
              class="block px-3 py-2 rounded-xl text-base font-medium bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
              Register
            </a>
          @endauth
        </div>
      </div>
    </nav>

    <!-- Page Header with Gradient -->
    <header class="pt-20 md:pt-24 pb-8 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
          <div>
            @if(isset($heading))
              <h1 class="text-3xl md:text-4xl font-bold gradient-text page-transition">
                {{ $heading }}
              </h1>
            @elseif(View::hasSection('title'))
              <h1 class="text-3xl md:text-4xl font-bold gradient-text page-transition">
                @yield('title')
              </h1>
            @endif
            <!-- Breadcrumbs -->
            <nav class="flex mt-3" aria-label="Breadcrumb">
              <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                  <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Home
                  </a>
                </li>
                @if(isset($heading))
                <li>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm text-gray-700 dark:text-gray-300 font-medium md:ml-2">{{ $heading }}</span>
                  </div>
                </li>
                @elseif(View::hasSection('title'))
                <li>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm text-gray-700 dark:text-gray-300 font-medium md:ml-2">@yield('title')</span>
                  </div>
                </li>
                @endif
              </ol>
            </nav>
          </div>
          
          <!-- Quick Stats (only for admin roles) -->
          @if(auth()->check() && auth()->user()->hasRole(['admin', 'superadmin']))
          <div class="hidden lg:flex items-center space-x-6">
            <div class="text-center">
              <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalCourses ?? '0' }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">Courses</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $totalUsers ?? '0' }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">Users</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ $pendingCount ?? '0' }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
            </div>
          </div>
          @endif
        </div>
      </div>
    </header>

    <!-- Main Content Area - THIS IS THE KEY CHANGE -->
    <main class="flex-1 pb-12 page-transition">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
        <!-- Content Card with Shadow -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8">
          @if(isset($slot))
            {{ $slot }}
          @else
            @yield('content')
          @endif
        </div>
      </div>
    </main>

    <!-- Modern Footer -->
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 text-white mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Brand Section -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
              <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH Logo" class="h-10 w-10 rounded-full">
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
          </div>
          
          <!-- Quick Links -->
          <div>
            <h4 class="text-white font-semibold mb-4">Quick Links</h4>
            <ul class="space-y-2">
              <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Home</a></li>
              @auth
                @if(!auth()->user()->hasRole(['admin', 'superadmin']))
                  <li><a href="{{ route('mycourses') }}" class="text-gray-400 hover:text-white text-sm transition-colors">My Courses</a></li>
                @endif
                <li><a href="{{ route('profile.show') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Profile</a></li>
              @endauth
              <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Support</a></li>
            </ul>
          </div>
          
          <!-- Newsletter -->
          <div>
            <h4 class="text-white font-semibold mb-4">Stay Updated</h4>
            <p class="text-gray-400 text-sm mb-4">Get the latest updates on new courses</p>
            <form class="flex">
              <input type="email" 
                     placeholder="Your email" 
                     class="flex-1 px-4 py-2 bg-gray-800 text-white rounded-l-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <button type="submit" 
                      class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-r-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 btn-shine">
                Subscribe
              </button>
            </form>
          </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
          <p>&copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago. All rights reserved. | Developed with ❤️ by Kyle Blackman</p>
        </div>
      </div>
    </footer>
  </div>

  <!-- Toast Notifications Container -->
  <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-4"></div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    function layoutData() {
      return {
        darkMode: false,
        scrolled: false,
        mobileMenuOpen: false,
        
        init() {
          // Check localStorage for dark mode preference
          const savedDarkMode = localStorage.getItem('darkMode');
          if (savedDarkMode !== null) {
            this.darkMode = savedDarkMode === 'true';
          } else {
            // Check system preference
            this.darkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
          }
          
          // Apply dark mode
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
          if (this.darkMode) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
          localStorage.setItem('darkMode', this.darkMode.toString());
        }
      }
    }
    
    // Thanos snap particle effect
    function createParticles(element) {
      const rect = element.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;
      
      // Create 20 particles
      for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'thanos-particle';
        
        // Random position around the badge
        const angle = (Math.PI * 2 * i) / 20;
        const distance = 50 + Math.random() * 100;
        const x = Math.cos(angle) * distance;
        const y = Math.sin(angle) * distance;
        
        particle.style.cssText = `
          --x: ${x}px;
          --y: ${y}px;
          left: ${centerX}px;
          top: ${centerY}px;
          animation-delay: ${Math.random() * 0.5}s;
        `;
        
        document.body.appendChild(particle);
        
        // Remove particle after animation
        setTimeout(() => particle.remove(), 1500);
      }
      
      // Play a snap sound effect (optional - you can add an audio element)
      const audio = new Audio('data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAAAAA=');
      audio.volume = 0.3;
      audio.play().catch(() => {}); // Catch error if audio doesn't play
    }
  </script>
  
  <!-- Additional Page Scripts -->
  @stack('scripts')
</body>
</html>