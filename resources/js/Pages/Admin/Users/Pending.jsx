import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function StatusBadge({ status }) {
    const styles = {
        pending: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        denied: 'bg-red-100 text-red-800',
    };

    const labels = {
        pending: 'Pending',
        active: 'Active',
        inactive: 'Inactive',
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

export default function Pending({ users, status: currentStatus = 'pending', counts = {} }) {
    const { flash } = usePage().props;
    const [processing, setProcessing] = useState(null);

    const tabs = [
        { key: 'pending', label: 'Pending Approval', count: counts.pending || 0 },
        { key: 'active', label: 'Active', count: counts.active || 0 },
        { key: 'inactive', label: 'Inactive/Denied', count: counts.inactive || 0 },
    ];

    const switchTab = (tabKey) => {
        router.get('/admin/users/pending', { status: tabKey }, { preserveState: true });
    };

    const handleApprove = (id) => {
        setProcessing(id);
        router.post(`/admin/users/${id}/approve`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleDeny = (id) => {
        setProcessing(id);
        router.post(`/admin/users/${id}/deny`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleDeactivate = (id) => {
        if (!confirm('Are you sure you want to deactivate this user?')) return;
        setProcessing(id);
        router.post(`/admin/users/${id}/deactivate`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleReactivate = (id) => {
        setProcessing(id);
        router.post(`/admin/users/${id}/reactivate`, {}, {
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

    const userItems = users?.data || [];

    return (
        <>
            <Head title="User Approvals" />

            <div className="space-y-6">
                {/* Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">External User Approvals</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Manage external user registration requests and account statuses.
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

                {/* Status Tabs */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8">
                        {tabs.map((tab) => (
                            <button
                                key={tab.key}
                                onClick={() => switchTab(tab.key)}
                                className={`whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium ${
                                    currentStatus === tab.key
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                }`}
                            >
                                {tab.label}
                                <span
                                    className={`ml-2 rounded-full px-2 py-0.5 text-xs ${
                                        currentStatus === tab.key
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

                {/* Users Table */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Organization
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Registration Date
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
                                {userItems.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-6 py-16 text-center">
                                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                There are no users with this status at the moment.
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    userItems.map((user) => (
                                        <tr key={user.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 flex-shrink-0">
                                                        <div className="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <span className="text-sm font-medium text-indigo-700">
                                                                {user.name?.charAt(0)?.toUpperCase() || '?'}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {user.name}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {user.email}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {user.organization || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {formatDate(user.created_at)}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge status={user.status || currentStatus} />
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div className="flex items-center justify-end gap-2">
                                                    {currentStatus === 'pending' && (
                                                        <>
                                                            <button
                                                                onClick={() => handleApprove(user.id)}
                                                                disabled={processing === user.id}
                                                                className="inline-flex items-center rounded-md bg-green-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                {processing === user.id ? 'Processing...' : 'Approve'}
                                                            </button>
                                                            <button
                                                                onClick={() => handleDeny(user.id)}
                                                                disabled={processing === user.id}
                                                                className="inline-flex items-center rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                {processing === user.id ? 'Processing...' : 'Deny'}
                                                            </button>
                                                        </>
                                                    )}
                                                    {currentStatus === 'active' && (
                                                        <button
                                                            onClick={() => handleDeactivate(user.id)}
                                                            disabled={processing === user.id}
                                                            className="inline-flex items-center rounded-md bg-yellow-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                        >
                                                            {processing === user.id ? 'Processing...' : 'Deactivate'}
                                                        </button>
                                                    )}
                                                    {currentStatus === 'inactive' && (
                                                        <button
                                                            onClick={() => handleReactivate(user.id)}
                                                            disabled={processing === user.id}
                                                            className="inline-flex items-center rounded-md bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                        >
                                                            {processing === user.id ? 'Processing...' : 'Reactivate'}
                                                        </button>
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
                <Pagination links={users?.links} />
            </div>
        </>
    );
}
