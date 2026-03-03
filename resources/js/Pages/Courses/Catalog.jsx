import { Head, Link, router, usePage } from '@inertiajs/react';

export default function Catalog({ courses = [] }) {
    const { flash } = usePage().props;

    const handleEnroll = (courseId) => {
        router.post(`/courses/${courseId}/enroll`);
    };

    const handleRequestAccess = (courseId) => {
        router.post(`/courses/${courseId}/request-access`);
    };

    const getActionButton = (course) => {
        const status = course.user_enrollment_status;

        switch (status) {
            case 'enrolled':
                return (
                    <a
                        href={`/courses/${course.id}/access-moodle`}
                        className="inline-flex w-full items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-500 transition-colors"
                    >
                        Enter Course
                    </a>
                );
            case 'pending':
                return (
                    <span className="inline-flex w-full items-center justify-center rounded-md bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-800">
                        Pending Approval
                    </span>
                );
            case 'can_request':
            case 'requires_approval':
                return (
                    <button
                        onClick={() => handleRequestAccess(course.id)}
                        className="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-500 transition-colors"
                    >
                        Request Access
                    </button>
                );
            default:
                return (
                    <button
                        onClick={() => handleEnroll(course.id)}
                        className="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors"
                    >
                        Enroll Now
                    </button>
                );
        }
    };

    const getEnrollmentBadge = (course) => {
        if (course.enrollment_type === 'requires_approval') {
            return (
                <span className="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">
                    APPROVAL
                </span>
            );
        }
        return (
            <span className="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                OPEN
            </span>
        );
    };

    return (
        <>
            <Head title="Course Catalog" />

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
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Course Catalog</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            {courses.length} {courses.length === 1 ? 'course' : 'courses'} available
                        </p>
                    </div>
                </div>

                {/* Course Grid */}
                {courses.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {courses.map((course) => (
                            <div key={course.id} className="overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-shadow flex flex-col">
                                {/* Image */}
                                {course.image ? (
                                    <img src={course.image} alt={course.title} className="h-44 w-full object-cover" />
                                ) : (
                                    <div className="h-44 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                        <svg className="h-14 w-14 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                            <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                        </svg>
                                    </div>
                                )}

                                <div className="flex flex-1 flex-col p-4">
                                    {/* Badges */}
                                    <div className="flex items-center gap-2 mb-2">
                                        {getEnrollmentBadge(course)}
                                        {course.category && (
                                            <span className="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                                {course.category.name || course.category}
                                            </span>
                                        )}
                                    </div>

                                    {/* Title */}
                                    <Link href={`/catalog/${course.id}`} className="hover:text-indigo-600 transition-colors">
                                        <h3 className="text-base font-semibold text-gray-900 line-clamp-2">{course.title}</h3>
                                    </Link>

                                    {/* Description */}
                                    {course.description && (
                                        <p className="mt-2 text-sm text-gray-500 line-clamp-3 flex-1">{course.description}</p>
                                    )}

                                    {/* Action Button */}
                                    <div className="mt-4">
                                        {getActionButton(course)}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="rounded-lg bg-white p-12 text-center shadow">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No courses available</h3>
                        <p className="mt-1 text-sm text-gray-500">Check back later for new courses.</p>
                    </div>
                )}
            </div>
        </>
    );
}
