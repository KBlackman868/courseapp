<x-layouts>
    <x-slot:heading>
        Permissions for: {{ $role->display_name ?? $role->name }}
    </x-slot:heading>

    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="text-2xl font-bold text-gray-800">
                Permissions for: {{ $role->display_name ?? $role->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
                    Back to Roles
                </a>
                @if($role->name !== 'superadmin')
                    <a href="{{ route('admin.roles.edit', $role) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                        Edit Permissions
                    </a>
                @endif
            </div>
        </div>

        @if($role->name === 'superadmin')
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-blue-700">The Superadmin role has all permissions by default and cannot be modified.</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Assigned Permissions</h3>
                <span class="px-3 py-1 text-sm font-medium bg-indigo-100 text-indigo-800 rounded-full">
                    {{ $role->name === 'superadmin' ? 'All' : $role->permissions->count() }} permissions
                </span>
            </div>
            <div class="p-6">
                @if($role->name === 'superadmin')
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-yellow-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-800">Full System Access</h4>
                        <p class="text-gray-500 mt-1">This role has unrestricted access to all system features.</p>
                    </div>
                @elseif($permissions->isEmpty())
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-800">No Permissions Assigned</h4>
                        <p class="text-gray-500 mt-1">This role currently has no permissions.</p>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                            Add Permissions
                        </a>
                    </div>
                @else
                    @foreach($permissions as $category => $categoryPermissions)
                        <div class="mb-6">
                            <h5 class="text-sm font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2 mb-3 flex items-center justify-between">
                                <span>
                                    <svg class="w-4 h-4 inline-block mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $category)) }}
                                </span>
                                <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                    {{ $categoryPermissions->count() }}
                                </span>
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($categoryPermissions as $permission)
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $permission->name }}</span>
                                            @if($permission->description)
                                                <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Users with this role --}}
        <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Users with this Role</h3>
            </div>
            <div class="p-6">
                @php
                    $usersWithRole = \App\Models\User::role($role->name)->get();
                @endphp

                @if($usersWithRole->isEmpty())
                    <p class="text-gray-500 text-center py-4">No users have been assigned this role yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($usersWithRole as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->department ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts>
