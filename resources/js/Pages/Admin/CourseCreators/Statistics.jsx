import { Head, Link } from '@inertiajs/react';

function StatCard({ label, value, color = 'indigo' }) {
    const colorClasses = {
        indigo: 'bg-indigo-50 text-indigo-700 border-indigo-200',
        green: 'bg-green-50 text-green-700 border-green-200',
        blue: 'bg-blue-50 text-blue-700 border-blue-200',
        yellow: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    };

    return (
        <div className={`rounded-lg border p-5 ${colorClasses[color]}`}>
            <dt className="truncate text-sm font-medium">{label}</dt>
            <dd className="mt-1 text-3xl font-semibold tracking-tight">{value}</dd>
        </div>
    );
}

export default function Statistics({ stats = {} }) {
    const byType = stats.by_type || {};
    const internalStats = byType.internal || { creators: 0, courses: 0 };
    const externalStats = byType.external || { creators: 0, courses: 0 };
    const recentCourses = stats.recent_courses || [];
    const topCreators = stats.top_creators || [];

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    return (
        <>
            <Head title="Course Creator Statistics" />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Course Creator Statistics</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Overview of course creator activity and contributions.
                        </p>
                    </div>
                    <div className="mt-4 sm:mt-0">
                        <Link
                            href="/admin/course-creators"
                            className="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors"
                        >
                            Back to Course Creators
                        </Link>
                    </div>
                </div>

                {/* Stats by Type */}
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard label="Internal Creators" value={internalStats.creators} color="green" />
                    <StatCard label="Internal Courses" value={internalStats.courses} color="green" />
                    <StatCard label="External Creators" value={externalStats.creators} color="blue" />
                    <StatCard label="External Courses" value={externalStats.courses} color="blue" />
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* Top Creators */}
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 className="text-base font-semibold leading-6 text-gray-900">Top Course Creators</h3>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Creator
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Courses
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {topCreators.length === 0 ? (
                                        <tr>
                                            <td colSpan={3} className="px-6 py-12 text-center text-sm text-gray-500">
                                                No course creators found.
                                            </td>
                                        </tr>
                                    ) : (
                                        topCreators.map((creator) => (
                                            <tr key={creator.id} className="hover:bg-gray-50 transition-colors">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {creator.first_name} {creator.last_name}
                                                    </div>
                                                    <div className="text-sm text-gray-500">{creator.email}</div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                        creator.user_type === 'internal'
                                                            ? 'bg-green-100 text-green-800'
                                                            : 'bg-blue-100 text-blue-800'
                                                    }`}>
                                                        {creator.user_type ? creator.user_type.charAt(0).toUpperCase() + creator.user_type.slice(1) : 'N/A'}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                                    {creator.created_courses_count || 0}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Recent Courses */}
                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 className="text-base font-semibold leading-6 text-gray-900">Recently Created Courses</h3>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Course
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Creator
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {recentCourses.length === 0 ? (
                                        <tr>
                                            <td colSpan={3} className="px-6 py-12 text-center text-sm text-gray-500">
                                                No courses created yet.
                                            </td>
                                        </tr>
                                    ) : (
                                        recentCourses.map((course) => (
                                            <tr key={course.id} className="hover:bg-gray-50 transition-colors">
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {course.title}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {course.creator
                                                        ? `${course.creator.first_name} ${course.creator.last_name}`
                                                        : 'Unknown'}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {formatDate(course.created_at)}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
