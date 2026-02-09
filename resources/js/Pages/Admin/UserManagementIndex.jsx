import { useState, useCallback, useMemo } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { useClientFilter } from '@/hooks/useClientFilter';
import {
    MagnifyingGlassIcon,
    XMarkIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    TrashIcon,
    NoSymbolIcon,
    CheckCircleIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';

/**
 * UserManagementIndex - User management with client-side filtering
 *
 * Props from Laravel/Inertia (loaded once):
 * @param {Array} users - All users with their roles
 * @param {Array} roles - Available roles
 * @param {Object} auth - Current authenticated user
 * @param {Object} flash - Flash messages
 */
export default function UserManagementIndex({ users = [], roles = [], auth, flash }) {
    const currentUserId = auth?.user?.id;
    const isSuperadmin = auth?.user?.roles?.some((r) => r.name === 'superadmin');

    // Pre-process users to add computed fields for filtering
    const processedUsers = useMemo(() => {
        return users.map((user) => ({
            ...user,
            // Computed fields for filtering
            fullName: `${user.first_name} ${user.last_name}`,
            statusFilter: user.is_suspended ? 'suspended' : 'active',
            moodleStatus: user.moodle_user_id ? 'synced' : 'not_synced',
            roleName: user.roles?.[0]?.name || 'user',
        }));
    }, [users]);

    // Client-side filtering using our custom hook
    const {
        query,
        setQuery,
        filters,
        setFilter,
        clearAll,
        paginatedItems,
        totalCount,
        currentPage,
        setCurrentPage,
        totalPages,
        hasNextPage,
        hasPrevPage,
        nextPage,
        prevPage,
        isFiltered,
    } = useClientFilter({
        items: processedUsers,
        searchFields: ['first_name', 'last_name', 'email', 'fullName', 'department'],
        itemsPerPage: 20,
    });

    // Bulk selection state
    const [bulkMode, setBulkMode] = useState(false);
    const [selectedIds, setSelectedIds] = useState(new Set());

    /**
     * Get role badge color
     */
    const getRoleBadgeColor = useCallback((roleName) => {
        switch (roleName) {
            case 'superadmin':
                return 'bg-purple-100 text-purple-800';
            case 'admin':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }, []);

    /**
     * Check if user can be modified
     */
    const canModifyUser = useCallback(
        (user) => {
            if (user.id === currentUserId) return false;
            const userIsSuperadmin = user.roles?.some((r) => r.name === 'superadmin');
            if (userIsSuperadmin && !isSuperadmin) return false;
            return true;
        },
        [currentUserId, isSuperadmin]
    );

    /**
     * Handle select all checkbox
     */
    const handleSelectAll = useCallback(
        (e) => {
            if (e.target.checked) {
                const selectableIds = paginatedItems
                    .filter((user) => canModifyUser(user))
                    .map((user) => user.id);
                setSelectedIds(new Set(selectableIds));
            } else {
                setSelectedIds(new Set());
            }
        },
        [paginatedItems, canModifyUser]
    );

    /**
     * Handle individual checkbox
     */
    const handleSelectUser = useCallback((userId, checked) => {
        setSelectedIds((prev) => {
            const next = new Set(prev);
            if (checked) {
                next.add(userId);
            } else {
                next.delete(userId);
            }
            return next;
        });
    }, []);

    /**
     * Handle role update for single user
     */
    const handleRoleUpdate = useCallback((userId, newRole) => {
        router.post(
            route('admin.users.updateRole', userId),
            { role: newRole },
            { preserveScroll: true }
        );
    }, []);

    /**
     * Handle suspend user
     */
    const handleSuspend = useCallback((userId) => {
        router.patch(route('admin.users.suspend', userId), {}, { preserveScroll: true });
    }, []);

    /**
     * Handle reactivate user
     */
    const handleReactivate = useCallback((userId) => {
        router.patch(route('admin.users.reactivate', userId), {}, { preserveScroll: true });
    }, []);

    /**
     * Handle delete user
     */
    const handleDelete = useCallback((userId, userName) => {
        if (
            confirm(
                `Are you sure you want to delete ${userName}? This will also remove them from Moodle.`
            )
        ) {
            router.delete(route('admin.users.destroy', userId), { preserveScroll: true });
        }
    }, []);

    /**
     * Handle bulk delete
     */
    const handleBulkDelete = useCallback(() => {
        if (selectedIds.size === 0) {
            alert('Please select at least one user to delete.');
            return;
        }

        if (
            !confirm(
                `Are you sure you want to delete ${selectedIds.size} user(s)? This will also remove them from Moodle.`
            )
        ) {
            return;
        }

        router.delete(route('admin.users.bulkDelete'), {
            data: { user_ids: Array.from(selectedIds) },
            preserveScroll: true,
            onSuccess: () => {
                setSelectedIds(new Set());
                setBulkMode(false);
            },
        });
    }, [selectedIds]);

    /**
     * Get unique roles for filter dropdown
     */
    const uniqueRoles = useMemo(() => {
        const roleSet = new Set();
        users.forEach((user) => {
            user.roles?.forEach((role) => roleSet.add(role.name));
        });
        return Array.from(roleSet).sort();
    }, [users]);

    /**
     * Check if all selectable items on current page are selected
     */
    const allSelectedOnPage = useMemo(() => {
        const selectableIds = paginatedItems
            .filter((user) => canModifyUser(user))
            .map((user) => user.id);
        return selectableIds.length > 0 && selectableIds.every((id) => selectedIds.has(id));
    }, [paginatedItems, selectedIds, canModifyUser]);

    return (
        <AdminLayout title="User Management">
            <Head title="User Management" />

            <div className="max-w-7xl mx-auto">
                {/* Flash Messages */}
                {flash?.success && (
                    <div className="mb-4 rounded-md bg-green-50 p-4">
                        <p className="text-sm text-green-800">{flash.success}</p>
                    </div>
                )}
                {flash?.error && (
                    <div className="mb-4 rounded-md bg-red-50 p-4">
                        <p className="text-sm text-red-800">{flash.error}</p>
                    </div>
                )}

                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900">All Users</h2>
                        <p className="mt-1 text-sm text-gray-500">
                            Manage users, roles, and Moodle sync status
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <button
                            type="button"
                            onClick={() => {
                                setBulkMode(!bulkMode);
                                setSelectedIds(new Set());
                            }}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            {bulkMode ? 'Cancel Selection' : 'Select Multiple'}
                        </button>
                        {bulkMode && selectedIds.size > 0 && (
                            <button
                                type="button"
                                onClick={handleBulkDelete}
                                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                            >
                                <TrashIcon className="h-4 w-4 mr-2" />
                                Delete Selected ({selectedIds.size})
                            </button>
                        )}
                    </div>
                </div>

                {/* Filters Card */}
                <div className="bg-white rounded-lg shadow mb-6 p-4">
                    <div className="flex flex-col lg:flex-row gap-4">
                        {/* Search Input */}
                        <div className="flex-1 relative">
                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon className="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                value={query}
                                onChange={setQuery}
                                placeholder="Search by name, email, or department..."
                                className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>

                        {/* Role Filter */}
                        <div className="w-full lg:w-40">
                            <select
                                value={filters.roleName || 'all'}
                                onChange={(e) => setFilter('roleName', e.target.value)}
                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="all">All Roles</option>
                                {uniqueRoles.map((roleName) => (
                                    <option key={roleName} value={roleName}>
                                        {roleName.charAt(0).toUpperCase() + roleName.slice(1)}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Status Filter */}
                        <div className="w-full lg:w-40">
                            <select
                                value={filters.statusFilter || 'all'}
                                onChange={(e) => setFilter('statusFilter', e.target.value)}
                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>

                        {/* Clear Button */}
                        {isFiltered && (
                            <button
                                type="button"
                                onClick={clearAll}
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                <XMarkIcon className="h-4 w-4 mr-2" />
                                Clear
                            </button>
                        )}
                    </div>

                    {/* Results count */}
                    <div className="mt-3 text-sm text-gray-500">
                        Showing {paginatedItems.length} of {totalCount} users
                        {isFiltered && ' (filtered)'}
                    </div>
                </div>

                {/* Users Table */}
                <div className="bg-white shadow rounded-lg overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    {bulkMode && (
                                        <th scope="col" className="px-4 py-3 text-left">
                                            <input
                                                type="checkbox"
                                                checked={allSelectedOnPage}
                                                onChange={handleSelectAll}
                                                className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </th>
                                    )}
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        User
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Email
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Department
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Role
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Moodle
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {paginatedItems.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan={bulkMode ? 8 : 7}
                                            className="px-4 py-12 text-center"
                                        >
                                            <UserGroupIcon className="mx-auto h-12 w-12 text-gray-400" />
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">
                                                No users found
                                            </h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                {isFiltered
                                                    ? 'Try adjusting your search or filter.'
                                                    : 'No users available.'}
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    paginatedItems.map((user) => (
                                        <tr key={user.id} className="hover:bg-gray-50">
                                            {/* Bulk Select Checkbox */}
                                            {bulkMode && (
                                                <td className="px-4 py-3">
                                                    {canModifyUser(user) ? (
                                                        <input
                                                            type="checkbox"
                                                            checked={selectedIds.has(user.id)}
                                                            onChange={(e) =>
                                                                handleSelectUser(
                                                                    user.id,
                                                                    e.target.checked
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                        />
                                                    ) : null}
                                                </td>
                                            )}

                                            {/* User Info */}
                                            <td className="px-4 py-3">
                                                <div className="flex items-center">
                                                    {user.profile_photo ? (
                                                        <img
                                                            className="h-10 w-10 rounded-full object-cover mr-3"
                                                            src={`/storage/${user.profile_photo}`}
                                                            alt={user.first_name}
                                                        />
                                                    ) : (
                                                        <div className="h-10 w-10 rounded-full bg-gray-300 mr-3 flex items-center justify-center">
                                                            <span className="text-gray-600 font-semibold">
                                                                {user.first_name?.charAt(0) || 'U'}
                                                            </span>
                                                        </div>
                                                    )}
                                                    <div>
                                                        <p className="font-semibold text-gray-900">
                                                            {user.first_name} {user.last_name}
                                                        </p>
                                                        <p className="text-xs text-gray-500">
                                                            ID: {user.id}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Email */}
                                            <td className="px-4 py-3 text-sm text-gray-600">
                                                {user.email}
                                            </td>

                                            {/* Department */}
                                            <td className="px-4 py-3 text-sm text-gray-600">
                                                {user.department || 'N/A'}
                                            </td>

                                            {/* Role */}
                                            <td className="px-4 py-3">
                                                {!user.roles?.some(
                                                    (r) => r.name === 'superadmin'
                                                ) ? (
                                                    <select
                                                        defaultValue={
                                                            user.roles?.[0]?.name || 'user'
                                                        }
                                                        onChange={(e) =>
                                                            handleRoleUpdate(user.id, e.target.value)
                                                        }
                                                        className="text-sm border rounded px-2 py-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                    >
                                                        <option value="user">User</option>
                                                        <option value="admin">Admin</option>
                                                        {isSuperadmin && (
                                                            <option value="superadmin">
                                                                Superadmin
                                                            </option>
                                                        )}
                                                    </select>
                                                ) : (
                                                    <span className="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">
                                                        Superadmin
                                                    </span>
                                                )}
                                            </td>

                                            {/* Status */}
                                            <td className="px-4 py-3">
                                                {user.is_suspended ? (
                                                    <span className="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                                        Suspended
                                                    </span>
                                                ) : (
                                                    <span className="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                                        Active
                                                    </span>
                                                )}
                                            </td>

                                            {/* Moodle Status */}
                                            <td className="px-4 py-3">
                                                {user.moodle_user_id ? (
                                                    <span className="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                                        Synced ({user.moodle_user_id})
                                                    </span>
                                                ) : (
                                                    <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                                        Not Synced
                                                    </span>
                                                )}
                                            </td>

                                            {/* Actions */}
                                            <td className="px-4 py-3">
                                                {user.id !== currentUserId ? (
                                                    <div className="flex items-center space-x-2">
                                                        {/* Suspend/Reactivate */}
                                                        {user.is_suspended ? (
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    handleReactivate(user.id)
                                                                }
                                                                className="text-green-600 hover:text-green-800"
                                                                title="Reactivate"
                                                            >
                                                                <CheckCircleIcon className="h-5 w-5" />
                                                            </button>
                                                        ) : (
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    handleSuspend(user.id)
                                                                }
                                                                className="text-yellow-600 hover:text-yellow-800"
                                                                title="Suspend"
                                                            >
                                                                <NoSymbolIcon className="h-5 w-5" />
                                                            </button>
                                                        )}

                                                        {/* Delete */}
                                                        {canModifyUser(user) && (
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    handleDelete(
                                                                        user.id,
                                                                        `${user.first_name} ${user.last_name}`
                                                                    )
                                                                }
                                                                className="text-red-600 hover:text-red-800"
                                                                title="Delete"
                                                            >
                                                                <TrashIcon className="h-5 w-5" />
                                                            </button>
                                                        )}
                                                    </div>
                                                ) : (
                                                    <span className="text-xs text-gray-500">
                                                        Current User
                                                    </span>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div className="flex-1 flex justify-between sm:hidden">
                                <button
                                    onClick={prevPage}
                                    disabled={!hasPrevPage}
                                    className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Previous
                                </button>
                                <button
                                    onClick={nextPage}
                                    disabled={!hasNextPage}
                                    className="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Next
                                </button>
                            </div>
                            <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p className="text-sm text-gray-700">
                                        Page <span className="font-medium">{currentPage}</span> of{' '}
                                        <span className="font-medium">{totalPages}</span>
                                    </p>
                                </div>
                                <div>
                                    <nav
                                        className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                        aria-label="Pagination"
                                    >
                                        <button
                                            onClick={prevPage}
                                            disabled={!hasPrevPage}
                                            className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span className="sr-only">Previous</span>
                                            <ChevronLeftIcon className="h-5 w-5" aria-hidden="true" />
                                        </button>
                                        <button
                                            onClick={nextPage}
                                            disabled={!hasNextPage}
                                            className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span className="sr-only">Next</span>
                                            <ChevronRightIcon className="h-5 w-5" aria-hidden="true" />
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}
