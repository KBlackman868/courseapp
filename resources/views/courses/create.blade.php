<x-layouts>
    <x-slot:heading>
        Create New Course
    </x-slot:heading>

    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="max-w-xl mx-auto p-6 bg-white shadow-md rounded-lg">
        @csrf
        <!-- Course Title -->
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Course Title:</label>
            <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
        </div>

        <!-- Course Description -->
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Course Description:</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required></textarea>
        </div>

        <!-- Course Status -->
        <div class="mb-4">
            <label for="status" class="block text-gray-700 font-bold mb-2">Status:</label>
            <input type="text" name="status" id="status" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
        </div>

        <!-- Course Image Upload -->
        <div class="mb-4">
            <label for="image" class="block text-gray-700 font-bold mb-2">Course Image:</label>
            <input type="file" name="image" id="image" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-200">
                Create Course
            </button>
        </div>
    </form>
</x-layouts>
