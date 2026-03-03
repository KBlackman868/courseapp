import { Head } from '@inertiajs/react';
import { useState } from 'react';

export default function Test() {
    const [connectionResult, setConnectionResult] = useState(null);
    const [connectionLoading, setConnectionLoading] = useState(false);
    const [createUserLoading, setCreateUserLoading] = useState(false);
    const [createUserResult, setCreateUserResult] = useState(null);
    const [syncUserLoading, setSyncUserLoading] = useState(false);
    const [syncUserResult, setSyncUserResult] = useState(null);
    const [syncUserId, setSyncUserId] = useState('');
    const [logs, setLogs] = useState(null);
    const [logsLoading, setLogsLoading] = useState(false);

    const [newUser, setNewUser] = useState({
        firstname: '',
        lastname: '',
        email: '',
        password: '',
    });

    const testConnection = async () => {
        setConnectionLoading(true);
        setConnectionResult(null);
        try {
            const response = await fetch('/admin/moodle/test/connection');
            const data = await response.json();
            setConnectionResult(data);
        } catch (error) {
            setConnectionResult({ status: 'error', message: 'Network error: ' + error.message });
        } finally {
            setConnectionLoading(false);
        }
    };

    const createTestUser = async (e) => {
        e.preventDefault();
        setCreateUserLoading(true);
        setCreateUserResult(null);
        try {
            const response = await fetch('/admin/moodle/test/create-user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
                body: JSON.stringify(newUser),
            });
            const data = await response.json();
            setCreateUserResult(data);
            if (data.status === 'success') {
                setNewUser({ firstname: '', lastname: '', email: '', password: '' });
            }
        } catch (error) {
            setCreateUserResult({ status: 'error', message: 'Network error: ' + error.message });
        } finally {
            setCreateUserLoading(false);
        }
    };

    const syncUser = async () => {
        if (!syncUserId) return;
        setSyncUserLoading(true);
        setSyncUserResult(null);
        try {
            const response = await fetch(`/admin/moodle/test/sync-user/${syncUserId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
            });
            const data = await response.json();
            setSyncUserResult(data);
        } catch (error) {
            setSyncUserResult({ status: 'error', message: 'Network error: ' + error.message });
        } finally {
            setSyncUserLoading(false);
        }
    };

    const viewLogs = async () => {
        setLogsLoading(true);
        setLogs(null);
        try {
            const response = await fetch('/admin/moodle/test/logs');
            const data = await response.json();
            setLogs(data.logs);
        } catch (error) {
            setLogs('Error fetching logs: ' + error.message);
        } finally {
            setLogsLoading(false);
        }
    };

    const resultBadge = (result) => {
        if (!result) return null;
        const isSuccess = result.status === 'success';
        return (
            <div className={`mt-3 rounded-md p-3 ${isSuccess ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}`}>
                <p className="text-sm font-medium">{result.message}</p>
                {result.moodle_url && (
                    <p className="text-xs mt-1">Moodle URL: {result.moodle_url}</p>
                )}
                {result.site_name && (
                    <p className="text-xs mt-1">Site: {result.site_name} (v{result.moodle_version})</p>
                )}
                {result.laravel_id && (
                    <p className="text-xs mt-1">Laravel ID: {result.laravel_id} | Moodle ID: {result.moodle_id || 'N/A'}</p>
                )}
            </div>
        );
    };

    return (
        <>
            <Head title="Moodle Test Dashboard" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Moodle Test Dashboard</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Test and debug the Moodle integration.
                    </p>
                </div>

                {/* Test Connection */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Test Connection</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <p className="text-sm text-gray-500 mb-4">
                            Verify that the application can connect to the Moodle instance.
                        </p>
                        <button
                            onClick={testConnection}
                            disabled={connectionLoading}
                            className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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
                        {resultBadge(connectionResult)}
                    </div>
                </div>

                {/* Create Test User */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Create Test User</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <form onSubmit={createTestUser} className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">First Name</label>
                                    <input
                                        type="text"
                                        value={newUser.firstname}
                                        onChange={(e) => setNewUser({ ...newUser, firstname: e.target.value })}
                                        required
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input
                                        type="text"
                                        value={newUser.lastname}
                                        onChange={(e) => setNewUser({ ...newUser, lastname: e.target.value })}
                                        required
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        type="email"
                                        value={newUser.email}
                                        onChange={(e) => setNewUser({ ...newUser, email: e.target.value })}
                                        required
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Password</label>
                                    <input
                                        type="password"
                                        value={newUser.password}
                                        onChange={(e) => setNewUser({ ...newUser, password: e.target.value })}
                                        required
                                        minLength={6}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                            </div>
                            <button
                                type="submit"
                                disabled={createUserLoading}
                                className="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {createUserLoading ? (
                                    <>
                                        <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Creating...
                                    </>
                                ) : (
                                    'Create Test User'
                                )}
                            </button>
                        </form>
                        {resultBadge(createUserResult)}
                    </div>
                </div>

                {/* Sync User */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Sync User to Moodle</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <p className="text-sm text-gray-500 mb-4">
                            Enter a user ID to sync an existing user to Moodle.
                        </p>
                        <div className="flex items-end gap-3">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">User ID</label>
                                <input
                                    type="number"
                                    value={syncUserId}
                                    onChange={(e) => setSyncUserId(e.target.value)}
                                    placeholder="Enter user ID"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                            </div>
                            <button
                                onClick={syncUser}
                                disabled={syncUserLoading || !syncUserId}
                                className="inline-flex items-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {syncUserLoading ? (
                                    <>
                                        <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Syncing...
                                    </>
                                ) : (
                                    'Sync User'
                                )}
                            </button>
                        </div>
                        {resultBadge(syncUserResult)}
                    </div>
                </div>

                {/* View Logs */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Moodle Logs</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <p className="text-sm text-gray-500 mb-4">
                            View recent Moodle-related log entries.
                        </p>
                        <button
                            onClick={viewLogs}
                            disabled={logsLoading}
                            className="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            {logsLoading ? (
                                <>
                                    <svg className="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                    </svg>
                                    Loading...
                                </>
                            ) : (
                                'View Logs'
                            )}
                        </button>
                        {logs !== null && (
                            <div className="mt-4 rounded-md bg-gray-900 p-4 overflow-x-auto">
                                <pre className="text-xs text-green-400 whitespace-pre-wrap break-words">{logs}</pre>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
