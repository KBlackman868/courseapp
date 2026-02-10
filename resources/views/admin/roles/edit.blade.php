<x-layouts>
    <x-slot:heading>
        Edit Role: {{ $role->display_name ?? $role->name }}
    </x-slot:heading>

    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="text-2xl font-bold text-gray-800">
                Edit Role: {{ $role->display_name ?? $role->name }}
            </h2>
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
                Back to Roles
            </a>
        </div>

        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Role Details -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Role Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role Name (System Name)</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" value="{{ $role->name }}" readonly>
                            <p class="mt-1 text-xs text-gray-500">System name cannot be changed</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Display Name <span class="text-red-500">*</span></label>
                            <input type="text" name="display_name"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('display_name') border-red-500 @else border-gray-300 @enderror"
                                   value="{{ old('display_name', $role->display_name) }}" required>
                            @error('display_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" rows="2">{{ old('description', $role->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Permissions Selection -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Manage Permissions</h3>
                    <span class="px-3 py-1 text-sm font-medium bg-indigo-100 text-indigo-800 rounded-full">
                        <span id="selectedCount">{{ count($rolePermissions) }}</span> permissions selected
                    </span>
                </div>
                <div class="p-6">
                    @foreach($permissions as $category => $categoryPermissions)
                        <div class="mb-6">
                            <h5 class="text-sm font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2 mb-3 flex items-center justify-between">
                                <span>
                                    <svg class="w-4 h-4 inline-block mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $category)) }}
                                </span>
                                <button type="button" class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors"
                                        onclick="toggleCategory('{{ $category }}')">
                                    Toggle All
                                </button>
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($categoryPermissions as $permission)
                                    <label class="flex items-start gap-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                        <input type="checkbox"
                                               class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 permission-checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               data-category="{{ $category }}"
                                               {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $permission->name }}</span>
                                            @if($permission->description)
                                                <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        Update Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
    function toggleCategory(category) {
        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-category="${category}"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        updateCount();
    }

    function updateCount() {
        const count = document.querySelectorAll('.permission-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });
    });
    </script>
</x-layouts>
