<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - Ministry of Health LMS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      overflow-x: hidden;
    }

    /* Modern gradient background */
    .modern-gradient {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
    }

    /* Animated shapes in background */
    .shape {
      position: absolute;
      opacity: 0.1;
    }

    .shape-1 {
      top: 10%;
      left: 10%;
      width: 300px;
      height: 300px;
      background: linear-gradient(180deg, #ffd89b 0%, #19547b 100%);
      animation: morph 8s ease-in-out infinite;
      border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
    }

    .shape-2 {
      bottom: 10%;
      right: 10%;
      width: 400px;
      height: 400px;
      background: linear-gradient(180deg, #f093fb 0%, #f5576c 100%);
      animation: morph 8s ease-in-out infinite reverse;
      border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
    }

    @keyframes morph {
      0% {
        border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
        transform: rotate(0deg);
      }
      50% {
        border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%;
        transform: rotate(180deg);
      }
      100% {
        border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
        transform: rotate(360deg);
      }
    }

    /* Card glass effect */
    .glass-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.4);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    }

    /* Input animations */
    .input-group {
      position: relative;
      margin-bottom: 1.5rem;
    }

    .form-input {
      width: 100%;
      padding: 0.75rem 0.75rem 0.75rem 2.5rem;
      font-size: 1rem;
      border: 2px solid #e5e7eb;
      border-radius: 0.5rem;
      transition: all 0.3s;
      background: white;
    }

    .form-input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input:focus + .input-icon {
      color: #667eea;
    }

    .input-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      transition: all 0.3s;
      pointer-events: none;
    }

    /* Button styles */
    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 0.875rem 2rem;
      border-radius: 0.5rem;
      font-weight: 600;
      font-size: 1rem;
      border: none;
      cursor: pointer;
      transition: all 0.3s;
      width: 100%;
      position: relative;
      overflow: hidden;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    /* Google button */
    .btn-google {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 0.875rem 2rem;
      background: white;
      border: 2px solid #e5e7eb;
      border-radius: 0.5rem;
      color: #374151;
      font-weight: 500;
      transition: all 0.3s;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-google:hover {
      background: #f9fafb;
      border-color: #d1d5db;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Checkbox custom style */
    .custom-checkbox {
      width: 1.25rem;
      height: 1.25rem;
      border: 2px solid #d1d5db;
      border-radius: 0.25rem;
      cursor: pointer;
      transition: all 0.2s;
    }

    .custom-checkbox:checked {
      background-color: #667eea;
      border-color: #667eea;
    }

    /* Loading spinner */
    .spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #667eea;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .loading .spinner {
      display: block;
    }

    .loading .btn-text {
      display: none;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
      .glass-card {
        margin: 1rem;
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Fixed gradient background -->
  <div class="modern-gradient">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
  </div>

  <!-- Main Container - Full height with scroll -->
  <div class="min-h-screen flex items-center justify-center p-4 overflow-y-auto">
    
    <!-- Content Wrapper -->
    <div class="w-full max-w-md">
      
      <!-- Logo Section -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
          <img src="{{ asset('images/moh_logo.jpg') }}" alt="MOH Logo" class="w-16 h-16 rounded-full">
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Ministry of Health</h1>
        <p class="text-white/80">Learning Management System</p>
      </div>

      <!-- Login Card -->
      <div class="glass-card rounded-2xl p-8">
        
        <!-- Error Messages -->
        @if(session('error'))
          <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded-lg flex items-start">
            <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700 text-sm">{{ session('error') }}</span>
          </div>
        @endif

        @if(session('success'))
          <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded-lg flex items-start">
            <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700 text-sm">{{ session('success') }}</span>
          </div>
        @endif

        @if($errors->any())
          <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded-lg">
            @foreach($errors->all() as $error)
              <p class="text-red-700 text-sm mb-1 last:mb-0">• {{ $error }}</p>
            @endforeach
          </div>
        @endif

        <!-- Welcome Text -->
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
          <p class="text-gray-600 mt-1">Please sign in to your account</p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
          @csrf
          
          <!-- Email Input -->
          <div class="input-group">
            <input 
              type="email" 
              name="email" 
              id="email"
              value="{{ old('email') }}"
              required
              autocomplete="email"
              placeholder="Email address"
              class="form-input @error('email') border-red-500 @enderror"
            >
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 00-8 0c0 1.283.884 2.362 2.083 2.657V16a1 1 0 001.834 0v-1.343c1.2-.295 2.083-1.374 2.083-2.657z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>

          <!-- Password Input -->
          <div class="input-group">
            <input 
              type="password" 
              name="password" 
              id="password"
              required
              autocomplete="current-password"
              placeholder="Password"
              class="form-input @error('password') border-red-500 @enderror"
            >
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
              <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
              </svg>
            </button>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
              <input type="checkbox" name="remember" class="custom-checkbox mr-2">
              <span class="text-sm text-gray-700">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
              Forgot password?
            </a>
          </div>

          <!-- Login Button -->
          <button type="submit" class="btn-primary" id="loginBtn">
            <span class="btn-text">Sign In</span>
            <div class="spinner"></div>
          </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
          </div>
          <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-white px-2 text-gray-500">Or continue with</span>
          </div>
        </div>

        <!-- Google Sign In -->
        <a href="{{ route('auth.google') }}" class="btn-google">
          <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Sign in with Google
        </a>

        <!-- Additional OAuth Options (Optional) -->
        <div class="mt-3 grid grid-cols-2 gap-3">
          <!-- Microsoft Button -->
          <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
            <svg class="w-4 h-4 mr-2" viewBox="0 0 21 21">
              <path fill="#f25022" d="M0 0h10v10H0z"/>
              <path fill="#00a4ef" d="M11 0h10v10H11z"/>
              <path fill="#7fba00" d="M0 11h10v10H0z"/>
              <path fill="#ffb900" d="M11 11h10v10H11z"/>
            </svg>
            Microsoft
          </button>

          <!-- Apple Button -->
          <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18.71 19.5C18.33 20.3 17.88 21.05 17.35 21.76C16.63 22.72 16.04 23.38 15.6 23.74C14.91 24.35 14.18 24.67 13.39 24.69C12.82 24.69 12.13 24.53 11.34 24.2C10.52 23.88 9.77 23.72 9.08 23.72C8.36 23.72 7.59 23.88 6.77 24.2C5.94 24.53 5.29 24.7 4.8 24.72C4.03 24.75 3.27 24.42 2.51 23.74C2.03 23.35 1.43 22.67 0.71 21.68C-0.07 20.61 -0.7 19.36 -0.7 17.92C-0.7 16.26 -0.29 14.81 0.54 13.59C1.19 12.62 2.04 11.85 3.1 11.29C4.15 10.73 5.28 10.44 6.49 10.42C7.09 10.42 7.89 10.6 8.91 10.94C9.9 11.29 10.55 11.47 10.85 11.47C11.08 11.47 11.82 11.27 13.04 10.86C14.19 10.49 14.85 10.33 15.44 10.35C17.19 10.48 18.5 11.21 19.35 12.56C17.79 13.54 17.02 14.9 17.03 16.62C17.04 18 17.58 19.15 18.64 20.04C19.11 20.45 19.64 20.77 20.24 21L20.23 21.01C20.07 21.45 19.89 21.87 19.7 22.28L18.71 19.5ZM15.54 0.81C16.04 0.21 16.67 -0.21 17.42 -0.45C18.17 -0.7 18.87 -0.84 19.54 -0.81C19.66 0.03 19.81 0.88 19.97 1.74C20.13 2.6 20.07 3.47 19.8 4.35C19.52 5.09 19.11 5.76 18.57 6.35C18.08 6.91 17.45 7.33 16.68 7.6C16 7.83 15.35 7.95 14.73 7.95C14.61 7.13 14.64 6.27 14.84 5.38C15.12 4.14 15.68 3.11 16.53 2.29L15.54 0.81Z"/>
            </svg>
            Apple
          </button>
        </div>

        <!-- Sign Up Link -->
        <div class="mt-6 pt-6 border-t border-gray-200">
          <p class="text-center text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-purple-600 hover:text-purple-700 ml-1">
              Create Account
            </a>
          </p>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="mt-6 text-center">
        <div class="flex justify-center space-x-6 text-sm">
          <a href="#" class="text-white/70 hover:text-white transition">Privacy</a>
          <a href="#" class="text-white/70 hover:text-white transition">Terms</a>
          <a href="#" class="text-white/70 hover:text-white transition">Help</a>
        </div>
        <p class="mt-4 text-xs text-white/60">
          © 2025 Ministry of Health, Trinidad and Tobago. All rights reserved.
        </p>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
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

    // Form submission loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
      document.getElementById('loginBtn').classList.add('loading');
    });

    // Auto-hide alerts
    setTimeout(() => {
      const alerts = document.querySelectorAll('.bg-red-100, .bg-green-100');
      alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => alert.style.display = 'none', 500);
      });
    }, 5000);

    // Focus email field on load
    window.addEventListener('load', () => {
      document.getElementById('email').focus();
    });
  </script>
</body>
</html>