<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Request Submitted - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            {{-- Success Card --}}
            <div class="card bg-base-100 shadow-2xl">
                <div class="card-body text-center">
                    {{-- Success Icon --}}
                    <div class="mx-auto mb-6">
                        <div class="w-24 h-24 rounded-full bg-success/20 flex items-center justify-center mx-auto animate-bounce">
                            <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Request Submitted Successfully!
                    </h1>

                    {{-- Message --}}
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Thank you for requesting an MOH Staff account.
                        <br><br>
                        Your request has been submitted and is pending approval by an administrator.
                    </p>

                    {{-- What Happens Next --}}
                    <div class="bg-base-200 rounded-lg p-4 text-left mb-6">
                        <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-300 mb-3">What happens next?</h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center">
                                    <span class="text-xs font-bold text-primary">1</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    An administrator will review your request
                                </p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center">
                                    <span class="text-xs font-bold text-primary">2</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    You'll receive an email notification when approved
                                </p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center">
                                    <span class="text-xs font-bold text-primary">3</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Use the same email and password to log in
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Alert --}}
                    <div class="alert alert-info text-left mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm">
                                <strong>Important:</strong> You cannot log in until your account is approved.
                                Approval typically takes 1-2 business days.
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="card-actions justify-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Return to Home
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-ghost">
                            Go to Login
                        </a>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <p class="text-center text-sm text-white/60 mt-6">
                Questions? Contact <a href="mailto:support@health.gov.tt" class="text-white/80 hover:text-white underline">support@health.gov.tt</a>
            </p>
        </div>
    </div>
</body>
</html>
