import { Head, Link, router, usePage } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { AcademicCapIcon, MagnifyingGlassIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';

function StatusBadge({ status }) {
    const styles = {
        enrolled: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        rejected: 'bg-red-100 text-red-800',
        sync_failed: 'bg-red-100 text-red-800',
        syncing: 'bg-blue-100 text-blue-800',
        available: 'bg-gray-100 text-gray-800',
    };
    const labels = {
        enrolled: 'Enrolled',
        pending: 'Pending',
        rejected: 'Rejected',
        sync_failed: 'Sync Failed',
        syncing: 'Setting up...',
        available: 'Available',
    };
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${styles[status] || styles.available}`}>
            {labels[status] || status}
        </span>
    );
}

export default function Learner({
    courses,
    userCourseStatuses = {},
    activeTab = 'moh',
    search = '',
    category = '',
    categories = {},
    showOnboarding = false,
    canAccessMohTab = true,
    canAccessExternalTab = true,
}) {
    const [searchInput, setSearchInput] = useState(search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('dashboard.learner'), { tab: activeTab, search: searchInput }, { preserveState: true });
    };

    const switchTab = (tab) => {
        router.get(route('dashboard.learner'), { tab }, { preserveState: true });
    };

    const courseItems = courses?.data || [];

    return (
        <DashboardLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    My Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="space-y-6">
                {/* Onboarding Banner */}
                {showOnboarding && (
                    <div className="rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white shadow-lg">
                        <h3 className="text-lg font-bold">Welcome to MOH Learning!</h3>
                        <p className="mt-1 text-indigo-100">Browse our course catalog and enroll to get started.</p>
                    </div>
                )}

                {/* Tabs */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8">
                        {canAccessMohTab && (
                            <button
                                onClick={() => switchTab('moh')}
                                className={`whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium ${
                                    activeTab === 'moh'
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                }`}
                            >
                                MOH Courses
                            </button>
                        )}
                        {canAccessExternalTab && (
                            <button
                                onClick={() => switchTab('external')}
                                className={`whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium ${
                                    activeTab === 'external'
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                }`}
                            >
                                External Courses
                            </button>
                        )}
                    </nav>
                </div>

                {/* Search */}
                <form onSubmit={handleSearch} className="flex gap-3">
                    <div className="relative flex-1">
                        <MagnifyingGlassIcon className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Search courses..."
                            value={searchInput}
                            onChange={(e) => setSearchInput(e.target.value)}
                            className="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <button
                        type="submit"
                        className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Search
                    </button>
                </form>

                {/* Course Grid */}
                {courseItems.length === 0 ? (
                    <div className="rounded-lg bg-white p-12 text-center shadow">
                        <AcademicCapIcon className="mx-auto h-12 w-12 text-gray-400" />
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                        <p className="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {courseItems.map((course) => {
                            const courseStatus = userCourseStatuses[course.id];
                            return (
                                <div key={course.id} className="overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-shadow">
                                    <div className="p-6">
                                        <div className="flex items-start justify-between">
                                            <h3 className="text-base font-semibold text-gray-900 line-clamp-2">
                                                {course.title}
                                            </h3>
                                            {courseStatus && (
                                                <StatusBadge status={courseStatus.status} />
                                            )}
                                        </div>
                                        {course.description && (
                                            <p className="mt-2 text-sm text-gray-600 line-clamp-3">
                                                {course.description}
                                            </p>
                                        )}
                                        {course.category && (
                                            <p className="mt-2 text-xs text-gray-500">
                                                {course.category.name}
                                            </p>
                                        )}
                                        <div className="mt-4">
                                            {courseStatus?.action === 'access' ? (
                                                <a
                                                    href={`/courses/${course.id}`}
                                                    className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                                >
                                                    Go to Course
                                                </a>
                                            ) : courseStatus?.action === 'request' ? (
                                                <Link
                                                    href={`/courses/${course.id}/request-access`}
                                                    method="post"
                                                    as="button"
                                                    className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                                >
                                                    Request Access
                                                </Link>
                                            ) : courseStatus?.cta ? (
                                                <span className="text-sm text-gray-500">{courseStatus.cta}</span>
                                            ) : (
                                                <a
                                                    href={`/courses/${course.id}`}
                                                    className="inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                                                >
                                                    View Details
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}

                {/* Pagination */}
                {courses?.links && courses.links.length > 3 && (
                    <nav className="flex justify-center">
                        <div className="flex gap-1">
                            {courses.links.map((link, i) => (
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
                )}
            </div>
        </DashboardLayout>
    );
}
