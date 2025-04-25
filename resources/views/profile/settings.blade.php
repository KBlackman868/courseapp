<x-layouts>
  <x-slot:heading>Settings</x-slot:heading>

  <div class="space-y-8">
    {{-- Change Profile Photo --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Update Profile Picture</h3>
      <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="file" name="profile_photo" accept="image/*" required>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
      </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Change Password</h3>
      <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-gray-700 dark:text-gray-300">Current Password</label>
          <input type="password" name="current_password" required
                 class="w-full mt-1 px-3 py-2 border rounded focus:outline-none focus:ring">
        </div>
        <div>
          <label class="block text-gray-700 dark:text-gray-300">New Password</label>
          <input type="password" name="password" required
                 class="w-full mt-1 px-3 py-2 border rounded focus:outline-none focus:ring">
        </div>
        <div>
          <label class="block text-gray-700 dark:text-gray-300">Confirm New</label>
          <input type="password" name="password_confirmation" required
                 class="w-full mt-1 px-3 py-2 border rounded focus:outline-none focus:ring">
        </div>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Update Password
        </button>
      </form>
    </div>
  </div>
</x-layouts>
