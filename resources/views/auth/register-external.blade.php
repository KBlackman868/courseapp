<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Ministry of Health - External Registration</title>
  <meta name="description" content="Register as an external user for the Ministry of Health Learning Management System" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
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

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-10px) rotate(1deg); }
    }

    .float {
      animation: float 3s ease-in-out infinite;
    }

    .glass {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    @keyframes blob {
      0%, 100% { transform: translate(0px, 0px) scale(1); }
      33% { transform: translate(30px, -50px) scale(1.1); }
      66% { transform: translate(-20px, 20px) scale(0.9); }
    }

    .blob {
      animation: blob 7s infinite;
    }

    .strength-bar {
      transition: all 0.3s ease;
    }

    .custom-checkbox {
      appearance: none;
      width: 1.25rem;
      height: 1.25rem;
      border: 2px solid #d1d5db;
      border-radius: 0.375rem;
      transition: all 0.3s ease;
    }

    .custom-checkbox:checked {
      background-color: #3b82f6;
      border-color: #3b82f6;
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
      background-position: center;
      background-repeat: no-repeat;
    }
  </style>
</head>
<body class="animated-gradient min-h-screen flex items-center justify-center relative overflow-y-auto py-12">

  <!-- Animated background elements -->
  <div class="absolute inset-0 overflow-hidden">
    <div class="absolute top-20 -left-20 w-60 h-60 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob"></div>
    <div class="absolute top-40 -right-20 w-60 h-60 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob" style="animation-delay: 2s;"></div>
    <div class="absolute -bottom-20 left-40 w-60 h-60 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob" style="animation-delay: 4s;"></div>

    <div class="absolute top-1/4 left-1/4 w-4 h-4 bg-white rounded-full opacity-50 float"></div>
    <div class="absolute top-3/4 right-1/3 w-3 h-3 bg-white rounded-full opacity-40 float" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-1/4 right-1/4 w-2 h-2 bg-white rounded-full opacity-30 float" style="animation-delay: 2s;"></div>
  </div>

  <!-- Success/Error Messages -->
  @if(session('success'))
    <div id="success-alert" class="fixed top-0 left-0 right-0 bg-gradient-to-r from-green-600 to-emerald-600 text-white p-4 text-center transform transition-all duration-500 ease-in-out shadow-2xl z-50" style="transform: translateY(-100%); opacity: 0;">
      <div class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('success') }}
      </div>
    </div>
  @endif

  @if(session('error') || $errors->any())
    <div id="error-alert" class="fixed top-0 left-0 right-0 bg-gradient-to-r from-red-600 to-pink-600 text-white p-4 text-center transform transition-all duration-500 ease-in-out shadow-2xl z-50" style="transform: translateY(-100%); opacity: 0;">
      <div class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') ?? $errors->first() }}
      </div>
    </div>
  @endif

  <!-- Main Container -->
  <div class="flex flex-col justify-center px-6 py-12 lg:px-8 z-10 w-full max-w-lg">
    <!-- Registration Form Card -->
    <div class="glass rounded-3xl shadow-2xl p-8 pulse-glow">
      <!-- Logo and Header -->
      <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 p-0.5 mb-4 float">
          <img class="h-full w-full rounded-full bg-white p-1" src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health" />
        </div>
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
          External User Registration
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Register as an external organization user
        </p>
      </div>

      <!-- Info Notice -->
      <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
        <div class="flex">
          <svg class="h-5 w-5 text-amber-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="text-sm text-amber-800">
            <p class="font-medium">Account Approval Required</p>
            <p class="mt-1">Your account will need to be approved by an administrator before you can log in.</p>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form class="space-y-5" method="POST" action="{{ route('register.external.store') }}">
        @csrf

        <!-- Name Fields Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- First Name -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <input type="text" name="first_name" id="first_name" required
              value="{{ old('first_name') }}"
              placeholder="First name"
              class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300 @error('first_name') border-red-500 @enderror" />
            @error('first_name')
              <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
          </div>

          <!-- Last Name -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <input type="text" name="last_name" id="last_name" required
              value="{{ old('last_name') }}"
              placeholder="Last name"
              class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300 @error('last_name') border-red-500 @enderror" />
            @error('last_name')
              <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Email Field -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>
          <input type="email" name="email" id="email" required
            value="{{ old('email') }}"
            placeholder="Email address"
            class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300 @error('email') border-red-500 @enderror" />
          @error('email')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <!-- Organization Field -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <input type="text" name="organization" id="organization" required
            value="{{ old('organization') }}"
            placeholder="Organization / Company name"
            class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300 @error('organization') border-red-500 @enderror" />
          @error('organization')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password Field -->
        <div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
            </div>
            <input type="password" name="password" id="password" required
              placeholder="Password"
              onkeyup="checkPasswordStrength()"
              class="w-full pl-10 pr-10 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300 @error('password') border-red-500 @enderror" />
            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <svg id="eye-open-password" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              <svg id="eye-closed-password" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
              </svg>
            </button>
          </div>
          @error('password')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
          <!-- Password Strength Indicator -->
          <div class="mt-2">
            <div class="flex items-center justify-between text-xs mb-1">
              <span class="text-gray-500">Password strength</span>
              <span id="strength-text" class="font-semibold"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
              <div id="strength-bar" class="h-1.5 rounded-full strength-bar" style="width: 0%"></div>
            </div>
          </div>
        </div>

        <!-- Confirm Password Field -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <input type="password" name="password_confirmation" id="password_confirmation" required
            placeholder="Confirm Password"
            class="w-full pl-10 pr-10 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300" />
          <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg id="eye-open-confirm" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <svg id="eye-closed-confirm" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            </svg>
          </button>
        </div>

        <!-- Submit Button -->
        <button type="submit"
          class="w-full flex justify-center items-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-3 px-4 text-sm font-semibold text-white shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          <span>Submit Registration</span>
          <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
          </svg>
        </button>
      </form>

      <!-- Divider -->
      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-4 bg-white text-gray-500 rounded-full">Already registered?</span>
        </div>
      </div>

      <!-- Sign In Link -->
      <div class="text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center text-blue-600 hover:text-blue-500 font-semibold transition-colors">
          <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
          </svg>
          Sign in to your account
        </a>
      </div>

      <!-- MOH Staff Link -->
      <div class="mt-4 text-center">
        <p class="text-sm text-gray-600">
          MOH Staff?
          <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium transition-colors">
            Register as internal user
          </a>
        </p>
      </div>
    </div>

    <!-- Footer Links -->
    <div class="mt-8 text-center">
      <div class="flex justify-center space-x-4 text-sm">
        <a href="#" class="text-white/80 hover:text-white transition-colors">Help</a>
        <span class="text-white/60">|</span>
        <a href="#" class="text-white/80 hover:text-white transition-colors">Contact Support</a>
      </div>
      <p class="mt-4 text-xs text-white/60">
        © {{ date('Y') }} Ministry of Health, Trinidad and Tobago. All rights reserved.
      </p>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    function togglePassword(fieldId) {
      const passwordInput = document.getElementById(fieldId);
      const eyeOpenId = fieldId === 'password' ? 'eye-open-password' : 'eye-open-confirm';
      const eyeClosedId = fieldId === 'password' ? 'eye-closed-password' : 'eye-closed-confirm';
      const eyeOpen = document.getElementById(eyeOpenId);
      const eyeClosed = document.getElementById(eyeClosedId);

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

    function checkPasswordStrength() {
      const password = document.getElementById('password').value;
      const strengthBar = document.getElementById('strength-bar');
      const strengthText = document.getElementById('strength-text');

      let strength = 0;

      if (password.length >= 8) strength += 25;
      if (password.length >= 12) strength += 25;
      if (password.match(/[a-z]/)) strength += 12.5;
      if (password.match(/[A-Z]/)) strength += 12.5;
      if (password.match(/[0-9]/)) strength += 12.5;
      if (password.match(/[^a-zA-Z0-9]/)) strength += 12.5;

      strengthBar.style.width = strength + '%';

      if (strength < 25) {
        strengthBar.className = 'h-1.5 rounded-full bg-red-500 strength-bar';
        strengthText.textContent = 'Weak';
        strengthText.className = 'font-semibold text-red-500';
      } else if (strength < 50) {
        strengthBar.className = 'h-1.5 rounded-full bg-orange-500 strength-bar';
        strengthText.textContent = 'Fair';
        strengthText.className = 'font-semibold text-orange-500';
      } else if (strength < 75) {
        strengthBar.className = 'h-1.5 rounded-full bg-yellow-500 strength-bar';
        strengthText.textContent = 'Good';
        strengthText.className = 'font-semibold text-yellow-500';
      } else {
        strengthBar.className = 'h-1.5 rounded-full bg-green-500 strength-bar';
        strengthText.textContent = 'Strong';
        strengthText.className = 'font-semibold text-green-500';
      }
    }

    document.addEventListener("DOMContentLoaded", function() {
      const successAlert = document.getElementById("success-alert");
      const errorAlert = document.getElementById("error-alert");

      if (successAlert) {
        setTimeout(() => {
          successAlert.style.transform = "translateY(0)";
          successAlert.style.opacity = "1";
        }, 100);
        setTimeout(() => {
          successAlert.style.transform = "translateY(-100%)";
          successAlert.style.opacity = "0";
        }, 5000);
      }

      if (errorAlert) {
        setTimeout(() => {
          errorAlert.style.transform = "translateY(0)";
          errorAlert.style.opacity = "1";
        }, 100);
        setTimeout(() => {
          errorAlert.style.transform = "translateY(-100%)";
          errorAlert.style.opacity = "0";
        }, 5000);
      }
    });
  </script>
</body>
</html>
