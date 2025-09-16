<x-layouts>
  <x-slot:heading>
    User Management
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

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-bold text-gray-800">All Users</h2>
      
      {{-- Bulk Actions --}}
      <div class="flex space-x-2">
        <button onclick="toggleBulkSelect()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
          Select Multiple
        </button>
        <button onclick="bulkDelete()" class="hidden bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" id="bulkDeleteBtn">
          Delete Selected
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white rounded-lg shadow">
        <thead class="bg-gray-100">
          <tr>
            <th class="hidden px-4 py-3 text-left" id="checkboxHeader">
              <input type="checkbox" id="selectAll" class="rounded">
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moodle</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach($users as $user)
            <tr class="hover:bg-gray-50">
              {{-- Bulk Select Checkbox --}}
              <td class="hidden px-4 py-3 bulk-select-col">
                @if($user->id !== auth()->id() && (!$user->hasRole('superadmin') || auth()->user()->hasRole('superadmin')))
                  <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded">
                @endif
              </td>

              {{-- User Info --}}
              <td class="px-4 py-3">
                <div class="flex items-center">
                  @if($user->profile_photo)
                    <img class="w-10 h-10 rounded-full mr-3" src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->first_name }}">
                  @else
                    <div class="w-10 h-10 rounded-full bg-gray-300 mr-3 flex items-center justify-center">
                      <span class="text-gray-600 font-semibold">{{ substr($user->first_name, 0, 1) }}</span>
                    </div>
                  @endif
                  <div>
                    <p class="font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                    <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                  </div>
                </div>
              </td>

              {{-- Email --}}
              <td class="px-4 py-3">
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
              </td>

              {{-- Department --}}
              <td class="px-4 py-3">
                <p class="text-sm text-gray-600">{{ $user->department ?? 'N/A' }}</p>
              </td>

              {{-- Role --}}
              <td class="px-4 py-3">
                @if(!$user->hasRole('superadmin'))
                  <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="flex items-center space-x-1">
                    @csrf
                    <select name="role" class="text-sm border rounded px-2 py-1">
                      <option value="user" {{ $user->hasRole('user') ? 'selected' : '' }}>User</option>
                      <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                      @if(auth()->user()->hasRole('superadmin'))
                        <option value="superadmin" {{ $user->hasRole('superadmin') ? 'selected' : '' }}>Superadmin</option>
                      @endif
                    </select>
                    <button type="submit" class="bg-blue-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700">
                      Update
                    </button>
                  </form>
                @else
                  <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">Superadmin</span>
                @endif
              </td>

              {{-- Status --}}
              <td class="px-4 py-3">
                @if($user->is_suspended)
                  <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Suspended</span>
                @else
                  <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Active</span>
                @endif
              </td>

              {{-- Moodle Status --}}
              <td class="px-4 py-3">
                @if($user->moodle_user_id)
                  <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                    Synced ({{ $user->moodle_user_id }})
                  </span>
                @else
                  <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Not Synced</span>
                @endif
              </td>

              {{-- Actions --}}
              <td class="px-4 py-3">
                <div class="flex items-center space-x-2">
                  @if($user->id !== auth()->id())
                    {{-- Suspend/Reactivate Button --}}
                    @if($user->is_suspended)
                      <form action="{{ route('admin.users.reactivate', $user->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-green-600 hover:text-green-800" title="Reactivate">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                        </button>
                      </form>
                    @else
                      <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Suspend">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                          </svg>
                        </button>
                      </form>
                    @endif

                    {{-- Delete Button --}}
                    @if(!$user->hasRole('superadmin') || auth()->user()->hasRole('superadmin'))
                      <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" 
                            onsubmit="return confirm('Are you sure you want to delete this user? This will also remove them from Moodle.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </form>
                    @endif
                  @else
                    <span class="text-xs text-gray-500">Current User</span>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $users->links() }}
    </div>
  </div>

  {{-- Bulk Delete Form --}}
  <form id="bulkDeleteForm" action="{{ route('admin.users.bulkDelete') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>

  <script>
    function toggleBulkSelect() {
      const checkboxCols = document.querySelectorAll('.bulk-select-col');
      const checkboxHeader = document.getElementById('checkboxHeader');
      const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
      
      checkboxCols.forEach(col => col.classList.toggle('hidden'));
      checkboxHeader.classList.toggle('hidden');
      bulkDeleteBtn.classList.toggle('hidden');
    }

    document.getElementById('selectAll').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.user-checkbox');
      checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    function bulkDelete() {
      const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
      if (checkedBoxes.length === 0) {
        alert('Please select at least one user to delete.');
        return;
      }

      if (!confirm(`Are you sure you want to delete ${checkedBoxes.length} user(s)? This will also remove them from Moodle.`)) {
        return;
      }

      const form = document.getElementById('bulkDeleteForm');
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