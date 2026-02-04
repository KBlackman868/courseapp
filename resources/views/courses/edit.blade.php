<x-layouts>
    <x-slot:heading>
        Edit Course
    </x-slot:heading>

    <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data"
          class="max-w-xl mx-auto p-6 bg-white shadow-md rounded-lg">
        @csrf
        @method('PUT')

        {{-- Display success/error messages --}}
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Course Title -->
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Course Title:</label>
            <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}"
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
                      required>{{ old('description', $course->description) }}</textarea>
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
                <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Course Image Upload -->
        <div class="mb-4">
            <label for="image" class="block text-gray-700 font-bold mb-2">Course Image:</label>
            @if($course->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}"
                         class="w-32 h-32 object-cover rounded border">
                    <p class="text-gray-500 text-sm mt-1">Current image. Upload a new one to replace it.</p>
                </div>
            @endif
            <input type="file" name="image" id="image" accept="image/*"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 @error('image') border-red-500 @enderror">
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Access Control Section --}}
        <div class="border-t pt-4 mt-4">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Access Control</h3>

            <!-- Audience Type -->
            <div class="mb-4">
                <label for="audience_type" class="block text-gray-700 font-bold mb-2">Target Audience:</label>
                @php
                    $currentAudience = old('audience_type', $course->audience_type);
                    // Normalize legacy values for the dropdown
                    $audienceMap = [
                        'MOH_ONLY' => 'moh', 'EXTERNAL_ONLY' => 'external', 'BOTH' => 'all',
                        'moh' => 'moh', 'external' => 'external', 'all' => 'all',
                    ];
                    $normalizedAudience = $audienceMap[$currentAudience] ?? 'moh';
                @endphp
                <select name="audience_type" id="audience_type"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 @error('audience_type') border-red-500 @enderror">
                    <option value="moh" {{ $normalizedAudience == 'moh' ? 'selected' : '' }}>MOH Staff Only</option>
                    <option value="external" {{ $normalizedAudience == 'external' ? 'selected' : '' }}>External Users Only</option>
                    <option value="all" {{ $normalizedAudience == 'all' ? 'selected' : '' }}>All Users</option>
                </select>
                <p class="text-gray-500 text-sm mt-1">Who can view and enroll in this course</p>
                @error('audience_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Enrollment Type -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Enrollment Type:</label>
                @php
                    $isFree = old('is_free', $course->is_free) ? '1' : '0';
                @endphp
                <div class="space-y-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="is_free" value="1"
                               class="mr-2 w-4 h-4 text-blue-600 focus:ring-blue-500"
                               {{ $isFree == '1' ? 'checked' : '' }}>
                        <span class="text-gray-700">
                            <span class="font-medium">Open Enrollment</span>
                            <span class="text-sm text-gray-500 ml-2">- Users can enroll directly without approval</span>
                        </span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="is_free" value="0"
                               class="mr-2 w-4 h-4 text-blue-600 focus:ring-blue-500"
                               {{ $isFree == '0' ? 'checked' : '' }}>
                        <span class="text-gray-700">
                            <span class="font-medium">Requires Approval</span>
                            <span class="text-sm text-gray-500 ml-2">- Users must request access and wait for admin approval</span>
                        </span>
                    </label>
                </div>
                @error('is_free')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Moodle Integration Section --}}
        <div class="border-t pt-4 mt-6">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Moodle LMS Integration</h3>

            @if($course->moodle_course_id)
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded">
                    <p class="text-green-700 text-sm font-medium">
                        Synced to Moodle (Course ID: {{ $course->moodle_course_id }})
                    </p>
                </div>
            @endif

            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="sync_to_moodle" id="sync_to_moodle" value="1"
                           class="mr-2 w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                           {{ old('sync_to_moodle', $course->moodle_course_id ? true : false) ? 'checked' : '' }}
                           onchange="toggleMoodleFields()">
                    <span class="text-gray-700">
                        {{ $course->moodle_course_id ? 'Update Moodle course on save' : 'Sync this course to Moodle LMS' }}
                    </span>
                </label>
            </div>

            <div id="moodle-fields" class="space-y-4" style="{{ $course->moodle_course_id ? '' : 'display: none;' }}">
                <!-- Moodle Short Name -->
                <div>
                    <label for="moodle_course_shortname" class="block text-gray-700 font-bold mb-2">
                        Moodle Short Name: <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="moodle_course_shortname" id="moodle_course_shortname"
                           value="{{ old('moodle_course_shortname', $course->moodle_course_shortname) }}"
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
                    @php $moodleCatId = old('moodle_category_id', $course->moodle_category_id ?? ''); @endphp
                    <select name="moodle_category_id" id="moodle_category_id"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                        <option value="">Select Category</option>
                        <option value="10" {{ $moodleCatId == 10 ? 'selected' : '' }}>LMS Support</option>
                        <option value="14" {{ $moodleCatId == 14 ? 'selected' : '' }}>Sandboxes</option>
                        <option value="27" {{ $moodleCatId == 27 ? 'selected' : '' }}>Office Productivity</option>
                        <option value="2" {{ $moodleCatId == 2 ? 'selected' : '' }}>HIV Related Training</option>
                        <option value="23" {{ $moodleCatId == 23 ? 'selected' : '' }}>HIV Testing</option>
                        <option value="24" {{ $moodleCatId == 24 ? 'selected' : '' }}>HCW Continuing Education</option>
                        <option value="22" {{ $moodleCatId == 22 ? 'selected' : '' }}>Infection Prevention and Control (IPC)</option>
                        <option value="26" {{ $moodleCatId == 26 ? 'selected' : '' }}>Monitoring and Evaluation Support Training</option>
                        <option value="25" {{ $moodleCatId == 25 ? 'selected' : '' }}>Job Aids, Manuals, and SOPs</option>
                        <option value="18" {{ $moodleCatId == 18 ? 'selected' : '' }}>Capacity Building</option>
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
                Update Course
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

        document.addEventListener('DOMContentLoaded', function() {
            toggleMoodleFields();
        });
    </script>
</x-layouts>
