import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function Pagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <nav className="flex justify-center mt-6">
            <div className="flex gap-1">
                {links.map((link, i) => (
                    <Link
                        key={i}
                        href={link.url || '#'}
                        className={`rounded-md px-3 py-2 text-sm ${
                            link.active
                                ? 'bg-indigo-600 text-white'
                                : link.url
                                  ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                  : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        }`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                        preserveState
                    />
                ))}
            </div>
        </nav>
    );
}

function StatusBadge({ status }) {
    const styles = {
        approved: 'bg-green-100 text-green-800',
        active: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-blue-100 text-blue-800',
        rejected: 'bg-red-100 text-red-800',
    };
    const labels = {
        approved: 'Active',
        active: 'Active',
        pending: 'Pending',
        completed: 'Completed',
        rejected: 'Rejected',
    };
    return (
        <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${styles[status] || 'bg-gray-100 text-gray-800'}`}>
            {labels[status] || status}
        </span>
    );
}

export default function Index({ enrollments, counts = {}, status = 'all', search = '' }) {
    const { flash } = usePage().props;
    const [searchInput, setSearchInput] = useState(search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/my-learning', { search: searchInput, status }, { preserveState: true });
    };

    const handleStatusFilter = (newStatus) => {
        router.get('/my-learning', { status: newStatus, search: searchInput }, { preserveState: true });
    };

    const tabs = [
        { key: 'all', label: 'All', count: counts.all ?? 0 },
        { key: 'active', label: 'Active', count: counts.approved ?? counts.active ?? 0 },
        { key: 'pending', label: 'Pending', count: counts.pending ?? 0 },
        { key: 'completed', label: 'Completed', count: counts.completed ?? 0 },
    ];

    const enrollmentItems = enrollments?.data || [];

    return (
        <>
            <Head title="My Learning" />

            <div className="space-y-6">
                {/* Flash Messages */}
                {flash?.success && (
                    <div className="rounded-md bg-green-50 p-4">
                        <p className="text-sm font-medium text-green-800">{flash.success}</p>
                    </div>
                )}
                {flash?.error && (
                    <div className="rounded-md bg-red-50 p-4">
                        <p className="text-sm font-medium text-red-800">{flash.error}</p>
                    </div>
                )}

                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">My Learning</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Track your learning progress and access your courses.
                    </p>
                </div>

                {/* Search */}
                <div className="rounded-lg bg-white p-4 shadow">
                    <form onSubmit={handleSearch} className="flex gap-3">
                        <div className="relative flex-1">
                            <svg
                                className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clipRule="evenodd"
                                />
                            </svg>
                            <input
                                type="text"
                                placeholder="Search your courses..."
                                value={searchInput}
                                onChange={(e) => setSearchInput(e.target.value)}
                                className="block w-full rounded-md border-gray-300 pl-10 pr-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <button
                            type="submit"
                            className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            Search
                        </button>
                    </form>
                </div>

                {/* Status Filter Tabs */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8">
                        {tabs.map((tab) => (
                            <button
                                key={tab.key}
                                onClick={() => handleStatusFilter(tab.key)}
                                className={`whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium ${
                                    status === tab.key
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                }`}
                            >
                                {tab.label}
                                <span
                                    className={`ml-2 rounded-full px-2 py-0.5 text-xs ${
                                        status === tab.key
                                            ? 'bg-indigo-100 text-indigo-600'
                                            : 'bg-gray-100 text-gray-600'
                                    }`}
                                >
                                    {tab.count}
                                </span>
                            </button>
                        ))}
                    </nav>
                </div>

                {/* Course Grid */}
                {enrollmentItems.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {enrollmentItems.map((enrollment) => {
                            const course = enrollment.course;
                            if (!course) return null;

                            return (
                                <div key={enrollment.id} className="overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-shadow flex flex-col">
                                    {/* Image */}
                                    {course.image ? (
                                        <div className="relative">
                                            <img src={course.image} alt={course.title} className="h-40 w-full object-cover" />
                                            <div className="absolute top-2 right-2">
                                                <StatusBadge status={enrollment.status} />
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="relative h-40 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                            <svg className="h-12 w-12 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                                <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                            </svg>
                                            <div className="absolute top-2 right-2">
                                                <StatusBadge status={enrollment.status} />
                                            </div>
                                        </div>
                                    )}

                                    <div className="flex flex-1 flex-col p-4">
                                        <h3 className="text-sm font-semibold text-gray-900 line-clamp-2">{course.title}</h3>

                                        <p className="mt-1 text-xs text-gray-500">
                                            Enrolled: {new Date(enrollment.created_at).toLocaleDateString()}
                                        </p>

                                        {/* Progress Bar */}
                                        {(enrollment.status === 'approved' || enrollment.status === 'active') && (
                                            <div className="mt-3">
                                                <div className="flex items-center justify-between text-xs text-gray-500 mb-1">
                                                    <span>Progress</span>
                                                    <span>{enrollment.progress ?? 0}%</span>
                                                </div>
                                                <div className="h-2 w-full rounded-full bg-gray-200">
                                                    <div
                                                        className="h-2 rounded-full bg-indigo-600 transition-all"
                                                        style={{ width: `${enrollment.progress ?? 0}%` }}
                                                    />
                                                </div>
                                            </div>
                                        )}

                                        {/* Actions */}
                                        <div className="mt-auto pt-3">
                                            {(enrollment.status === 'approved' || enrollment.status === 'active') && (
                                                <a
                                                    href={`/courses/${course.id}/access-moodle`}
                                                    className="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors"
                                                >
                                                    Continue Learning
                                                </a>
                                            )}
                                            {enrollment.status === 'pending' && (
                                                <span className="inline-flex w-full items-center justify-center rounded-md bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-800">
                                                    Awaiting Approval
                                                </span>
                                            )}
                                            {enrollment.status === 'completed' && (
                                                <a
                                                    href={`/courses/${course.id}/access-moodle`}
                                                    className="inline-flex w-full items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-500 transition-colors"
                                                >
                                                    Review Course
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    <div className="rounded-lg bg-white p-12 text-center shadow">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                        <p className="mt-1 text-sm text-gray-500">
                            {status !== 'all'
                                ? `No courses with "${status}" status.`
                                : 'You have not enrolled in any courses yet.'}
                        </p>
                        <div className="mt-4">
                            <Link
                                href="/catalog"
                                className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                            >
                                Browse Catalog
                            </Link>
                        </div>
                    </div>
                )}

                {/* Pagination */}
                <Pagination links={enrollments?.links} />
            </div>
        </>
    );
}
