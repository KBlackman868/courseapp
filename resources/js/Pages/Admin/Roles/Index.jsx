import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function RoleBadge({ role }) {
    const colors = {
        superadmin: 'bg-purple-100 text-purple-800',
        admin: 'bg-indigo-100 text-indigo-800',
        course_admin: 'bg-blue-100 text-blue-800',
        moh_staff: 'bg-green-100 text-green-800',
        external_staff: 'bg-yellow-100 text-yellow-800',
    };
    const label = role ? role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase()) : 'No Role';
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${colors[role] || 'bg-gray-100 text-gray-800'}`}>
            {label}
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

export default function RolesIndex({ users, roles = [] }) {
    const { flash } = usePage().props;
    const [selectedUsers, setSelectedUsers] = useState([]);
    const [bulkRole, setBulkRole] = useState('');
    const [processing, setProcessing] = useState(null);

    const userList = users?.data || [];

    const isAllSelected = userList.length > 0 && selectedUsers.length === userList.length;

    const toggleSelectAll = () => {
        if (isAllSelected) {
            setSelectedUsers([]);
        } else {
            setSelectedUsers(userList.map((u) => u.id));
        }
    };

    const toggleUser = (userId) => {
        setSelectedUsers((prev) =>
            prev.includes(userId)
                ? prev.filter((id) => id !== userId)
                : [...prev, userId]
        );
    };

    const handleRoleChange = (userId, role) => {
        setProcessing(userId);
        router.post(`/admin/roles/assign/${userId}`, { role }, {
            preserveState: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleBulkAssign = () => {
        if (!bulkRole || selectedUsers.length === 0) return;

        if (!confirm(`Assign role "${bulkRole.replace(/_/g, ' ')}" to ${selectedUsers.length} user(s)?`)) return;

        setProcessing('bulk');
        const promises = selectedUsers.map((userId) =>
            new Promise((resolve) => {
                router.post(`/admin/roles/assign/${userId}`, { role: bulkRole }, {
                    preserveState: true,
                    onFinish: resolve,
                });
            })
        );

        Promise.all(promises).then(() => {
            setProcessing(null);
            setSelectedUsers([]);
            setBulkRole('');
        });
    };

    return (
        <>
            <Head title="Role Management" />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Role Management</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Assign and manage user roles across the platform.
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

                {/* Bulk Actions */}
                {selectedUsers.length > 0 && (
                    <div className="rounded-lg bg-indigo-50 border border-indigo-200 p-4">
                        <div className="flex items-center gap-4 flex-wrap">
                            <span className="text-sm font-medium text-indigo-800">
                                {selectedUsers.length} user(s) selected
                            </span>
                            <select
                                value={bulkRole}
                                onChange={(e) => setBulkRole(e.target.value)}
                                className="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Select role...</option>
                                {roles.map((role) => (
                                    <option key={role.id || role.name} value={role.name}>
                                        {role.name.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                    </option>
                                ))}
                            </select>
                            <button
                                onClick={handleBulkAssign}
                                disabled={!bulkRole || processing === 'bulk'}
                                className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                            >
                                {processing === 'bulk' ? 'Assigning...' : 'Assign to Selected'}
                            </button>
                            <button
                                onClick={() => setSelectedUsers([])}
                                className="text-sm text-gray-600 hover:text-gray-800"
                            >
                                Clear Selection
                            </button>
                        </div>
                    </div>
                )}

                {/* Users Table */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            checked={isAllSelected}
                                            onChange={toggleSelectAll}
                                            className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Current Role
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Assign Role
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {userList.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-6 py-12 text-center text-sm text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                ) : (
                                    userList.map((user) => {
                                        const primaryRole = user.roles?.[0]?.name || null;
                                        return (
                                            <tr key={user.id} className="hover:bg-gray-50 transition-colors">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <input
                                                        type="checkbox"
                                                        checked={selectedUsers.includes(user.id)}
                                                        onChange={() => toggleUser(user.id)}
                                                        className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <img
                                                            className="h-8 w-8 rounded-full object-cover"
                                                            src={user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent((user.first_name || '') + ' ' + (user.last_name || ''))}&background=6366f1&color=fff&size=32`}
                                                            alt=""
                                                        />
                                                        <span className="ml-3 text-sm font-medium text-gray-900">
                                                            {user.first_name} {user.last_name}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {user.email}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <RoleBadge role={primaryRole} />
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <select
                                                        value={primaryRole || ''}
                                                        onChange={(e) => handleRoleChange(user.id, e.target.value)}
                                                        disabled={processing === user.id}
                                                        className="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50"
                                                    >
                                                        <option value="">Select role...</option>
                                                        {roles.map((role) => (
                                                            <option key={role.id || role.name} value={role.name}>
                                                                {role.name.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                            </tr>
                                        );
                                    })
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
