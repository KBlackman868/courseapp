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

        <h2 class="text-2xl font-bold mb-6">Enrollment Requests ({{ ucfirst($status) }})</h2>

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Enrollment ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Student</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Course</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Current Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($enrollments as $enrollment)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-800">{{ $enrollment->id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-800">
                            {{ $enrollment->user->first_name }} {{ $enrollment->user->last_name }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-800">
                            {{ $enrollment->course->title }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-800">{{ ucfirst($enrollment->status) }}</td>
                        <td class="px-4 py-2">
                            @if($enrollment->status === 'pending')
                                <form action="{{ route('admin.enrollments.update', $enrollment->id) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="bg-green-600 text-white text-sm px-3 py-1 rounded hover:bg-green-700">
                                        Accept
                                    </button>
                                </form>
                            @else
                                <span class="text-sm text-gray-600">No Action</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts>
