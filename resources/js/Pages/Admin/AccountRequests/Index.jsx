import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState, useCallback } from 'react';

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
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
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${styles[status] || 'bg-gray-100 text-gray-800'}`}>
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

function RejectModal({ isOpen, onClose, onConfirm, processing, requestName }) {
    const [reason, setReason] = useState('');
    const [error, setError] = useState('');

    if (!isOpen) return null;

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!reason.trim()) {
            setError('Please provide a rejection reason.');
            return;
        }
        setError('');
        onConfirm(reason.trim());
    };

    const handleClose = () => {
        setReason('');
        setError('');
        onClose();
    };

    return (
        <div className="fixed inset-0 z-50 overflow-y-auto">
            <div className="flex min-h-full items-center justify-center p-4">
                <div className="fixed inset-0 bg-gray-500/75 transition-opacity" onClick={handleClose} />
                <div className="relative w-full max-w-md transform rounded-xl bg-white p-6 shadow-xl transition-all">
                    <div className="mb-4">
                        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                            <svg className="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <h3 className="mt-3 text-center text-lg font-semibold text-gray-900">
                            Reject Account Request
                        </h3>
                        <p className="mt-1 text-center text-sm text-gray-500">
                            {requestName ? `Rejecting request from ${requestName}` : 'Please provide a reason for rejection.'}
                        </p>
                    </div>
                    <form onSubmit={handleSubmit}>
                        <div className="mb-4">
                            <label htmlFor="rejection_reason" className="block text-sm font-medium text-gray-700 mb-1.5">
                                Rejection Reason <span className="text-red-500">*</span>
                            </label>
                            <textarea
                                id="rejection_reason"
                                rows={4}
                                value={reason}
                                onChange={(e) => { setReason(e.target.value); setError(''); }}
                                placeholder="Explain why this request is being rejected..."
                                className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"
                                autoFocus
                            />
                            {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
                        </div>
                        <div className="flex gap-3 justify-end">
                            <button
                                type="button"
                                onClick={handleClose}
                                disabled={processing}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors disabled:opacity-50"
                            >
                                {processing ? 'Rejecting...' : 'Confirm Rejection'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default function AccountRequestsIndex({
    requests,
    departments = [],
    counts = {},
    status: currentStatus = '',
    department: currentDepartment = '',
    search: initialSearch = '',
}) {
    const { flash } = usePage().props;
    const [search, setSearch] = useState(initialSearch);
    const [selectedRequests, setSelectedRequests] = useState([]);
    const [processing, setProcessing] = useState(null);
    const [rejectModal, setRejectModal] = useState({ open: false, requestId: null, requestName: '' });

    const tabs = [
        { key: 'pending', label: 'Pending', count: counts.pending || 0 },
        { key: 'approved', label: 'Approved', count: counts.approved || 0 },
        { key: 'rejected', label: 'Denied', count: counts.rejected || 0 },
        { key: '', label: 'All', count: counts.all || 0 },
    ];

    const debouncedSearch = useCallback(
        debounce((value) => {
            router.get('/admin/account-requests', {
                search: value || undefined,
                status: currentStatus || undefined,
                department: currentDepartment || undefined,
            }, { preserveState: true, replace: true });
        }, 300),
        [currentStatus, currentDepartment]
    );

    const handleSearchChange = (e) => {
        const value = e.target.value;
        setSearch(value);
        debouncedSearch(value);
    };

    const handleTabChange = (statusKey) => {
        router.get('/admin/account-requests', {
            status: statusKey || undefined,
            department: currentDepartment || undefined,
            search: search || undefined,
        }, { preserveState: true, replace: true });
    };

    const handleDepartmentFilter = (e) => {
        const value = e.target.value;
        router.get('/admin/account-requests', {
            status: currentStatus || undefined,
            department: value || undefined,
            search: search || undefined,
        }, { preserveState: true, replace: true });
    };

    const handleApprove = (requestId) => {
        setProcessing(requestId);
        router.post(`/admin/account-requests/${requestId}/approve`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleRejectClick = (requestId, requestName) => {
        setRejectModal({ open: true, requestId, requestName });
    };

    const handleRejectConfirm = (reason) => {
        const { requestId } = rejectModal;
        setProcessing(requestId);
        router.post(`/admin/account-requests/${requestId}/reject`, {
            rejection_reason: reason,
        }, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                setProcessing(null);
                setRejectModal({ open: false, requestId: null, requestName: '' });
            },
        });
    };

    const handleBulkApprove = () => {
        if (selectedRequests.length === 0) return;
        if (!confirm(`Approve ${selectedRequests.length} account request(s)?`)) return;

        setProcessing('bulk');
        const promises = selectedRequests.map((requestId) =>
            new Promise((resolve) => {
                router.post(`/admin/account-requests/${requestId}/approve`, {}, {
                    preserveState: true,
                    preserveScroll: true,
                    onFinish: resolve,
                });
            })
        );

        Promise.all(promises).then(() => {
            setProcessing(null);
            setSelectedRequests([]);
        });
    };

    const toggleSelectAll = () => {
        const pendingList = requestList.filter((r) => r.status === 'pending');
        if (selectedRequests.length === pendingList.length && pendingList.length > 0) {
            setSelectedRequests([]);
        } else {
            setSelectedRequests(pendingList.map((r) => r.id));
        }
    };

    const toggleRequest = (requestId) => {
        setSelectedRequests((prev) =>
            prev.includes(requestId)
                ? prev.filter((id) => id !== requestId)
                : [...prev, requestId]
        );
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const requestList = requests?.data || [];
    const pendingList = requestList.filter((r) => r.status === 'pending');
    const isAllPendingSelected = pendingList.length > 0 && selectedRequests.length === pendingList.length;

    return (
        <>
            <Head title="Account Requests" />

            <RejectModal
                isOpen={rejectModal.open}
                onClose={() => setRejectModal({ open: false, requestId: null, requestName: '' })}
                onConfirm={handleRejectConfirm}
                processing={processing === rejectModal.requestId}
                requestName={rejectModal.requestName}
            />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Account Requests</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Review and manage account registration requests.
                        </p>
                    </div>
                    {selectedRequests.length > 0 && (
                        <div className="mt-4 sm:mt-0">
                            <button
                                onClick={handleBulkApprove}
                                disabled={processing === 'bulk'}
                                className="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 disabled:opacity-50 transition-colors"
                            >
                                {processing === 'bulk' ? 'Approving...' : `Approve Selected (${selectedRequests.length})`}
                            </button>
                        </div>
                    )}
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
                                onClick={() => handleTabChange(tab.key)}
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

                {/* Filters */}
                <div className="flex items-center gap-4 flex-wrap">
                    <div className="relative flex-1 max-w-md">
                        <svg className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input
                            type="text"
                            placeholder="Search by name or email..."
                            value={search}
                            onChange={handleSearchChange}
                            className="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <select
                        value={currentDepartment}
                        onChange={handleDepartmentFilter}
                        className="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Departments</option>
                        {departments.map((dept) => (
                            <option key={dept} value={dept}>
                                {dept}
                            </option>
                        ))}
                    </select>
                </div>

                {/* Requests Table */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    {currentStatus === 'pending' && (
                                        <th scope="col" className="px-6 py-3 text-left">
                                            <input
                                                type="checkbox"
                                                checked={isAllPendingSelected}
                                                onChange={toggleSelectAll}
                                                className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </th>
                                    )}
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {requestList.length === 0 ? (
                                    <tr>
                                        <td colSpan={currentStatus === 'pending' ? 8 : 7} className="px-6 py-12 text-center">
                                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                            </svg>
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No account requests found</h3>
                                            <p className="mt-1 text-sm text-gray-500">No requests match your current filters.</p>
                                        </td>
                                    </tr>
                                ) : (
                                    requestList.map((request) => (
                                        <tr key={request.id} className="hover:bg-gray-50 transition-colors">
                                            {currentStatus === 'pending' && (
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    {request.status === 'pending' && (
                                                        <input
                                                            type="checkbox"
                                                            checked={selectedRequests.includes(request.id)}
                                                            onChange={() => toggleRequest(request.id)}
                                                            className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                        />
                                                    )}
                                                </td>
                                            )}
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {request.first_name} {request.last_name}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{request.email}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{request.department || '-'}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{formatDate(request.created_at)}</td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge status={request.status} />
                                                {request.status === 'rejected' && request.rejection_reason && (
                                                    <p className="mt-1 text-xs text-red-500 max-w-[200px] truncate" title={request.rejection_reason}>
                                                        {request.rejection_reason}
                                                    </p>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div className="flex items-center justify-end gap-2">
                                                    {request.status === 'pending' && (
                                                        <>
                                                            <button
                                                                onClick={() => handleApprove(request.id)}
                                                                disabled={processing === request.id}
                                                                className="inline-flex items-center rounded-md bg-green-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                {processing === request.id ? 'Processing...' : 'Approve'}
                                                            </button>
                                                            <button
                                                                onClick={() => handleRejectClick(request.id, `${request.first_name} ${request.last_name}`)}
                                                                disabled={processing === request.id}
                                                                className="inline-flex items-center rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                Reject
                                                            </button>
                                                        </>
                                                    )}
                                                    {request.status !== 'pending' && (
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
                <Pagination links={requests?.links} />
            </div>
        </>
    );
}
