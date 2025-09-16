<!-- resources/views/admin/moodle-test.blade.php -->
<x-layouts>
    <x-slot:heading>
        Moodle Integration Test Dashboard
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Connection Status Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">🔌 Connection Status</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Moodle URL:</p>
                    <p class="font-mono text-sm">{{ env('MOODLE_URL') ?: 'NOT CONFIGURED' }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Token Status:</p>
                    <p class="font-mono text-sm">{{ env('MOODLE_TOKEN') ? '✅ Configured' : '❌ NOT CONFIGURED' }}</p>
                </div>
            </div>

            <button onclick="testConnection()" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Test Connection
            </button>
            <div id="connection-result" class="mt-4"></div>
        </div>

        <!-- Quick User Sync Test -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">👤 Test User Creation</h2>
            
            <form id="test-user-form" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" id="firstname" placeholder="First Name" value="Test" 
                           class="border rounded px-3 py-2">
                    <input type="text" id="lastname" placeholder="Last Name" value="User{{ rand(100, 999) }}" 
                           class="border rounded px-3 py-2">
                </div>
                <input type="email" id="email" placeholder="Email" value="test{{ rand(1000, 9999) }}@example.com" 
                       class="w-full border rounded px-3 py-2">
                <input type="password" id="password" placeholder="Password" value="Test@123456" 
                       class="w-full border rounded px-3 py-2">
                
                <button type="button" onclick="createTestUser()" 
                        class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    Create Test User in Both Systems
                </button>
            </form>
            <div id="user-result" class="mt-4"></div>
        </div>

        <!-- Recent Users Status -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">📊 Recent User Registrations</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laravel ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Moodle ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sync Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(\App\Models\User::latest()->take(10)->get() as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $user->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($user->moodle_user_id)
                                    <span class="text-green-600 font-bold">{{ $user->moodle_user_id }}</span>
                                @else
                                    <span class="text-red-600">Not Synced</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->moodle_user_id)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Synced
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        ❌ Not Synced
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if(!$user->moodle_user_id)
                                    <button onclick="syncUser({{ $user->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        Sync Now
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Logs Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">📝 Recent Moodle Logs</h2>
            <button onclick="loadLogs()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 mb-4">
                Refresh Logs
            </button>
            <div id="logs-container" class="bg-black text-green-400 p-4 rounded font-mono text-xs overflow-auto max-h-96">
                Click "Refresh Logs" to load recent Moodle-related logs...
            </div>
        </div>
    </div>

    <script>
        function testConnection() {
            const resultDiv = document.getElementById('connection-result');
            resultDiv.innerHTML = '<div class="text-blue-600">Testing connection...</div>';
            
            fetch('/admin/test-moodle-connection')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        resultDiv.innerHTML = `
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                ✅ ${data.message}<br>
                                <small>URL: ${data.moodle_url}</small>
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = `
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                ❌ ${data.message}<br>
                                <small>${data.error || ''}</small>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            ❌ Error: ${error.message}
                        </div>
                    `;
                });
        }

        function createTestUser() {
            const resultDiv = document.getElementById('user-result');
            const formData = {
                firstname: document.getElementById('firstname').value,
                lastname: document.getElementById('lastname').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };
            
            resultDiv.innerHTML = '<div class="text-blue-600">Creating test user...</div>';
            
            fetch('/admin/test-create-user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resultDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            ✅ User created successfully!<br>
                            Laravel ID: ${data.laravel_id}<br>
                            Moodle ID: ${data.moodle_id || 'Failed to sync'}<br>
                            Email: ${data.email}
                        </div>
                    `;
                    setTimeout(() => location.reload(), 3000);
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            ❌ Error: ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        ❌ Error: ${error.message}
                    </div>
                `;
            });
        }

        function syncUser(userId) {
            if (!confirm('Sync this user to Moodle?')) return;
            
            fetch(`/admin/sync-user/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload();
                }
            });
        }

        function loadLogs() {
            const logsDiv = document.getElementById('logs-container');
            logsDiv.innerHTML = 'Loading logs...';
            
            fetch('/admin/moodle-logs')
                .then(response => response.json())
                .then(data => {
                    logsDiv.innerHTML = data.logs || 'No recent Moodle logs found.';
                })
                .catch(error => {
                    logsDiv.innerHTML = 'Error loading logs: ' + error.message;
                });
        }
    </script>
</x-layouts>