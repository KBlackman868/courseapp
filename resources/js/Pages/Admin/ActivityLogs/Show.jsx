import { Head, Link, usePage } from '@inertiajs/react';

function SeverityBadge({ severity }) {
    const styles = {
        info: 'bg-blue-100 text-blue-800',
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        error: 'bg-red-100 text-red-800',
        critical: 'bg-red-200 text-red-900',
    };

    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${styles[severity] || 'bg-gray-100 text-gray-800'}`}>
            {severity ? severity.charAt(0).toUpperCase() + severity.slice(1) : 'Unknown'}
        </span>
    );
}

function StatusBadge({ status }) {
    const styles = {
        success: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
        pending: 'bg-yellow-100 text-yellow-800',
    };

    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${styles[status] || 'bg-gray-100 text-gray-800'}`}>
            {status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown'}
        </span>
    );
}

export default function ActivityLogShow({ log }) {
    const { flash } = usePage().props;

    const formatDateTime = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    };

    return (
        <>
            <Head title={`Activity Log #${log.id}`} />

            <div className="space-y-6">
                {/* Back Link */}
                <div>
                    <Link
                        href="/admin/activity-logs"
                        className="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        <svg className="mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Back to Activity Logs
                    </Link>
                </div>

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

                {/* Page Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Activity Log #{log.id}</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Detailed view of this activity log entry.
                    </p>
                </div>

                {/* Detail Card */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:p-6">
                        <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Action</dt>
                                <dd className="mt-1">
                                    <span className="inline-flex items-center px-2.5 py-1 rounded text-sm font-medium bg-gray-100 text-gray-800">
                                        {log.action}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">User</dt>
                                <dd className="mt-1 text-sm text-gray-900">{log.user_name || '-'}</dd>
                            </div>
                            <div className="sm:col-span-2">
                                <dt className="text-sm font-medium text-gray-500">Description</dt>
                                <dd className="mt-1 text-sm text-gray-900">{log.description || '-'}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Severity</dt>
                                <dd className="mt-1">
                                    <SeverityBadge severity={log.severity} />
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Status</dt>
                                <dd className="mt-1">
                                    <StatusBadge status={log.status} />
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">IP Address</dt>
                                <dd className="mt-1 text-sm text-gray-900 font-mono">{log.ip_address || '-'}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">URL</dt>
                                <dd className="mt-1 text-sm text-gray-900 font-mono break-all">{log.url || '-'}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Timestamp</dt>
                                <dd className="mt-1 text-sm text-gray-900">{formatDateTime(log.created_at)}</dd>
                            </div>
                            {log.metadata && Object.keys(log.metadata).length > 0 && (
                                <div className="sm:col-span-2">
                                    <dt className="text-sm font-medium text-gray-500">Metadata</dt>
                                    <dd className="mt-1">
                                        <pre className="rounded-md bg-gray-50 p-4 text-sm text-gray-800 overflow-x-auto">
                                            {JSON.stringify(log.metadata, null, 2)}
                                        </pre>
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
