<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password - Ministry of Health</title>
  <link rel="icon" type="image/jpeg" href="/images/moh_logo.jpg">
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
          oninput="updatePasswordChecklist(this.value)"
          class="block py-3 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 focus:outline-none focus:border-indigo-600 peer transition duration-300" />
        <label for="password"
          class="absolute text-lg text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 origin-[0] peer-focus:left-0 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0">
          New Password (min. 12 characters)
        </label>
        @error('password')
          <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
        <!-- Password Requirements Checklist -->
        <div id="password-checklist" class="mt-2 rounded-lg border border-gray-200 bg-gray-50 p-3 hidden">
          <p class="mb-2 text-xs font-semibold text-gray-600 uppercase tracking-wide">Password Requirements</p>
          <ul class="space-y-1">
            <li id="req-length" class="flex items-center text-sm"><span class="mr-2 flex-shrink-0 text-red-500">&#10060;</span><span class="text-red-600">At least 12 characters <span id="req-length-count" class="text-gray-500"></span></span></li>
            <li id="req-upper" class="flex items-center text-sm"><span class="mr-2 flex-shrink-0 text-red-500">&#10060;</span><span class="text-red-600">At least one uppercase letter (A-Z)</span></li>
            <li id="req-lower" class="flex items-center text-sm"><span class="mr-2 flex-shrink-0 text-red-500">&#10060;</span><span class="text-red-600">At least one lowercase letter (a-z)</span></li>
            <li id="req-digit" class="flex items-center text-sm"><span class="mr-2 flex-shrink-0 text-red-500">&#10060;</span><span class="text-red-600">At least one number (0-9)</span></li>
            <li id="req-special" class="flex items-center text-sm"><span class="mr-2 flex-shrink-0 text-red-500">&#10060;</span><span class="text-red-600">At least one special character (! @ # $ % ^ & *)</span></li>
            <li id="req-forbidden" class="flex items-center text-sm"><span class="mr-2 flex-shrink-0 text-green-600">&#9989;</span><span class="text-green-700">Does not contain \ ~ &lt; &gt;</span></li>
          </ul>
        </div>
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
  <script>
  function updatePasswordChecklist(password) {
    const checklist = document.getElementById('password-checklist');
    if (!password) { checklist.classList.add('hidden'); return; }
    checklist.classList.remove('hidden');

    const checks = {
      length: password.length >= 12,
      upper: /[A-Z]/.test(password),
      lower: /[a-z]/.test(password),
      digit: /[0-9]/.test(password),
      special: /[!@#$%^&*()\-_=+\[\]{}|;:'",.?\/]/.test(password),
      forbidden: !/[\\~<>]/.test(password),
    };

    Object.keys(checks).forEach(function(key) {
      const li = document.getElementById('req-' + key);
      const icon = li.querySelector('span:first-child');
      const text = li.querySelector('span:last-child');
      if (checks[key]) {
        icon.textContent = '\u2705';
        icon.className = 'mr-2 flex-shrink-0 text-green-600';
        text.className = 'text-green-700';
      } else {
        icon.textContent = '\u274C';
        icon.className = 'mr-2 flex-shrink-0 text-red-500';
        text.className = 'text-red-600';
      }
    });

    const countSpan = document.getElementById('req-length-count');
    if (!checks.length) {
      countSpan.textContent = '(' + password.length + '/12)';
      countSpan.className = 'text-gray-500';
    } else {
      countSpan.textContent = '';
    }
  }
  </script>
</body>
</html>
