import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';

function StatusBadge({ active }) {
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
            {active ? 'Active' : 'Inactive'}
        </span>
    );
}

function SyncBadge({ moodleId }) {
    return moodleId ? (
        <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <svg className="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
            </svg>
            Synced (ID: {moodleId})
        </span>
    ) : (
        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            Not Synced
        </span>
    );
}

function InfoRow({ label, children }) {
    return (
        <div className="sm:grid sm:grid-cols-3 sm:gap-4 px-6 py-4">
            <dt className="text-sm font-medium text-gray-500">{label}</dt>
            <dd className="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{children}</dd>
        </div>
    );
}

export default function CourseShow({ course }) {
    const { flash } = usePage().props;
    const [editing, setEditing] = useState(false);
    const [confirmingDelete, setConfirmingDelete] = useState(false);

    const form = useForm({
        title: course.title || '',
        description: course.description || '',
        is_active: course.is_active ? '1' : '0',
        audience_type: course.audience_type || 'all',
        enrollment_type: course.enrollment_type || 'OPEN_ENROLLMENT',
    });

    const deleteForm = useForm({});

    const handleUpdate = (e) => {
        e.preventDefault();
        form.put(`/admin/courses/${course.id}`, {
            onSuccess: () => setEditing(false),
        });
    };

    const handleDelete = (e) => {
        e.preventDefault();
        deleteForm.delete(`/admin/courses/${course.id}`);
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const imageUrl = course.image_url || null;

    return (
        <>
            <Head title={course.title} />

            <div className="space-y-6">
                {/* Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/admin/courses"
                            className="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                        >
                            <svg className="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Back
                        </Link>
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900">{course.title}</h1>
                            <p className="mt-1 text-sm text-gray-500">Course ID: {course.id}</p>
                        </div>
                    </div>
                    <div className="mt-4 sm:mt-0 flex gap-2">
                        {!editing && (
                            <button
                                onClick={() => setEditing(true)}
                                className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                            >
                                <svg className="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                Edit
                            </button>
                        )}
                        <button
                            onClick={() => setConfirmingDelete(true)}
                            className="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors"
                        >
                            <svg className="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="rounded-md bg-green-50 p-4">
                        <div className="flex">
                            <svg className="h-5 w-5 text-green-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                            </svg>
                            <p className="ml-3 text-sm font-medium text-green-800">{flash.success}</p>
                        </div>
                    </div>
                )}
                {flash?.error && (
                    <div className="rounded-md bg-red-50 p-4">
                        <div className="flex">
                            <svg className="h-5 w-5 text-red-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" />
                            </svg>
                            <p className="ml-3 text-sm font-medium text-red-800">{flash.error}</p>
                        </div>
                    </div>
                )}

                {/* Delete Confirmation Modal */}
                {confirmingDelete && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <div className="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                                    <svg className="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-gray-900">Delete Course</h3>
                            </div>
                            <p className="text-sm text-gray-600 mb-2">
                                Are you sure you want to delete <strong>"{course.title}"</strong>?
                            </p>
                            <p className="text-sm text-gray-500 mb-6">
                                This action cannot be undone. {course.moodle_course_id ? 'The course will also be removed from Moodle.' : ''}
                                All enrollments and data associated with this course will be permanently deleted.
                            </p>
                            <div className="flex justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => setConfirmingDelete(false)}
                                    className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={handleDelete}
                                    disabled={deleteForm.processing}
                                    className="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 disabled:opacity-50 transition-colors"
                                >
                                    {deleteForm.processing ? 'Deleting...' : 'Delete Course'}
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {editing ? (
                    /* Edit Form */
                    <div className="rounded-lg bg-white shadow">
                        <div className="px-6 py-5 border-b border-gray-200">
                            <h3 className="text-base font-semibold text-gray-900">Edit Course</h3>
                            <p className="mt-1 text-sm text-gray-500">Update course details below.</p>
                        </div>
                        <form onSubmit={handleUpdate} className="px-6 py-5 space-y-5">
                            <div>
                                <label htmlFor="title" className="block text-sm font-medium text-gray-700">Title</label>
                                <input
                                    id="title"
                                    type="text"
                                    value={form.data.title}
                                    onChange={(e) => form.setData('title', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                {form.errors.title && <p className="mt-1 text-sm text-red-600">{form.errors.title}</p>}
                            </div>
                            <div>
                                <label htmlFor="description" className="block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    id="description"
                                    rows={4}
                                    value={form.data.description}
                                    onChange={(e) => form.setData('description', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                {form.errors.description && <p className="mt-1 text-sm text-red-600">{form.errors.description}</p>}
                            </div>
                            <div className="grid grid-cols-1 gap-5 sm:grid-cols-3">
                                <div>
                                    <label htmlFor="is_active" className="block text-sm font-medium text-gray-700">Status</label>
                                    <select
                                        id="is_active"
                                        value={form.data.is_active}
                                        onChange={(e) => form.setData('is_active', e.target.value)}
                                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div>
                                    <label htmlFor="audience_type" className="block text-sm font-medium text-gray-700">Audience</label>
                                    <select
                                        id="audience_type"
                                        value={form.data.audience_type}
                                        onChange={(e) => form.setData('audience_type', e.target.value)}
                                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="all">All Users</option>
                                        <option value="moh">MOH Staff Only</option>
                                        <option value="external">External Users Only</option>
                                    </select>
                                </div>
                                <div>
                                    <label htmlFor="enrollment_type" className="block text-sm font-medium text-gray-700">Enrollment</label>
                                    <select
                                        id="enrollment_type"
                                        value={form.data.enrollment_type}
                                        onChange={(e) => form.setData('enrollment_type', e.target.value)}
                                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="OPEN_ENROLLMENT">Open Enrollment</option>
                                        <option value="APPROVAL_REQUIRED">Requires Approval</option>
                                    </select>
                                </div>
                            </div>
                            <div className="flex justify-end gap-3 pt-4 border-t border-gray-200">
                                <button
                                    type="button"
                                    onClick={() => { setEditing(false); form.reset(); }}
                                    className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={form.processing}
                                    className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-50 transition-colors"
                                >
                                    {form.processing ? 'Saving...' : 'Save Changes'}
                                </button>
                            </div>
                        </form>
                    </div>
                ) : (
                    /* View Mode */
                    <>
                        {/* Course Image + Details */}
                        <div className="rounded-lg bg-white shadow overflow-hidden">
                            {imageUrl && (
                                <div className="h-48 bg-gray-100">
                                    <img src={imageUrl} alt={course.title} className="h-full w-full object-cover" />
                                </div>
                            )}
                            <div className="divide-y divide-gray-200">
                                <InfoRow label="Title">{course.title}</InfoRow>
                                <InfoRow label="Short Name">{course.moodle_course_shortname || '-'}</InfoRow>
                                <InfoRow label="Description">
                                    <p className="whitespace-pre-wrap">{course.description || 'No description provided.'}</p>
                                </InfoRow>
                                <InfoRow label="Status"><StatusBadge active={course.is_active} /></InfoRow>
                                <InfoRow label="Moodle Sync"><SyncBadge moodleId={course.moodle_course_id} /></InfoRow>
                                <InfoRow label="Audience">
                                    {course.audience_type === 'MOH_ONLY' || course.audience_type === 'moh' ? 'MOH Staff Only' :
                                     course.audience_type === 'EXTERNAL_ONLY' || course.audience_type === 'external' ? 'External Users Only' :
                                     'All Users'}
                                </InfoRow>
                                <InfoRow label="Enrollment Type">
                                    {course.enrollment_type === 'APPROVAL_REQUIRED' ? 'Requires Approval' :
                                     course.is_free === false ? 'Requires Approval' : 'Open Enrollment'}
                                </InfoRow>
                                <InfoRow label="Enrollments">{course.enrollments_count ?? 0}</InfoRow>
                                <InfoRow label="Created">{formatDate(course.created_at)}</InfoRow>
                                <InfoRow label="Last Updated">{formatDate(course.updated_at)}</InfoRow>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </>
    );
}
