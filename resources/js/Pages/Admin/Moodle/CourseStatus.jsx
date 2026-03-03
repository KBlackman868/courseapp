import { Head, usePage, router } from '@inertiajs/react';
import { useState } from 'react';

export default function CourseStatus({ courses = {}, stats = {} }) {
    const { flash } = usePage().props;
    const [syncing, setSyncing] = useState({});

    const { total = 0, synced = 0, not_synced = 0, active = 0, inactive = 0 } = stats;

    const courseData = courses.data || [];
    const pagination = {
        current_page: courses.current_page || 1,
        last_page: courses.last_page || 1,
        from: courses.from || 0,
        to: courses.to || 0,
        total: courses.total || 0,
        links: courses.links || [],
    };

    const handleSync = (courseId) => {
        setSyncing((prev) => ({ ...prev, [courseId]: true }));
        router.post(`/admin/courses/${courseId}/sync-to-moodle`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                setSyncing((prev) => ({ ...prev, [courseId]: false }));
            },
        });
    };

    const handlePageChange = (url) => {
        if (!url) return;
        router.get(url, {}, { preserveState: true, preserveScroll: true });
    };

    return (
        <>
            <Head title="Course Sync Status" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Course Sync Status</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        View and manage the Moodle sync status of all courses.
                    </p>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="rounded-md bg-green-50 p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-green-800">{flash.success}</p>
                            </div>
                        </div>
                    </div>
                )}

                {flash?.error && (
                    <div className="rounded-md bg-red-50 p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-red-800">{flash.error}</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <dt className="truncate text-sm font-medium text-gray-500">Total Courses</dt>
                            <dd className="mt-1 text-2xl font-semibold text-gray-900">{total}</dd>
                        </div>
                    </div>
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <dt className="truncate text-sm font-medium text-gray-500">Synced</dt>
                            <dd className="mt-1 text-2xl font-semibold text-green-600">{synced}</dd>
                        </div>
                    </div>
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <dt className="truncate text-sm font-medium text-gray-500">Not Synced</dt>
                            <dd className="mt-1 text-2xl font-semibold text-red-600">{not_synced}</dd>
                        </div>
                    </div>
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <dt className="truncate text-sm font-medium text-gray-500">Active</dt>
                            <dd className="mt-1 text-2xl font-semibold text-blue-600">{active}</dd>
                        </div>
                    </div>
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-5">
                            <dt className="truncate text-sm font-medium text-gray-500">Inactive</dt>
                            <dd className="mt-1 text-2xl font-semibold text-gray-500">{inactive}</dd>
                        </div>
                    </div>
                </div>

                {/* Courses Table */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">
                            All Courses
                        </h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Moodle ID
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Shortname
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sync Status
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Updated
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {courseData.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="px-6 py-12 text-center text-sm text-gray-500">
                                            No courses found.
                                        </td>
                                    </tr>
                                ) : (
                                    courseData.map((course) => (
                                        <tr key={course.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {course.id}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                                {course.title}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {course.moodle_course_id || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {course.moodle_course_shortname || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                                    course.status === 'active'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-gray-100 text-gray-800'
                                                }`}>
                                                    {course.status ? course.status.charAt(0).toUpperCase() + course.status.slice(1) : 'Unknown'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                {course.moodle_course_id ? (
                                                    <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                        Synced
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                        Not Synced
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {course.updated_at
                                                    ? new Date(course.updated_at).toLocaleDateString()
                                                    : '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                {course.moodle_course_id ? (
                                                    <button
                                                        onClick={() => handleSync(course.id)}
                                                        disabled={syncing[course.id]}
                                                        className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                    >
                                                        {syncing[course.id] ? 'Syncing...' : 'Re-sync'}
                                                    </button>
                                                ) : (
                                                    <span className="text-xs text-gray-400">Not linked</span>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {pagination.last_page > 1 && (
                        <div className="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                            <div className="flex items-center justify-between">
                                <div className="text-sm text-gray-700">
                                    Showing <span className="font-medium">{pagination.from}</span> to{' '}
                                    <span className="font-medium">{pagination.to}</span> of{' '}
                                    <span className="font-medium">{pagination.total}</span> results
                                </div>
                                <div className="flex gap-1">
                                    {pagination.links.map((link, index) => (
                                        <button
                                            key={index}
                                            onClick={() => handlePageChange(link.url)}
                                            disabled={!link.url || link.active}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                            className={`relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${
                                                link.active
                                                    ? 'bg-indigo-600 text-white'
                                                    : link.url
                                                        ? 'bg-white text-gray-700 hover:bg-gray-50 ring-1 ring-inset ring-gray-300'
                                                        : 'bg-white text-gray-300 cursor-not-allowed'
                                            }`}
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
