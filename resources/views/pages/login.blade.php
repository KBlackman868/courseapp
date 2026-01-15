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

    /* Tab styles */
    .tab-btn {
      flex: 1;
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      font-weight: 600;
      text-align: center;
      border-bottom: 3px solid transparent;
      color: #6b7280;
      transition: all 0.3s;
      cursor: pointer;
      background: transparent;
      border-top: none;
      border-left: none;
      border-right: none;
    }

    .tab-btn:hover {
      color: #667eea;
    }

    .tab-btn.active {
      color: #667eea;
      border-bottom-color: #667eea;
    }

    /* Info boxes */
    .info-box {
      padding: 0.875rem;
      border-radius: 0.5rem;
      margin-bottom: 1.25rem;
      font-size: 0.875rem;
      display: flex;
      align-items: flex-start;
    }

    .info-box-internal {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      border-left: 4px solid #667eea;
    }

    .info-box-external {
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
      border-left: 4px solid #10b981;
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

    /* User type badge */
    .user-type-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-left: 0.5rem;
    }

    .badge-internal {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .badge-external {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
      .glass-card {
        margin: 1rem;
        padding: 1.5rem;
      }
      
      .tab-btn {
        font-size: 0.75rem;
        padding: 0.625rem 0.5rem;
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

        @if(session('info'))
          <div class="mb-4 p-3 bg-blue-100 border border-blue-300 rounded-lg flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-blue-700 text-sm">{{ session('info') }}</span>
          </div>
        @endif

        @if($errors->any())
          <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded-lg">
            @foreach($errors->all() as $error)
              <p class="text-red-700 text-sm mb-1 last:mb-0">• {{ $error }}</p>
            @endforeach
          </div>
        @endif

        <!-- Welcome Text with User Type Badge -->
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            Welcome back
            <span id="user-type-badge" class="user-type-badge badge-internal">MOH Staff</span>
          </h2>
          <p class="text-gray-600 mt-1">Please sign in to your account</p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 mb-4" id="login-tabs">
          <button type="button" 
                  class="tab-btn active"
                  data-tab="internal"
                  id="tab-internal">
            <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            MOH Staff
          </button>
          <button type="button" 
                  class="tab-btn"
                  data-tab="external"
                  id="tab-external">
            <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            External User
          </button>
        </div>

        <!-- Info Boxes -->
        <div id="internal-info" class="info-box info-box-internal">
          <svg class="w-5 h-5 text-purple-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div>
            <span class="font-semibold text-purple-700">MOH Staff:</span>
            <span class="text-purple-600"> Use your Ministry email (@health.gov.tt) and network password.</span>
          </div>
        </div>

        <div id="external-info" class="info-box info-box-external hidden">
          <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div>
            <span class="font-semibold text-green-700">External Users:</span>
            <span class="text-green-600"> Use your registered email and password.</span>
          </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
          @csrf
          
          <!-- Email Input -->
          <div class="input-group">
            <input 
              type="test" 
              name="email" 
              id="email"
              value="{{ old('email') }}"
              required
              autocomplete="email"
              placeholder="Email address"
              class="form-input @error('email') border-red-500 @enderror"
            >
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        @if(Route::has('auth.google'))
        <a href="{{ route('auth.google') }}" class="btn-google">
          <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Sign in with Google (MOH Only)
        </a>
        @endif

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
    // Tab switching
    const tabs = document.querySelectorAll('.tab-btn');
    const internalInfo = document.getElementById('internal-info');
    const externalInfo = document.getElementById('external-info');
    const userTypeBadge = document.getElementById('user-type-badge');
    const emailInput = document.getElementById('email');

    function switchTab(tab) {
      // Update tab styles
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      // Update info boxes and badge
      if (tab.dataset.tab === 'internal') {
        internalInfo.classList.remove('hidden');
        externalInfo.classList.add('hidden');
        userTypeBadge.textContent = 'MOH Staff';
        userTypeBadge.classList.remove('badge-external');
        userTypeBadge.classList.add('badge-internal');
      } else {
        internalInfo.classList.add('hidden');
        externalInfo.classList.remove('hidden');
        userTypeBadge.textContent = 'External';
        userTypeBadge.classList.remove('badge-internal');
        userTypeBadge.classList.add('badge-external');
      }
    }

    tabs.forEach(tab => {
      tab.addEventListener('click', function() {
        switchTab(this);
      });
    });

    // Auto-detect tab based on email domain
    emailInput.addEventListener('input', function() {
      const email = this.value.toLowerCase();
      if (email.includes('@health.gov.tt') || email.includes('@moh.gov.tt')) {
        switchTab(document.getElementById('tab-internal'));
      } else if (email.includes('@') && email.indexOf('@') < email.length - 1) {
        switchTab(document.getElementById('tab-external'));
      }
    });

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

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
      const alerts = document.querySelectorAll('.bg-red-100, .bg-green-100, .bg-blue-100');
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