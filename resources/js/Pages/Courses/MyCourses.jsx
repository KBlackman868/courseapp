import { Head, Link, router, usePage } from '@inertiajs/react';

export default function MyCourses({ enrollments = {}, allCourses = [], enrolledCourses = [], pendingCourses = [], isInternal = false }) {
    const { flash, auth } = usePage().props;

    const availableCourses = allCourses.filter(
        (course) =>
            !enrolledCourses.find((ec) => ec.id === course.id) &&
            !pendingCourses.find((pc) => pc.id === course.id)
    );

    const handleEnroll = (courseId) => {
        router.post(`/courses/${courseId}/enroll`);
    };

    const handleRequestAccess = (courseId) => {
        router.post(`/courses/${courseId}/request-access`);
    };

    return (
        <>
            <Head title="My Courses" />

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

                {/* Welcome Header */}
                <div className="rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white shadow-lg">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold">Welcome back, {auth?.user?.name || auth?.user?.first_name || 'Learner'}!</h1>
                            <p className="mt-1 text-indigo-100">
                                Here is an overview of your learning journey.
                            </p>
                        </div>
                        <span className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ${
                            isInternal
                                ? 'bg-white/20 text-white'
                                : 'bg-yellow-400/20 text-yellow-100'
                        }`}>
                            {isInternal ? 'Internal Staff' : 'External'}
                        </span>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div className="rounded-lg bg-white p-6 shadow">
                        <div className="flex items-center">
                            <div className="flex-shrink-0 rounded-lg bg-green-100 p-3">
                                <svg className="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-500">Enrolled</p>
                                <p className="text-2xl font-bold text-gray-900">{enrolledCourses.length}</p>
                            </div>
                        </div>
                    </div>
                    <div className="rounded-lg bg-white p-6 shadow">
                        <div className="flex items-center">
                            <div className="flex-shrink-0 rounded-lg bg-yellow-100 p-3">
                                <svg className="h-6 w-6 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-500">Pending</p>
                                <p className="text-2xl font-bold text-gray-900">{pendingCourses.length}</p>
                            </div>
                        </div>
                    </div>
                    <div className="rounded-lg bg-white p-6 shadow">
                        <div className="flex items-center">
                            <div className="flex-shrink-0 rounded-lg bg-indigo-100 p-3">
                                <svg className="h-6 w-6 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.75 16.82A7.462 7.462 0 0115 15.5c.71 0 1.396.098 2.046.282A.75.75 0 0018 15.06v-11a.75.75 0 00-.546-.721A9.006 9.006 0 0015 3a8.999 8.999 0 00-4.25 1.065V16.82zM9.25 4.065A8.999 8.999 0 005 3c-.85 0-1.673.118-2.454.34A.75.75 0 002 4.06v11a.75.75 0 00.954.721A7.506 7.506 0 015 15.5c1.579 0 3.042.487 4.25 1.32V4.065z" />
                                </svg>
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-500">Available</p>
                                <p className="text-2xl font-bold text-gray-900">{availableCourses.length}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* My Enrolled Courses */}
                <div>
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">My Enrolled Courses</h2>
                    {enrolledCourses.length > 0 ? (
                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {enrolledCourses.map((course) => (
                                <div key={course.id} className="overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-shadow">
                                    {course.image ? (
                                        <img src={course.image} alt={course.title} className="h-40 w-full object-cover" />
                                    ) : (
                                        <div className="h-40 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                            <svg className="h-12 w-12 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                                <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                            </svg>
                                        </div>
                                    )}
                                    <div className="p-4">
                                        <h3 className="text-sm font-semibold text-gray-900 line-clamp-1">{course.title}</h3>
                                        {course.description && (
                                            <p className="mt-1 text-xs text-gray-500 line-clamp-2">{course.description}</p>
                                        )}
                                        <div className="mt-3">
                                            <a
                                                href={`/courses/${course.id}/access-moodle`}
                                                className="inline-flex w-full items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-500 transition-colors"
                                            >
                                                <svg className="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fillRule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5zm7.25-.75a.75.75 0 01.75-.75h3.5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0V6.31l-5.47 5.47a.75.75 0 01-1.06-1.06l5.47-5.47H12.5a.75.75 0 01-.75-.75z" clipRule="evenodd" />
                                                </svg>
                                                Access Course
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="rounded-lg bg-white p-8 text-center shadow">
                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            <h3 className="mt-2 text-sm font-medium text-gray-900">No enrolled courses</h3>
                            <p className="mt-1 text-sm text-gray-500">Browse the catalog and enroll in a course to get started.</p>
                        </div>
                    )}
                </div>

                {/* Pending Approval */}
                {pendingCourses.length > 0 && (
                    <div>
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">Pending Approval</h2>
                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {pendingCourses.map((course) => (
                                <div key={course.id} className="overflow-hidden rounded-lg bg-white shadow opacity-75 grayscale">
                                    {course.image ? (
                                        <img src={course.image} alt={course.title} className="h-40 w-full object-cover" />
                                    ) : (
                                        <div className="h-40 bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center">
                                            <svg className="h-12 w-12 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                                <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                            </svg>
                                        </div>
                                    )}
                                    <div className="p-4">
                                        <h3 className="text-sm font-semibold text-gray-900 line-clamp-1">{course.title}</h3>
                                        {course.description && (
                                            <p className="mt-1 text-xs text-gray-500 line-clamp-2">{course.description}</p>
                                        )}
                                        <div className="mt-3">
                                            <span className="inline-flex w-full items-center justify-center rounded-md bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-800">
                                                <svg className="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clipRule="evenodd" />
                                                </svg>
                                                Awaiting Approval
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Available Courses (Internal users only) */}
                {isInternal && availableCourses.length > 0 && (
                    <div>
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">Available Courses</h2>
                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {availableCourses.map((course) => (
                                <div key={course.id} className="overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-shadow">
                                    {course.image ? (
                                        <img src={course.image} alt={course.title} className="h-40 w-full object-cover" />
                                    ) : (
                                        <div className="h-40 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                            <svg className="h-12 w-12 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                                <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                            </svg>
                                        </div>
                                    )}
                                    <div className="p-4">
                                        <h3 className="text-sm font-semibold text-gray-900 line-clamp-1">{course.title}</h3>
                                        {course.description && (
                                            <p className="mt-1 text-xs text-gray-500 line-clamp-2">{course.description}</p>
                                        )}
                                        <div className="mt-3 flex gap-2">
                                            {course.enrollment_type === 'requires_approval' ? (
                                                <button
                                                    onClick={() => handleRequestAccess(course.id)}
                                                    className="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-500 transition-colors"
                                                >
                                                    Request Access
                                                </button>
                                            ) : (
                                                <button
                                                    onClick={() => handleEnroll(course.id)}
                                                    className="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors"
                                                >
                                                    Enroll Now
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
