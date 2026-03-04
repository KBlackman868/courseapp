import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState, useCallback } from 'react';

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

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

function StatusBadge({ status }) {
    const isActive = status === 'active';
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
            {isActive ? 'Active' : 'Suspended'}
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

function AddUserModal({ isOpen, onClose, roles = [], processing }) {
    const [form, setForm] = useState({
        first_name: '',
        last_name: '',
        email: '',
        role: 'moh_staff',
        date_of_birth: '',
    });
    const [errors, setErrors] = useState({});

    if (!isOpen) return null;

    const handleChange = (field, value) => {
        setForm((prev) => ({ ...prev, [field]: value }));
        if (errors[field]) {
            setErrors((prev) => ({ ...prev, [field]: '' }));
        }
    };

    const validate = () => {
        const errs = {};
        if (!form.first_name.trim() || form.first_name.trim().length < 2) errs.first_name = 'First name is required (min 2 characters).';
        if (!form.last_name.trim() || form.last_name.trim().length < 2) errs.last_name = 'Last name is required (min 2 characters).';
        if (!form.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errs.email = 'A valid email address is required.';
        if (!form.role) errs.role = 'Please select a role.';
        return errs;
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const errs = validate();
        if (Object.keys(errs).length > 0) {
            setErrors(errs);
            return;
        }

        router.post('/admin/users', {
            first_name: form.first_name.trim(),
            last_name: form.last_name.trim(),
            email: form.email.trim(),
            role: form.role,
            date_of_birth: form.date_of_birth || null,
        }, {
            preserveState: true,
            onSuccess: () => {
                setForm({ first_name: '', last_name: '', email: '', role: 'moh_staff', date_of_birth: '' });
                setErrors({});
                onClose();
            },
            onError: (serverErrors) => {
                setErrors(serverErrors);
            },
        });
    };

    const handleClose = () => {
        setForm({ first_name: '', last_name: '', email: '', role: 'moh_staff', date_of_birth: '' });
        setErrors({});
        onClose();
    };

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
                                <label htmlFor="first_name" className="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input
                                    id="first_name"
                                    type="text"
                                    value={form.first_name}
                                    onChange={(e) => handleChange('first_name', e.target.value)}
                                    className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="John"
                                />
                                {errors.first_name && <p className="mt-1 text-xs text-red-600">{errors.first_name}</p>}
                            </div>
                            <div>
                                <label htmlFor="last_name" className="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input
                                    id="last_name"
                                    type="text"
                                    value={form.last_name}
                                    onChange={(e) => handleChange('last_name', e.target.value)}
                                    className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Doe"
                                />
                                {errors.last_name && <p className="mt-1 text-xs text-red-600">{errors.last_name}</p>}
                            </div>
                        </div>

                        <div>
                            <label htmlFor="user_email" className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input
                                id="user_email"
                                type="email"
                                value={form.email}
                                onChange={(e) => handleChange('email', e.target.value)}
                                className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="john.doe@example.com"
                            />
                            {errors.email && <p className="mt-1 text-xs text-red-600">{errors.email}</p>}
                        </div>

                        <div>
                            <label htmlFor="user_role" className="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select
                                id="user_role"
                                value={form.role}
                                onChange={(e) => handleChange('role', e.target.value)}
                                className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                {roleList.map((role) => (
                                    <option key={role} value={role}>
                                        {role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                    </option>
                                ))}
                            </select>
                            {errors.role && <p className="mt-1 text-xs text-red-600">{errors.role}</p>}
                        </div>

                        <div>
                            <label htmlFor="user_dob" className="block text-sm font-medium text-gray-700 mb-1">
                                Date of Birth <span className="text-gray-400">(Optional)</span>
                            </label>
                            <input
                                id="user_dob"
                                type="date"
                                value={form.date_of_birth}
                                onChange={(e) => handleChange('date_of_birth', e.target.value)}
                                className="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {errors.date_of_birth && <p className="mt-1 text-xs text-red-600">{errors.date_of_birth}</p>}
                        </div>

                        <div className="flex gap-3 justify-end pt-2">
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
                                className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors disabled:opacity-50"
                            >
                                {processing ? 'Creating...' : 'Create User'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default function UsersIndex({ users, roles = [], flash }) {
    const [search, setSearch] = useState('');
    const [processing, setProcessing] = useState(null);
    const [showAddModal, setShowAddModal] = useState(false);

    const debouncedSearch = useCallback(
        debounce((value) => {
            router.get('/admin/users', { search: value || undefined }, { preserveState: true, replace: true });
        }, 300),
        []
    );

    const handleSearchChange = (e) => {
        const value = e.target.value;
        setSearch(value);
        debouncedSearch(value);
    };

    const handleSuspend = (userId) => {
        setProcessing(userId);
        router.patch(`/admin/users/${userId}/suspend`, {}, {
            preserveState: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleReactivate = (userId) => {
        setProcessing(userId);
        router.patch(`/admin/users/${userId}/reactivate`, {}, {
            preserveState: true,
            onFinish: () => setProcessing(null),
        });
    };

    const handleDelete = (userId, userName) => {
        if (confirm(`Are you sure you want to delete ${userName}? This action cannot be undone.`)) {
            setProcessing(userId);
            router.delete(`/admin/users/${userId}`, {
                preserveState: true,
                onFinish: () => setProcessing(null),
            });
        }
    };

    const userList = users?.data || [];

    return (
        <>
            <Head title="User Management" />

            <AddUserModal
                isOpen={showAddModal}
                onClose={() => setShowAddModal(false)}
                roles={roles}
                processing={processing === 'creating'}
            />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">User Management</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Manage user accounts, roles, and access permissions.
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

                {/* Search */}
                <div className="flex items-center gap-4">
                    <div className="relative flex-1 max-w-md">
                        <svg className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input
                            type="text"
                            placeholder="Search users by name or email..."
                            value={search}
                            onChange={handleSearchChange}
                            className="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                </div>

                {/* Users Table */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moodle Sync</th>
                                    <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {userList.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-6 py-12 text-center text-sm text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                ) : (
                                    userList.map((user) => {
                                        const primaryRole = user.roles?.[0]?.name || null;
                                        const isActive = user.status === 'active';
                                        const avatarUrl = user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent((user.first_name || '') + ' ' + (user.last_name || ''))}&background=6366f1&color=fff&size=40`;

                                        return (
                                            <tr key={user.id} className="hover:bg-gray-50 transition-colors">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <img className="h-10 w-10 rounded-full object-cover" src={avatarUrl} alt="" />
                                                        <div className="ml-4">
                                                            <div className="text-sm font-medium text-gray-900">{user.first_name} {user.last_name}</div>
                                                            <div className="text-sm text-gray-500">{user.email}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{user.department || '-'}</td>
                                                <td className="px-6 py-4 whitespace-nowrap"><RoleBadge role={primaryRole} /></td>
                                                <td className="px-6 py-4 whitespace-nowrap"><StatusBadge status={user.status} /></td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {user.moodle_user_id ? (
                                                        <span className="inline-flex items-center gap-1 text-green-700">
                                                            <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                                            </svg>
                                                            Synced
                                                        </span>
                                                    ) : (
                                                        <span className="inline-flex items-center gap-1 text-gray-400">
                                                            <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                                                            </svg>
                                                            Not Synced
                                                        </span>
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

                {/* Pagination */}
                <Pagination links={users?.links} />
            </div>
        </>
    );
}
