import { Head, router, usePage } from '@inertiajs/react';
import { useState, useEffect, useMemo } from 'react';

function RoleBadge({ role }) {
    const colors = {
        superadmin: 'bg-purple-100 text-purple-800',
        admin: 'bg-indigo-100 text-indigo-800',
        course_admin: 'bg-blue-100 text-blue-800',
        moh_staff: 'bg-green-100 text-green-800',
        external_staff: 'bg-yellow-100 text-yellow-800',
        external: 'bg-yellow-100 text-yellow-800',
    };
    const label = role ? role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase()) : 'No Role';
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${colors[role] || 'bg-gray-100 text-gray-800'}`}>
            {label}
        </span>
    );
}

export default function RolesIndex({ users, roles = [] }) {
    const { flash } = usePage().props;
    const [selectedUsers, setSelectedUsers] = useState([]);
    const [bulkRole, setBulkRole] = useState('');
    const [processing, setProcessing] = useState(null);
    const [search, setSearch] = useState('');
    const [debouncedSearch, setDebouncedSearch] = useState('');
    const [roleFilter, setRoleFilter] = useState('');

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(search), 300);
        return () => clearTimeout(timer);
    }, [search]);

    const allUsers = Array.isArray(users) ? users : (users?.data || []);

    const filteredUsers = useMemo(() => {
        let list = allUsers;

        if (debouncedSearch.trim()) {
            const term = debouncedSearch.toLowerCase();
            list = list.filter(
                (u) =>
                    `${u.first_name} ${u.last_name}`.toLowerCase().includes(term) ||
                    u.email.toLowerCase().includes(term)
            );
        }

        if (roleFilter) {
            list = list.filter((u) => u.roles?.[0]?.name === roleFilter);
        }

        return list;
    }, [debouncedSearch, allUsers, roleFilter]);

    const isAllSelected = filteredUsers.length > 0 && filteredUsers.every((u) => selectedUsers.includes(u.id));

    const toggleSelectAll = () => {
        if (isAllSelected) {
            setSelectedUsers([]);
        } else {
            setSelectedUsers(filteredUsers.map((u) => u.id));
        }
    };

    const toggleUser = (userId) => {
        setSelectedUsers((prev) =>
            prev.includes(userId) ? prev.filter((id) => id !== userId) : [...prev, userId]
        );
    };

    const handleRoleChange = (userId, role) => {
        if (!role) return;
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
        router.post('/admin/roles/bulk-assign', {
            user_ids: selectedUsers,
            role: bulkRole,
        }, {
            preserveState: true,
            onSuccess: () => { setSelectedUsers([]); setBulkRole(''); setProcessing(null); },
            onFinish: () => setProcessing(null),
        });
    };

    const roleList = Array.isArray(roles) ? roles : [];

    return (
        <>
            <Head title="Role Management" />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Role Management</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Assign and manage user roles. Showing {filteredUsers.length} of {allUsers.length} users.
                        </p>
                    </div>
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

                {/* Available Roles Overview */}
                <div className="rounded-lg bg-white p-4 shadow">
                    <h3 className="text-sm font-medium text-gray-700 mb-3">Available Roles</h3>
                    <div className="flex flex-wrap gap-2">
                        {roleList.map((role) => {
                            const roleName = typeof role === 'string' ? role : role.name;
                            const count = allUsers.filter((u) => u.roles?.[0]?.name === roleName).length;
                            return (
                                <button
                                    key={roleName}
                                    onClick={() => setRoleFilter(roleFilter === roleName ? '' : roleName)}
                                    className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium transition-colors ${
                                        roleFilter === roleName
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    }`}
                                >
                                    {roleName.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                    <span className={`rounded-full px-1.5 py-0.5 text-xs ${
                                        roleFilter === roleName ? 'bg-white/20' : 'bg-gray-200'
                                    }`}>
                                        {count}
                                    </span>
                                </button>
                            );
                        })}
                        {roleFilter && (
                            <button
                                onClick={() => setRoleFilter('')}
                                className="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium text-gray-500 hover:text-gray-700"
                            >
                                Clear filter
                                <svg className="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        )}
                    </div>
                </div>

                {/* Search */}
                <div className="relative">
                    <svg className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        type="text"
                        placeholder="Search users by name or email..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="block w-full rounded-lg border-gray-300 pl-10 pr-10 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    {search && (
                        <button onClick={() => setSearch('')} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    )}
                </div>

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
                                {roleList.map((role) => {
                                    const roleName = typeof role === 'string' ? role : role.name;
                                    return (
                                        <option key={roleName} value={roleName}>
                                            {roleName.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                        </option>
                                    );
                                })}
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
                <div className="overflow-hidden rounded-lg bg-white shadow min-h-[200px]">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-4 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            checked={isAllSelected}
                                            onChange={toggleSelectAll}
                                            className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Role</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assign Role</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {filteredUsers.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-6 py-12 text-center">
                                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                            </svg>
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                {debouncedSearch.trim()
                                                    ? `No users match "${debouncedSearch}".`
                                                    : roleFilter
                                                      ? `No users with role "${roleFilter.replace(/_/g, ' ')}".`
                                                      : 'No users to display.'}
                                            </p>
                                            {(debouncedSearch.trim() || roleFilter) && (
                                                <button onClick={() => { setSearch(''); setRoleFilter(''); }} className="mt-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500">
                                                    Clear Filters
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ) : (
                                    filteredUsers.map((user) => {
                                        const primaryRole = user.roles?.[0]?.name || null;
                                        return (
                                            <tr key={user.id} className={`hover:bg-gray-50 transition-colors ${selectedUsers.includes(user.id) ? 'bg-indigo-50' : ''}`}>
                                                <td className="px-4 py-4 whitespace-nowrap">
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
                                                        <option value="" disabled>Select role...</option>
                                                        {roleList.map((role) => {
                                                            const roleName = typeof role === 'string' ? role : role.name;
                                                            return (
                                                                <option key={roleName} value={roleName}>
                                                                    {roleName.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                                                </option>
                                                            );
                                                        })}
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

                {/* Show total count at bottom */}
                {filteredUsers.length > 0 && (
                    <p className="text-center text-xs text-gray-400">
                        Showing {filteredUsers.length} of {allUsers.length} total users
                    </p>
                )}
            </div>
        </>
    );
}
