<x-layouts>
    <x-slot:heading>
        Edit Course: {{ $course->title }}
    </x-slot:heading>

    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('courses.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Courses
            </a>
        </div>

        <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data"
              class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            {{-- Display success/error messages --}}
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Current Image Preview -->
            @if($course->image)
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Current Image:</label>
                    <div class="relative inline-block">
                        <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}"
                             class="h-32 w-48 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                    </div>
                </div>
            @endif

            <!-- Course Title -->
            <div class="mb-4">
                <label for="title" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Course Title:</label>
                <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror"
                       required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Description -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Course Description:</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                          required>{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Status -->
            <div class="mb-4">
                <label for="status" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Status:</label>
                <select name="status" id="status"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror"
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
                <label for="image" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    {{ $course->image ? 'Replace Course Image:' : 'Course Image:' }}
                </label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('image') border-red-500 @enderror">
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Leave empty to keep current image</p>
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Access Control Section --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4">Access Control</h3>

                <!-- Audience Type -->
                <div class="mb-4">
                    <label for="audience_type" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Target Audience:</label>
                    <select name="audience_type" id="audience_type"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('audience_type') border-red-500 @enderror">
                        <option value="moh" {{ old('audience_type', $course->audience_type ?? 'moh') == 'moh' ? 'selected' : '' }}>MOH Staff Only</option>
                        <option value="external" {{ old('audience_type', $course->audience_type) == 'external' ? 'selected' : '' }}>External Users Only</option>
                        <option value="all" {{ old('audience_type', $course->audience_type) == 'all' ? 'selected' : '' }}>All Users</option>
                    </select>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Who can view and enroll in this course</p>
                    @error('audience_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Enrollment Type -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Enrollment Type:</label>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="is_free" value="1"
                                   class="mr-2 w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                                   {{ old('is_free', $course->is_free) == '1' ? 'checked' : '' }}>
                            <span class="text-gray-700 dark:text-gray-300">
                                <span class="font-medium">Open Enrollment</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">- Users can enroll directly without approval</span>
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="is_free" value="0"
                                   class="mr-2 w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                                   {{ old('is_free', $course->is_free) == '0' ? 'checked' : '' }}>
                            <span class="text-gray-700 dark:text-gray-300">
                                <span class="font-medium">Requires Approval</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">- Users must request access and wait for admin approval</span>
                            </span>
                        </label>
                    </div>
                    @error('is_free')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Moodle Integration Section --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-6">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4">Moodle LMS Integration</h3>

                @if($course->moodle_course_id)
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-green-700 dark:text-green-300 font-medium">Synced to Moodle</span>
                        </div>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">Moodle Course ID: {{ $course->moodle_course_id }}</p>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="sync_to_moodle" id="sync_to_moodle" value="1"
                               class="mr-2 w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500"
                               {{ old('sync_to_moodle', $course->moodle_course_id ? '1' : '') ? 'checked' : '' }}
                               onchange="toggleMoodleFields()">
                        <span class="text-gray-700 dark:text-gray-300">
                            {{ $course->moodle_course_id ? 'Update Moodle course on save' : 'Sync this course to Moodle LMS' }}
                        </span>
                    </label>
                </div>

                <div id="moodle-fields" class="space-y-4" style="{{ $course->moodle_course_id || old('sync_to_moodle') ? '' : 'display: none;' }}">
                    <!-- Moodle Short Name -->
                    <div>
                        <label for="moodle_course_shortname" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                            Moodle Short Name: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="moodle_course_shortname" id="moodle_course_shortname"
                               value="{{ old('moodle_course_shortname', $course->moodle_course_shortname) }}"
                               placeholder="e.g., CS101_2025"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('moodle_course_shortname') border-red-500 @enderror">
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Unique identifier for Moodle (no spaces)</p>
                        @error('moodle_course_shortname')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Moodle Category -->
                    <div>
                        <label for="moodle_category_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                            Moodle Category: <span class="text-red-500">*</span>
                        </label>
                        <select name="moodle_category_id" id="moodle_category_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Category</option>
                            <option value="10" {{ old('moodle_category_id', $course->moodle_category_id) == '10' ? 'selected' : '' }}>LMS Support</option>
                            <option value="14" {{ old('moodle_category_id', $course->moodle_category_id) == '14' ? 'selected' : '' }}>Sandboxes</option>
                            <option value="27" {{ old('moodle_category_id', $course->moodle_category_id) == '27' ? 'selected' : '' }}>Office Productivity</option>
                            <option value="2" {{ old('moodle_category_id', $course->moodle_category_id) == '2' ? 'selected' : '' }}>HIV Related Training</option>
                            <option value="23" {{ old('moodle_category_id', $course->moodle_category_id) == '23' ? 'selected' : '' }}>HIV Testing</option>
                            <option value="24" {{ old('moodle_category_id', $course->moodle_category_id) == '24' ? 'selected' : '' }}>HCW Continuing Education</option>
                            <option value="22" {{ old('moodle_category_id', $course->moodle_category_id) == '22' ? 'selected' : '' }}>Infection Prevention and Control (IPC)</option>
                            <option value="26" {{ old('moodle_category_id', $course->moodle_category_id) == '26' ? 'selected' : '' }}>Monitoring and Evaluation Support Training</option>
                            <option value="25" {{ old('moodle_category_id', $course->moodle_category_id) == '25' ? 'selected' : '' }}>Job Aids, Manuals, and SOPs</option>
                            <option value="18" {{ old('moodle_category_id', $course->moodle_category_id) == '18' ? 'selected' : '' }}>Capacity Building</option>
                        </select>
                        @error('moodle_category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('courses.index') }}"
                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                    Update Course
                </button>
            </div>
        </form>

        <!-- Danger Zone -->
        <div class="mt-8 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
            <h3 class="text-lg font-bold text-red-800 dark:text-red-200 mb-2">Danger Zone</h3>
            <p class="text-sm text-red-600 dark:text-red-400 mb-4">
                Deleting a course is permanent and cannot be undone. All enrollments will also be removed.
            </p>
            <form action="{{ route('courses.destroy', $course->id) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                    Delete Course
                </button>
            </form>
        </div>
    </div>

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
            toggleMoodleFields();
        });
    </script>
</x-layouts>
