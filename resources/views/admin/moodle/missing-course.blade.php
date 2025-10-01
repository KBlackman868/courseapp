<x-layouts>
    <x-slot:heading>
        Missing Moodle Courses
    </x-slot:heading>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_moodle'] }}</div>
                <div class="text-sm text-gray-500">Total in Moodle</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_local'] }}</div>
                <div class="text-sm text-gray-500">Total Local</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-green-600">{{ $stats['synced'] }}</div>
                <div class="text-sm text-gray-500">Synced</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-red-600">{{ $stats['missing'] }}</div>
                <div class="text-sm text-gray-500">Missing</div>
            </div>
        </div>

        <!-- Missing Courses Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Courses in Moodle but not in Local Database</h3>
                <p class="text-sm text-gray-500 mt-1">These courses exist in Moodle but haven't been imported to your local system yet.</p>
            </div>
            
            @if(count($missingCourses) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" class="rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moodle ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Short Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($missingCourses as $course)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="course_ids[]" value="{{ $course['moodle_id'] }}" class="course-checkbox rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $course['moodle_id'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $course['shortname'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $course['fullname'] }}">
                                            {{ $course['fullname'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Cat {{ $course['category'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($course['visible'])
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Visible
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Hidden
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $course['enrolled_count'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="importSingleCourse({{ $course['moodle_id'] }})" 
                                                class="text-indigo-600 hover:text-indigo-900">
                                            Import
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Bulk Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <button onclick="importSelected()" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mr-2">
                                Import Selected
                            </button>
                            <button onclick="importAll()" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                Import All Missing
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ count($missingCourses) }} courses found
                        </div>
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">All Synced!</h3>
                    <p class="mt-1 text-sm text-gray-500">All Moodle courses have been imported to your local database.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Select all functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.course-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        function importSingleCourse(moodleId) {
            if (confirm('Import this course from Moodle?')) {
                // Implementation for single course import
                window.location.href = `/admin/moodle/courses/import-single/${moodleId}`;
            }
        }

        function importSelected() {
            const selected = document.querySelectorAll('.course-checkbox:checked');
            if (selected.length === 0) {
                alert('Please select at least one course to import');
                return;
            }
            
            if (confirm(`Import ${selected.length} selected course(s)?`)) {
                // Implementation for bulk import
                const ids = Array.from(selected).map(cb => cb.value);
                // Submit form with selected IDs
            }
        }

        function importAll() {
            if (confirm('Import all missing courses? This may take some time.')) {
                // Implementation for importing all
                window.location.href = '{{ route("admin.moodle.courses.sync") }}';
            }
        }
    </script>
</x-layouts>