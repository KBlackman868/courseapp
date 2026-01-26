<x-layouts>
    <x-slot:heading>
        Enrollment Requests
    </x-slot:heading>

    <div class="max-w-7xl mx-auto">
        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <!-- Status Tabs -->
            <div class="flex-1 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.enrollment-requests.index', ['status' => 'pending', 'course_id' => $courseId, 'user_type' => $userType]) }}"
                       class="@if($status === 'pending') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        Pending
                        @if($counts['pending'] > 0)
                            <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                {{ $counts['pending'] }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.enrollment-requests.index', ['status' => 'approved', 'course_id' => $courseId, 'user_type' => $userType]) }}"
                       class="@if($status === 'approved') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        Approved
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $counts['approved'] }}
                        </span>
                    </a>
                    <a href="{{ route('admin.enrollment-requests.index', ['status' => 'denied', 'course_id' => $courseId, 'user_type' => $userType]) }}"
                       class="@if($status === 'denied') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        Denied
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            {{ $counts['denied'] }}
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Filter Dropdowns -->
            <div class="flex gap-3">
                <select onchange="window.location.href=this.value"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="{{ route('admin.enrollment-requests.index', ['status' => $status, 'user_type' => $userType]) }}">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ route('admin.enrollment-requests.index', ['status' => $status, 'course_id' => $course->id, 'user_type' => $userType]) }}"
                                @if($courseId == $course->id) selected @endif>
                            {{ Str::limit($course->title, 30) }}
                        </option>
                    @endforeach
                </select>

                <select onchange="window.location.href=this.value"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="{{ route('admin.enrollment-requests.index', ['status' => $status, 'course_id' => $courseId]) }}">All User Types</option>
                    <option value="{{ route('admin.enrollment-requests.index', ['status' => $status, 'course_id' => $courseId, 'user_type' => 'internal']) }}"
                            @if($userType === 'internal') selected @endif>MOH Staff</option>
                    <option value="{{ route('admin.enrollment-requests.index', ['status' => $status, 'course_id' => $courseId, 'user_type' => 'external']) }}"
                            @if($userType === 'external') selected @endif>External Users</option>
                </select>
            </div>
        </div>

        @if($requests->isEmpty())
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No {{ $status }} enrollment requests</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($status === 'pending')
                        No enrollment requests are awaiting approval.
                    @else
                        No {{ $status }} enrollment requests found.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
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
                                        {{ $request->course->title }}
                                    </div>
                                    @if($request->course->moodle_course_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Moodle ID: {{ $request->course->moodle_course_id }}
                                        </span>
                                    @endif
                                    @if($request->request_reason)
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="font-medium">Reason:</span> {{ Str::limit($request->request_reason, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $request->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $request->created_at->diffForHumans() }}</div>
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
                                        @if($request->reviewer)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                by {{ $request->reviewer->first_name }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Denied
                                        </span>
                                        @if($request->reviewer)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                by {{ $request->reviewer->first_name }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @if($request->status === 'pending')
                                            <form action="{{ route('admin.enrollment-requests.approve', $request) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.enrollment-requests.deny', $request) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Deny
                                                </button>
                                            </form>
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
</x-layouts>
