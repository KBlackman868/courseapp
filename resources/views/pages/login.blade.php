<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Ministry of Health</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom gradient background */
    body {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center relative">
  <!-- Error Banner -->
  @if($errors->any())
    <div id="error-alert" class="fixed top-0 left-0 right-0 bg-red-600 text-white p-4 text-center transform transition-all duration-500 ease-in-out" style="transform: translateY(-100%); opacity: 0;">
      {{ $errors->first('email') }}
    </div>
  @endif

  <!-- Glassmorphism Form Container -->
  <div class="flex flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-2xl p-8">
        <div class="text-center">
          <img class="mx-auto h-12 w-auto" src="{{ asset('images/moh_logo.jpg') }}" alt="Ministry of Health" />
          <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">
            Sign in to your account
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Welcome back! Please enter your credentials.
          </p>
        </div>

        <div class="mt-8">
          <form class="space-y-6" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <!-- Email Field -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">
                Email address
              </label>
              <div class="mt-1">
                <input
                  type="email"
                  name="email"
                  id="email"
                  autocomplete="email"
                  required
                  class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-200 transition duration-200"
                />
              </div>
            </div>

            <!-- Password Field with Forgot Password Link -->
            <div>
              <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-gray-700">
                  Password
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                  Forgot password?
                </a>
              </div>
              <div class="mt-1">
                <input
                  type="password"
                  name="password"
                  id="password"
                  autocomplete="current-password"
                  required
                  class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-200 transition duration-200"
                />
              </div>
            </div>

            <!-- Submit Button -->
            <div>
              <button
                type="submit"
                class="w-full flex justify-center rounded-md bg-indigo-600 py-2 px-4 text-sm font-semibold text-white shadow hover:bg-indigo-500 transition duration-200 focus:outline-none focus:ring focus:ring-indigo-400"
              >
                Sign in
              </button>
            </div>
          </form>
        </div>

        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">
              Sign up
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Error banner animation -->
  @if($errors->any())
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const errorAlert = document.getElementById("error-alert");
        // Slide down and fade in the error banner
        errorAlert.style.transform = "translateY(0)";
        errorAlert.style.opacity = "1";
        // After 3 seconds, slide up and fade out
        setTimeout(() => {
          errorAlert.style.transform = "translateY(-100%)";
          errorAlert.style.opacity = "0";
        }, 3000);
      });
    </script>
  @endif

</body>
</html>
