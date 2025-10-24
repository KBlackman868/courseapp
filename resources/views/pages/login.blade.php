<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Ministry of Health - Sign In</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Animated gradient background */
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    .animated-gradient {
      background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #0ea5e9);
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
    }

    /* Pulse glow effect */
    @keyframes pulse-glow {
      0%, 100% { 
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.5),
                   0 0 40px rgba(59, 130, 246, 0.3);
      }
      50% { 
        box-shadow: 0 0 30px rgba(59, 130, 246, 0.8),
                   0 0 60px rgba(59, 130, 246, 0.5);
      }
    }

    .pulse-glow {
      animation: pulse-glow 2s ease-in-out infinite;
    }

    /* Floating animation */
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }

    .float {
      animation: float 3s ease-in-out infinite;
    }

    /* Glass morphism */
    .glass {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Blob animation */
    @keyframes blob {
      0%, 100% { transform: translate(0px, 0px) scale(1); }
      33% { transform: translate(30px, -50px) scale(1.1); }
      66% { transform: translate(-20px, 20px) scale(0.9); }
    }

    .blob {
      animation: blob 7s infinite;
    }

    /* Input focus effect */
    .input-focus {
      transition: all 0.3s ease;
      position: relative;
    }

    .input-focus::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #3b82f6, #6366f1);
      transition: width 0.3s ease;
    }

    .input-focus:focus::after {
      width: 100%;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
    }

    ::-webkit-scrollbar-track {
      background: #f3f4f6;
    }

    ::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, #3b82f6, #6366f1);
      border-radius: 5px;
    }
  </style>
</head>
<body class="animated-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
  
  <!-- Animated background elements -->
  <div class="absolute inset-0 overflow-hidden">
    <!-- Floating orbs -->
    <div class="absolute top-20 -left-20 w-60 h-60 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob"></div>
    <div class="absolute top-40 -right-20 w-60 h-60 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob" style="animation-delay: 2s;"></div>
    <div class="absolute -bottom-20 left-40 w-60 h-60 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob" style="animation-delay: 4s;"></div>
    
    <!-- Floating particles -->
    <div class="absolute top-1/4 left-1/4 w-4 h-4 bg-white rounded-full opacity-50 float"></div>
    <div class="absolute top-3/4 right-1/3 w-3 h-3 bg-white rounded-full opacity-40 float" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-1/4 right-1/4 w-2 h-2 bg-white rounded-full opacity-30 float" style="animation-delay: 2s;"></div>
  </div>

  <!-- Error Banner -->
  @if($errors->any())
    <div id="error-alert" class="fixed top-0 left-0 right-0 bg-gradient-to-r from-red-600 to-pink-600 text-white p-4 text-center transform transition-all duration-500 ease-in-out shadow-2xl z-50" style="transform: translateY(-100%); opacity: 0;">
      <div class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ $errors->first('email') }}
      </div>
    </div>
  @endif

  <!-- Main Container -->
  <div class="flex flex-col justify-center px-6 py-12 lg:px-8 z-10">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- Login Form Card -->
      <div class="glass rounded-3xl shadow-2xl p-8 pulse-glow">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
          <div class="mx-auto h-16 w-16 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 p-0.5 mb-4 float">
            <img class="h-full w-full rounded-full bg-white p-1" src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health" />
          </div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            Welcome Back
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Sign in to access your Learning Management System
          </p>
        </div>

        <!-- Form -->
        <form class="space-y-6" method="POST" action="{{ route('login.submit') }}">
          @csrf
          
          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email Address
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
              </div>
              <input
                type="email"
                name="email"
                id="email"
                autocomplete="email"
                required
                placeholder="name@health.gov.tt"
                class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="password" class="block text-sm font-medium text-gray-700">
                Password
              </label>
              <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-500 transition-colors">
                Forgot password?
              </a>
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <input
                type="password"
                name="password"
                id="password"
                autocomplete="current-password"
                required
                placeholder="Enter your password"
                class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300"
              />
              <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <svg id="eye-open" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <svg id="eye-closed" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember Me Checkbox -->
          <div class="flex items-center">
            <input
              id="remember"
              name="remember"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-700">
              Remember me
            </label>
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              class="w-full flex justify-center items-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-3 px-4 text-sm font-semibold text-white shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <span>Sign In</span>
              <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </button>
          </div>
        </form>
                  {{-- Social Login Separator --}}
          <div class="relative my-6">
              <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                  <span class="px-2 bg-white text-gray-500">Or continue with</span>
              </div>
          </div>

          {{-- Google Login Button --}}
          <div class="mt-4">
              <a href="{{ route('auth.google') }}" 
                class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                  {{-- Google Icon --}}
                  <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                      <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                      <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                      <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                  </svg>
                  Sign in with Google
              </a>
          </div>

          {{-- Optional: Add more social providers later --}}
          {{-- 
          <div class="mt-3 grid grid-cols-2 gap-3">
              <a href="#" class="...">Microsoft</a>
              <a href="#" class="...">GitHub</a>
          </div>
          --}}
        <!-- Sign Up Link -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            New to the platform?
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition-colors">
              Create an account
            </a>
          </p>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="mt-8 text-center">
        <div class="flex justify-center space-x-4 text-sm">
          <a href="#" class="text-white/80 hover:text-white transition-colors">Privacy Policy</a>
          <span class="text-white/60">•</span>
          <a href="#" class="text-white/80 hover:text-white transition-colors">Terms of Service</a>
          <span class="text-white/60">•</span>
          <a href="#" class="text-white/80 hover:text-white transition-colors">Help</a>
        </div>
        <p class="mt-4 text-xs text-white/60">
          © 2025 Ministry of Health, Trinidad and Tobago. All rights reserved.
        </p>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeOpen = document.getElementById('eye-open');
      const eyeClosed = document.getElementById('eye-closed');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
      } else {
        passwordInput.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
      }
    }

    // Error banner animation
    @if($errors->any())
      document.addEventListener("DOMContentLoaded", function() {
        const errorAlert = document.getElementById("error-alert");
        // Slide down and fade in
        setTimeout(() => {
          errorAlert.style.transform = "translateY(0)";
          errorAlert.style.opacity = "1";
        }, 100);
        // After 5 seconds, slide up and fade out
        setTimeout(() => {
          errorAlert.style.transform = "translateY(-100%)";
          errorAlert.style.opacity = "0";
        }, 5000);
      });
    @endif
  </script>
</body>
</html>