import { Head, Link, router, usePage } from '@inertiajs/react';

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
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
    };
    const labels = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected',
    };
    return (
        <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${styles[status] || 'bg-gray-100 text-gray-800'}`}>
            {labels[status] || status}
        </span>
    );
}

export default function Index({ requests, counts = {}, status = 'all' }) {
    const { flash } = usePage().props;

    const handleStatusFilter = (newStatus) => {
        router.get('/my-requests', { status: newStatus }, { preserveState: true });
    };

    const handleRequestAgain = (courseId) => {
        router.post(`/courses/${courseId}/request-access`);
    };

    const tabs = [
        { key: 'all', label: 'All', count: counts.all ?? 0 },
        { key: 'pending', label: 'Pending', count: counts.pending ?? 0 },
        { key: 'approved', label: 'Approved', count: counts.approved ?? 0 },
        { key: 'rejected', label: 'Rejected', count: counts.rejected ?? 0 },
    ];

    const requestItems = requests?.data || [];

    return (
        <>
            <Head title="My Requests" />

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
                    <h1 className="text-2xl font-bold text-gray-900">My Requests</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Track the status of your course enrollment requests.
                    </p>
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

                {/* Request Cards */}
                {requestItems.length > 0 ? (
                    <div className="space-y-4">
                        {requestItems.map((request) => {
                            const course = request.course;
                            if (!course) return null;

                            return (
                                <div key={request.id} className="overflow-hidden rounded-lg bg-white shadow">
                                    <div className="flex flex-col sm:flex-row">
                                        {/* Course Image */}
                                        <div className="sm:w-48 flex-shrink-0">
                                            {course.image_url ? (
                                                <img src={course.image_url} alt={course.title} className="h-32 w-full sm:h-full object-cover" />
                                            ) : (
                                                <div className="h-32 sm:h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                                    <svg className="h-10 w-10 text-white/50" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                                        <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                                    </svg>
                                                </div>
                                            )}
                                        </div>

                                        {/* Request Details */}
                                        <div className="flex flex-1 flex-col p-4 sm:p-5">
                                            <div className="flex items-start justify-between">
                                                <div>
                                                    <h3 className="text-base font-semibold text-gray-900">{course.title}</h3>
                                                    <p className="mt-1 text-sm text-gray-500">
                                                        Requested: {new Date(request.created_at).toLocaleDateString('en-US', {
                                                            year: 'numeric',
                                                            month: 'short',
                                                            day: 'numeric',
                                                        })}
                                                    </p>
                                                </div>
                                                <StatusBadge status={request.status} />
                                            </div>

                                            {/* Rejection Reason */}
                                            {request.status === 'rejected' && request.rejection_reason && (
                                                <div className="mt-3 rounded-md bg-red-50 p-3">
                                                    <p className="text-sm text-red-700">
                                                        <span className="font-medium">Reason:</span> {request.rejection_reason}
                                                    </p>
                                                </div>
                                            )}

                                            {/* Moodle Sync Status */}
                                            {request.status === 'approved' && request.moodle_sync_status && (
                                                <div className="mt-3">
                                                    <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                                        request.moodle_sync_status === 'synced'
                                                            ? 'bg-green-100 text-green-800'
                                                            : request.moodle_sync_status === 'failed'
                                                              ? 'bg-red-100 text-red-800'
                                                              : 'bg-blue-100 text-blue-800'
                                                    }`}>
                                                        {request.moodle_sync_status === 'synced' && 'Moodle Synced'}
                                                        {request.moodle_sync_status === 'failed' && 'Sync Failed'}
                                                        {request.moodle_sync_status === 'pending' && 'Sync Pending'}
                                                        {!['synced', 'failed', 'pending'].includes(request.moodle_sync_status) && `Moodle: ${request.moodle_sync_status}`}
                                                    </span>
                                                </div>
                                            )}

                                            {/* Actions */}
                                            <div className="mt-auto pt-3 flex gap-3">
                                                {request.status === 'approved' && (
                                                    <a
                                                        href={`/courses/${course.id}/access-moodle`}
                                                        className="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-500 transition-colors"
                                                    >
                                                        <svg className="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fillRule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5zm7.25-.75a.75.75 0 01.75-.75h3.5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0V6.31l-5.47 5.47a.75.75 0 01-1.06-1.06l5.47-5.47H12.5a.75.75 0 01-.75-.75z" clipRule="evenodd" />
                                                        </svg>
                                                        Go to Course
                                                    </a>
                                                )}
                                                {request.status === 'rejected' && (
                                                    <button
                                                        onClick={() => handleRequestAgain(course.id)}
                                                        className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors"
                                                    >
                                                        Request Again
                                                    </button>
                                                )}
                                                {request.status === 'pending' && (
                                                    <span className="inline-flex items-center text-sm text-yellow-700">
                                                        <svg className="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clipRule="evenodd" />
                                                        </svg>
                                                        Waiting for review
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    <div className="rounded-lg bg-white p-12 text-center shadow">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No requests found</h3>
                        <p className="mt-1 text-sm text-gray-500">
                            {status !== 'all'
                                ? `No requests with "${status}" status.`
                                : 'You have not made any enrollment requests yet.'}
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
                <Pagination links={requests?.links} />
            </div>
        </>
    );
}
