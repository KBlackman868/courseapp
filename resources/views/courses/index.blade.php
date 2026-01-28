<x-layouts>
    <x-slot:heading>
        Course Management
    </x-slot:heading>

    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #0ea5e9);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slideIn {
            animation: slideIn 0.5s ease-out;
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease-out;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkbox-custom:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
            transform: scale(1.1);
        }

        .bulk-actions-bar {
            transform: translateY(-100%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .bulk-actions-bar.active {
            transform: translateY(0);
            opacity: 1;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header with Gradient -->
        <div class="animated-gradient rounded-3xl p-8 text-white mb-8 shadow-2xl animate-slideIn">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Course Management</h1>
                    <p class="text-blue-100">Manage, organize and sync your courses with Moodle LMS</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('courses.create') }}"
                       class="bg-white text-blue-600 px-6 py-3 rounded-xl font-bold hover:scale-105 transition-all flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Course
                    </a>
                    <a href="{{ route('admin.moodle.courses.import') }}"
                       class="bg-white/20 backdrop-blur border-2 border-white text-white px-6 py-3 rounded-xl font-bold hover:bg-white hover:text-blue-600 transition-all flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sync Moodle
                    </a>
                    <button onclick="showDeleteAllModal()"
                       class="bg-red-500/80 backdrop-blur border-2 border-red-400 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-600 transition-all flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete All
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 animate-fadeInUp">
            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total Courses</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Active</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-600">{{ $stats['inactive'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Inactive</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-indigo-600">{{ $stats['synced'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Synced</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['not_synced'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Not Synced</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-xl shadow-lg mb-6 animate-slideIn">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-4 rounded-xl shadow-lg mb-6 animate-slideIn">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 glass animate-fadeInUp">
            <form method="GET" action="{{ route('admin.courses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search courses..."
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                
                <div>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="all">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div>
                    <select name="sync_status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="all">All Sync Status</option>
                        <option value="synced" {{ request('sync_status') === 'synced' ? 'selected' : '' }}>Synced</option>
                        <option value="not_synced" {{ request('sync_status') === 'not_synced' ? 'selected' : '' }}>Not Synced</option>
                    </select>
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                        Filter
                    </button>
                    <a href="{{ route('admin.courses.index') }}" class="px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="bulk-actions-bar fixed bottom-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4 shadow-2xl z-40">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span class="font-semibold">
                        <span id="selectedCount">0</span> courses selected
                    </span>
                    <button onclick="selectAll()" class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-all">
                        Select All
                    </button>
                    <button onclick="deselectAll()" class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-all">
                        Deselect All
                    </button>
                </div>
                <div class="flex space-x-3">
                    <button onclick="bulkChangeStatus()" class="px-6 py-2 bg-yellow-500 text-white rounded-lg font-semibold hover:bg-yellow-600 transition-all">
                        Change Status
                    </button>
                    <button onclick="bulkDelete()" class="px-6 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-all">
                        Delete Selected
                    </button>
                    <button onclick="closeBulkActions()" class="px-6 py-2 bg-white/20 rounded-lg font-semibold hover:bg-white/30 transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-6 flex justify-end">
            <button onclick="enableBulkSelection()" 
                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Bulk Actions
            </button>
        </div>

        <!-- Courses Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-fadeInUp">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="hidden bulk-select-col px-6 py-4">
                                <input type="checkbox" id="selectAllCheckbox" class="checkbox-custom rounded-lg">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Moodle</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Enrollments</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="hidden bulk-select-col px-6 py-4">
                                    <input type="checkbox" 
                                           value="{{ $course->id }}" 
                                           class="course-checkbox checkbox-custom rounded-lg"
                                           onchange="updateSelectedCount()">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($course->image)
                                            <img src="{{ Storage::url($course->image) }}" 
                                                 class="w-12 h-12 rounded-xl object-cover mr-3">
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $course->title }}</div>
                                            <div class="text-xs text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($course->moodle_course_id)
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                ID: {{ $course->moodle_course_id }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Not Synced
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                        {{ $course->enrollments->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                               {{ $course->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $course->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('courses.show', $course->id) }}" 
                                           class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        <a href="{{ route('courses.edit', $course->id) }}" 
                                           class="p-2 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        
                                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Delete this course? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900">No courses found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new course or syncing from Moodle.</p>
                                    <div class="mt-6 flex justify-center space-x-3">
                                        <a href="{{ route('courses.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create Course
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($courses->hasPages())
                <div class="px-6 py-4 bg-gray-50">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Status Change Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden z-50">
        <div class="relative top-20 mx-auto p-5 w-full max-w-md animate-fadeInUp">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-6">
                    <h3 class="text-xl font-bold text-white">Change Course Status</h3>
                    <p class="text-yellow-100 text-sm mt-1">Update status for selected courses</p>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3">
                        <label class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 cursor-pointer">
                            <input type="radio" name="new_status" value="active" class="mr-3" checked>
                            <span class="text-sm font-medium">Active</span>
                        </label>
                        <label class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 cursor-pointer">
                            <input type="radio" name="new_status" value="inactive" class="mr-3">
                            <span class="text-sm font-medium">Inactive</span>
                        </label>
                    </div>
                    
                    <div class="mt-6 flex space-x-3">
                        <button onclick="closeStatusModal()" 
                                class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                            Cancel
                        </button>
                        <button onclick="applyStatusChange()" 
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                            Apply Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete All Confirmation Modal -->
    <div id="deleteAllModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden z-50">
        <div class="relative top-20 mx-auto p-5 w-full max-w-md animate-fadeInUp">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="text-xl font-bold text-white">Delete All Courses</h3>
                            <p class="text-red-100 text-sm mt-1">This action cannot be undone!</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <p class="text-red-800 text-sm">
                            <strong>Warning:</strong> You are about to delete <strong>ALL {{ $stats['total'] ?? 0 }} courses</strong> from the system.
                            This will also remove all associated enrollments and data. This action is permanent and cannot be reversed.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type <strong>"DELETE ALL"</strong> to confirm:
                        </label>
                        <input type="text" id="deleteConfirmInput"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                               placeholder="DELETE ALL"
                               autocomplete="off">
                    </div>

                    <div class="flex space-x-3">
                        <button onclick="closeDeleteAllModal()"
                                class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                            Cancel
                        </button>
                        <button onclick="confirmDeleteAll()"
                                id="confirmDeleteBtn"
                                class="flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-all opacity-50 cursor-not-allowed"
                                disabled>
                            Delete All Courses
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="bulkDeleteForm" action="{{ route('admin.courses.bulkDelete') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <form id="bulkStatusForm" action="{{ route('admin.courses.bulkStatus') }}" method="POST" class="hidden">
        @csrf
    </form>

    <form id="deleteAllForm" action="{{ route('admin.courses.deleteAll') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        let bulkMode = false;

        function enableBulkSelection() {
            bulkMode = true;
            document.querySelectorAll('.bulk-select-col').forEach(el => el.classList.remove('hidden'));
            document.getElementById('bulkActionsBar').classList.add('active');
            updateSelectedCount();
        }

        function closeBulkActions() {
            bulkMode = false;
            document.querySelectorAll('.bulk-select-col').forEach(el => el.classList.add('hidden'));
            document.getElementById('bulkActionsBar').classList.remove('active');
            document.querySelectorAll('.course-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.course-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        function selectAll() {
            document.querySelectorAll('.course-checkbox').forEach(cb => cb.checked = true);
            updateSelectedCount();
        }

        function deselectAll() {
            document.querySelectorAll('.course-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        }

        document.getElementById('selectAllCheckbox')?.addEventListener('change', function() {
            document.querySelectorAll('.course-checkbox').forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one course to delete.');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${checkedBoxes.length} course(s)? This action cannot be undone.`)) {
                return;
            }

            const form = document.getElementById('bulkDeleteForm');
            form.querySelectorAll('input[name="course_ids[]"]').forEach(input => input.remove());
            
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'course_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            form.submit();
        }

        function bulkChangeStatus() {
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one course to change status.');
                return;
            }
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        function applyStatusChange() {
            const status = document.querySelector('input[name="new_status"]:checked').value;
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
            
            const form = document.getElementById('bulkStatusForm');
            form.querySelectorAll('input').forEach(input => {
                if (input.name !== '_token' && input.name !== '_method') {
                    input.remove();
                }
            });
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);
            
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'course_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
            
            form.submit();
        }

        // Delete All Modal Functions
        function showDeleteAllModal() {
            document.getElementById('deleteAllModal').classList.remove('hidden');
            document.getElementById('deleteConfirmInput').value = '';
            document.getElementById('confirmDeleteBtn').disabled = true;
            document.getElementById('confirmDeleteBtn').classList.add('opacity-50', 'cursor-not-allowed');
        }

        function closeDeleteAllModal() {
            document.getElementById('deleteAllModal').classList.add('hidden');
            document.getElementById('deleteConfirmInput').value = '';
        }

        // Enable delete button only when "DELETE ALL" is typed
        document.getElementById('deleteConfirmInput')?.addEventListener('input', function() {
            const btn = document.getElementById('confirmDeleteBtn');
            if (this.value === 'DELETE ALL') {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });

        function confirmDeleteAll() {
            const input = document.getElementById('deleteConfirmInput');
            if (input.value !== 'DELETE ALL') {
                alert('Please type "DELETE ALL" to confirm.');
                return;
            }

            // Final confirmation
            if (confirm('FINAL WARNING: Are you absolutely sure you want to delete ALL courses? This cannot be undone!')) {
                document.getElementById('deleteAllForm').submit();
            }
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const statusModal = document.getElementById('statusModal');
            const deleteAllModal = document.getElementById('deleteAllModal');

            if (event.target == statusModal) {
                closeStatusModal();
            }
            if (event.target == deleteAllModal) {
                closeDeleteAllModal();
            }
        }
    </script>
</x-layouts>