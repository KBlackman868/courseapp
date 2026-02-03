import { Head } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    UsersIcon,
    AcademicCapIcon,
    ClipboardDocumentCheckIcon,
    ExclamationTriangleIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    PlusIcon,
    ArrowDownTrayIcon,
    UserPlusIcon,
} from '@heroicons/react/24/outline';

// Reusable Stat Card Component
function StatCard({ title, value, change, changeType, icon: Icon, color = 'indigo' }) {
    const colorClasses = {
        indigo: 'bg-indigo-500',
        green: 'bg-green-500',
        yellow: 'bg-yellow-500',
        red: 'bg-red-500',
        blue: 'bg-blue-500',
    };

    return (
        <div className="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6 sm:py-6">
            <dt>
                <div className={`absolute rounded-md ${colorClasses[color]} p-3`}>
                    <Icon className="h-6 w-6 text-white" aria-hidden="true" />
                </div>
                <p className="ml-16 truncate text-sm font-medium text-gray-500">{title}</p>
            </dt>
            <dd className="ml-16 flex items-baseline">
                <p className="text-2xl font-semibold text-gray-900">{value}</p>
                {change && (
                    <p
                        className={`ml-2 flex items-baseline text-sm font-semibold ${
                            changeType === 'increase' ? 'text-green-600' : 'text-red-600'
                        }`}
                    >
                        {changeType === 'increase' ? (
                            <ArrowTrendingUpIcon className="h-5 w-5 flex-shrink-0 self-center text-green-500" />
                        ) : (
                            <ArrowTrendingDownIcon className="h-5 w-5 flex-shrink-0 self-center text-red-500" />
                        )}
                        <span className="ml-1">{change}</span>
                    </p>
                )}
            </dd>
        </div>
    );
}

// Quick Action Button Component
function QuickAction({ icon: Icon, label, onClick, variant = 'primary' }) {
    const variants = {
        primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        secondary: 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500',
    };

    return (
        <button
            type="button"
            onClick={onClick}
            className={`inline-flex items-center gap-x-2 rounded-md px-3.5 py-2.5 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors ${variants[variant]}`}
        >
            <Icon className="-ml-0.5 h-5 w-5" aria-hidden="true" />
            {label}
        </button>
    );
}

