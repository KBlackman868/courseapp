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

function StatusBadge({ status, suspended }) {
    const isActive = !suspended && status !== 'suspended';
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
            {isActive ? 'Active' : 'Suspended'}
        </span>
    );
}

function AddUserModal({ isOpen, onClose, roles = [] }) {
    const [form, setForm] = useState({
        first_name: '', last_name: '', email: '', role: 'moh_staff', date_of_birth: '',
    });
    const [errors, setErrors] = useState({});

    if (!isOpen) return null;

    const handleChange = (field, value) => {
        setForm((prev) => ({ ...prev, [field]: value }));
        if (errors[field]) setErrors((prev) => ({ ...prev, [field]: '' }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const errs = {};
        if (!form.first_name.trim() || form.first_name.trim().length < 2) errs.first_name = 'First name is required (min 2 characters).';
        if (!form.last_name.trim() || form.last_name.trim().length < 2) errs.last_name = 'Last name is required (min 2 characters).';
        if (!form.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errs.email = 'A valid email address is required.';
        if (!form.role) errs.role = 'Please select a role.';
        if (Object.keys(errs).length > 0) { setErrors(errs); return; }

        router.post('/admin/users', {
            first_name: form.first_name.trim(), last_name: form.last_name.trim(),
            email: form.email.trim(), role: form.role, date_of_birth: form.date_of_birth || null,
        }, {
            preserveState: true,
            onSuccess: () => { setForm({ first_name: '', last_name: '', email: '', role: 'moh_staff', date_of_birth: '' }); setErrors({}); onClose(); },
            onError: (serverErrors) => setErrors(serverErrors),
        });
    };

    const handleClose = () => { setForm({ first_name: '', last_name: '', email: '', role: 'moh_staff', date_of_birth: '' }); setErrors({}); onClose(); };
    const roleList = Array.isArray(roles) ? roles : [];

    return (
        <div className="fixed inset-0 z-50 overflow-y-auto">
            <div className="flex min-h-full items-center justify-center p-4">
                <div className="fixed inset-0 bg-gray-500/75 transition-opacity" onClick={handleClose} />
                <div className="relative w-full max-w-lg transform rounded-xl bg-white p-6 shadow-xl transition-all">
                    <div className="mb-5">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100">
                                <svg className="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900">Add New User</h3>
                                <p className="text-sm text-gray-500">Create a new user account with a temporary password.</p>
                            </div>
                        </div>
                    </div>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" value={form.first_name} onChange={(e) => handleChange('first_name', e.target.value)} className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="John" />
                                {errors.first_name && <p className="mt-1 text-xs text-red-600">{errors.first_name}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" value={form.last_name} onChange={(e) => handleChange('last_name', e.target.value)} className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Doe" />
                                {errors.last_name && <p className="mt-1 text-xs text-red-600">{errors.last_name}</p>}
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" value={form.email} onChange={(e) => handleChange('email', e.target.value)} className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="john.doe@example.com" />
                            {errors.email && <p className="mt-1 text-xs text-red-600">{errors.email}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select value={form.role} onChange={(e) => handleChange('role', e.target.value)} className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                {roleList.map((role) => (
                                    <option key={role} value={role}>{role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}</option>
                                ))}
                            </select>
                            {errors.role && <p className="mt-1 text-xs text-red-600">{errors.role}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span className="text-gray-400">(Optional)</span></label>
                            <input type="date" value={form.date_of_birth} onChange={(e) => handleChange('date_of_birth', e.target.value)} className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            {errors.date_of_birth && <p className="mt-1 text-xs text-red-600">{errors.date_of_birth}</p>}
                        </div>
                        <div className="flex gap-3 justify-end pt-2">
                            <button type="button" onClick={handleClose} className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="submit" className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default function UsersIndex({ users, roles = [] }) {
    const { flash } = usePage().props;
    const [search, setSearch] = useState('');
    const [debouncedSearch, setDebouncedSearch] = useState('');
    const [processing, setProcessing] = useState(null);
    const [showAddModal, setShowAddModal] = useState(false);
    const [selectedUsers, setSelectedUsers] = useState([]);
    const [roleFilter, setRoleFilter] = useState('');
    const [statusFilter, setStatusFilter] = useState('');

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
                    u.email.toLowerCase().includes(term) ||
                    (u.department && u.department.toLowerCase().includes(term))
            );
        }

        if (roleFilter) {
            list = list.filter((u) => u.roles?.[0]?.name === roleFilter);
        }

        if (statusFilter === 'active') {
            list = list.filter((u) => !u.is_suspended);
        } else if (statusFilter === 'suspended') {
            list = list.filter((u) => u.is_suspended);
        }

        return list;
    }, [debouncedSearch, allUsers, roleFilter, statusFilter]);

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

    const handleSuspend = (userId) => {
        setProcessing(userId);
        router.patch(`/admin/users/${userId}/suspend`, {}, { preserveState: true, onFinish: () => setProcessing(null) });
    };

    const handleReactivate = (userId) => {
        setProcessing(userId);
        router.patch(`/admin/users/${userId}/reactivate`, {}, { preserveState: true, onFinish: () => setProcessing(null) });
    };

    const handleDelete = (userId, userName) => {
        if (confirm(`Are you sure you want to delete ${userName}? This action cannot be undone.`)) {
            setProcessing(userId);
            router.delete(`/admin/users/${userId}`, { preserveState: true, onFinish: () => setProcessing(null) });
        }
    };

    const handleRoleChange = (userId, role) => {
        if (!role) return;
        setProcessing(userId);
        router.post(`/admin/users/${userId}/role`, { role }, { preserveState: true, onFinish: () => setProcessing(null) });
    };

    const handleBulkDelete = () => {
        if (selectedUsers.length === 0) return;
        if (!confirm(`Are you sure you want to delete ${selectedUsers.length} user(s)? This action cannot be undone.`)) return;
        setProcessing('bulk');
        router.delete('/admin/users/bulk-delete', {
            data: { user_ids: selectedUsers },
            preserveState: true,
            onSuccess: () => { setSelectedUsers([]); setProcessing(null); },
            onFinish: () => setProcessing(null),
        });
    };

    const handleBulkSuspend = () => {
        if (selectedUsers.length === 0) return;
        if (!confirm(`Are you sure you want to suspend ${selectedUsers.length} user(s)?`)) return;
        setProcessing('bulk');
        router.post('/admin/users/bulk-suspend', { user_ids: selectedUsers }, {
            preserveState: true,
            onSuccess: () => { setSelectedUsers([]); setProcessing(null); },
            onFinish: () => setProcessing(null),
        });
    };

    const roleList = Array.isArray(roles) ? roles : [];

    return (
        <>
            <Head title="User Management" />

            <AddUserModal isOpen={showAddModal} onClose={() => setShowAddModal(false)} roles={roleList} />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">User Management</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Showing {filteredUsers.length} of {allUsers.length} users
                        </p>
                    </div>
                    <div className="mt-4 sm:mt-0">
                        <button
                            onClick={() => setShowAddModal(true)}
                            className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                        >
                            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Add User
                        </button>
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

                {/* Search & Filters */}
                <div className="flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <svg className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input
                            type="text"
                            placeholder="Search users by name, email, or department..."
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
                    <select
                        value={roleFilter}
                        onChange={(e) => setRoleFilter(e.target.value)}
                        className="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Roles</option>
                        {roleList.map((role) => (
                            <option key={role} value={role}>{role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}</option>
                        ))}
                    </select>
                    <select
                        value={statusFilter}
                        onChange={(e) => setStatusFilter(e.target.value)}
                        className="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                {/* Bulk Actions Bar */}
                {selectedUsers.length > 0 && (
                    <div className="rounded-lg bg-indigo-50 border border-indigo-200 p-4">
                        <div className="flex items-center gap-4 flex-wrap">
                            <span className="text-sm font-medium text-indigo-800">
                                {selectedUsers.length} user(s) selected
                            </span>
                            <button
                                onClick={handleBulkSuspend}
                                disabled={processing === 'bulk'}
                                className="inline-flex items-center rounded-md bg-yellow-100 px-3 py-1.5 text-xs font-medium text-yellow-800 hover:bg-yellow-200 disabled:opacity-50 transition-colors"
                            >
                                <svg className="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Suspend Selected
                            </button>
                            <button
                                onClick={handleBulkDelete}
                                disabled={processing === 'bulk'}
                                className="inline-flex items-center rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-800 hover:bg-red-200 disabled:opacity-50 transition-colors"
                            >
                                <svg className="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                Delete Selected
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
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moodle</th>
                                    <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {filteredUsers.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="px-6 py-12 text-center">
                                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                            </svg>
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                {debouncedSearch.trim() ? `No users match "${debouncedSearch}".` : 'No users to display.'}
                                            </p>
                                            {debouncedSearch.trim() && (
                                                <button onClick={() => setSearch('')} className="mt-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500">
                                                    Clear Search
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ) : (
                                    filteredUsers.map((user) => {
                                        const primaryRole = user.roles?.[0]?.name || null;
                                        const isActive = !user.is_suspended;
                                        const avatarUrl = user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent((user.first_name || '') + ' ' + (user.last_name || ''))}&background=6366f1&color=fff&size=40`;

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
                                                        <img className="h-10 w-10 rounded-full object-cover" src={avatarUrl} alt="" onError={(e) => { e.target.onerror = null; e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent((user.first_name || '') + ' ' + (user.last_name || ''))}&background=6366f1&color=fff&size=40`; }} />
                                                        <div className="ml-4">
                                                            <div className="text-sm font-medium text-gray-900">{user.first_name} {user.last_name}</div>
                                                            <div className="text-sm text-gray-500">{user.email}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{user.department || '-'}</td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <select
                                                        value={primaryRole || ''}
                                                        onChange={(e) => handleRoleChange(user.id, e.target.value)}
                                                        disabled={processing === user.id}
                                                        className="rounded-md border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50"
                                                    >
                                                        <option value="" disabled>Select role...</option>
                                                        {roleList.map((role) => (
                                                            <option key={role} value={role}>
                                                                {role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <StatusBadge suspended={user.is_suspended} />
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {user.moodle_user_id ? (
                                                        <span className="inline-flex items-center gap-1 text-green-700">
                                                            <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                                            </svg>
                                                            Synced
                                                        </span>
                                                    ) : (
                                                        <span className="text-gray-400">-</span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div className="flex items-center justify-end gap-2">
                                                        {isActive ? (
                                                            <button
                                                                onClick={() => handleSuspend(user.id)}
                                                                disabled={processing === user.id}
                                                                className="inline-flex items-center rounded-md bg-yellow-50 px-2.5 py-1.5 text-xs font-medium text-yellow-800 hover:bg-yellow-100 disabled:opacity-50 transition-colors"
                                                            >
                                                                Suspend
                                                            </button>
                                                        ) : (
                                                            <button
                                                                onClick={() => handleReactivate(user.id)}
                                                                disabled={processing === user.id}
                                                                className="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1.5 text-xs font-medium text-green-800 hover:bg-green-100 disabled:opacity-50 transition-colors"
                                                            >
                                                                Reactivate
                                                            </button>
                                                        )}
                                                        <button
                                                            onClick={() => handleDelete(user.id, `${user.first_name} ${user.last_name}`)}
                                                            disabled={processing === user.id}
                                                            className="inline-flex items-center rounded-md bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-800 hover:bg-red-100 disabled:opacity-50 transition-colors"
                                                        >
                                                            Delete
                                                        </button>
                                                    </div>
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
