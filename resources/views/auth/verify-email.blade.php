<x-layouts>
    <x-slot:heading>
        Email Verification
    </x-slot:heading>

    <div class="max-w-md mx-auto mt-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">Verify Your Email Address</h2>
            </div>
            
            @if (session('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif
            
            <p class="text-gray-600 mb-6">
                Before proceeding, please check your email for a verification link. 
                If you did not receive the email, click the button below to request another.
            </p>
            
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">
                    Resend Verification Email
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</x-layouts>