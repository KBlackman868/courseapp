<x-layouts>
    <x-slot:heading>
        Enrollment Management
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 py-10">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Enrollment Requests</h2>
            
            {{-- Status Filter Tabs --}}
            <div class="mt-4 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.enrollments.index', ['status' => 'pending']) }}" 
                       class="@if(request('status', 'pending') === 'pending') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pending ({{ $enrollments->where('status', 'pending')->count() }})
                    </a>
                    <a href="{{ route('admin.enrollments.index', ['status' => 'approved']) }}" 
                       class="@if(request('status') === 'approved') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Approved ({{ $enrollments->where('status', 'approved')->count() }})
                    </a>
                    <a href="{{ route('admin.enrollments.index', ['status' => 'denied']) }}" 
                       class="@if(request('status') === 'denied') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Denied ({{ $enrollments->where('status', 'denied')->count() }})
                    </a>
                </nav>
            </div>
        </div>

        @if($enrollments->isEmpty())
            <div class="bg-gray-50 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No enrollments</h3>
                <p class="mt-1 text-sm text-gray-500">No {{ request('status', 'pending') }} enrollment requests found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    #{{ $enrollment->id }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">
                                            {{ $enrollment->user->first_name }} {{ $enrollment->user->last_name }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $enrollment->user->email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">
                                            {{ $enrollment->course->title }}
                                        </div>
                                        @if($enrollment->course->moodle_course_id)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Moodle ID: {{ $enrollment->course->moodle_course_id }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $enrollment->created_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs">{{ $enrollment->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($enrollment->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($enrollment->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @elseif($enrollment->status === 'denied')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Denied
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex space-x-2">
                                        @if($enrollment->status === 'pending')
                                            <form action="{{ route('admin.enrollments.approve', $enrollment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.enrollments.deny', $enrollment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                                                    Deny
                                                </button>
                                            </form>
                                        @elseif($enrollment->status === 'approved')
                                            @if($enrollment->course->moodle_course_id && $enrollment->user->moodle_user_id)
                                                <form action="{{ route('admin.enrollments.syncToMoodle', $enrollment->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                                        Sync to Moodle
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.enrollments.unenroll', $enrollment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to unenroll this student?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-gray-600 text-white px-3 py-1 rounded text-xs hover:bg-gray-700">
                                                    Unenroll
                                                </button>
                                            </form>
                                        @elseif($enrollment->status === 'denied')
                                            <form action="{{ route('admin.enrollments.update', $enrollment->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="bg-yellow-600 text-white px-3 py-1 rounded text-xs hover:bg-yellow-700">
                                                    Reopen
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
        @endif
    </div>
</x-layouts>