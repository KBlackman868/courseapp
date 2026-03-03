import { Head, Link } from '@inertiajs/react';

export default function Permissions({ role, permissions = {} }) {
    const permissionGroups = Object.entries(permissions);
    const totalPermissions = permissionGroups.reduce(
        (sum, [, perms]) => sum + (Array.isArray(perms) ? perms.length : Object.values(perms).length),
        0
    );

    return (
        <>
            <Head title={`Permissions: ${role.display_name || role.name}`} />

            <div className="space-y-6">
                {/* Back Link */}
                <div>
                    <Link
                        href="/admin/roles"
                        className="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        <svg className="mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Back to Roles
                    </Link>
                </div>

                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">
                            Permissions for {role.display_name || role.name}
                        </h1>
                        <p className="mt-1 text-sm text-gray-500">
                            This role has {totalPermissions} permission{totalPermissions !== 1 ? 's' : ''} assigned across {permissionGroups.length} categor{permissionGroups.length !== 1 ? 'ies' : 'y'}.
                        </p>
                    </div>
                    <div className="mt-4 sm:mt-0">
                        <Link
                            href={`/admin/roles/${role.id}/edit`}
                            className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition-colors"
                        >
                            Edit Permissions
                        </Link>
                    </div>
                </div>

                {/* Role Info Card */}
                <div className="overflow-hidden rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">Role Details</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <dl className="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Name</dt>
                                <dd className="mt-1 text-sm text-gray-900 font-mono">{role.name}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Display Name</dt>
                                <dd className="mt-1 text-sm text-gray-900">{role.display_name || '-'}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Description</dt>
                                <dd className="mt-1 text-sm text-gray-900">{role.description || '-'}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {/* Permissions by Category */}
                {permissionGroups.length === 0 ? (
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="px-4 py-12 text-center">
                            <p className="text-sm text-gray-500">No permissions assigned to this role.</p>
                        </div>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {permissionGroups.map(([category, perms]) => {
                            const categoryPerms = Array.isArray(perms) ? perms : Object.values(perms);

                            return (
                                <div key={category} className="overflow-hidden rounded-lg bg-white shadow">
                                    <div className="px-4 py-4 sm:px-6 border-b border-gray-200 flex items-center justify-between">
                                        <h3 className="text-sm font-semibold text-gray-900 capitalize">
                                            {category || 'General'}
                                        </h3>
                                        <span className="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                            {categoryPerms.length} permission{categoryPerms.length !== 1 ? 's' : ''}
                                        </span>
                                    </div>
                                    <div className="px-4 py-4 sm:px-6">
                                        <div className="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                            {categoryPerms.map((permission) => (
                                                <div
                                                    key={permission.id}
                                                    className="flex items-center gap-2 text-sm text-gray-700"
                                                >
                                                    <svg className="h-4 w-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                    {permission.name.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </>
    );
}
