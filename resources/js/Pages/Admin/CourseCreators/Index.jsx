import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

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

function StatCard({ label, value, color = 'indigo' }) {
    const colorClasses = {
        indigo: 'bg-indigo-50 text-indigo-700',
        green: 'bg-green-50 text-green-700',
        blue: 'bg-blue-50 text-blue-700',
        yellow: 'bg-yellow-50 text-yellow-700',
    };

    return (
        <div className="overflow-hidden rounded-lg bg-white shadow">
            <div className="px-4 py-5 sm:p-6">
                <dt className="truncate text-sm font-medium text-gray-500">{label}</dt>
                <dd className="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{value}</dd>
            </div>
            <div className={`px-4 py-2 ${colorClasses[color]}`}>
                <span className="text-xs font-medium">{label}</span>
            </div>
        </div>
    );
}

export default function Index({ courseCreators, stats = {}, potentialCreators = [] }) {
    const { flash } = usePage().props;
    const [selectedUser, setSelectedUser] = useState('');
    const [processing, setProcessing] = useState(null);

    const creatorList = courseCreators?.data || [];

    const handleGrant = () => {
        if (!selectedUser) return;
        setProcessing('grant');
        router.post(`/admin/course-creators/${selectedUser}/grant`, {}, {
            preserveState: true,
            onFinish: () => {
                setProcessing(null);
                setSelectedUser('');
            },
        });
    };

    const handleRevoke = (userId) => {
        if (!confirm('Are you sure you want to revoke course creator status from this user?')) return;
        setProcessing(userId);
        router.post(`/admin/course-creators/${userId}/revoke`, {}, {
            preserveState: true,
            onFinish: () => setProcessing(null),
        });
    };

    return (
        <>
            <Head title="Course Creators" />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Course Creators</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Manage users who can create and manage courses on the platform.
                        </p>
                    </div>
                    <div className="mt-4 sm:mt-0">
                        <Link
                            href="/admin/course-creators/statistics"
                            className="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors"
                        >
                            View Statistics
                        </Link>
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

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard label="Total Creators" value={stats.total_creators || 0} color="indigo" />
                    <StatCard label="Internal Creators" value={stats.internal_creators || 0} color="green" />
                    <StatCard label="External Creators" value={stats.external_creators || 0} color="blue" />
                    <StatCard label="Courses Created" value={stats.total_courses_created || 0} color="yellow" />
                </div>

                {/* Grant Course Creator Status */}
                <div className="rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Grant Course Creator Status</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <div className="flex items-center gap-4">
                            <select
                                value={selectedUser}
                                onChange={(e) => setSelectedUser(e.target.value)}
                                className="block w-full max-w-md rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Select a user...</option>
                                {potentialCreators.map((user) => (
                                    <option key={user.id} value={user.id}>
                                        {user.first_name} {user.last_name} ({user.email})
                                    </option>
                                ))}
                            </select>
                            <button
                                onClick={handleGrant}
                                disabled={!selectedUser || processing === 'grant'}
                                className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                            >
                                {processing === 'grant' ? 'Granting...' : 'Grant Access'}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Course Creators Table */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Course Creators</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {creatorList.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-6 py-12 text-center text-sm text-gray-500">
                                            No course creators found.
                                        </td>
                                    </tr>
                                ) : (
                                    creatorList.map((creator) => (
                                        <tr key={creator.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {creator.first_name} {creator.last_name}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {creator.email}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    creator.user_type === 'internal'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-blue-100 text-blue-800'
                                                }`}>
                                                    {creator.user_type ? creator.user_type.charAt(0).toUpperCase() + creator.user_type.slice(1) : 'N/A'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {creator.department || '-'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button
                                                    onClick={() => handleRevoke(creator.id)}
                                                    disabled={processing === creator.id}
                                                    className="inline-flex items-center rounded-md bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-800 hover:bg-red-100 disabled:opacity-50 transition-colors"
                                                >
                                                    {processing === creator.id ? 'Revoking...' : 'Revoke'}
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Pagination */}
                <Pagination links={courseCreators?.links} />
            </div>
        </>
    );
}