// Data Table Component
function DataTable({ title, columns, data, emptyMessage = 'No data available' }) {
    return (
        <div className="overflow-hidden rounded-lg bg-white shadow">
            <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 className="text-base font-semibold leading-6 text-gray-900">{title}</h3>
            </div>
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            {columns.map((column) => (
                                <th
                                    key={column.key}
                                    scope="col"
                                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    {column.label}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {data.length === 0 ? (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="px-6 py-12 text-center text-sm text-gray-500"
                                >
                                    {emptyMessage}
                                </td>
                            </tr>
                        ) : (
                            data.map((row, rowIndex) => (
                                <tr key={rowIndex} className="hover:bg-gray-50 transition-colors">
                                    {columns.map((column) => (
                                        <td
                                            key={column.key}
                                            className="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {column.render ? column.render(row) : row[column.key]}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

// Status Badge Component
function StatusBadge({ status }) {
    const statusStyles = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
    };

    return (
        <span
            className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                statusStyles[status] || statusStyles.pending
            }`}
        >
            {status.charAt(0).toUpperCase() + status.slice(1)}
        </span>
    );
}

export default function AdminDashboard({
    stats = {},
    recentActivity = [],
    pendingRequests = [],
}) {
    // Default stats if not provided
    const defaultStats = {
        totalUsers: stats.totalUsers || 1247,
        totalCourses: stats.totalCourses || 34,
        pendingRequests: stats.pendingRequests || 12,
        activeEnrollments: stats.activeEnrollments || 892,
    };

    // Sample recent activity data
    const sampleActivity = recentActivity.length > 0 ? recentActivity : [
        { id: 1, user: 'John Doe', action: 'Enrolled in Introduction to HIV Prevention', time: '2 minutes ago', type: 'enrollment' },
        { id: 2, user: 'Jane Smith', action: 'Completed Health Worker Training', time: '15 minutes ago', type: 'completion' },
        { id: 3, user: 'Mike Johnson', action: 'Requested access to Advanced Nursing Course', time: '1 hour ago', type: 'request' },
        { id: 4, user: 'Sarah Williams', action: 'Updated profile information', time: '2 hours ago', type: 'update' },
        { id: 5, user: 'Admin User', action: 'Created new course: Infection Control 101', time: '3 hours ago', type: 'create' },
    ];

    // Sample pending requests data
    const sampleRequests = pendingRequests.length > 0 ? pendingRequests : [
        { id: 1, user: 'Emily Chen', course: 'Advanced HIV Treatment', requestedAt: '2024-02-03', status: 'pending' },
        { id: 2, user: 'Robert Brown', course: 'Nursing Fundamentals', requestedAt: '2024-02-02', status: 'pending' },
        { id: 3, user: 'Lisa Anderson', course: 'Public Health Basics', requestedAt: '2024-02-01', status: 'pending' },
    ];

    const activityColumns = [
        { key: 'user', label: 'User' },
        { key: 'action', label: 'Action' },
        { key: 'time', label: 'Time' },
    ];

    const requestColumns = [
        { key: 'user', label: 'User' },
        { key: 'course', label: 'Course' },
        { key: 'requestedAt', label: 'Requested' },
        {
            key: 'status',
            label: 'Status',
            render: (row) => <StatusBadge status={row.status} />,
        },
        {
            key: 'actions',
            label: 'Actions',
            render: () => (
                <div className="flex gap-2">
                    <button className="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        Approve
                    </button>
                    <button className="text-red-600 hover:text-red-900 text-sm font-medium">
                        Reject
                    </button>
                </div>
            ),
        },
    ];

    return (
        <AdminLayout title="Dashboard">
            <Head title="Admin Dashboard" />

            <div className="space-y-6">
                {/* Page Header with Quick Actions */}
                <div className="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Welcome back! Here's what's happening with your platform.
                        </p>
                    </div>
                    <div className="mt-4 sm:mt-0 flex flex-wrap gap-3">
                        <QuickAction
                            icon={UserPlusIcon}
                            label="Add User"
                            onClick={() => console.log('Add user')}
                        />
                        <QuickAction
                            icon={PlusIcon}
                            label="Create Course"
                            onClick={() => console.log('Create course')}
                            variant="secondary"
                        />
                        <QuickAction
                            icon={ArrowDownTrayIcon}
                            label="Export"
                            onClick={() => console.log('Export')}
                            variant="secondary"
                        />
                    </div>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        title="Total Users"
                        value={defaultStats.totalUsers.toLocaleString()}
                        change="+12%"
                        changeType="increase"
                        icon={UsersIcon}
                        color="indigo"
                    />
                    <StatCard
                        title="Active Courses"
                        value={defaultStats.totalCourses}
                        change="+3"
                        changeType="increase"
                        icon={AcademicCapIcon}
                        color="green"
                    />
                    <StatCard
                        title="Pending Requests"
                        value={defaultStats.pendingRequests}
                        icon={ClipboardDocumentCheckIcon}
                        color="yellow"
                    />
                    <StatCard
                        title="Active Enrollments"
                        value={defaultStats.activeEnrollments.toLocaleString()}
                        change="+8%"
                        changeType="increase"
                        icon={ExclamationTriangleIcon}
                        color="blue"
                    />
                </div>

                {/* Two Column Layout for Tables */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* Recent Activity */}
                    <DataTable
                        title="Recent Activity"
                        columns={activityColumns}
                        data={sampleActivity}
                        emptyMessage="No recent activity"
                    />

                    {/* Pending Requests */}
                    <DataTable
                        title="Pending Access Requests"
                        columns={requestColumns}
                        data={sampleRequests}
                        emptyMessage="No pending requests"
                    />
                </div>

                {/* System Status Section */}
                <div className="rounded-lg bg-white shadow">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">System Status</h3>
                    </div>
                    <div className="px-4 py-5 sm:p-6">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div className="flex items-center gap-3">
                                <div className="h-3 w-3 rounded-full bg-green-500"></div>
                                <span className="text-sm text-gray-700">Laravel Application: Operational</span>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="h-3 w-3 rounded-full bg-green-500"></div>
                                <span className="text-sm text-gray-700">Database: Connected</span>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="h-3 w-3 rounded-full bg-green-500"></div>
                                <span className="text-sm text-gray-700">Moodle SSO: Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
