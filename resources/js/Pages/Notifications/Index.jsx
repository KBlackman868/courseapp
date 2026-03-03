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

function NotificationIcon({ type }) {
    switch (type) {
        case 'enrollment_approved':
            return (
                <div className="flex-shrink-0 rounded-full bg-green-100 p-2">
                    <svg className="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                    </svg>
                </div>
            );
        case 'enrollment_denied':
            return (
                <div className="flex-shrink-0 rounded-full bg-red-100 p-2">
                    <svg className="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" />
                    </svg>
                </div>
            );
        case 'enrollment_request':
            return (
                <div className="flex-shrink-0 rounded-full bg-blue-100 p-2">
                    <svg className="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" />
                    </svg>
                </div>
            );
        default:
            return (
                <div className="flex-shrink-0 rounded-full bg-gray-100 p-2">
                    <svg className="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M10 2a6 6 0 00-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 00.515 1.076 32.91 32.91 0 003.256.508 3.5 3.5 0 006.972 0 32.903 32.903 0 003.256-.508.75.75 0 00.515-1.076A11.448 11.448 0 0116 8a6 6 0 00-6-6zM8.05 14.943a33.54 33.54 0 003.9 0 2 2 0 01-3.9 0z" clipRule="evenodd" />
                    </svg>
                </div>
            );
    }
}

function timeAgo(dateString) {
    if (!dateString) return '';
    const now = new Date();
    const date = new Date(dateString);
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Just now';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
}

export default function Index({ notifications, filter = 'all', unreadCount = 0 }) {
    const { flash } = usePage().props;

    const handleMarkAsRead = (id) => {
        router.post(`/notifications/${id}/mark-as-read`, {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleMarkAllRead = () => {
        router.post('/notifications/mark-all-read', {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleDelete = (id) => {
        router.delete(`/notifications/${id}`, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleFilter = (newFilter) => {
        router.get('/notifications', { filter: newFilter }, { preserveState: true });
    };

    const tabs = [
        { key: 'all', label: 'All' },
        { key: 'unread', label: 'Unread' },
    ];

    return (
        <>
            <Head title="Notifications" />

            <div className="space-y-6">
                {/* Flash Messages */}
                {flash?.success && (
                    <div className="rounded-md bg-green-50 p-4">
                        <p className="text-sm font-medium text-green-800">{flash.success}</p>
                    </div>
                )}

                {/* Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Notifications</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            {unreadCount > 0
                                ? `You have ${unreadCount} unread notification${unreadCount !== 1 ? 's' : ''}`
                                : 'You are all caught up'}
                        </p>
                    </div>
                    {unreadCount > 0 && (
                        <button
                            onClick={handleMarkAllRead}
                            className="mt-4 sm:mt-0 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                        >
                            <svg className="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                            </svg>
                            Mark All as Read
                        </button>
                    )}
                </div>

                {/* Filter Tabs */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8">
                        {tabs.map((tab) => (
                            <button
                                key={tab.key}
                                onClick={() => handleFilter(tab.key)}
                                className={`whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium ${
                                    filter === tab.key
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                }`}
                            >
                                {tab.label}
                                {tab.key === 'unread' && unreadCount > 0 && (
                                    <span className="ml-2 rounded-full bg-indigo-100 px-2 py-0.5 text-xs text-indigo-600">
                                        {unreadCount}
                                    </span>
                                )}
                            </button>
                        ))}
                    </nav>
                </div>

                {/* Notification List */}
                <div className="space-y-3">
                    {notifications?.data && notifications.data.length > 0 ? (
                        notifications.data.map((notification) => (
                            <div
                                key={notification.id}
                                className={`rounded-lg bg-white p-4 shadow transition-colors ${
                                    !notification.read_at ? 'border-l-4 border-indigo-500' : ''
                                }`}
                            >
                                <div className="flex items-start gap-4">
                                    <NotificationIcon type={notification.type} />

                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-start justify-between">
                                            <div>
                                                <p className={`text-sm ${!notification.read_at ? 'font-semibold text-gray-900' : 'font-medium text-gray-700'}`}>
                                                    {notification.title || notification.data?.title || 'Notification'}
                                                </p>
                                                <p className="mt-1 text-sm text-gray-500">
                                                    {notification.message || notification.data?.message || ''}
                                                </p>
                                            </div>
                                            <div className="flex items-center gap-2 ml-4">
                                                {!notification.read_at && (
                                                    <span className="inline-block h-2 w-2 rounded-full bg-indigo-500 flex-shrink-0" />
                                                )}
                                                <span className="text-xs text-gray-400 whitespace-nowrap">
                                                    {timeAgo(notification.created_at)}
                                                </span>
                                            </div>
                                        </div>

                                        <div className="mt-3 flex items-center gap-3">
                                            {!notification.read_at && (
                                                <button
                                                    onClick={() => handleMarkAsRead(notification.id)}
                                                    className="text-xs font-medium text-indigo-600 hover:text-indigo-500"
                                                >
                                                    Mark as read
                                                </button>
                                            )}
                                            <button
                                                onClick={() => handleDelete(notification.id)}
                                                className="text-xs font-medium text-red-600 hover:text-red-500"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="rounded-lg bg-white p-12 text-center shadow">
                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <h3 className="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                            <p className="mt-1 text-sm text-gray-500">
                                {filter === 'unread'
                                    ? 'You have no unread notifications.'
                                    : 'You have no notifications yet.'}
                            </p>
                        </div>
                    )}
                </div>

                {/* Pagination */}
                <Pagination links={notifications?.links} />
            </div>
        </>
    );
}
