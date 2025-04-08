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

        <h2 class="text-3xl font-bold mb-8 text-gray-800">All Users</h2>

        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($users as $user)
                <li class="py-3 sm:py-4">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <!-- User Avatar -->
                        <div class="shrink-0">
                            <img class="w-8 h-8 rounded-full" src="{{ $user->profile_photo_url ?? 'https://via.placeholder.com/32' }}" alt="{{ $user->first_name }} {{ $user->last_name }}">
                        </div>
                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                {{ $user->email }}
                            </p>
                        </div>
                        <!-- Current Role Display -->
                        <div class="inline-flex items-center text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $user->getRoleNames()->implode(', ') }}
                        </div>
                        <!-- Role Update Form -->
                        <div class="ml-4">
                            <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="flex items-center space-x-1">
                                @csrf
                                <select name="role" class="border rounded px-2 py-1 text-sm">
                                    <option value="user" {{ $user->hasRole('user') ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                                    <option value="superadmin" {{ $user->hasRole('superadmin') ? 'selected' : '' }}>Superadmin</option>
                                </select>
                                <button type="submit" class="bg-blue-600 text-white text-sm px-2 py-1 rounded">
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts>
