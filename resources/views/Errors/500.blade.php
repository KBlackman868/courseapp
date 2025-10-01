<!-- resources/views/errors/500.blade.php -->
<x-layouts>
    <x-slot:heading>
        Server Error
    </x-slot:heading>

    <div class="min-h-[60vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full text-center">
            <!-- Error Icon/Illustration -->
            <div class="mb-8">
                <svg class="mx-auto h-40 w-40 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                
                <!-- Large 500 Text -->
                <div class="mt-4">
                    <span class="text-9xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">500</span>
                </div>
            </div>

            <!-- Error Message -->
            <div class="space-y-4 mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                    Internal Server Error
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    Something went wrong on our end. We're working to fix the issue. Please try again later.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.location.reload()" 
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105">
                    <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Try Again
                </button>

                <a href="{{ url('/') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                    <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Go to Homepage
                </a>
            </div>

            <!-- Additional Help Text -->
            <div class="mt-12 text-sm text-gray-500 dark:text-gray-400">
                <p>If this problem persists, please 
                    <a href="mailto:support@health.gov.tt" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 underline">
                        contact our support team
                    </a>
                </p>
                @if(app()->bound('sentry') && app('sentry')->getLastEventId())
                    <p class="mt-2 text-xs">
                        Error ID: {{ app('sentry')->getLastEventId() }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        svg {
            animation: pulse 2s ease-in-out infinite;
        }
    </style>
</x-layouts>