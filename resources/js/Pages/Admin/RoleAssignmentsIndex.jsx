import { useState, useCallback, useMemo } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { useClientFilter } from '@/hooks/useClientFilter';
import {
    MagnifyingGlassIcon,
    XMarkIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';

/**
 * RoleAssignmentsIndex - User role management with client-side filtering
 *
 * Props from Laravel/Inertia (loaded once):
 * @param {Array} users - All users with their roles
 * @param {Array} roles - Available roles
 * @param {Object} auth - Current authenticated user
 * @param {Object} flash - Flash messages
 */
export default function RoleAssignmentsIndex({ users = [], roles = [], auth, flash }) {
    const currentUserId = auth?.user?.id;
    const isSuperadmin = auth?.user?.roles?.some((r) => r.name === 'superadmin');

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
        items: users,
        searchFields: ['first_name', 'last_name', 'email'],
        itemsPerPage: 20,
    });

    // Selected users for bulk operations
    const [selectedIds, setSelectedIds] = useState(new Set());

    // Bulk role assignment form
    const bulkForm = useForm({
        role: roles[0]?.name || 'user',
        user_ids: [],
    });

    // Single user role update form
    const singleForm = useForm({
        role: '',
    });

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
     * Check if user can be modified (not current user, not protected superadmin)
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
     * Handle bulk role assignment
     */
    const handleBulkAssign = useCallback(() => {
        if (selectedIds.size === 0) {
            alert('Please select at least one user.');
            return;
        }

        bulkForm.setData('user_ids', Array.from(selectedIds));
        bulkForm.post(route('admin.roles.bulkAssign'), {
            preserveScroll: true,
            onSuccess: () => {
                setSelectedIds(new Set());
            },
        });
    }, [selectedIds, bulkForm]);

    /**
     * Handle single user role update
     */
    const handleSingleRoleUpdate = useCallback(
        (userId, newRole) => {
            router.post(
                route('admin.roles.assign', userId),
                { role: newRole },
                { preserveScroll: true }
            );
        },
        []
    );

    /**
     * Get unique roles for dropdown filter
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
        <AdminLayout title="Role Management">
            <Head title="Role Management" />

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
                <div className="mb-6">
                    <h2 className="text-2xl font-bold text-gray-900">Role Assignment</h2>
                    <p className="mt-1 text-sm text-gray-500">
                        Manage user roles and permissions
                    </p>
                </div>

                {/* Bulk Role Assignment Card */}
                <div className="bg-white rounded-lg shadow mb-6 p-4">
                    <h3 className="font-semibold text-gray-900 mb-3">Bulk Role Assignment</h3>
                    <div className="flex flex-wrap items-center gap-4">
                        <select
                            value={bulkForm.data.role}
                            onChange={(e) => bulkForm.setData('role', e.target.value)}
                            className="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            {roles.map((role) => (
                                <option key={role.id} value={role.name}>
                                    {role.name.charAt(0).toUpperCase() + role.name.slice(1)}
                                </option>
                            ))}
                        </select>
                        <button
                            type="button"
                            onClick={handleBulkAssign}
                            disabled={selectedIds.size === 0 || bulkForm.processing}
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {bulkForm.processing ? 'Assigning...' : `Assign to Selected (${selectedIds.size})`}
                        </button>
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
                                placeholder="Search by name or email..."
                                className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>

                        {/* Role Filter Dropdown */}
                        <div className="w-full lg:w-48">
                            <select
                                value={filters.roles || 'all'}
                                onChange={(e) => setFilter('roles', e.target.value)}
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

                        {/* Clear Button */}
                        {isFiltered && (
                            <button
                                type="button"
                                onClick={clearAll}
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                                    <th scope="col" className="px-4 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            checked={allSelectedOnPage}
                                            onChange={handleSelectAll}
                                            className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                    </th>
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
                                        Current Role
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Assign Role
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {paginatedItems.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-12 text-center">
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
                                            {/* Checkbox */}
                                            <td className="px-4 py-3">
                                                {canModifyUser(user) ? (
                                                    <input
                                                        type="checkbox"
                                                        checked={selectedIds.has(user.id)}
                                                        onChange={(e) =>
                                                            handleSelectUser(user.id, e.target.checked)
                                                        }
                                                        className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                ) : null}
                                            </td>

                                            {/* User Name */}
                                            <td className="px-4 py-3">
                                                <div className="font-medium text-gray-900">
                                                    {user.first_name} {user.last_name}
                                                </div>
                                            </td>

                                            {/* Email */}
                                            <td className="px-4 py-3 text-sm text-gray-500">
                                                {user.email}
                                            </td>

                                            {/* Current Role */}
                                            <td className="px-4 py-3">
                                                <div className="flex flex-wrap gap-1">
                                                    {user.roles?.map((role) => (
                                                        <span
                                                            key={role.id}
                                                            className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleBadgeColor(
                                                                role.name
                                                            )}`}
                                                        >
                                                            {role.name.charAt(0).toUpperCase() +
                                                                role.name.slice(1)}
                                                        </span>
                                                    ))}
                                                </div>
                                            </td>

                                            {/* Assign Role */}
                                            <td className="px-4 py-3">
                                                {canModifyUser(user) ? (
                                                    <div className="flex items-center gap-2">
                                                        <select
                                                            defaultValue={user.roles?.[0]?.name || 'user'}
                                                            onChange={(e) =>
                                                                handleSingleRoleUpdate(user.id, e.target.value)
                                                            }
                                                            className="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        >
                                                            {roles.map((role) => (
                                                                <option key={role.id} value={role.name}>
                                                                    {role.name.charAt(0).toUpperCase() +
                                                                        role.name.slice(1)}
                                                                </option>
                                                            ))}
                                                        </select>
                                                    </div>
                                                ) : (
                                                    <span className="text-xs text-gray-500">
                                                        {user.id === currentUserId
                                                            ? 'Current User'
                                                            : 'Protected'}
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
