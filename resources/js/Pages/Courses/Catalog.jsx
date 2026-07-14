import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState, useEffect, useMemo } from 'react';

function AudienceBadge({ type }) {
    const map = {
        moh: { label: 'MOH Staff', cls: 'bg-emerald-100 text-emerald-700' },
        moh_only: { label: 'MOH Staff', cls: 'bg-emerald-100 text-emerald-700' },
        MOH_ONLY: { label: 'MOH Staff', cls: 'bg-emerald-100 text-emerald-700' },
        external: { label: 'External', cls: 'bg-amber-100 text-amber-700' },
        external_only: { label: 'External', cls: 'bg-amber-100 text-amber-700' },
        EXTERNAL_ONLY: { label: 'External', cls: 'bg-amber-100 text-amber-700' },
        all: { label: 'Everyone', cls: 'bg-blue-100 text-blue-700' },
        BOTH: { label: 'Everyone', cls: 'bg-blue-100 text-blue-700' },
    };
    const m = map[type] || { label: type, cls: 'bg-gray-100 text-gray-700' };
    return <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${m.cls}`}>{m.label}</span>;
}

function EnrollmentTypeBadge({ type }) {
    if (type === 'APPROVAL_REQUIRED' || type === 'requires_approval') {
        return <span className="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">Approval</span>;
    }
    return <span className="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Open</span>;
}

export default function Catalog({ courses = [], categories = [], filters = {} }) {
    const { flash } = usePage().props;
    const [search, setSearch] = useState(filters.search || '');
    const [debouncedSearch, setDebouncedSearch] = useState(filters.search || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.category || '');

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(search), 300);
        return () => clearTimeout(timer);
    }, [search]);

    const filteredCourses = useMemo(() => {
        let list = courses;
        if (selectedCategory) {
            list = list.filter((c) => String(c.category_id) === String(selectedCategory));
        }
        if (debouncedSearch.trim()) {
            const term = debouncedSearch.toLowerCase();
            list = list.filter(
                (c) =>
                    c.title.toLowerCase().includes(term) ||
                    (c.description && c.description.toLowerCase().includes(term))
            );
        }
        return list;
    }, [debouncedSearch, selectedCategory, courses]);

    const handleEnroll = (courseId) => {
        router.post(`/courses/${courseId}/enroll`);
    };

    const handleRequestAccess = (courseId) => {
        router.post(`/courses/${courseId}/request-access`);
    };

    const getActionButton = (course) => {
        const status = course.user_enrollment_status;
        switch (status) {
            case 'enrolled':
                return (
                    <a href={`/courses/${course.id}/access-moodle`} className="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-green-500 transition-colors">
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        Go to Course
                    </a>
                );
            case 'pending':
                return <span className="inline-flex w-full items-center justify-center rounded-lg bg-yellow-100 px-3 py-2.5 text-sm font-medium text-yellow-800">Pending Approval</span>;
            case 'syncing':
                return <span className="inline-flex w-full items-center justify-center rounded-lg bg-blue-100 px-3 py-2.5 text-sm font-medium text-blue-800">Setting up...</span>;
            case 'can_request':
            case 'requires_approval':
                return (
                    <button onClick={() => handleRequestAccess(course.id)} className="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
                        Request Access
                    </button>
                );
            case 'open':
            default:
                return (
                    <button onClick={() => handleEnroll(course.id)} className="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">
                        Enroll Now
                    </button>
                );
        }
    };

    return (
        <>
            <Head title="Course Catalog" />

            <div className="space-y-6">
                {flash?.success && (
                    <div className="rounded-md bg-green-50 p-4"><p className="text-sm font-medium text-green-800">{flash.success}</p></div>
                )}
                {flash?.error && (
                    <div className="rounded-md bg-red-50 p-4"><p className="text-sm font-medium text-red-800">{flash.error}</p></div>
                )}

                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Course Catalog</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Browse all available courses. Find something that interests you and enroll.
                    </p>
                </div>

                {/* Search + Category Filter */}
                <div className="flex flex-col gap-3 sm:flex-row">
                    <div className="relative flex-1">
                        <svg className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            type="text"
                            placeholder="Search by title or description..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="block w-full rounded-lg border-gray-300 pl-10 pr-10 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        {search && (
                            <button onClick={() => setSearch('')} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        )}
                    </div>
                    {categories.length > 0 && (
                        <select
                            value={selectedCategory}
                            onChange={(e) => setSelectedCategory(e.target.value)}
                            className="appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pl-3.5 pr-9 text-sm font-medium text-gray-700 shadow-sm hover:border-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                        >
                            <option value="">All Categories</option>
                            {categories.map((cat) => (
                                <option key={cat.id} value={cat.id}>{cat.name}</option>
                            ))}
                        </select>
                    )}
                </div>

                {/* Result count */}
                <p className="text-sm text-gray-500">
                    {filteredCourses.length} {filteredCourses.length === 1 ? 'course' : 'courses'} available
                </p>

                {/* Course Grid */}
                <div className="min-h-[300px]">
                    {filteredCourses.length > 0 ? (
                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {filteredCourses.map((course) => (
                                <div key={course.id} className="group overflow-hidden rounded-xl bg-white shadow hover:shadow-lg transition-all duration-200 flex flex-col">
                                    {/* Image */}
                                    <div className="relative">
                                        {course.image_url ? (
                                            <img src={course.image_url} alt={course.title} className="h-44 w-full object-cover" />
                                        ) : (
                                            <div className="h-44 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                                <svg className="h-14 w-14 text-white/40" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                                                    <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                                                </svg>
                                            </div>
                                        )}
                                        {/* Audience + Enrollment badges on image */}
                                        <div className="absolute top-2 left-2 flex flex-wrap gap-1.5">
                                            <AudienceBadge type={course.audience_type} />
                                            <EnrollmentTypeBadge type={course.enrollment_type} />
                                        </div>
                                    </div>

                                    <div className="flex flex-1 flex-col p-5">
                                        {/* Category */}
                                        {course.category && (
                                            <p className="text-xs font-medium text-indigo-600 mb-1">
                                                {course.category.name || course.category}
                                            </p>
                                        )}

                                        {/* Title */}
                                        <Link href={`/catalog/${course.id}`} className="hover:text-indigo-600 transition-colors">
                                            <h3 className="text-base font-semibold text-gray-900 line-clamp-2">{course.title}</h3>
                                        </Link>

                                        {/* Description */}
                                        {course.description && (
                                            <p className="mt-2 text-sm text-gray-500 line-clamp-3 flex-1">{course.description}</p>
                                        )}

                                        {/* Action */}
                                        <div className="mt-4 pt-3 border-t border-gray-100">
                                            {getActionButton(course)}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="rounded-lg bg-white p-12 text-center shadow">
                            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <h3 className="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                            <p className="mt-1 text-sm text-gray-500">
                                {debouncedSearch.trim() ? `No courses match "${debouncedSearch}".` : 'Check back later for new courses.'}
                            </p>
                            {(debouncedSearch.trim() || selectedCategory) && (
                                <button
                                    onClick={() => { setSearch(''); setSelectedCategory(''); }}
                                    className="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                                >
                                    Clear Filters
                                </button>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
