<x-layouts>
    <x-slot:heading>
        🔍 Live System Activity Log
    </x-slot:heading>

    <div class="max-w-7xl mx-auto p-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_today'] }}</div>
                <div class="text-gray-600 text-sm">Activities Today</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-green-600">{{ $stats['unique_users_today'] }}</div>
                <div class="text-gray-600 text-sm">Active Users Today</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-red-600">{{ $stats['failed_today'] }}</div>
                <div class="text-gray-600 text-sm">Failed Actions</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['critical_events'] }}</div>
                <div class="text-gray-600 text-sm">Critical Events</div>
            </div>
        </div>

        {{-- Live Status Indicator --}}
        <div class="bg-white rounded-lg shadow mb-4 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span id="live-indicator" class="relative flex h-3 w-3 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Live Updates Active</span>
                </div>
                <div class="flex space-x-2">
                    <button onclick="toggleLiveUpdates()" id="toggle-live" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                        Pause Live Updates
                    </button>
                    <button onclick="exportLogs()" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                        Export CSV
                    </button>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form id="filter-form" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <select name="user_id" class="border rounded px-3 py-2">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endforeach
                </select>
                
                <input type="text" name="action" placeholder="Filter by action..." class="border rounded px-3 py-2">
                
                <input type="date" name="date_from" class="border rounded px-3 py-2">
                
                <input type="date" name="date_to" class="border rounded px-3 py-2">
                
                <select name="severity" class="border rounded px-3 py-2">
                    <option value="">All Severities</option>
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="error">Error</option>
                    <option value="critical">Critical</option>
                </select>
                
                <button type="submit" class="bg-blue-500 text-white rounded px-4 py-2 hover:bg-blue-600">
                    Apply Filters
                </button>
            </form>
        </div>

        {{-- Activity Log Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address
                            </th>
                        </tr>
                    </thead>
                    <tbody id="logs-tbody" class="bg-white divide-y divide-gray-200">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 transition-colors log-row" data-id="{{ $log->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div>{{ $log->created_at->format('H:i:s') }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $log->user_name ?? 'System' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="text-lg mr-1">{{ $log->action_icon }}</span>
                                    <span class="text-gray-700">{{ $log->formatted_action }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($log->description, 80) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        bg-{{ $log->status_color }}-100 text-{{ $log->status_color }}-800">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                    @if($log->severity !== 'info')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            bg-{{ $log->severity_color }}-100 text-{{ $log->severity_color }}-800 ml-1">
                                            {{ ucfirst($log->severity) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->ip_address }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- JavaScript for Live Updates --}}
    <script>
        let liveUpdatesEnabled = true;
        let lastLogId = {{ $logs->first()?->id ?? 0 }};
        let updateInterval;

        function startLiveUpdates() {
            updateInterval = setInterval(fetchNewLogs, 5000); // Check every 5 seconds
        }

        function stopLiveUpdates() {
            clearInterval(updateInterval);
        }

        function toggleLiveUpdates() {
            liveUpdatesEnabled = !liveUpdatesEnabled;
            const button = document.getElementById('toggle-live');
            const indicator = document.getElementById('live-indicator');
            
            if (liveUpdatesEnabled) {
                startLiveUpdates();
                button.textContent = 'Pause Live Updates';
                button.classList.remove('bg-gray-500');
                button.classList.add('bg-blue-500');
                indicator.querySelector('.bg-green-500').classList.remove('bg-red-500');
                indicator.querySelector('.bg-green-500').classList.add('bg-green-500');
            } else {
                stopLiveUpdates();
                button.textContent = 'Resume Live Updates';
                button.classList.remove('bg-blue-500');
                button.classList.add('bg-gray-500');
                indicator.querySelector('.bg-green-500').classList.remove('bg-green-500');
                indicator.querySelector('.bg-green-500').classList.add('bg-red-500');
            }
        }

        async function fetchNewLogs() {
            try {
                const response = await fetch(`/admin/activity-logs/live?last_id=${lastLogId}`);
                const data = await response.json();
                
                if (data.logs && data.logs.length > 0) {
                    prependNewLogs(data.logs);
                    lastLogId = data.logs[0].id;
                    
                    // Show notification for critical events
                    data.logs.forEach(log => {
                        if (log.severity === 'critical' || log.status === 'failed') {
                            showNotification(log);
                        }
                    });
                }
            } catch (error) {
                console.error('Failed to fetch new logs:', error);
            }
        }

        function prependNewLogs(logs) {
            const tbody = document.getElementById('logs-tbody');
            
            logs.forEach(log => {
                const existingRow = document.querySelector(`tr[data-id="${log.id}"]`);
                if (existingRow) return; // Skip if already exists
                
                const row = createLogRow(log);
                tbody.insertBefore(row, tbody.firstChild);
                
                // Highlight new row
                row.classList.add('bg-yellow-50');
                setTimeout(() => row.classList.remove('bg-yellow-50'), 3000);
                
                // Remove oldest row if we have too many
                if (tbody.children.length > 50) {
                    tbody.removeChild(tbody.lastChild);
                }
            });
        }

        function createLogRow(log) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors log-row';
            tr.dataset.id = log.id;
            
            const statusClass = `bg-${log.status_color}-100 text-${log.status_color}-800`;
            const severityClass = log.severity !== 'info' ? 
                `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-${log.severity_color}-100 text-${log.severity_color}-800 ml-1">${log.severity}</span>` : '';
            
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    <div>${log.time}</div>
                    <div class="text-xs text-gray-400">${log.timestamp}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${log.user}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="text-lg mr-1">${log.icon}</span>
                    <span class="text-gray-700">${log.action}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    ${log.description}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                        ${log.status}
                    </span>
                    ${severityClass}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${log.ip || 'N/A'}
                </td>
            `;
            
            return tr;
        }

        function showNotification(log) {
            // You can use browser notifications or a toast library
            if (Notification.permission === 'granted') {
                new Notification('System Alert', {
                    body: `${log.severity.toUpperCase()}: ${log.description}`,
                    icon: '/favicon.ico'
                });
            }
        }

        function exportLogs() {
            const form = document.getElementById('filter-form');
            const params = new URLSearchParams(new FormData(form));
            window.location.href = `/admin/activity-logs/export?${params.toString()}`;
        }

        // Request notification permission
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Start live updates when page loads
        document.addEventListener('DOMContentLoaded', () => {
            if (liveUpdatesEnabled) {
                startLiveUpdates();
            }
        });

        // Stop updates when page unloads
        window.addEventListener('beforeunload', () => {
            stopLiveUpdates();
        });
    </script>
</x-layouts>