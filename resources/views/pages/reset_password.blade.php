<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password - Ministry of Health</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center">
  <div class="bg-white/90 backdrop-blur-lg shadow-2xl rounded-xl p-8 max-w-md w-full">
    <h2 class="text-3xl font-bold text-center text-gray-900 mb-6">Reset Your Password</h2>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
      @csrf

      <!-- Hidden token field -->
      <input type="hidden" name="token" value="{{ $token }}">

      <!-- Email Field -->
      <div class="relative z-0 w-full group">
        <input type="email" name="email" id="email" placeholder=" " required
          class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 focus:outline-none focus:border-indigo-600 peer transition duration-300"
          value="{{ old('email', $email ?? '') }}" />
        <label for="email"
          class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0">
          Email Address
        </label>
        @error('email')
          <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
      </div>

      <!-- New Password Field -->
      <div class="relative z-0 w-full group">
        <input type="password" name="password" id="password" placeholder=" " required
          class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 focus:outline-none focus:border-indigo-600 peer transition duration-300" />
        <label for="password"
          class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0">
          New Password
        </label>
        @error('password')
          <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm Password Field -->
      <div class="relative z-0 w-full group">
        <input type="password" name="password_confirmation" id="password_confirmation" placeholder=" " required
          class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 focus:outline-none focus:border-indigo-600 peer transition duration-300" />
        <label for="password_confirmation"
          class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0">
          Confirm Password
        </label>
      </div>

      <button type="submit"
        class="w-full py-3 px-5 text-lg font-semibold text-white bg-indigo-600 rounded-lg shadow-lg hover:bg-indigo-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Reset Password
      </button>
    </form>

    <p class="mt-6 text-center text-gray-600">
      <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline">
        Back to Sign In
      </a>
    </p>
  </div>
</body>
</html>
