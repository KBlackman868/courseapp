import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function ProgressBar({ value, max, color = 'indigo' }) {
    const percentage = max > 0 ? Math.round((value / max) * 100) : 0;
    const colorClasses = {
        indigo: 'bg-indigo-600',
        green: 'bg-green-600',
        yellow: 'bg-yellow-500',
        blue: 'bg-blue-600',
    };

    return (
        <div>
            <div className="flex items-center justify-between text-sm mb-1">
                <span className="text-gray-600">{value} of {max} synced</span>
                <span className="font-medium text-gray-900">{percentage}%</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2.5">
                <div
                    className={`h-2.5 rounded-full transition-all duration-500 ${colorClasses[color]}`}
                    style={{ width: `${percentage}%` }}
                />
            </div>
        </div>
    );
}

function StatCard({ title, value, subtitle, children, icon }) {
    return (
        <div className="overflow-hidden rounded-lg bg-white shadow">
            <div className="p-5">
                <div className="flex items-center">
                    <div className="flex-shrink-0">{icon}</div>
                    <div className="ml-5 w-0 flex-1">
                        <dl>
                            <dt className="truncate text-sm font-medium text-gray-500">{title}</dt>
                            <dd className="mt-1">
                                <div className="text-2xl font-semibold text-gray-900">{value}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
                {subtitle && (
                    <div className="mt-3 text-sm text-gray-500">{subtitle}</div>
                )}
                {children && <div className="mt-4">{children}</div>}
            </div>
        </div>
    );
}

