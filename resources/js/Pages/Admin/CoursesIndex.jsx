import { useCallback, useMemo } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { useClientFilter } from '@/hooks/useClientFilter';
import {
    MagnifyingGlassIcon,
    XMarkIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    PlusIcon,
    ArrowPathIcon,
    PencilSquareIcon,
    EyeIcon,
    TrashIcon,
    AcademicCapIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';

/**
 * CoursesIndex - Course management with client-side filtering
 *
 * Props from Laravel/Inertia (loaded once):
 * @param {Array} courses - All courses with enrollments
 * @param {Object} stats - Course statistics
 * @param {Object} flash - Flash messages
 */
export default function CoursesIndex({ courses = [], stats = {}, flash }) {
    // Pre-process courses to add computed syncStatus field for filtering
    const processedCourses = useMemo(() => {
        return courses.map((course) => ({
            ...course,
            syncStatus: course.moodle_course_id ? 'synced' : 'not_synced',
        }));
    }, [courses]);

    // Client-side filtering using our custom hook
    const {
        query,
        setQuery,
        filters,
        setFilter,
        clearAll,
        paginatedItems,
        totalCount,
        currentPage,
        setCurrentPage,
        totalPages,
        hasNextPage,
        hasPrevPage,
        nextPage,
        prevPage,
        isFiltered,
    } = useClientFilter({
        items: processedCourses,
        searchFields: ['title', 'code', 'description'],
        itemsPerPage: 15,
    });

    /**
     * Handle course deletion
     */
    const handleDelete = useCallback((courseId, courseTitle) => {
        if (
            confirm(
                `Are you sure you want to delete "${courseTitle}"? This action cannot be undone.`
            )
        ) {
            router.delete(route('courses.destroy', courseId), {
                preserveScroll: true,
            });
        }
    }, []);

    /**
     * Get status badge styling
     */
    const getStatusBadge = useCallback((status) => {
        if (status === 'active') {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <CheckCircleIcon className="h-3 w-3" />
                    Active
                </span>
            );
        }
        return (
            <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <XCircleIcon className="h-3 w-3" />
                Inactive
            </span>
        );
    }, []);

    /**
     * Get sync status badge
     */
    const getSyncBadge = useCallback((moodleCourseId) => {
        if (moodleCourseId) {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <CheckCircleIcon className="h-3 w-3" />
                    ID: {moodleCourseId}
                </span>
            );
        }
        return (
            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                Not Synced
            </span>
        );
    }, []);

    /**
     * Calculate filtered stats
     */
    const filteredStats = useMemo(() => {
        const filtered = paginatedItems;
        return {
            total: totalCount,
            active: courses.filter((c) => c.status === 'active').length,
            inactive: courses.filter((c) => c.status !== 'active').length,
            synced: courses.filter((c) => c.moodle_course_id).length,
            notSynced: courses.filter((c) => !c.moodle_course_id).length,
        };
    }, [courses, paginatedItems, totalCount]);

    return (
        <AdminLayout title="Course Management">
            <Head title="Course Management" />

            <div className="max-w-7xl mx-auto">
                {/* Flash Messages */}
                {flash?.success && (
                    <div className="mb-4 rounded-md bg-green-50 p-4">
                        <p className="text-sm text-green-800">{flash.success}</p>
                    </div>
                )}
                {flash?.error && (
                    <div className="mb-4 rounded-md bg-red-50 p-4">
                        <p className="text-sm text-red-800">{flash.error}</p>
                    </div>
                )}
                {flash?.warning && (
                    <div className="mb-4 rounded-md bg-yellow-50 p-4">
                        <p className="text-sm text-yellow-800">{flash.warning}</p>
                    </div>
                )}

                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Course Management</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Manage and sync courses with Moodle LMS
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Link
                            href={route('courses.create')}
                            className="inline-flex items-center gap-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <PlusIcon className="h-5 w-5" />
                            Create Course
                        </Link>
                        <Link
                            href={route('admin.moodle.courses.import')}
                            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <ArrowPathIcon className="h-5 w-5" />
                            Sync Moodle
                        </Link>
                    </div>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                    <div className="bg-white rounded-lg shadow p-4">
                        <div className="text-sm font-medium text-gray-500">Total</div>
                        <div className="mt-1 text-2xl font-semibold text-indigo-600">
                            {stats.total ?? filteredStats.total}
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow p-4">
                        <div className="text-sm font-medium text-gray-500">Active</div>
                        <div className="mt-1 text-2xl font-semibold text-green-600">
                            {stats.active ?? filteredStats.active}
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow p-4">
                        <div className="text-sm font-medium text-gray-500">Inactive</div>
                        <div className="mt-1 text-2xl font-semibold text-gray-600">
                            {stats.inactive ?? filteredStats.inactive}
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow p-4">
                        <div className="text-sm font-medium text-gray-500">Synced</div>
                        <div className="mt-1 text-2xl font-semibold text-blue-600">
                            {stats.synced ?? filteredStats.synced}
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow p-4">
                        <div className="text-sm font-medium text-gray-500">Not Synced</div>
                        <div className="mt-1 text-2xl font-semibold text-yellow-600">
                            {stats.not_synced ?? filteredStats.notSynced}
                        </div>
                    </div>
                </div>

                {/* Filters Card */}
                <div className="bg-white rounded-lg shadow mb-6 p-4">
                    <div className="flex flex-col lg:flex-row gap-4">
                        {/* Search Input */}
                        <div className="flex-1 relative">
                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon className="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                value={query}
                                onChange={setQuery}
                                placeholder="Search courses by name, code, or description..."
                                className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>

                        {/* Status Filter */}
                        <div className="w-full lg:w-40">
                            <select
                                value={filters.status || 'all'}
                                onChange={(e) => setFilter('status', e.target.value)}
                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        {/* Sync Status Filter */}
                        <div className="w-full lg:w-44">
                            <select
                                value={filters.syncStatus || 'all'}
                                onChange={(e) => setFilter('syncStatus', e.target.value)}
                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="all">All Sync Status</option>
                                <option value="synced">Synced</option>
                                <option value="not_synced">Not Synced</option>
                            </select>
                        </div>

                        {/* Clear Button */}
                        {isFiltered && (
                            <button
                                type="button"
                                onClick={clearAll}
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <XMarkIcon className="h-4 w-4 mr-2" />
                                Clear
                            </button>
                        )}
                    </div>

                    {/* Results count */}
                    <div className="mt-3 text-sm text-gray-500">
                        Showing {paginatedItems.length} of {totalCount} courses
                        {isFiltered && ' (filtered)'}
                    </div>
                </div>

                {/* Courses Table */}
                <div className="bg-white shadow rounded-lg overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Course
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Moodle
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Enrollments
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Created
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {paginatedItems.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-4 py-12 text-center">
                                            <AcademicCapIcon className="mx-auto h-12 w-12 text-gray-400" />
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">
                                                No courses found
                                            </h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                {isFiltered
                                                    ? 'Try adjusting your search or filter.'
                                                    : 'Get started by creating a new course or syncing from Moodle.'}
                                            </p>
                                            {!isFiltered && (
                                                <div className="mt-6">
                                                    <Link
                                                        href={route('courses.create')}
                                                        className="inline-flex items-center gap-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                                                    >
                                                        <PlusIcon className="h-5 w-5" />
                                                        Create Course
                                                    </Link>
                                                </div>
                                            )}
                                        </td>
                                    </tr>
                                ) : (
                                    paginatedItems.map((course) => (
                                        <tr key={course.id} className="hover:bg-gray-50">
                                            {/* Course Info */}
                                            <td className="px-4 py-4">
                                                <div className="flex items-center gap-3">
                                                    {course.image ? (
                                                        <img
                                                            src={`/storage/${course.image}`}
                                                            alt={course.title}
                                                            className="h-12 w-12 rounded-lg object-cover"
                                                            loading="lazy"
                                                        />
                                                    ) : (
                                                        <div className="h-12 w-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                                                            <AcademicCapIcon className="h-6 w-6 text-indigo-600" />
                                                        </div>
                                                    )}
                                                    <div>
                                                        <div className="font-medium text-gray-900">
                                                            {course.title}
                                                        </div>
                                                        {course.code && (
                                                            <div className="text-sm text-gray-500">
                                                                {course.code}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Moodle Sync Status */}
                                            <td className="px-4 py-4">
                                                {getSyncBadge(course.moodle_course_id)}
                                            </td>

                                            {/* Enrollments Count */}
                                            <td className="px-4 py-4">
                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {course.enrollments?.length ??
                                                        course.enrollments_count ??
                                                        0}
                                                </span>
                                            </td>

                                            {/* Status */}
                                            <td className="px-4 py-4">
                                                {getStatusBadge(course.status)}
                                            </td>

                                            {/* Created Date */}
                                            <td className="px-4 py-4 text-sm text-gray-500">
                                                {course.created_at
                                                    ? new Date(course.created_at).toLocaleDateString(
                                                          'en-US',
                                                          {
                                                              month: 'short',
                                                              day: 'numeric',
                                                              year: 'numeric',
                                                          }
                                                      )
                                                    : '-'}
                                            </td>

                                            {/* Actions */}
                                            <td className="px-4 py-4">
                                                <div className="flex items-center justify-center gap-2">
                                                    <Link
                                                        href={route('courses.show', course.id)}
                                                        className="text-gray-400 hover:text-gray-600"
                                                        title="View"
                                                    >
                                                        <EyeIcon className="h-5 w-5" />
                                                    </Link>
                                                    <Link
                                                        href={route('courses.edit', course.id)}
                                                        className="text-gray-400 hover:text-indigo-600"
                                                        title="Edit"
                                                    >
                                                        <PencilSquareIcon className="h-5 w-5" />
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            handleDelete(course.id, course.title)
                                                        }
                                                        className="text-gray-400 hover:text-red-600"
                                                        title="Delete"
                                                    >
                                                        <TrashIcon className="h-5 w-5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div className="flex-1 flex justify-between sm:hidden">
                                <button
                                    onClick={prevPage}
                                    disabled={!hasPrevPage}
                                    className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Previous
                                </button>
                                <button
                                    onClick={nextPage}
                                    disabled={!hasNextPage}
                                    className="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Next
                                </button>
                            </div>
                            <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p className="text-sm text-gray-700">
                                        Page <span className="font-medium">{currentPage}</span> of{' '}
                                        <span className="font-medium">{totalPages}</span>
                                    </p>
                                </div>
                                <div>
                                    <nav
                                        className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                        aria-label="Pagination"
                                    >
                                        <button
                                            onClick={prevPage}
                                            disabled={!hasPrevPage}
                                            className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span className="sr-only">Previous</span>
                                            <ChevronLeftIcon className="h-5 w-5" aria-hidden="true" />
                                        </button>
                                        <button
                                            onClick={nextPage}
                                            disabled={!hasNextPage}
                                            className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span className="sr-only">Next</span>
                                            <ChevronRightIcon className="h-5 w-5" aria-hidden="true" />
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}
