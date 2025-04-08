<x-layouts>
    <x-slot:heading>
        My Courses
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <h2 class="text-3xl font-bold mb-6">My Enrolled Courses</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($enrollments as $enrollment)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <img src="{{ asset('images/' . ($enrollment->course->image ?? 'default.jpg')) }}" alt="{{ $enrollment->course->title }}" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold mb-2">{{ $enrollment->course->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($enrollment->course->description, 100) }}</p>
                        <!-- No unenroll button is provided for approved enrollments -->
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                            Enrolled
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">You are not enrolled in any courses yet.</p>
            @endforelse
        </div>
    </div>
</x-layouts>
`
