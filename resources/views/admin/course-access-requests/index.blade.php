<x-layouts>
    <x-slot:heading>
        Course Access Requests
    </x-slot:heading>

    <div class="max-w-7xl mx-auto">
        <!-- Page Description -->
        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-400">
                Review and approve course enrollment requests from users.
            </p>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <!-- Status Tabs -->
            <div class="flex-1 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <a href="{{ route('admin.course-access-requests.index', ['status' => 'pending', 'course_id' => $courseId]) }}"
                       class="@if($status === 'pending') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        Pending
                        @if($counts['pending'] > 0)
                            <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                {{ $counts['pending'] }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.course-access-requests.index', ['status' => 'approved', 'course_id' => $courseId]) }}"
                       class="@if($status === 'approved') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        Approved
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $counts['approved'] }}
                        </span>
                    </a>
                    <a href="{{ route('admin.course-access-requests.index', ['status' => 'rejected', 'course_id' => $courseId]) }}"
                       class="@if($status === 'rejected') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        Rejected
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            {{ $counts['rejected'] }}
                        </span>
                    </a>
                    @if($counts['failed'] > 0)
                        <a href="{{ route('admin.course-access-requests.index', ['status' => 'failed', 'course_id' => $courseId]) }}"
                           class="@if($status === 'failed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            Sync Failed
                            <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                {{ $counts['failed'] }}
                            </span>
                        </a>
                    @endif
                    <a href="{{ route('admin.course-access-requests.index', ['status' => 'all', 'course_id' => $courseId]) }}"
                       class="@if($status === 'all') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        All
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Filter Dropdowns -->
            <div class="flex gap-3 items-center">
                <!-- Course Filter -->
                <select onchange="window.location.href=this.value"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="{{ route('admin.course-access-requests.index', ['status' => $status]) }}">All Courses</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ route('admin.course-access-requests.index', ['status' => $status, 'course_id' => $id]) }}"
                                @if($courseId == $id) selected @endif>
                            {{ Str::limit($title, 30) }}
                        </option>
                    @endforeach
                </select>

                <!-- Search -->
                <form action="{{ route('admin.course-access-requests.index') }}" method="GET" class="flex gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="hidden" name="course_id" value="{{ $courseId }}">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search by user..."
                           class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 w-48">
                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Bulk Actions (Only for pending) -->
        @if($status === 'pending' && $counts['pending'] > 0)
            <div class="mb-4 p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="select-all" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <label for="select-all" class="text-sm font-medium text-gray-700 dark:text-gray-300">Select All</label>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('admin.course-access-requests.bulkApprove') }}" method="POST" id="bulk-approve-form">
                        @csrf
                        <div id="selected-ids-container"></div>
                        <button type="submit" id="bulk-approve-btn" disabled
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium">
                            Approve Selected (<span id="selected-count">0</span>)
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($requests->isEmpty())
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No {{ $status === 'failed' ? 'sync failed' : $status }} course access requests</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($status === 'pending')
                        No course access requests are awaiting approval.
                    @elseif($status === 'failed')
                        No course access requests have failed to sync with Moodle.
                    @else
                        No {{ $status }} course access requests found.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            @if($status === 'pending')
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <span class="sr-only">Select</span>
                                </th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Course
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Requested
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($requests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                @if($status === 'pending')
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="request_ids[]" value="{{ $request->id }}"
                                               class="request-checkbox rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($request->user->profile_photo)
                                                <img class="h-10 w-10 rounded-full" src="{{ Storage::url($request->user->profile_photo) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-medium">
                                                    {{ strtoupper(substr($request->user->first_name, 0, 1) . substr($request->user->last_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $request->user->first_name }} {{ $request->user->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $request->user->email }}
                                            </div>
                                            <div class="mt-1">
                                                @if($request->user->isInternal())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        MOH Staff
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                        External
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ Str::limit($request->course->title, 40) }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($request->course->hasMoodleIntegration())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Moodle: {{ $request->course->moodle_course_id }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                No Moodle
                                            </span>
                                        @endif
                                    </div>
                                    @if($request->request_reason)
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="font-medium">Reason:</span> {{ Str::limit($request->request_reason, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $request->requested_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $request->requested_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Approved
                                        </span>
                                        <!-- Moodle Sync Status -->
                                        @if($request->moodle_sync_status === 'synced')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mt-1">
                                                Synced
                                            </span>
                                        @elseif($request->moodle_sync_status === 'syncing')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mt-1">
                                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Syncing
                                            </span>
                                        @elseif($request->moodle_sync_status === 'failed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1">
                                                Sync Failed
                                            </span>
                                        @endif
                                        @if($request->approver)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                by {{ $request->approver->first_name }}
                                            </div>
                                        @endif
                                    @elseif($request->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Rejected
                                        </span>
                                        @if($request->approver)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                by {{ $request->approver->first_name }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @if($request->status === 'pending')
                                            <!-- Approve Button -->
                                            <button type="button"
                                                    onclick="openApproveModal({{ $request->id }}, '{{ $request->user->full_name }}', '{{ $request->course->title }}')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approve
                                            </button>
                                            <!-- Reject Button -->
                                            <button type="button"
                                                    onclick="openRejectModal({{ $request->id }}, '{{ $request->user->full_name }}', '{{ $request->course->title }}')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Reject
                                            </button>
                                        @elseif($request->hasSyncFailed())
                                            <!-- Retry Sync Button -->
                                            <form action="{{ route('admin.course-access-requests.retrySync', $request) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                    Retry Sync
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.course-access-requests.show', $request) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Approve Modal -->
    <div id="approve-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeApproveModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="approve-form" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                    Approve Course Access
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    You are approving access for <strong id="approve-user"></strong> to <strong id="approve-course"></strong>.
                                </p>
                                <div class="mt-4">
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Admin Notes (optional)
                                    </label>
                                    <textarea name="admin_notes" id="admin_notes" rows="3"
                                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                              placeholder="Any notes about this approval..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Approve Access
                        </button>
                        <button type="button" onclick="closeApproveModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRejectModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="reject-form" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                    Reject Course Access
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    You are rejecting access for <strong id="reject-user"></strong> to <strong id="reject-course"></strong>.
                                </p>
                                <div class="mt-4">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Reason for Rejection <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                              placeholder="Please provide a reason for rejection..."></textarea>
                                </div>
                                <div class="mt-4">
                                    <label for="reject_admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Admin Notes (optional)
                                    </label>
                                    <textarea name="admin_notes" id="reject_admin_notes" rows="2"
                                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                              placeholder="Internal notes (not visible to user)..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Reject Request
                        </button>
                        <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Select All checkbox functionality
        const selectAllCheckbox = document.getElementById('select-all');
        const requestCheckboxes = document.querySelectorAll('.request-checkbox');
        const bulkApproveBtn = document.getElementById('bulk-approve-btn');
        const selectedCountSpan = document.getElementById('selected-count');
        const selectedIdsContainer = document.getElementById('selected-ids-container');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                requestCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        requestCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.request-checkbox:checked');
            const count = selected.length;

            if (selectedCountSpan) selectedCountSpan.textContent = count;
            if (bulkApproveBtn) bulkApproveBtn.disabled = count === 0;

            // Update hidden inputs
            if (selectedIdsContainer) {
                selectedIdsContainer.innerHTML = '';
                selected.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'request_ids[]';
                    input.value = checkbox.value;
                    selectedIdsContainer.appendChild(input);
                });
            }
        }

        // Modal Functions
        function openApproveModal(requestId, userName, courseName) {
            document.getElementById('approve-user').textContent = userName;
            document.getElementById('approve-course').textContent = courseName;
            document.getElementById('approve-form').action = `/admin/course-access-requests/${requestId}/approve`;
            document.getElementById('approve-modal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
        }

        function openRejectModal(requestId, userName, courseName) {
            document.getElementById('reject-user').textContent = userName;
            document.getElementById('reject-course').textContent = courseName;
            document.getElementById('reject-form').action = `/admin/course-access-requests/${requestId}/reject`;
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApproveModal();
                closeRejectModal();
            }
        });
    </script>
    @endpush
</x-layouts>
