import { Head, Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { useState } from 'react';

export default function Create({ permissions = {} }) {
    const [form, setForm] = useState({
        name: '',
        display_name: '',
        description: '',
        permissions: [],
    });
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
    };

    const togglePermission = (permissionId) => {
        setForm((prev) => ({
            ...prev,
            permissions: prev.permissions.includes(permissionId)
                ? prev.permissions.filter((id) => id !== permissionId)
                : [...prev.permissions, permissionId],
        }));
    };

    const toggleCategory = (categoryPermissions) => {
        const ids = categoryPermissions.map((p) => p.id);
        const allSelected = ids.every((id) => form.permissions.includes(id));

        setForm((prev) => ({
            ...prev,
            permissions: allSelected
                ? prev.permissions.filter((id) => !ids.includes(id))
                : [...new Set([...prev.permissions, ...ids])],
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        router.post('/admin/roles', form, {
            onError: (errs) => setErrors(errs),
            onFinish: () => setProcessing(false),
        });
    };

    const permissionGroups = Object.entries(permissions);

    return (
        <>
            <Head title="Create Role" />

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
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Create Role</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Define a new role with specific permissions.
                    </p>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 className="text-base font-semibold leading-6 text-gray-900">Role Details</h3>
                        </div>
                        <div className="px-4 py-5 sm:p-6 space-y-4">
                            {/* Name */}
                            <div>
                                <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                                    Name
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value={form.name}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="e.g. course_manager"
                                />
                                {errors.name && (
                                    <p className="mt-1 text-sm text-red-600">{errors.name}</p>
                                )}
                            </div>

                            {/* Display Name */}
                            <div>
                                <label htmlFor="display_name" className="block text-sm font-medium text-gray-700">
                                    Display Name
                                </label>
                                <input
                                    type="text"
                                    id="display_name"
                                    name="display_name"
                                    value={form.display_name}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="e.g. Course Manager"
                                />
                                {errors.display_name && (
                                    <p className="mt-1 text-sm text-red-600">{errors.display_name}</p>
                                )}
                            </div>

                            {/* Description */}
                            <div>
                                <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows={3}
                                    value={form.description}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="Describe the purpose of this role..."
                                />
                                {errors.description && (
                                    <p className="mt-1 text-sm text-red-600">{errors.description}</p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Permissions */}
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 className="text-base font-semibold leading-6 text-gray-900">Permissions</h3>
                            <p className="mt-1 text-sm text-gray-500">
                                Select the permissions for this role.
                            </p>
                        </div>
                        <div className="px-4 py-5 sm:p-6 space-y-6">
                            {permissionGroups.length === 0 ? (
                                <p className="text-sm text-gray-500">No permissions available.</p>
                            ) : (
                                permissionGroups.map(([category, perms]) => {
                                    const categoryPerms = Array.isArray(perms) ? perms : Object.values(perms);
                                    const ids = categoryPerms.map((p) => p.id);
                                    const allSelected = ids.length > 0 && ids.every((id) => form.permissions.includes(id));

                                    return (
                                        <div key={category} className="border border-gray-200 rounded-md p-4">
                                            <div className="flex items-center justify-between mb-3">
                                                <h4 className="text-sm font-semibold text-gray-900 capitalize">
                                                    {category || 'General'}
                                                </h4>
                                                <button
                                                    type="button"
                                                    onClick={() => toggleCategory(categoryPerms)}
                                                    className="text-xs text-indigo-600 hover:text-indigo-500 font-medium"
                                                >
                                                    {allSelected ? 'Deselect All' : 'Select All'}
                                                </button>
                                            </div>
                                            <div className="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                                {categoryPerms.map((permission) => (
                                                    <label
                                                        key={permission.id}
                                                        className="flex items-center gap-2 text-sm text-gray-700 cursor-pointer"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={form.permissions.includes(permission.id)}
                                                            onChange={() => togglePermission(permission.id)}
                                                            className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                        />
                                                        {permission.name.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())}
                                                    </label>
                                                ))}
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                            {errors.permissions && (
                                <p className="text-sm text-red-600">{errors.permissions}</p>
                            )}
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3">
                        <Link
                            href="/admin/roles"
                            className="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Creating...' : 'Create Role'}
                        </button>
                    </div>
                </form>
            </div>
        </>
    );
}
