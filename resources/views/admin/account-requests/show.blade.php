<x-layouts>
    <x-slot:heading>
        Account Request Details
    </x-slot:heading>

    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.account-requests.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Account Requests
            </a>
        </div>

        <!-- Status Banner -->
        <div class="mb-6 p-4 rounded-lg @if($request->status === 'approved') bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 @elseif($request->status === 'rejected') bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 @else bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 @endif">
            <div class="flex items-center">
                @if($request->status === 'approved')
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-green-800 dark:text-green-200">Request Approved</p>
                        <p class="text-sm text-green-600 dark:text-green-400">
                            Approved by {{ $request->reviewer->first_name ?? 'Admin' }} on {{ $request->reviewed_at?->format('M d, Y \a\t H:i') }}
                        </p>
                    </div>
                @elseif($request->status === 'rejected')
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-red-800 dark:text-red-200">Request Rejected</p>
                        <p class="text-sm text-red-600 dark:text-red-400">
                            Rejected by {{ $request->reviewer->first_name ?? 'Admin' }} on {{ $request->reviewed_at?->format('M d, Y \a\t H:i') }}
                        </p>
                    </div>
                @else
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-800 dark:text-yellow-200">Pending Review</p>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400">This request is awaiting approval</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Applicant Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Applicant Information</h2>
            </div>
            <div class="px-6 py-4">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 h-16 w-16">
                        <div class="h-16 w-16 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-medium">
                            {{ strtoupper(substr($request->first_name, 0, 1) . substr($request->last_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $request->first_name }} {{ $request->last_name }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $request->email }}</p>
                        @if($request->isMohStaffRequest())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mt-1">
                                MOH Staff
                            </span>
                        @endif
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($request->department)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Department</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $request->department }}</dd>
                        </div>
                    @endif

                    @if($request->organization)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Organization</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $request->organization }}</dd>
                        </div>
                    @endif

                    @if($request->job_title)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Job Title</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $request->job_title }}</dd>
                        </div>
                    @endif

                    @if($request->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $request->phone }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Request Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($request->request_type ?? 'Account') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $request->created_at->format('M d, Y \a\t H:i') }}
                            <span class="text-gray-500 dark:text-gray-400">({{ $request->created_at->diffForHumans() }})</span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($request->reason)
            <!-- Reason for Request -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Reason for Request</h2>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $request->reason }}</p>
                </div>
            </div>
        @endif

        @if($request->status === 'rejected' && $request->rejection_reason)
            <!-- Rejection Reason -->
            <div class="bg-red-50 dark:bg-red-900/20 shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-red-200 dark:border-red-800">
                    <h2 class="text-lg font-medium text-red-800 dark:text-red-200">Rejection Reason</h2>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-red-700 dark:text-red-300 whitespace-pre-line">{{ $request->rejection_reason }}</p>
                </div>
            </div>
        @endif

        @if($request->admin_notes)
            <!-- Admin Notes -->
            <div class="bg-gray-50 dark:bg-gray-700 shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Admin Notes</h2>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $request->admin_notes }}</p>
                </div>
            </div>
        @endif

        @if($request->user)
            <!-- Associated User Account -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Associated User Account</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                                {{ $request->user->first_name }} {{ $request->user->last_name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $request->user->email }}</p>
                        </div>
                        <a href="{{ route('admin.users.index', ['search' => $request->user->email]) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors">
                            View User
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.account-requests.index') }}"
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                Back to List
            </a>
        </div>
    </div>
</x-layouts>
