import { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

const inputClass = 'w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20';

export default function Create() {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        status: 'draft',
        image: null,
        audience_type: 'all',
        enrollment_type: 'open',
        sync_to_moodle: false,
        moodle_course_shortname: '',
        moodle_category_id: '',
    });
    const [imagePreview, setImagePreview] = useState(null);

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('image', file);
            const reader = new FileReader();
            reader.onloadend = () => setImagePreview(reader.result);
            reader.readAsDataURL(file);
        }
    };

    const removeImage = () => {
        setData('image', null);
        setImagePreview(null);
    };

    const submit = (e) => {
        e.preventDefault();
        post('/courses/store', {
            forceFormData: true,
        });
    };

    return (
        <>
            <Head title="Create Course" />

            <div className="max-w-3xl mx-auto">
                {/* Page Header */}
                <div className="mb-8">
                    <Link
                        href="/admin/courses"
                        className="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4 transition-colors"
                    >
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                        Back to Course Management
                    </Link>
                    <h1 className="text-2xl font-bold text-gray-900">Create New Course</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Fill in the details below to create a new course. Required fields are marked with an asterisk.
                    </p>
                </div>

                {/* Flash Messages */}
                {flash?.error && (
                    <div className="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                        <p className="text-sm font-medium text-red-700">{flash.error}</p>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-8">
                    {/* Basic Information */}
                    <div className="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-1">Basic Information</h2>
                        <p className="text-sm text-gray-500 mb-5">Core details about your course.</p>

                        <div className="space-y-5">
                            <div>
                                <label htmlFor="title" className="block text-sm font-medium text-gray-700 mb-1.5">
                                    Course Title <span className="text-red-500">*</span>
                                </label>
                                <input
                                    id="title"
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    className={inputClass}
                                    placeholder="e.g., Introduction to Public Health"
                                    autoFocus
                                />
                                {errors.title && <p className="mt-1.5 text-sm text-red-600">{errors.title}</p>}
                            </div>

                            <div>
                                <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1.5">
                                    Course Description <span className="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="description"
                                    rows={5}
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    className={inputClass + ' resize-none'}
                                    placeholder="Describe what this course covers, learning objectives, and target audience..."
                                />
                                {errors.description && <p className="mt-1.5 text-sm text-red-600">{errors.description}</p>}
                            </div>

                            <div>
                                <label htmlFor="status" className="block text-sm font-medium text-gray-700 mb-1.5">
                                    Status <span className="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    value={data.status}
                                    onChange={(e) => setData('status', e.target.value)}
                                    className={inputClass}
                                >
                                    <option value="draft">Draft</option>
                                    <option value="active">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                                {errors.status && <p className="mt-1.5 text-sm text-red-600">{errors.status}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Course Image */}
                    <div className="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-1">Course Image</h2>
                        <p className="text-sm text-gray-500 mb-5">Upload a cover image for your course (optional, max 2MB).</p>

                        {imagePreview ? (
                            <div className="relative inline-block">
                                <img
                                    src={imagePreview}
                                    alt="Course preview"
                                    className="h-40 w-64 object-cover rounded-lg ring-1 ring-gray-200"
                                />
                                <button
                                    type="button"
                                    onClick={removeImage}
                                    className="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white shadow-sm hover:bg-red-600 transition-colors"
                                >
                                    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        ) : (
                            <label className="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                                <div className="flex flex-col items-center">
                                    <svg className="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                                    </svg>
                                    <p className="text-sm text-gray-600">Click to upload an image</p>
                                    <p className="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 2MB</p>
                                </div>
                                <input
                                    type="file"
                                    className="hidden"
                                    accept="image/*"
                                    onChange={handleImageChange}
                                />
                            </label>
                        )}
                        {errors.image && <p className="mt-1.5 text-sm text-red-600">{errors.image}</p>}
                    </div>

                    {/* Access Control */}
                    <div className="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-1">Access Control</h2>
                        <p className="text-sm text-gray-500 mb-5">Define who can access this course and the enrollment process.</p>

                        <div className="space-y-5">
                            <div>
                                <label htmlFor="audience_type" className="block text-sm font-medium text-gray-700 mb-1.5">
                                    Target Audience
                                </label>
                                <select
                                    id="audience_type"
                                    value={data.audience_type}
                                    onChange={(e) => setData('audience_type', e.target.value)}
                                    className={inputClass}
                                >
                                    <option value="all">All Users</option>
                                    <option value="moh_staff">MOH Staff Only</option>
                                    <option value="external">External Users Only</option>
                                </select>
                                {errors.audience_type && <p className="mt-1.5 text-sm text-red-600">{errors.audience_type}</p>}
                            </div>

                            <div>
                                <label htmlFor="enrollment_type" className="block text-sm font-medium text-gray-700 mb-1.5">
                                    Enrollment Type
                                </label>
                                <select
                                    id="enrollment_type"
                                    value={data.enrollment_type}
                                    onChange={(e) => setData('enrollment_type', e.target.value)}
                                    className={inputClass}
                                >
                                    <option value="open">Open Enrollment</option>
                                    <option value="approval_required">Requires Approval</option>
                                </select>
                                <p className="mt-1 text-xs text-gray-400">
                                    {data.enrollment_type === 'open'
                                        ? 'Users can enroll themselves without admin approval.'
                                        : 'Users must request access and an admin must approve before enrollment.'}
                                </p>
                                {errors.enrollment_type && <p className="mt-1.5 text-sm text-red-600">{errors.enrollment_type}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Moodle Integration */}
                    <div className="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-1">Moodle Integration</h2>
                        <p className="text-sm text-gray-500 mb-5">Optionally sync this course to Moodle for LMS delivery.</p>

                        <div className="space-y-5">
                            <label className="flex items-center gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={data.sync_to_moodle}
                                    onChange={(e) => setData('sync_to_moodle', e.target.checked)}
                                    className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span className="text-sm font-medium text-gray-700">Create course in Moodle on save</span>
                            </label>

                            {data.sync_to_moodle && (
                                <div className="space-y-4 pl-7 border-l-2 border-indigo-100">
                                    <div>
                                        <label htmlFor="moodle_shortname" className="block text-sm font-medium text-gray-700 mb-1.5">
                                            Moodle Short Name <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="moodle_shortname"
                                            type="text"
                                            value={data.moodle_course_shortname}
                                            onChange={(e) => setData('moodle_course_shortname', e.target.value)}
                                            className={inputClass}
                                            placeholder="e.g., PH101"
                                        />
                                        {errors.moodle_course_shortname && <p className="mt-1.5 text-sm text-red-600">{errors.moodle_course_shortname}</p>}
                                    </div>
                                    <div>
                                        <label htmlFor="moodle_category" className="block text-sm font-medium text-gray-700 mb-1.5">
                                            Moodle Category ID <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="moodle_category"
                                            type="number"
                                            value={data.moodle_category_id}
                                            onChange={(e) => setData('moodle_category_id', e.target.value)}
                                            className={inputClass}
                                            placeholder="e.g., 10"
                                        />
                                        {errors.moodle_category_id && <p className="mt-1.5 text-sm text-red-600">{errors.moodle_category_id}</p>}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Submit */}
                    <div className="flex items-center justify-end gap-4 pb-8">
                        <Link
                            href="/admin/courses"
                            className="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {processing ? (
                                <span className="flex items-center gap-2">
                                    <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                    Creating...
                                </span>
                            ) : (
                                'Create Course'
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </>
    );
}
