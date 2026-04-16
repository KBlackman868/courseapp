import { Head, usePage, router } from '@inertiajs/react';
import { useState, useMemo } from 'react';

export default function CourseImport({ stats = {} }) {
    const { flash } = usePage().props;
    const [file, setFile] = useState(null);
    const [importMode, setImportMode] = useState('both');
    const [uploading, setUploading] = useState(false);

    // Moodle course selection state
    const [fetchLoading, setFetchLoading] = useState(false);
    const [moodleCourses, setMoodleCourses] = useState(null); // null = not fetched yet
    const [selectedIds, setSelectedIds] = useState([]);
    const [importLoading, setImportLoading] = useState(false);
    const [importResult, setImportResult] = useState(null);
    const [searchQuery, setSearchQuery] = useState('');
    const [filterMode, setFilterMode] = useState('all'); // 'all', 'new', 'imported'
    const [fetchError, setFetchError] = useState(null);

    const { local_courses = 0, moodle_synced = 0, not_synced = 0 } = stats;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Fetch available courses from Moodle
    const handleFetchCourses = async () => {
        setFetchLoading(true);
        setFetchError(null);
        setImportResult(null);
        try {
            const response = await fetch('/admin/moodle/courses/fetch', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const data = await response.json();
            if (data.status === 'success') {
                setMoodleCourses(data.courses);
                setSelectedIds([]);
            } else {
                setFetchError(data.message || 'Failed to fetch courses');
            }
        } catch (error) {
            setFetchError('Network error: ' + error.message);
        } finally {
            setFetchLoading(false);
        }
    };

    // Import selected courses
    const handleImportSelected = async () => {
        if (selectedIds.length === 0) return;
        setImportLoading(true);
        setImportResult(null);
        try {
            const response = await fetch('/admin/moodle/courses/sync-selected', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ moodle_ids: selectedIds }),
            });
            const data = await response.json();
            setImportResult(data);
            if (data.status === 'success') {
                // Re-fetch to update statuses
                handleFetchCourses();
            }
        } catch (error) {
            setImportResult({ status: 'error', message: 'Network error: ' + error.message });
        } finally {
            setImportLoading(false);
        }
    };

    const handleFileUpload = (e) => {
        e.preventDefault();
        if (!file) return;
        setUploading(true);
        const formData = new FormData();
        formData.append('file', file);
        formData.append('import_mode', importMode);
        router.post('/admin/moodle/courses/import/file', formData, {
            forceFormData: true,
            preserveState: true,
            preserveScroll: true,
            onFinish: () => { setUploading(false); setFile(null); },
        });
    };

    // Toggle selection
    const toggleCourse = (moodleId) => {
        setSelectedIds((prev) =>
            prev.includes(moodleId) ? prev.filter((id) => id !== moodleId) : [...prev, moodleId]
        );
    };

    const toggleAll = (courses) => {
        const selectableIds = courses.filter((c) => !c.already_imported).map((c) => c.moodle_id);
        const allSelected = selectableIds.every((id) => selectedIds.includes(id));
        if (allSelected) {
            setSelectedIds((prev) => prev.filter((id) => !selectableIds.includes(id)));
        } else {
            setSelectedIds((prev) => [...new Set([...prev, ...selectableIds])]);
        }
    };

    // Filtered + searched courses
    const filteredCourses = useMemo(() => {
        if (!moodleCourses) return [];
        return moodleCourses.filter((c) => {
            if (filterMode === 'new' && c.already_imported) return false;
            if (filterMode === 'imported' && !c.already_imported) return false;
            if (searchQuery) {
                const q = searchQuery.toLowerCase();
                return (
                    c.fullname.toLowerCase().includes(q) ||
                    c.shortname.toLowerCase().includes(q)
                );
            }
            return true;
        });
    }, [moodleCourses, filterMode, searchQuery]);

    const newCount = moodleCourses ? moodleCourses.filter((c) => !c.already_imported).length : 0;
    const importedCount = moodleCourses ? moodleCourses.filter((c) => c.already_imported).length : 0;

    return (
        <>
            <Head title="Course Import" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Course Import</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Select courses from Moodle to import, or upload a CSV/Excel file.
                    </p>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="rounded-md bg-green-50 p-4">
                        <div className="flex">
                            <svg className="h-5 w-5 text-green-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                            </svg>
                            <p className="ml-3 text-sm font-medium text-green-800">{flash.success}</p>
                        </div>
                    </div>
                )}
                {flash?.error && (
                    <div className="rounded-md bg-red-50 p-4">
                        <div className="flex">
                            <svg className="h-5 w-5 text-red-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" />
                            </svg>
                            <p className="ml-3 text-sm font-medium text-red-800">{flash.error}</p>
                        </div>
                    </div>
                )}

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <div className="rounded-md bg-indigo-500 p-3">
                                        <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                                    </div>
                                </div>
                                <div className="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt className="truncate text-sm font-medium text-gray-500">Local Courses</dt>
                                        <dd className="mt-1 text-2xl font-semibold text-gray-900">{local_courses}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <div className="rounded-md bg-green-500 p-3">
                                        <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                </div>
                                <div className="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt className="truncate text-sm font-medium text-gray-500">Moodle Synced</dt>
                                        <dd className="mt-1 text-2xl font-semibold text-gray-900">{moodle_synced}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <div className="rounded-md bg-yellow-500 p-3">
                                        <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                    </div>
                                </div>
                                <div className="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt className="truncate text-sm font-medium text-gray-500">Not Synced</dt>
                                        <dd className="mt-1 text-2xl font-semibold text-gray-900">{not_synced}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Import from Moodle */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 className="text-base font-semibold leading-6 text-gray-900">Import from Moodle</h3>
                            <p className="mt-1 text-sm text-gray-500">Fetch courses from Moodle and select which ones to import.</p>
                        </div>
                        <div className="flex items-center gap-3">
                            <button
                                onClick={handleFetchCourses}
                                disabled={fetchLoading}
                                className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {fetchLoading ? (
                                    <>
                                        <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
                                        Fetching...
                                    </>
                                ) : moodleCourses ? 'Refresh' : 'Fetch Courses'}
                            </button>
                            <a
                                href="/admin/moodle/courses/export"
                                className="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                            >
                                Export CSV
                            </a>
                        </div>
                    </div>

                    {fetchError && (
                        <div className="mx-4 mt-4 rounded-md bg-red-50 p-3">
                            <p className="text-sm text-red-800">{fetchError}</p>
                        </div>
                    )}

                    {importResult && (
                        <div className={`mx-4 mt-4 rounded-md p-3 ${importResult.status === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}`}>
                            <p className="text-sm font-medium">{importResult.message}</p>
                            {importResult.stats?.errors?.length > 0 && (
                                <ul className="mt-2 text-xs list-disc list-inside">
                                    {importResult.stats.errors.map((err, i) => <li key={i}>{err}</li>)}
                                </ul>
                            )}
                        </div>
                    )}

                    {moodleCourses !== null && (
                        <div className="px-4 py-4 sm:px-6">
                            {/* Toolbar */}
                            <div className="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
                                <input
                                    type="text"
                                    placeholder="Search courses..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    className="block w-full sm:w-64 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div className="flex items-center gap-2">
                                    <select
                                        value={filterMode}
                                        onChange={(e) => setFilterMode(e.target.value)}
                                        className="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="all">All ({moodleCourses.length})</option>
                                        <option value="new">Not Imported ({newCount})</option>
                                        <option value="imported">Already Imported ({importedCount})</option>
                                    </select>
                                </div>
                                <div className="flex items-center gap-3 sm:ml-auto">
                                    <span className="text-sm text-gray-500">
                                        {selectedIds.length} selected
                                    </span>
                                    <button
                                        onClick={handleImportSelected}
                                        disabled={importLoading || selectedIds.length === 0}
                                        className="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        {importLoading ? (
                                            <>
                                                <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
                                                Importing...
                                            </>
                                        ) : (
                                            `Import Selected (${selectedIds.length})`
                                        )}
                                    </button>
                                </div>
                            </div>

                            {/* Course list */}
                            {filteredCourses.length === 0 ? (
                                <p className="text-center text-sm text-gray-500 py-8">
                                    {searchQuery ? 'No courses match your search.' : 'No courses found.'}
                                </p>
                            ) : (
                                <div className="border border-gray-200 rounded-lg overflow-hidden">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-4 py-3 text-left">
                                                    <input
                                                        type="checkbox"
                                                        checked={
                                                            filteredCourses.filter((c) => !c.already_imported).length > 0 &&
                                                            filteredCourses.filter((c) => !c.already_imported).every((c) => selectedIds.includes(c.moodle_id))
                                                        }
                                                        onChange={() => toggleAll(filteredCourses)}
                                                        className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Short Name</th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moodle ID</th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {filteredCourses.map((course) => (
                                                <tr
                                                    key={course.moodle_id}
                                                    className={`${course.already_imported ? 'bg-gray-50' : 'hover:bg-indigo-50 cursor-pointer'} transition-colors`}
                                                    onClick={() => !course.already_imported && toggleCourse(course.moodle_id)}
                                                >
                                                    <td className="px-4 py-3">
                                                        <input
                                                            type="checkbox"
                                                            checked={selectedIds.includes(course.moodle_id)}
                                                            onChange={() => toggleCourse(course.moodle_id)}
                                                            disabled={course.already_imported}
                                                            className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 disabled:opacity-40"
                                                            onClick={(e) => e.stopPropagation()}
                                                        />
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        <div className="text-sm font-medium text-gray-900">{course.fullname}</div>
                                                        {course.summary && (
                                                            <div className="text-xs text-gray-500 mt-0.5 line-clamp-1">{course.summary}</div>
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-gray-500">{course.shortname}</td>
                                                    <td className="px-4 py-3 text-sm text-gray-500">{course.moodle_id}</td>
                                                    <td className="px-4 py-3">
                                                        {course.already_imported ? (
                                                            <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                                Imported
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                                                Not Imported
                                                            </span>
                                                        )}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    )}

                    {moodleCourses === null && !fetchLoading && (
                        <div className="px-4 py-12 sm:px-6 text-center">
                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" strokeWidth={1} stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                            <h3 className="mt-2 text-sm font-semibold text-gray-900">No courses loaded</h3>
                            <p className="mt-1 text-sm text-gray-500">Click "Fetch Courses" to load available courses from Moodle.</p>
                        </div>
                    )}
                </div>

                {/* File Upload */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Import from File</h3>
                        <p className="mt-1 text-sm text-gray-500">Upload a CSV or Excel file containing course data.</p>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <form onSubmit={handleFileUpload} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Import Mode</label>
                                <select
                                    value={importMode}
                                    onChange={(e) => setImportMode(e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="both">Create and Update</option>
                                    <option value="create_only">Create Only (skip existing)</option>
                                    <option value="update_only">Update Only (skip new)</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">File (CSV or Excel)</label>
                                <input
                                    type="file"
                                    accept=".csv,.xlsx,.xls"
                                    onChange={(e) => setFile(e.target.files[0] || null)}
                                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                            </div>
                            <div className="flex items-center gap-4">
                                <button
                                    type="submit"
                                    disabled={uploading || !file}
                                    className="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    {uploading ? (
                                        <>
                                            <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
                                            Uploading...
                                        </>
                                    ) : 'Upload and Import'}
                                </button>
                                <a
                                    href="/admin/moodle/courses/template"
                                    className="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                >
                                    Download Template
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