export default function Status({ stats = {} }) {
    const { flash } = usePage().props;
    const [connectionStatus, setConnectionStatus] = useState(null);
    const [connectionLoading, setConnectionLoading] = useState(false);
    const [syncingUsers, setSyncingUsers] = useState(false);
    const [importingCourses, setImportingCourses] = useState(false);
    const [processingQueue, setProcessingQueue] = useState(false);

    const {
        total_users = 0,
        moodle_synced_users = 0,
        total_courses = 0,
        moodle_synced_courses = 0,
        pending_enrollments = 0,
    } = stats;

    const testConnection = async () => {
        setConnectionLoading(true);
        setConnectionStatus(null);
        try {
            const response = await fetch('/admin/moodle/test-connection');
            const data = await response.json();
            setConnectionStatus({
                success: data.success ?? response.ok,
                message: data.message || (response.ok ? 'Connection successful' : 'Connection failed'),
            });
        } catch (error) {
            setConnectionStatus({
                success: false,
                message: 'Failed to reach the server. Please check your network connection.',
            });
        } finally {
            setConnectionLoading(false);
        }
    };

    const handleSyncUsers = () => {
        setSyncingUsers(true);
        router.post('/admin/moodle/sync-users', {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setSyncingUsers(false),
        });
    };

    const handleImportCourses = () => {
        setImportingCourses(true);
        router.post('/admin/moodle/import-courses', {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setImportingCourses(false),
        });
    };

    const handleProcessQueue = () => {
        setProcessingQueue(true);
        router.post('/admin/moodle/process-queue', {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setProcessingQueue(false),
        });
    };

    const unsyncedUsers = total_users - moodle_synced_users;
    const unsyncedCourses = total_courses - moodle_synced_courses;

    return (
        <>
            <Head title="Moodle Integration Status" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Moodle Integration Status</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Monitor and manage the Moodle LMS integration.
                    </p>
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

                {/* Connection Status Card */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Connection Status</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                {connectionStatus === null ? (
                                    <>
                                        <div className="h-3 w-3 rounded-full bg-gray-300" />
                                        <span className="text-sm text-gray-500">Not tested yet</span>
                                    </>
                                ) : connectionStatus.success ? (
                                    <>
                                        <div className="h-3 w-3 rounded-full bg-green-500" />
                                        <span className="text-sm font-medium text-green-700">{connectionStatus.message}</span>
                                    </>
                                ) : (
                                    <>
                                        <div className="h-3 w-3 rounded-full bg-red-500" />
                                        <span className="text-sm font-medium text-red-700">{connectionStatus.message}</span>
                                    </>
                                )}
                            </div>
                            <button
                                onClick={testConnection}
                                disabled={connectionLoading}
                                className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {connectionLoading ? (
                                    <>
                                        <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Testing...
                                    </>
                                ) : (
                                    'Test Connection'
                                )}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    {/* Total Users */}
                    <StatCard
                        title="Total Users"
                        value={total_users}
                        icon={
                            <div className="rounded-md bg-indigo-500 p-3">
                                <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                        }
                    >
                        <ProgressBar value={moodle_synced_users} max={total_users} color="indigo" />
                    </StatCard>

                    {/* Total Courses */}
                    <StatCard
                        title="Total Courses"
                        value={total_courses}
                        icon={
                            <div className="rounded-md bg-green-500 p-3">
                                <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                            </div>
                        }
                    >
                        <ProgressBar value={moodle_synced_courses} max={total_courses} color="green" />
                    </StatCard>

                    {/* Pending Enrollments */}
                    <StatCard
                        title="Pending Enrollments"
                        value={pending_enrollments}
                        subtitle="Enrollment requests awaiting Moodle sync"
                        icon={
                            <div className="rounded-md bg-yellow-500 p-3">
                                <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        }
                    />
                </div>

                {/* Quick Actions */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Quick Actions</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            {/* Sync Users */}
                            <div className="rounded-lg border border-gray-200 p-4">
                                <div className="flex items-center gap-3 mb-3">
                                    <div className="rounded-md bg-indigo-100 p-2">
                                        <svg className="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.181-3.183" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-semibold text-gray-900">Sync Unsynced Users</h4>
                                        <p className="text-xs text-gray-500">{unsyncedUsers} user{unsyncedUsers !== 1 ? 's' : ''} pending sync</p>
                                    </div>
                                </div>
                                <button
                                    onClick={handleSyncUsers}
                                    disabled={syncingUsers || unsyncedUsers === 0}
                                    className="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    {syncingUsers ? (
                                        <>
                                            <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                            </svg>
                                            Syncing...
                                        </>
                                    ) : (
                                        'Sync Users'
                                    )}
                                </button>
                            </div>

                            {/* Import Courses */}
                            <div className="rounded-lg border border-gray-200 p-4">
                                <div className="flex items-center gap-3 mb-3">
                                    <div className="rounded-md bg-green-100 p-2">
                                        <svg className="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-semibold text-gray-900">Import Moodle Courses</h4>
                                        <p className="text-xs text-gray-500">{unsyncedCourses} course{unsyncedCourses !== 1 ? 's' : ''} not yet linked</p>
                                    </div>
                                </div>
                                <button
                                    onClick={handleImportCourses}
                                    disabled={importingCourses}
                                    className="w-full inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    {importingCourses ? (
                                        <>
                                            <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                            </svg>
                                            Importing...
                                        </>
                                    ) : (
                                        'Import Courses'
                                    )}
                                </button>
                            </div>

                            {/* Process Queue */}
                            <div className="rounded-lg border border-gray-200 p-4">
                                <div className="flex items-center gap-3 mb-3">
                                    <div className="rounded-md bg-yellow-100 p-2">
                                        <svg className="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-semibold text-gray-900">Process Queue</h4>
                                        <p className="text-xs text-gray-500">{pending_enrollments} enrollment{pending_enrollments !== 1 ? 's' : ''} in queue</p>
                                    </div>
                                </div>
                                <button
                                    onClick={handleProcessQueue}
                                    disabled={processingQueue || pending_enrollments === 0}
                                    className="w-full inline-flex items-center justify-center rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    {processingQueue ? (
                                        <>
                                            <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                            </svg>
                                            Processing...
                                        </>
                                    ) : (
                                        'Process Queue'
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
