<x-layouts>
    <x-slot:heading>
        Account Pending Approval
    </x-slot:heading>

    <div class="min-h-[60vh] flex items-center justify-center">
        <div class="max-w-lg w-full">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body text-center">
                    {{-- Status Icon --}}
                    <div class="mx-auto mb-6">
                        @if($status === 'pending')
                            <div class="w-24 h-24 rounded-full bg-warning/20 flex items-center justify-center mx-auto">
                                <svg class="w-12 h-12 text-warning animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @elseif($status === 'rejected')
                            <div class="w-24 h-24 rounded-full bg-error/20 flex items-center justify-center mx-auto">
                                <svg class="w-12 h-12 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-24 h-24 rounded-full bg-info/20 flex items-center justify-center mx-auto">
                                <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        @if($status === 'pending')
                            Your Account is Pending Approval
                        @elseif($status === 'rejected')
                            Account Request Rejected
                        @else
                            Account Status
                        @endif
                    </h1>

                    {{-- Message --}}
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        @if($status === 'pending')
                            Thank you for registering, <strong>{{ $user->first_name }}</strong>!
                            <br><br>
                            Your account request is currently being reviewed by our administrators.
                            You will receive an email notification once your account has been approved.
                        @elseif($status === 'rejected')
                            We're sorry, but your account request has been rejected.
                            Please contact support for more information.
                        @else
                            Your account status is: <strong>{{ ucfirst($status) }}</strong>
                        @endif
                    </p>

                    {{-- Status Badge --}}
                    <div class="flex justify-center mb-6">
                        @if($status === 'pending')
                            <span class="badge badge-warning badge-lg gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pending Review
                            </span>
                        @elseif($status === 'rejected')
                            <span class="badge badge-error badge-lg gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Rejected
                            </span>
                        @endif
                    </div>

                    {{-- Request Details --}}
                    <div class="bg-base-200 rounded-lg p-4 text-left mb-6">
                        <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Request Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Email:</span>
                                <span class="font-medium">{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Submitted:</span>
                                <span class="font-medium">{{ $requestedAt->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Time Elapsed:</span>
                                <span class="font-medium">{{ $requestedAt->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- What to Expect --}}
                    @if($status === 'pending')
                        <div class="alert alert-info text-left mb-6">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-bold">What happens next?</h4>
                                <ul class="text-sm mt-1 list-disc list-inside">
                                    <li>An administrator will review your request</li>
                                    <li>You'll receive an email when approved</li>
                                    <li>Once approved, you can access all MOH courses</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="card-actions justify-center">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                        <a href="mailto:support@health.gov.tt" class="btn btn-ghost">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                Need help? Contact us at <a href="mailto:support@health.gov.tt" class="link link-primary">support@health.gov.tt</a>
            </p>
        </div>
    </div>
</x-layouts>
