<x-layouts>
    <x-slot:heading>
        Create New Course
    </x-slot:heading>

    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" 
          class="max-w-xl mx-auto p-6 bg-white shadow-md rounded-lg">
        @csrf
        
        {{-- Display success/error messages --}}
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Course Title -->
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Course Title:</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 @error('title') border-red-500 @enderror" 
                   required>
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Course Description -->
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Course Description:</label>
            <textarea name="description" id="description" rows="4" 
                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 @error('description') border-red-500 @enderror" 
                      required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Course Status -->
        <div class="mb-4">
            <label for="status" class="block text-gray-700 font-bold mb-2">Status:</label>
            <select name="status" id="status" 
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 @error('status') border-red-500 @enderror" 
                    required>
                <option value="">Select Status</option>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Course Image Upload -->
        <div class="mb-4">
            <label for="image" class="block text-gray-700 font-bold mb-2">Course Image:</label>
            <input type="file" name="image" id="image" accept="image/*"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 @error('image') border-red-500 @enderror">
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Moodle Integration Section --}}
        <div class="border-t pt-4 mt-6">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Moodle LMS Integration (Optional)</h3>
            
            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="sync_to_moodle" id="sync_to_moodle" value="1" 
                           class="mr-2 w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                           {{ old('sync_to_moodle') ? 'checked' : '' }}
                           onchange="toggleMoodleFields()">
                    <span class="text-gray-700">Sync this course to Moodle LMS</span>
                </label>
            </div>

            <div id="moodle-fields" class="space-y-4" style="display: none;">
                <!-- Moodle Short Name -->
                <div>
                    <label for="moodle_course_shortname" class="block text-gray-700 font-bold mb-2">
                        Moodle Short Name: <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="moodle_course_shortname" id="moodle_course_shortname" 
                           value="{{ old('moodle_course_shortname') }}"
                           placeholder="e.g., CS101_2025"
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 @error('moodle_course_shortname') border-red-500 @enderror">
                    <p class="text-gray-500 text-sm mt-1">Unique identifier for Moodle (no spaces)</p>
                    @error('moodle_course_shortname')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Moodle Category -->
                <div>
                    <label for="moodle_category_id" class="block text-gray-700 font-bold mb-2">
                        Moodle Category: <span class="text-red-500">*</span>
                    </label>
                    <select name="moodle_category_id" id="modal_moodle_category_id" 
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" 
                        required>
                    <option value="">Select Category</option>
                    <option value="10">LMS Support</option>
                    <option value="14">Sandboxes</option>
                    <option value="27">Office Productivity</option>
                    <option value="2">HIV Related Training</option>
                    <option value="23">HIV Testing</option>
                    <option value="24">HCW Continuing Education</option>
                    <option value="22">Infection Prevention and Control (IPC)</option>
                    <option value="26">Monitoring and Evaluation Support Training</option>
                    <option value="25">Job Aids, Manuals, and SOPs</option>
                    <option value="18">Capacity Building</option>
                </select>
                    @error('moodle_category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="text-center mt-6 space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-200">
                Create Course
            </button>
            <a href="{{ route('courses.index') }}" class="inline-block bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition duration-200">
                Cancel
            </a>
        </div>
    </form>

    <script>
        function toggleMoodleFields() {
            const checkbox = document.getElementById('sync_to_moodle');
            const moodleFields = document.getElementById('moodle-fields');
            moodleFields.style.display = checkbox.checked ? 'block' : 'none';
            
            // Toggle required attribute
            const shortname = document.getElementById('moodle_course_shortname');
            const category = document.getElementById('moodle_category_id');
            
            if (checkbox.checked) {
                shortname.setAttribute('required', 'required');
                category.setAttribute('required', 'required');
            } else {
                shortname.removeAttribute('required');
                category.removeAttribute('required');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('sync_to_moodle').checked) {
                toggleMoodleFields();
            }
        });
    </script>
</x-layouts>