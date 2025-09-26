<x-layouts>
    <x-slot:heading>
        Moodle Course Import & Sync
    </x-slot:heading>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes pulse-border {
            0%, 100% {
                border-color: rgba(59, 130, 246, 0.5);
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }
            50% {
                border-color: rgba(59, 130, 246, 1);
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        .pulse-border-animation {
            animation: pulse-border 2s infinite;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .gradient-border {
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #667eea 0%, #764ba2 100%) border-box;
            border: 3px solid transparent;
        }

        .file-drop-zone {
            transition: all 0.3s ease;
            border: 2px dashed #cbd5e1;
            position: relative;
            overflow: hidden;
        }

        .file-drop-zone.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
            transform: scale(1.02);
        }

        .file-drop-zone::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .file-drop-zone.dragover::before {
            left: 100%;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .progress-bar {
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #3b82f6 100%);
            background-size: 200% 100%;
            animation: progress-animation 2s linear infinite;
        }

        @keyframes progress-animation {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section with Stats -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white mb-8 shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h1 class="text-4xl font-bold mb-4">Moodle Course Management</h1>
                    <p class="text-blue-100 text-lg">
                        Sync courses between your Laravel application and Moodle LMS seamlessly
                    </p>
                </div>
                <div class="flex items-center justify-end">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="glass-effect rounded-xl p-4">
                            <div class="text-3xl font-bold">{{ $stats['local_courses'] ?? 0 }}</div>
                            <div class="text-sm text-blue-100">Total Courses</div>
                        </div>
                        <div class="glass-effect rounded-xl p-4">
                            <div class="text-3xl font-bold text-green-300">{{ $stats['moodle_synced'] ?? 0 }}</div>
                            <div class="text-sm text-blue-100">Synced</div>
                        </div>
                        <div class="glass-effect rounded-xl p-4">
                            <div class="text-3xl font-bold text-yellow-300">{{ $stats['not_synced'] ?? 0 }}</div>
                            <div class="text-sm text-blue-100">Pending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-6 slide-in">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-green-800">{{ session('success') }}</p>
                        @if(session('import_results'))
                            @php $results = session('import_results'); @endphp
                            <div class="mt-2 text-sm text-green-700">
                                <span class="inline-block bg-green-200 px-2 py-1 rounded mr-2">Created: {{ $results['created'] }}</span>
                                <span class="inline-block bg-blue-200 px-2 py-1 rounded mr-2">Updated: {{ $results['updated'] }}</span>
                                <span class="inline-block bg-gray-200 px-2 py-1 rounded mr-2">Skipped: {{ $results['skipped'] }}</span>
                                @if($results['failed'] > 0)
                                    <span class="inline-block bg-red-200 px-2 py-1 rounded">Failed: {{ $results['failed'] }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6 slide-in">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold text-red-800">{{ session('error') }}</p>
                        @if(session('validation_errors'))
                            <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                                @foreach(session('validation_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <button onclick="switchTab('api')" id="api-tab" 
                            class="tab-button w-1/3 py-4 px-6 text-center border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-all duration-200 tab-active">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        API Sync
                    </button>
                    <button onclick="switchTab('upload')" id="upload-tab"
                            class="tab-button w-1/3 py-4 px-6 text-center border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-all duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        File Upload
                    </button>
                    <button onclick="switchTab('template')" id="template-tab"
                            class="tab-button w-1/3 py-4 px-6 text-center border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-all duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Template
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-8">
                <!-- API Sync Tab -->
                <div id="api-content" class="tab-content">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Direct Moodle API Sync</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sync All Courses -->
                        <div class="gradient-border rounded-xl p-6 hover:shadow-xl transition-shadow">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">Sync All Courses</h4>
                                    <p class="text-gray-600 text-sm mt-1 mb-4">Import all courses from your Moodle instance</p>
                                    <button onclick="syncAllCourses()" 
                                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 font-medium shadow-lg">
                                        Start Full Sync
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sync by Category -->
                        <div class="gradient-border rounded-xl p-6 hover:shadow-xl transition-shadow">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">Sync by Category</h4>
                                    <p class="text-gray-600 text-sm mt-1 mb-4">Import courses from specific categories</p>
                                    <button onclick="showCategorySync()" 
                                            class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white px-4 py-2 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all transform hover:scale-105 font-medium shadow-lg">
                                        Select Category
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Export to Excel -->
                        <div class="gradient-border rounded-xl p-6 hover:shadow-xl transition-shadow">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">Export Courses</h4>
                                    <p class="text-gray-600 text-sm mt-1 mb-4">Download Moodle courses as Excel file</p>
                                    <a href="{{ route('admin.moodle.courses.export') }}" 
                                       class="block text-center w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-2 rounded-lg hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 font-medium shadow-lg">
                                        Export to Excel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Check Missing -->
                        <div class="gradient-border rounded-xl p-6 hover:shadow-xl transition-shadow">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">Check Missing</h4>
                                    <p class="text-gray-600 text-sm mt-1 mb-4">Find courses not yet imported</p>
                                    <a href="{{ route('admin.moodle.courses.missing') }}" 
                                       class="block text-center w-full bg-gradient-to-r from-yellow-600 to-yellow-700 text-white px-4 py-2 rounded-lg hover:from-yellow-700 hover:to-yellow-800 transition-all transform hover:scale-105 font-medium shadow-lg">
                                        View Missing
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Indicator (Hidden by default) -->
                    <div id="sync-progress" class="hidden mt-8">
                        <div class="bg-blue-50 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div class="loading-spinner mr-4"></div>
                                <div>
                                    <h4 class="font-semibold text-blue-900">Syncing in Progress...</h4>
                                    <p class="text-sm text-blue-700">Please wait while we sync your courses</p>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Upload Tab -->
                <div id="upload-content" class="tab-content hidden">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Import from Excel/CSV</h3>
                    
                    <form action="{{ route('admin.moodle.courses.import.file') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- File Drop Zone -->
                        <div id="file-drop-zone" class="file-drop-zone rounded-xl p-12 text-center cursor-pointer hover:border-blue-400 transition-all">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-700 mb-2">Drop your file here or click to browse</p>
                            <p class="text-sm text-gray-500">Accepted formats: CSV, XLSX, XLS (Max: 10MB)</p>
                            <input type="file" name="file" id="file-input" accept=".csv,.xlsx,.xls" required class="hidden">
                            
                            <!-- File Preview (Hidden by default) -->
                            <div id="file-preview" class="hidden mt-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="text-left">
                                            <p class="font-medium text-gray-900" id="file-name"></p>
                                            <p class="text-sm text-gray-500" id="file-size"></p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Import Options -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative">
                                <input type="radio" name="import_mode" value="both" checked class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">Create & Update</p>
                                            <p class="text-sm text-gray-600">Add new and update existing</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative">
                                <input type="radio" name="import_mode" value="create_only" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">Create Only</p>
                                            <p class="text-sm text-gray-600">Add new courses only</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative">
                                <input type="radio" name="import_mode" value="update_only" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">Update Only</p>
                                            <p class="text-sm text-gray-600">Update existing only</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition-all transform hover:scale-[1.02] shadow-lg">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload and Import Courses
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Template Tab -->
                <div id="template-content" class="tab-content hidden">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Import File Format</h3>
                    
                    <div class="bg-gray-50 rounded-xl p-6">
                        <p class="text-gray-700 mb-6">
                            Download our template or ensure your file follows the format below:
                        </p>
                        
                        <!-- Download Template Button -->
                        <div class="mb-8">
                            <a href="{{ route('admin.moodle.courses.template') }}" 
                               class="inline-flex items-center bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-indigo-800 transition-all transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Import Template
                            </a>
                        </div>

                        <!-- Column Specifications Table -->
                        <div class="bg-white rounded-lg overflow-hidden shadow">
                            <table class="w-full">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Column Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Example</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-sm">moodle_id</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Required</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Moodle course ID</td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-500">123</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-sm">shortname</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Optional</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Course short name</td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-500">CS101</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-sm">fullname</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Required</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Course full name</td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-500">Introduction to Computer Science</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-sm">summary</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Optional</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Course description</td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-500">This course covers...</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-sm">visible</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Optional</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">1 for active, 0 for inactive</td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-500">1</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-sm">categoryid</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Optional</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Moodle category ID</td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-500">10</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Sync Modal -->
    <div id="categorySyncModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="relative top-20 mx-auto p-5 w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6">
                    <h3 class="text-xl font-bold text-white">Sync by Category</h3>
                    <p class="text-purple-100 text-sm mt-1">Select a Moodle category to sync</p>
                </div>
                <form onsubmit="syncByCategory(event)" class="p-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category ID
                        </label>
                        <input type="number" id="categoryId" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" 
                               placeholder="Enter category ID" required>
                        <p class="mt-2 text-sm text-gray-500">
                            You can find category IDs in your Moodle admin panel
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeCategoryModal()" 
                                class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all">
                            Sync Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tab) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600', 'tab-active');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected content
            document.getElementById(tab + '-content').classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(tab + '-tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-blue-500', 'text-blue-600', 'tab-active');
        }

        // File upload handling
        const dropZone = document.getElementById('file-drop-zone');
        const fileInput = document.getElementById('file-input');
        const filePreview = document.getElementById('file-preview');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showFilePreview(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showFilePreview(e.target.files[0]);
            }
        });

        function showFilePreview(file) {
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = formatFileSize(file.size);
            filePreview.classList.remove('hidden');
            
            // Change drop zone appearance
            dropZone.classList.add('border-green-500', 'bg-green-50');
        }

        function removeFile() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
            dropZone.classList.remove('border-green-500', 'bg-green-50');
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' bytes';
            else if (bytes < 1048576) return Math.round(bytes / 1024) + ' KB';
            else return Math.round(bytes / 1048576) + ' MB';
        }

        // Sync functions
        function syncAllCourses() {
            if (confirm('This will sync all courses from Moodle. This may take a while. Continue?')) {
                document.getElementById('sync-progress').classList.remove('hidden');
                
                fetch('{{ route("admin.moodle.courses.sync") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('sync-progress').classList.add('hidden');
                    
                    if (data.status === 'success') {
                        alert(`Sync completed! Created: ${data.stats.created}, Updated: ${data.stats.updated}, Failed: ${data.stats.failed}`);
                        location.reload();
                    } else {
                        alert('Sync failed: ' + data.message);
                    }
                })
                .catch(error => {
                    document.getElementById('sync-progress').classList.add('hidden');
                    alert('Error: ' + error);
                });
            }
        }

        function showCategorySync() {
            document.getElementById('categorySyncModal').classList.remove('hidden');
        }

        function closeCategoryModal() {
            document.getElementById('categorySyncModal').classList.add('hidden');
        }

        function syncByCategory(event) {
            event.preventDefault();
            const categoryId = document.getElementById('categoryId').value;
            
            document.getElementById('sync-progress').classList.remove('hidden');
            closeCategoryModal();
            
            fetch('{{ route("admin.moodle.courses.sync") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ category_id: categoryId })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('sync-progress').classList.add('hidden');
                
                if (data.status === 'success') {
                    alert(`Sync completed! Created: ${data.stats.created}, Updated: ${data.stats.updated}`);
                    location.reload();
                } else {
                    alert('Sync failed: ' + data.message);
                }
            })
            .catch(error => {
                document.getElementById('sync-progress').classList.add('hidden');
                alert('Error: ' + error);
            });
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('categorySyncModal');
            if (event.target == modal) {
                closeCategoryModal();
            }
        }
    </script>
</x-layouts>