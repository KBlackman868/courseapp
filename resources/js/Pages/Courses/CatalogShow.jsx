import { Head, Link, router, usePage } from '@inertiajs/react';

export default function CatalogShow({ course, enrollmentStatus = 'none', enrollmentRequest = null }) {
    const { flash } = usePage().props;

    const handleEnroll = () => {
        router.post(`/courses/${course.id}/enroll`);
    };

    const handleRequestAccess = () => {
        router.post(`/courses/${course.id}/request-access`);
    };

    const renderStatusSection = () => {
        switch (enrollmentStatus) {
            case 'enrolled':
                return (
                    <div className="rounded-lg border border-green-200 bg-green-50 p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-lg font-semibold text-green-800">You are enrolled</h3>
                                <p className="mt-1 text-sm text-green-700">
                                    You have access to this course. Click the button to start learning.
                                </p>
                            </div>
                            <a
                                href={`/courses/${course.id}/access-moodle`}
                                className="inline-flex items-center rounded-md bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors"
                            >
                                <svg className="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5zm7.25-.75a.75.75 0 01.75-.75h3.5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0V6.31l-5.47 5.47a.75.75 0 01-1.06-1.06l5.47-5.47H12.5a.75.75 0 01-.75-.75z" clipRule="evenodd" />
                                </svg>
                                Enter Course
                            </a>
                        </div>
                    </div>
                );

            case 'pending':
                return (
                    <div className="rounded-lg border border-yellow-200 bg-yellow-50 p-6">
                        <div className="flex items-center">
                            <svg className="mr-3 h-6 w-6 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clipRule="evenodd" />
                            </svg>
                            <div>
                                <h3 className="text-lg font-semibold text-yellow-800">Pending Approval</h3>
                                <p className="mt-1 text-sm text-yellow-700">
                                    Your enrollment request is being reviewed.
                                    {enrollmentRequest?.created_at && (
                                        <span className="ml-1">
                                            Submitted on {new Date(enrollmentRequest.created_at).toLocaleDateString()}.
                                        </span>
                                    )}
                                </p>
                            </div>
                        </div>
                    </div>
                );

            case 'rejected':
                return (
                    <div className="rounded-lg border border-red-200 bg-red-50 p-6">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <svg className="mr-3 h-6 w-6 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" />
                                </svg>
                                <div>
                                    <h3 className="text-lg font-semibold text-red-800">Request Denied</h3>
                                    <p className="mt-1 text-sm text-red-700">
                                        Your enrollment request was denied.
                                        {enrollmentRequest?.rejection_reason && (
                                            <span className="block mt-1">Reason: {enrollmentRequest.rejection_reason}</span>
                                        )}
                                    </p>
                                </div>
                            </div>
                            <button
                                onClick={handleRequestAccess}
                                className="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors"
                            >
                                Request Again
                            </button>
                        </div>
                    </div>
                );

            case 'can_request':
            case 'requires_approval':
                return (
                    <div className="rounded-lg border border-blue-200 bg-blue-50 p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-lg font-semibold text-blue-800">Approval Required</h3>
                                <p className="mt-1 text-sm text-blue-700">
                                    This course requires approval before you can access it.
                                </p>
                            </div>
                            <button
                                onClick={handleRequestAccess}
                                className="inline-flex items-center rounded-md bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors"
                            >
                                Request Access
                            </button>
                        </div>
                    </div>
                );

            default:
                return (
                    <div className="rounded-lg border border-indigo-200 bg-indigo-50 p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-lg font-semibold text-indigo-800">Ready to Learn?</h3>
                                <p className="mt-1 text-sm text-indigo-700">
                                    Enroll in this course to start your learning journey.
                                </p>
                            </div>
                            <button
                                onClick={handleEnroll}
                                className="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                            >
                                Enroll Now
                            </button>
                        </div>
                    </div>
                );
        }
    };

    return (
        <>
            <Head title={course.title} />

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

                {/* Course Image */}
                <div className="overflow-hidden rounded-lg shadow">
                    {course.image_url ? (
                        <img
                            src={course.image_url}
                            alt={course.title}
                            className="h-64 w-full object-cover"
                        />
                    ) : (
                        <div className="flex h-64 items-center justify-center bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
                            <svg className="h-20 w-20 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                <path d="M4.462 19.462c.42-.419.753-.89 1-1.394.453.213.902.434 1.347.661a6.743 6.743 0 01-1.286 1.794.75.75 0 11-1.06-1.06z" />
                            </svg>
                        </div>
                    )}
                </div>

                {/* Course Details */}
                <div className="rounded-lg bg-white p-6 shadow">
                    <div className="flex flex-wrap items-center gap-2 mb-4">
                        <span
                            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                course.status === 'active'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-gray-100 text-gray-800'
                            }`}
                        >
                            {course.status === 'active' ? 'Active' : 'Inactive'}
                        </span>
                        {course.enrollment_type === 'requires_approval' ? (
                            <span className="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                Requires Approval
                            </span>
                        ) : (
                            <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                Open Enrollment
                            </span>
                        )}
                        {course.moodle_course_id && (
                            <span className="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                Moodle Integrated
                            </span>
                        )}
                    </div>

                    <h1 className="text-3xl font-bold text-gray-900">{course.title}</h1>

                    {course.description && (
                        <div className="mt-4 text-gray-600 leading-relaxed whitespace-pre-line">
                            {course.description}
                        </div>
                    )}
                </div>

                {/* Enrollment Status */}
                {renderStatusSection()}

                {/* Back Link */}
                <div>
                    <Link
                        href="/catalog"
                        className="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        <svg className="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fillRule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clipRule="evenodd" />
                        </svg>
                        Back to Catalog
                    </Link>
                </div>
            </div>
        </>
    );
}
