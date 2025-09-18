{{-- resources/views/admin/moodle_sync_status.blade.php --}}
<x-layouts>
    <x-slot:heading>
        Moodle Sync Status
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold mb-6">User Sync Status</h2>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Local ID</th>
                    <th class="px-4 py-2">Moodle ID</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="px-4 py-2">{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ $user->id }}</td>
                        <td class="px-4 py-2">
                            @if($user->moodle_user_id)
                                <span class="text-green-600">{{ $user->moodle_user_id }}</span>
                            @else
                                <span class="text-red-600">Not synced</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($user->moodle_user_id)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Synced</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if(!$user->moodle_user_id)
                                <form action="{{ route('admin.moodle.sync-user', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        Sync to Moodle
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts>