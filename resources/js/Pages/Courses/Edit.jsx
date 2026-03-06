import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function Edit({ course }) {
    const { flash } = usePage().props;
    const [moodleEnabled, setMoodleEnabled] = useState(!!course.moodle_enabled || !!course.moodle_course_id);

    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        title: course.title || '',
        description: course.description || '',
        status: course.status || 'active',
        image: null,
        audience_type: course.audience_type || 'all',
        enrollment_type: course.enrollment_type || 'open',
        moodle_enabled: !!course.moodle_enabled || !!course.moodle_course_id,
        moodle_short_name: course.moodle_short_name || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/courses/${course.id}`, {
            forceFormData: true,
        });
    };

    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
            router.delete(`/courses/${course.id}`);
        }
    };

    const handleMoodleToggle = (e) => {
        const checked = e.target.checked;
        setMoodleEnabled(checked);
        setData('moodle_enabled', checked);
    };

    return (
        <>
            <Head title={`Edit: ${course.title}`} />

            <div className="space-y-6">
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

                {/* Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Edit Course</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Update course details for "{course.title}".
                        </p>
                    </div>
                    <Link
                        href="/courses"
                        className="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        <svg className="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fillRule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clipRule="evenodd" />
                        </svg>
                        Back to Courses
                    </Link>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Basic Information */}
                    <div className="rounded-lg bg-white p-6 shadow">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                        <div className="space-y-4">
                            {/* Title */}
                            <div>
                                <label htmlFor="title" className="block text-sm font-medium text-gray-700">
                                    Title
                                </label>
                                <input
                                    id="title"
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="Enter course title"
                                />
                                {errors.title && (
                                    <p className="mt-1 text-sm text-red-600">{errors.title}</p>
                                )}
                            </div>

                            {/* Description */}
                            <div>
                                <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    rows={4}
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="Enter course description"
                                />
                                {errors.description && (
                                    <p className="mt-1 text-sm text-red-600">{errors.description}</p>
                                )}
                            </div>

                            {/* Status */}
                            <div>
                                <label htmlFor="status" className="block text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    value={data.status}
                                    onChange={(e) => setData('status', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                {errors.status && (
                                    <p className="mt-1 text-sm text-red-600">{errors.status}</p>
                                )}
                            </div>

                            {/* Course Image */}
                            <div>
                                <label htmlFor="image" className="block text-sm font-medium text-gray-700">
                                    Course Image
                                </label>
                                {course.image_url && (
                                    <div className="mt-2 mb-3">
                                        <img
                                            src={course.image_url}
                                            alt={course.title}
                                            className="h-32 w-48 rounded-md object-cover"
                                        />
                                        <p className="mt-1 text-xs text-gray-500">Current image. Upload a new one to replace.</p>
                                    </div>
                                )}
                                <input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) => setData('image', e.target.files[0])}
                                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                {errors.image && (
                                    <p className="mt-1 text-sm text-red-600">{errors.image}</p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Access Control */}
                    <div className="rounded-lg bg-white p-6 shadow">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">Access Control</h2>
                        <div className="space-y-4">
                            {/* Audience Type */}
                            <div>
                                <label htmlFor="audience_type" className="block text-sm font-medium text-gray-700">
                                    Audience Type
                                </label>
                                <select
                                    id="audience_type"
                                    value={data.audience_type}
                                    onChange={(e) => setData('audience_type', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="all">All Users</option>
                                    <option value="moh_only">MOH Only</option>
                                    <option value="external_only">External Only</option>
                                </select>
                                {errors.audience_type && (
                                    <p className="mt-1 text-sm text-red-600">{errors.audience_type}</p>
                                )}
                            </div>

                            {/* Enrollment Type */}
                            <div>
                                <label htmlFor="enrollment_type" className="block text-sm font-medium text-gray-700">
                                    Enrollment Type
                                </label>
                                <select
                                    id="enrollment_type"
                                    value={data.enrollment_type}
                                    onChange={(e) => setData('enrollment_type', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="open">Open</option>
                                    <option value="requires_approval">Requires Approval</option>
                                </select>
                                {errors.enrollment_type && (
                                    <p className="mt-1 text-sm text-red-600">{errors.enrollment_type}</p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Moodle LMS Integration */}
                    <div className="rounded-lg bg-white p-6 shadow">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">Moodle LMS Integration</h2>
                        <div className="space-y-4">
                            {/* Enable Moodle */}
                            <div className="flex items-center">
                                <input
                                    id="moodle_enabled"
                                    type="checkbox"
                                    checked={moodleEnabled}
                                    onChange={handleMoodleToggle}
                                    className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label htmlFor="moodle_enabled" className="ml-2 block text-sm font-medium text-gray-700">
                                    Enable Moodle Integration
                                </label>
                            </div>
                            {errors.moodle_enabled && (
                                <p className="text-sm text-red-600">{errors.moodle_enabled}</p>
                            )}

                            {/* Moodle Short Name */}
                            {moodleEnabled && (
                                <div>
                                    <label htmlFor="moodle_short_name" className="block text-sm font-medium text-gray-700">
                                        Moodle Short Name
                                    </label>
                                    <input
                                        id="moodle_short_name"
                                        type="text"
                                        value={data.moodle_short_name}
                                        onChange={(e) => setData('moodle_short_name', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Enter Moodle course short name"
                                    />
                                    {errors.moodle_short_name && (
                                        <p className="mt-1 text-sm text-red-600">{errors.moodle_short_name}</p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-500">
                                        This must match the short name of the course in your Moodle instance.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-between">
                        <button
                            type="button"
                            onClick={handleDelete}
                            className="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                        >
                            Delete Course
                        </button>
                        <div className="flex items-center gap-3">
                            <Link
                                href="/courses"
                                className="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Saving...' : 'Save Changes'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
}
