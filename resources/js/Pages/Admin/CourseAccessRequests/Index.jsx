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
        failed: 'bg-orange-100 text-orange-800',
    };
    const labels = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected',
        failed: 'Failed',
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
            Pending
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

function ReasonModal({ isOpen, onClose, onConfirm, processing, title, description, label, confirmLabel, confirmColor = 'bg-red-600 hover:bg-red-700' }) {
    const [reason, setReason] = useState('');
    const [error, setError] = useState('');

    if (!isOpen) return null;

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!reason.trim()) {
            setError(`Please provide a ${label.toLowerCase()}.`);
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
                        <h3 className="mt-3 text-center text-lg font-semibold text-gray-900">{title}</h3>
                        {description && <p className="mt-1 text-center text-sm text-gray-500">{description}</p>}
                    </div>
                    <form onSubmit={handleSubmit}>
                        <div className="mb-4">
                            <label htmlFor="modal_reason" className="block text-sm font-medium text-gray-700 mb-1.5">
                                {label} <span className="text-red-500">*</span>
                            </label>
                            <textarea
                                id="modal_reason"
                                rows={4}
                                value={reason}
                                onChange={(e) => { setReason(e.target.value); setError(''); }}
                                placeholder={`Enter ${label.toLowerCase()}...`}
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
                                className={`rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors disabled:opacity-50 ${confirmColor}`}
                            >
                                {processing ? 'Processing...' : confirmLabel}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default function CourseAccessRequestsIndex({
    requests,
    courses = [],
    counts = {},
    status: currentStatus = '',
    courseId: currentCourseId = '',
    search: initialSearch = '',
}) {
    const { flash } = usePage().props;
    const [search, setSearch] = useState(initialSearch);
    const [selectedRequests, setSelectedRequests] = useState([]);
    const [processing, setProcessing] = useState(null);
    const [modal, setModal] = useState({ open: false, type: null, requestId: null, userName: '', courseName: '' });

    // Normalize courses - handle both array and object formats
    const courseList = Array.isArray(courses) ? courses : Object.entries(courses).map(([id, title]) => ({ id, title }));

    const tabs = [
        { key: 'pending', label: 'Pending', count: counts.pending || 0 },
        { key: 'approved', label: 'Approved', count: counts.approved || 0 },
        { key: 'rejected', label: 'Rejected', count: counts.rejected || 0 },
        { key: 'failed', label: 'Failed', count: counts.failed || 0 },
        { key: '', label: 'All', count: counts.all || 0 },
    ];

    const debouncedSearch = useCallback(
        debounce((value) => {
            router.get('/admin/course-access-requests', {
                search: value || undefined,
                status: currentStatus || undefined,
                course_id: currentCourseId || undefined,
            }, { preserveState: true, replace: true });
        }, 300),
        [currentStatus, currentCourseId]
    );

    const handleSearchChange = (e) => {
        const value = e.target.value;
        setSearch(value);
        debouncedSearch(value);
    };

    const handleTabChange = (statusKey) => {
        router.get('/admin/course-access-requests', {
            status: statusKey || undefined,
            course_id: currentCourseId || undefined,
            search: search || undefined,
        }, { preserveState: true, replace: true });
    };

    const handleCourseFilter = (e) => {
        const value = e.target.value;
        router.get('/admin/course-access-requests', {
            status: currentStatus || undefined,
            course_id: value || undefined,
            search: search || undefined,
        }, { preserveState: true, replace: true });
    };

    const handleApprove = (requestId) => {
        setProcessing(requestId);
        router.post(`/admin/course-access-requests/${requestId}/approve`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleRejectClick = (requestId, userName, courseName) => {
        setModal({ open: true, type: 'reject', requestId, userName, courseName });
    };

    const handleRevokeClick = (requestId, userName, courseName) => {
        setModal({ open: true, type: 'revoke', requestId, userName, courseName });
    };

    const handleModalConfirm = (reason) => {
        const { requestId, type } = modal;
        setProcessing(requestId);

        const url = type === 'reject'
            ? `/admin/course-access-requests/${requestId}/reject`
            : `/admin/course-access-requests/${requestId}/revoke`;

        const data = type === 'reject'
            ? { rejection_reason: reason }
            : { reason };

        router.post(url, data, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                setProcessing(null);
                setModal({ open: false, type: null, requestId: null, userName: '', courseName: '' });
            },
        });
    };

    const handleRetrySync = (requestId) => {
        setProcessing(requestId);
        router.post(`/admin/course-access-requests/${requestId}/retry-sync`, {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleBulkApprove = () => {
        if (selectedRequests.length === 0) return;
        if (!confirm(`Approve ${selectedRequests.length} course access request(s)?`)) return;

        setProcessing('bulk');
        const promises = selectedRequests.map((requestId) =>
            new Promise((resolve) => {
                router.post(`/admin/course-access-requests/${requestId}/approve`, {}, {
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

    const isRejectModal = modal.type === 'reject';

    return (
        <>
            <Head title="Course Access Requests" />

            <ReasonModal
                isOpen={modal.open}
                onClose={() => setModal({ open: false, type: null, requestId: null, userName: '', courseName: '' })}
                onConfirm={handleModalConfirm}
                processing={processing === modal.requestId}
                title={isRejectModal ? 'Reject Course Access' : 'Revoke Course Access'}
                description={modal.userName ? `${isRejectModal ? 'Rejecting' : 'Revoking'} access for ${modal.userName}${modal.courseName ? ` to "${modal.courseName}"` : ''}` : ''}
                label={isRejectModal ? 'Rejection Reason' : 'Revocation Reason'}
                confirmLabel={isRejectModal ? 'Confirm Rejection' : 'Confirm Revocation'}
                confirmColor={isRejectModal ? 'bg-red-600 hover:bg-red-700' : 'bg-yellow-600 hover:bg-yellow-700'}
            />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Course Access Requests</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Review and manage course enrollment requests.
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
                    <nav className="-mb-px flex space-x-8 overflow-x-auto">
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
                                <span className={`ml-2 rounded-full px-2 py-0.5 text-xs ${
                                    currentStatus === tab.key ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600'
                                }`}>
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
                            placeholder="Search by user or course..."
                            value={search}
                            onChange={handleSearchChange}
                            className="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <select
                        value={currentCourseId}
                        onChange={handleCourseFilter}
                        className="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Courses</option>
                        {courseList.map((course) => (
                            <option key={course.id} value={course.id}>{course.title}</option>
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
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moodle Sync</th>
                                    <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {requestList.length === 0 ? (
                                    <tr>
                                        <td colSpan={currentStatus === 'pending' ? 8 : 7} className="px-6 py-12 text-center">
                                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No course access requests found</h3>
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
                                                <div className="flex items-center">
                                                    <img
                                                        className="h-8 w-8 rounded-full object-cover"
                                                        src={request.user?.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent((request.user?.first_name || '') + ' ' + (request.user?.last_name || ''))}&background=6366f1&color=fff&size=32`}
                                                        alt=""
                                                        onError={(e) => { e.target.onerror = null; e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent((request.user?.first_name || '') + ' ' + (request.user?.last_name || ''))}&background=6366f1&color=fff&size=32`; }}
                                                    />
                                                    <div className="ml-3">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {request.user?.first_name} {request.user?.last_name}
                                                        </div>
                                                        <div className="text-sm text-gray-500">{request.user?.email}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{request.course?.title || '-'}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{formatDate(request.requested_at || request.created_at)}</td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge status={request.status} />
                                                {request.status === 'rejected' && request.rejection_reason && (
                                                    <p className="mt-1 text-xs text-red-500 max-w-[200px] truncate" title={request.rejection_reason}>
                                                        {request.rejection_reason}
                                                    </p>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <SyncStatusBadge synced={request.moodle_synced} syncError={request.moodle_sync_error} />
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
                                                                onClick={() => handleRejectClick(request.id, `${request.user?.first_name} ${request.user?.last_name}`, request.course?.title)}
                                                                disabled={processing === request.id}
                                                                className="inline-flex items-center rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                                            >
                                                                Reject
                                                            </button>
                                                        </>
                                                    )}
                                                    {request.status === 'approved' && (
                                                        <button
                                                            onClick={() => handleRevokeClick(request.id, `${request.user?.first_name} ${request.user?.last_name}`, request.course?.title)}
                                                            disabled={processing === request.id}
                                                            className="inline-flex items-center rounded-md bg-yellow-50 px-2.5 py-1.5 text-xs font-medium text-yellow-800 hover:bg-yellow-100 disabled:opacity-50 transition-colors"
                                                        >
                                                            Revoke
                                                        </button>
                                                    )}
                                                    {(request.status === 'failed' || request.moodle_sync_error) && (
                                                        <button
                                                            onClick={() => handleRetrySync(request.id)}
                                                            disabled={processing === request.id}
                                                            className="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1.5 text-xs font-medium text-indigo-800 hover:bg-indigo-100 disabled:opacity-50 transition-colors"
                                                        >
                                                            {processing === request.id ? 'Retrying...' : 'Retry Sync'}
                                                        </button>
                                                    )}
                                                    {request.status === 'rejected' && (
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
