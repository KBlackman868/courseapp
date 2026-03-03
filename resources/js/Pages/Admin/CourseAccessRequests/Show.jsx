import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function StatusBadge({ status }) {
    const styles = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        failed: 'bg-orange-100 text-orange-800',
        revoked: 'bg-gray-100 text-gray-800',
    };

    const labels = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected',
        failed: 'Failed',
        revoked: 'Revoked',
    };

    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${styles[status] || 'bg-gray-100 text-gray-800'}`}>
            {labels[status] || status}
        </span>
    );
}

function SyncStatusBadge({ synced, syncError }) {
    if (syncError) {
        return (
            <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <svg className="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" />
                </svg>
                Sync Failed
            </span>
        );
    }
    if (synced) {
        return (
            <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <svg className="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                </svg>
                Synced
            </span>
        );
    }
    return (
        <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            <svg className="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clipRule="evenodd" />
            </svg>
            Pending Sync
        </span>
    );
}

export default function CourseAccessRequestShow({ accessRequest }) {
    const { flash } = usePage().props;
    const [processing, setProcessing] = useState(false);

    const formatDateTime = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const handleApprove = () => {
        setProcessing(true);
        router.post(`/admin/course-access-requests/${accessRequest.id}/approve`, {}, {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
        });
    };

    const handleReject = () => {
        setProcessing(true);
        router.post(`/admin/course-access-requests/${accessRequest.id}/reject`, {}, {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title={`Course Access Request #${accessRequest.id}`} />

            <div className="space-y-6">
                {/* Back Link */}
                <div>
                    <Link
                        href="/admin/course-access-requests"
                        className="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        <svg className="mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Back to Course Access Requests
                    </Link>
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

                {/* Page Header with Actions */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Course Access Request</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Review the details of this course enrollment request.
                        </p>
                    </div>
                    {accessRequest.status === 'pending' && (
                        <div className="mt-4 sm:mt-0 flex gap-3">
                            <button
                                onClick={handleApprove}
                                disabled={processing}
                                className="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {processing ? 'Processing...' : 'Approve'}
                            </button>
                            <button
                                onClick={handleReject}
                                disabled={processing}
                                className="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {processing ? 'Processing...' : 'Reject'}
                            </button>
                        </div>
                    )}
                </div>

                {/* User Information */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">User Information</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {accessRequest.user
                                        ? `${accessRequest.user.first_name} ${accessRequest.user.last_name}`
                                        : '-'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Email</dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {accessRequest.user?.email || '-'}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {/* Course Information */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Course Information</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Course Title</dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {accessRequest.course?.title || '-'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Enrollment Type</dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {accessRequest.course?.enrollment_type
                                        ? accessRequest.course.enrollment_type.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())
                                        : '-'}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {/* Request Details */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Request Details</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Status</dt>
                                <dd className="mt-1">
                                    <StatusBadge status={accessRequest.status} />
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Request Date</dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {formatDateTime(accessRequest.created_at || accessRequest.requested_at)}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Moodle Sync Status</dt>
                                <dd className="mt-1">
                                    <SyncStatusBadge
                                        synced={accessRequest.moodle_synced}
                                        syncError={accessRequest.moodle_sync_error}
                                    />
                                </dd>
                            </div>
                            {accessRequest.approver && (
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Reviewed By</dt>
                                    <dd className="mt-1 text-sm text-gray-900">
                                        {accessRequest.approver.first_name} {accessRequest.approver.last_name}
                                    </dd>
                                </div>
                            )}
                            {accessRequest.admin_notes && (
                                <div className="sm:col-span-2">
                                    <dt className="text-sm font-medium text-gray-500">Admin Notes</dt>
                                    <dd className="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                        {accessRequest.admin_notes}
                                    </dd>
                                </div>
                            )}
                            {accessRequest.rejection_reason && (
                                <div className="sm:col-span-2">
                                    <dt className="text-sm font-medium text-gray-500">Rejection Reason</dt>
                                    <dd className="mt-1 text-sm text-red-700 bg-red-50 rounded-md p-3">
                                        {accessRequest.rejection_reason}
                                    </dd>
                                </div>
                            )}
                        </dl>
                    </div>
                </div>
            </div>
        </>
    );
}
