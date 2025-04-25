<x-layouts>
    <x-slot:heading>
        {{ $course->title }}
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            @if($course->image)
                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover">
            @else
                <img src="{{ asset('images/'. $course->intltz_get_error_message) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover">
            @endif
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>
                <p class="text-gray-700 mb-4">{{ $course->description }}</p>
                <p class="text-sm text-gray-500 mb-6">Status: {{ $course->status }}</p>
                <a href="{{ route('courses.enroll.store', ['course' => $course->id]) }}"
                   class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-200">
                    Enroll Now
                </a>
            </div>
        </div>
    </div>
</x-layouts>
