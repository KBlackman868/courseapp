<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Ministry of Health - Register</title>
  <meta name="description" content="Register for the Ministry of Health Learning Management System" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /*
     * FIX FOR SCROLL ISSUE:
     * - Removed h-full from html to allow natural scrolling
     * - Changed body to use min-h-screen instead of fixed height
     * - Removed any focus-trap or overflow-hidden that could block scrolling
     * - Added proper padding for mobile and desktop
     */
    html, body {
      min-height: 100%;
      height: auto;
      overflow-x: hidden;
      overflow-y: auto;
    }

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
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-10px) rotate(1deg); }
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

    /* Custom input styling */
    .floating-input {
      transition: all 0.3s ease;
    }

    .floating-input:focus {
      border-color: #3b82f6;
    }

    .floating-label {
      transition: all 0.3s ease;
      pointer-events: none;
    }

    .floating-input:focus ~ .floating-label,
    .floating-input:not(:placeholder-shown) ~ .floating-label {
      transform: translateY(-1.5rem) scale(0.85);
      color: #3b82f6;
    }

    /* Progress bar for password strength */
    .strength-bar {
      transition: all 0.3s ease;
    }

    /* Custom checkbox */
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
<body class="animated-gradient min-h-screen relative py-8 sm:py-12">
  <!--
    SCROLL FIX EXPLANATION:
    - Removed 'flex items-center justify-center' from body as it can cause scroll issues
    - Using min-h-screen instead of fixed height
    - Added padding for proper spacing
    - Container below handles centering
  -->
  
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

  @if($errors->any())
    <div id="error-alert" class="fixed top-0 left-0 right-0 bg-gradient-to-r from-red-600 to-pink-600 text-white p-4 text-center transform transition-all duration-500 ease-in-out shadow-2xl z-50" style="transform: translateY(-100%); opacity: 0;">
      <div class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ $errors->first() }}
      </div>
    </div>
  @endif

  <!-- Main Container - Centered with proper scroll support -->
  <div class="flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8 z-10 w-full mx-auto max-w-lg min-h-screen">
    <!-- Registration Form Card -->
    <div class="glass rounded-3xl shadow-2xl p-8 pulse-glow">
      <!-- Logo and Header -->
      <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 p-0.5 mb-4 float">
          <img class="h-full w-full rounded-full bg-white p-1" src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health" />
        </div>
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
          Create Your Account
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Join the Ministry of Health Learning Management System
        </p>
      </div>

      <!-- Progress Steps -->
      <div class="flex items-center justify-center mb-8">
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-semibold">1</div>
          <div class="w-16 h-1 bg-blue-600"></div>
          <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-xs font-semibold">2</div>
          <div class="w-16 h-1 bg-gray-300"></div>
          <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-xs font-semibold">3</div>
        </div>
      </div>

      <!-- Form -->
      <form class="space-y-6" method="POST" action="{{ route('register.submit') }}">
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
              placeholder="First name"
              class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300" />
          </div>

          <!-- Last Name -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <input type="text" name="last_name" id="last_name" required
              placeholder="Last name"
              class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300" />
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
            placeholder="Email address"
            class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300" />
        </div>

        <!-- Department Field -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <select name="department" id="department" required
            class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300 appearance-none">
            <option value="" disabled selected>Select Department</option>
            <option value="administration">Administration</option>
            <option value="clinical">Clinical Services</option>
            <option value="nursing">Nursing</option>
            <option value="pharmacy">Pharmacy</option>
            <option value="laboratory">Laboratory</option>
            <option value="radiology">Radiology</option>
            <option value="emergency">Emergency Medicine</option>
            <option value="pediatrics">Pediatrics</option>
            <option value="surgery">Surgery</option>
            <option value="internal_medicine">Internal Medicine</option>
            <option value="public_health">Public Health</option>
            <option value="it">Information Technology</option>
            <option value="hr">Human Resources</option>
            <option value="finance">Finance</option>
          </select>
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>

        <!-- Date of Birth Field -->
        <div class="relative">
          <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <input type="date" name="date_of_birth" id="date_of_birth" required
              value="{{ old('date_of_birth') }}"
              max="{{ date('Y-m-d', strtotime('-18 years')) }}"
              class="w-full pl-10 pr-3 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300" />
          </div>
          @error('date_of_birth')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
          <p class="mt-1 text-xs text-gray-500">You must be at least 18 years old to register.</p>
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
              placeholder="Password (minimum 12 characters)"
              onkeyup="checkPasswordStrength()"
              class="w-full pl-10 pr-10 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300" />
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
        <!-- Terms Checkbox -->
        <div class="flex items-start">
          <input id="terms" name="terms" type="checkbox" required
            class="custom-checkbox mt-1" />
          <label for="terms" class="ml-2 block text-sm text-gray-700">
            I agree to the
            <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a>
            and
            <a href="{{ route('privacy-policy') }}" target="_blank" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit"
          class="w-full flex justify-center items-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-3 px-4 text-sm font-semibold text-white shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          <span>Create Account</span>
          <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
          </svg>
        </button>
      </form>
      <!-- Sign In Link -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          Already have an account?
          <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition-colors">
            Sign in
          </a>
        </p>
      </div>
    </div>

    <!-- Footer Links -->
    <div class="mt-8 text-center">
      <div class="flex justify-center space-x-4 text-sm">
        <a href="{{ route('terms') }}" class="text-white/80 hover:text-white transition-colors">Terms and Conditions</a>
        <span class="text-white/60">•</span>
        <a href="{{ route('privacy-policy') }}" class="text-white/80 hover:text-white transition-colors">Privacy Policy</a>
        <span class="text-white/60">•</span>
        <a href="mailto:support@health.gov.tt" class="text-white/80 hover:text-white transition-colors">Contact Support</a>
      </div>
      <p class="mt-4 text-xs text-white/60">
        &copy; {{ date('Y') }} Ministry of Health, Trinidad and Tobago. All rights reserved.
      </p>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Toggle password visibility
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

    // Password strength checker
    function checkPasswordStrength() {
      const password = document.getElementById('password').value;
      const strengthBar = document.getElementById('strength-bar');
      const strengthText = document.getElementById('strength-text');
      
      let strength = 0;
      
      // Length check (minimum 12 for standard, 14 for high-risk)
      if (password.length >= 12) strength += 25;
      if (password.length >= 14) strength += 25;
      
      // Character variety
      if (password.match(/[a-z]/)) strength += 12.5;
      if (password.match(/[A-Z]/)) strength += 12.5;
      if (password.match(/[0-9]/)) strength += 12.5;
      if (password.match(/[^a-zA-Z0-9]/)) strength += 12.5;
      
      // Update bar
      strengthBar.style.width = strength + '%';
      
      // Update color and text
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

    // Alert animations
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