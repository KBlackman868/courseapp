<x-layouts>
  <x-slot:heading>Your Profile</x-slot:heading>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    {{-- Avatar + Basic Info --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <img src="{{ $user->profile_photo 
                    ? asset('storage/'.$user->profile_photo) 
                    : asset('images/moh_logo.jpg') }}"
           class="w-32 h-32 rounded-full mx-auto" alt="Avatar">
      <h2 class="mt-4 text-center text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ $user->first_name }} {{ $user->last_name }}
      </h2>
      <p class="text-center text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
    </div>

    {{-- Enrolled Courses --}}
    <div class="md:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">My Courses</h3>
      @if($enrollments->isEmpty())
        <p class="text-gray-600 dark:text-gray-300">You have no approved courses yet.</p>
      @else
        <ul class="space-y-2">
          @foreach($enrollments as $enroll)
            <li class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded">
              <span class="font-medium text-gray-900 dark:text-gray-100">
                {{ $enroll->course->title }}
              </span>
              <span class="text-sm text-green-600 dark:text-green-400 uppercase">{{ $enroll->status }}</span>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</x-layouts>
