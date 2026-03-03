import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function StatusBadge({ status }) {
    const styles = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        denied: 'bg-red-100 text-red-800',
    };

    const labels = {
        pending: 'Pending',
        approved: 'Approved',
        denied: 'Denied',
    };

    return (
        <span
            className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                styles[status] || 'bg-gray-100 text-gray-800'
            }`}
        >
            {labels[status] || status}
        </span>
    );
}

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

export default function Index({ enrollments }) {
    const { flash } = usePage().props;
    const [activeTab, setActiveTab] = useState('pending');
    const [processing, setProcessing] = useState(null);

    const tabs = [
        { key: 'pending', label: 'Pending' },
        { key: 'approved', label: 'Approved' },
        { key: 'denied', label: 'Denied' },
    ];

    const filteredEnrollments = (enrollments?.data || []).filter(
        (enrollment) => enrollment.status === activeTab
    );

    const handleApprove = (id) => {
        setProcessing(id);
        router.post(`/admin/enrollments/${id}/approve`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleDeny = (id) => {
        setProcessing(id);
        router.post(`/admin/enrollments/${id}/deny`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleSyncToMoodle = (id) => {
        setProcessing(id);
        router.post(`/admin/enrollments/${id}/sync-moodle`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleUnenroll = (id) => {
        if (!confirm('Are you sure you want to unenroll this student?')) return;
        setProcessing(id);
        router.post(`/admin/enrollments/${id}/unenroll`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    return (
        <>
            <Head title="Enrollment Management" />

            <div className="space-y-6">
                {/* Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Enrollment Management</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Review and manage student enrollment requests.
                        </p>
                    </div>
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

                {/* Status Filter Tabs */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8">
                        {tabs.map((tab) => (
                            <button
                                key={tab.key}
                                onClick={() => setActiveTab(tab.key)}
                                className={`whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium ${
                                    activeTab === tab.key
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                }`}
                            >
                                {tab.label}
                                <span
                                    className={`ml-2 rounded-full px-2 py-0.5 text-xs ${
                                        activeTab === tab.key
                                            ? 'bg-indigo-100 text-indigo-600'
                                            : 'bg-gray-100 text-gray-600'
                                    }`}
                                >
                                    {(enrollments?.data || []).filter((e) => e.status === tab.key).length}
                                </span>
                            </button>
                        ))}
                    </nav>
                </div>

                {/* Enrollments Table */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Request Date
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {filteredEnrollments.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-6 py-16 text-center">
                                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No {activeTab} enrollments</h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                There are no enrollments with {activeTab} status at this time.
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    filteredEnrollments.map((enrollment) => (
                                        <tr key={enrollment.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                #{enrollment.id}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {enrollment.user?.name}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {enrollment.user?.email}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {enrollment.course?.title}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {formatDate(enrollment.created_at)}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge status={enrollment.status} />
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div className="flex items-center justify-end gap-2">
                                                    {enrollment.status === 'pending' && (
                                                        <>
                                                            <button
                                                                onClick={() => handleApprove(enrollment.id)}
                                                                disabled={processing === enrollment.id}
                                                                className="inline-flex items-center rounded-md bg-green-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                {processing === enrollment.id ? 'Processing...' : 'Approve'}
                                                            </button>
                                                            <button
                                                                onClick={() => handleDeny(enrollment.id)}
                                                                disabled={processing === enrollment.id}
                                                                className="inline-flex items-center rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                {processing === enrollment.id ? 'Processing...' : 'Deny'}
                                                            </button>
                                                        </>
                                                    )}
                                                    {enrollment.status === 'approved' && (
                                                        <>
                                                            {enrollment.course?.moodle_course_id && (
                                                                <button
                                                                    onClick={() => handleSyncToMoodle(enrollment.id)}
                                                                    disabled={processing === enrollment.id}
                                                                    className="inline-flex items-center rounded-md bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                                >
                                                                    {processing === enrollment.id ? 'Syncing...' : 'Sync to Moodle'}
                                                                </button>
                                                            )}
                                                            <button
                                                                onClick={() => handleUnenroll(enrollment.id)}
                                                                disabled={processing === enrollment.id}
                                                                className="inline-flex items-center rounded-md bg-gray-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                {processing === enrollment.id ? 'Processing...' : 'Unenroll'}
                                                            </button>
                                                        </>
                                                    )}
                                                    {enrollment.status === 'denied' && (
                                                        <span className="text-xs text-gray-400">No actions available</span>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Pagination */}
                <Pagination links={enrollments?.links} />
            </div>
        </>
    );
}
