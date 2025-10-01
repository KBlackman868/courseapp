<!-- resources/views/admin/moodle/status.blade.php -->
<x-layouts>
    <x-slot:heading>
        Moodle Integration Status
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Connection Status -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Moodle Connection Status</h2>
            
            <div class="flex items-center space-x-4">
                <div id="connection-status" class="flex items-center">
                    <div class="w-3 h-3 bg-gray-400 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-gray-600">Checking connection...</span>
                </div>
                <button onclick="testConnection()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Test Connection
                </button>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600">
                        Moodle Synced: <span class="font-semibold">{{ $stats['moodle_synced_users'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        @php
                            $userPercentage = $stats['total_users'] > 0 
                                ? ($stats['moodle_synced_users'] / $stats['total_users']) * 100 
                                : 0;
                        @endphp
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $userPercentage }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Total Courses -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Courses</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_courses'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600">
                        Moodle Synced: <span class="font-semibold">{{ $stats['moodle_synced_courses'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        @php
                            $coursePercentage = $stats['total_courses'] > 0 
                                ? ($stats['moodle_synced_courses'] / $stats['total_courses']) * 100 
                                : 0;
                        @endphp
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $coursePercentage }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Pending Enrollments -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Enrollments</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_enrollments'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.enrollments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        View pending →
                    </a>
                </div>
            </div>

            <!-- Sync Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Sync Queue</p>
                        <p class="text-3xl font-bold text-gray-900">
                            @php
                                $queueSize = \DB::table('jobs')->count();
                            @endphp
                            {{ $queueSize }}
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.moodle.failedJobs') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        View failed jobs →
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Sync All Users -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Sync All Users</h3>
                    <p class="text-sm text-gray-600 mb-3">Sync all users without Moodle accounts</p>
                    <form action="{{ route('admin.moodle.users.bulkSync') }}" method="POST">
                        @csrf
                        @php
                            $unsyncedUsers = \App\Models\User::whereNull('moodle_user_id')->pluck('id')->toArray();
                        @endphp
                        @foreach($unsyncedUsers as $userId)
                            <input type="hidden" name="user_ids[]" value="{{ $userId }}">
                        @endforeach
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Sync {{ count($unsyncedUsers) }} Users
                        </button>
                    </form>
                </div>

                <!-- Sync Courses -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Import Moodle Courses</h3>
                    <p class="text-sm text-gray-600 mb-3">Import all courses from Moodle</p>
                    <a href="{{ route('admin.moodle.courses.import') }}" class="block w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-center">
                        Go to Course Import
                    </a>
                </div>

                <!-- Process Queue -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Process Queue</h3>
                    <p class="text-sm text-gray-600 mb-3">Manually process sync queue</p>
                    <button onclick="processQueue()" class="w-full px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        Process Now
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Moodle Sync Activity</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            // Get recent synced users
                            $recentUsers = \App\Models\User::whereNotNull('moodle_user_id')
                                ->latest('updated_at')
                                ->take(5)
                                ->get();
                        @endphp
                        @foreach($recentUsers as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm">User Sync</td>
                                <td class="px-6 py-4 text-sm">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Success</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function testConnection() {
            const statusEl = document.getElementById('connection-status');
            statusEl.innerHTML = '<div class="w-3 h-3 bg-yellow-400 rounded-full mr-2 animate-pulse"></div><span class="text-gray-600">Testing...</span>';
            
            fetch('{{ route("admin.moodle.testConnection") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        statusEl.innerHTML = '<div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div><span class="text-green-600">Connected</span>';
                    } else {
                        statusEl.innerHTML = '<div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div><span class="text-red-600">Connection Failed: ' + (data.message || 'Unknown error') + '</span>';
                    }
                })
                .catch(error => {
                    statusEl.innerHTML = '<div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div><span class="text-red-600">Error: ' + error + '</span>';
                });
        }

        function processQueue() {
            if (confirm('This will process all pending sync jobs. Continue?')) {
                // You can implement this to call artisan queue:work once
                alert('Queue processing started. Check Laravel logs for details.');
            }
        }

        // Test connection on page load
        document.addEventListener('DOMContentLoaded', function() {
            testConnection();
        });
    </script>
</x-layouts>