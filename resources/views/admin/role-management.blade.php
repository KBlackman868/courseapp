{{-- resources/views/admin/role-management.blade.php --}}
<x-layouts>
    <x-slot:heading>
        Role Management
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Role Assignment</h2>
            
            {{-- Bulk Role Assignment --}}
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <h3 class="font-semibold mb-3">Bulk Role Assignment</h3>
                <form id="bulkRoleForm" action="{{ route('admin.roles.bulkAssign') }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    <select name="role" class="border rounded px-3 py-2">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="bulkAssignRoles()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Assign to Selected Users
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assign Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if($user->id !== auth()->id() && (!$user->hasRole('superadmin') || auth()->user()->hasRole('superadmin')))
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded">
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-1 text-xs font-medium bg-{{ $role->name === 'superadmin' ? 'purple' : ($role->name === 'admin' ? 'blue' : 'gray') }}-100 text-{{ $role->name === 'superadmin' ? 'purple' : ($role->name === 'admin' ? 'blue' : 'gray') }}-800 rounded">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">
                                @if($user->id !== auth()->id() && (!$user->hasRole('superadmin') || auth()->user()->hasRole('superadmin')))
                                    <form action="{{ route('admin.roles.assign', $user) }}" method="POST" class="flex items-center space-x-2">
                                        @csrf
                                        <select name="role" class="text-sm border rounded px-2 py-1">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="bg-indigo-600 text-white text-xs px-3 py-1 rounded hover:bg-indigo-700">
                                            Update
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500">
                                        @if($user->id === auth()->id())
                                            Current User
                                        @else
                                            Protected
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        function bulkAssignRoles() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one user.');
                return;
            }

            const form = document.getElementById('bulkRoleForm');
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            form.submit();
        }
    </script>
</x-layouts>