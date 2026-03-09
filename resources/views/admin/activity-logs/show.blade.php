<x-layouts>
    <x-slot:heading>
        Activity Log Details
    </x-slot:heading>

    <div class="max-w-4xl mx-auto p-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-ghost btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Activity Logs
            </a>
        </div>

        <!-- Log Entry Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">{{ $log->action_icon }}</span>
                        <div>
                            <h2 class="text-lg font-bold text-white">{{ $log->formatted_action }}</h2>
                            <p class="text-blue-100 text-sm">Log Entry #{{ $log->id }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-{{ $log->status_color }}-100 text-{{ $log->status_color }}-800">
                            {{ ucfirst($log->status) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-{{ $log->severity_color }}-100 text-{{ $log->severity_color }}-800">
                            {{ ucfirst($log->severity) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                        <p class="text-gray-900">{{ $log->description }}</p>
                    </div>

                    <!-- User -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">User</label>
                        <p class="text-gray-900 font-medium">{{ $log->user_name ?? 'System' }}</p>
                        @if($log->user_email)
                            <p class="text-gray-500 text-sm">{{ $log->user_email }}</p>
                        @endif
                    </div>

                    <!-- Timestamp -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date & Time</label>
                        <p class="text-gray-900">{{ $log->created_at->format('F j, Y') }}</p>
                        <p class="text-gray-500 text-sm">{{ $log->created_at->format('g:i:s A') }} ({{ $log->created_at->diffForHumans() }})</p>
                    </div>

                    <!-- Action -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Action</label>
                        <p class="text-gray-900">{{ $log->action }}</p>
                    </div>

                    <!-- IP Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">IP Address</label>
                        <p class="text-gray-900">{{ $log->ip_address ?? 'N/A' }}</p>
                    </div>

                    <!-- HTTP Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">HTTP Method</label>
                        <p class="text-gray-900">{{ $log->method ?? 'N/A' }}</p>
                    </div>

                    <!-- URL -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">URL</label>
                        <p class="text-gray-900 break-all text-sm">{{ $log->url ?? 'N/A' }}</p>
                    </div>

                    <!-- User Agent -->
                    @if($log->user_agent)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">User Agent</label>
                        <p class="text-gray-700 text-sm break-all bg-gray-50 rounded p-2">{{ $log->user_agent }}</p>
                    </div>
                    @endif

                    <!-- Related Model -->
                    @if($log->model_type)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Related Model</label>
                        <p class="text-gray-900">{{ class_basename($log->model_type) }}</p>
                        @if($log->model_id)
                            <p class="text-gray-500 text-sm">ID: {{ $log->model_id }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Properties / Additional Data -->
                @if($log->properties && count($log->properties) > 0)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Additional Properties</label>
                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Key</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($log->properties as $key => $value)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-700">{{ $key }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 break-all">
                                        @if(is_array($value) || is_object($value))
                                            <pre class="text-xs bg-white rounded p-2 overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts>
