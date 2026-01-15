<x-layouts>
    <x-slot:heading>
        Email Verification Required
    </x-slot:heading>

    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Verify Your Email Address
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Before you can access the system, please verify your email address.
                </p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Your email</p>
                        <p class="text-sm text-gray-600">{{ $user->email ?? 'Not available' }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <p class="text-sm text-gray-600 text-center">
                    Click the button below to receive a 6-digit verification code at your email address.
                </p>

                <form method="POST" action="{{ route('verification.initiate') }}">
                    @csrf
                    <button type="submit"
                            @if(!($canResend ?? true)) disabled @endif
                            class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        @if($canResend ?? true)
                            Send Verification Code
                        @else
                            Please wait {{ $secondsUntilResend ?? 60 }} seconds
                        @endif
                    </button>
                </form>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Important:</strong> You cannot access the system until your email is verified.
                            The verification code will expire in 10 minutes.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700">
                        Sign out and use a different account
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts>
