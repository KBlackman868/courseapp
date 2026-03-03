import { Head, Link } from '@inertiajs/react';

export default function AccountPending({ user, status = 'pending', requestedAt }) {
    const isPending = status === 'pending';
    const isRejected = status === 'rejected';

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

    return (
        <>
            <Head title={isPending ? 'Account Pending Approval' : 'Account Status'} />

            <div className="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4 py-12">
                <div className="w-full max-w-md">
                    {/* Main Card */}
                    <div className="overflow-hidden rounded-lg bg-white shadow-lg">
                        <div className="px-6 py-8">
                            {/* Status Icon */}
                            <div className="flex justify-center mb-6">
                                {isPending ? (
                                    <div className="rounded-full bg-yellow-100 p-4">
                                        <svg className="h-12 w-12 text-yellow-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                ) : (
                                    <div className="rounded-full bg-red-100 p-4">
                                        <svg className="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                )}
                            </div>

                            {/* Title */}
                            <h1 className="text-center text-xl font-bold text-gray-900">
                                {isPending
                                    ? 'Your Account is Pending Approval'
                                    : 'Account Request Denied'}
                            </h1>

                            {/* Description */}
                            <p className="mt-3 text-center text-sm text-gray-600">
                                {isPending
                                    ? 'Your registration request has been submitted and is currently under review by our administrators.'
                                    : 'Unfortunately, your account registration request has been denied. Please contact support for more information.'}
                            </p>

                            {/* User Details */}
                            <div className="mt-6 rounded-md bg-gray-50 p-4">
                                <dl className="space-y-3">
                                    <div className="flex justify-between">
                                        <dt className="text-sm font-medium text-gray-500">Email</dt>
                                        <dd className="text-sm text-gray-900">{user?.email}</dd>
                                    </div>
                                    <div className="flex justify-between">
                                        <dt className="text-sm font-medium text-gray-500">Submitted</dt>
                                        <dd className="text-sm text-gray-900">{formatDate(requestedAt)}</dd>
                                    </div>
                                    <div className="flex justify-between">
                                        <dt className="text-sm font-medium text-gray-500">Status</dt>
                                        <dd>
                                            <span
                                                className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                                    isPending
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-red-100 text-red-800'
                                                }`}
                                            >
                                                {isPending ? 'Pending Review' : 'Denied'}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {/* What happens next - only for pending */}
                            {isPending && (
                                <div className="mt-6">
                                    <h2 className="text-sm font-semibold text-gray-900">What happens next?</h2>
                                    <ul className="mt-3 space-y-3">
                                        <li className="flex items-start gap-3">
                                            <div className="flex-shrink-0 mt-0.5">
                                                <div className="h-5 w-5 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span className="text-xs font-bold text-indigo-600">1</span>
                                                </div>
                                            </div>
                                            <p className="text-sm text-gray-600">
                                                An administrator will review your registration request.
                                            </p>
                                        </li>
                                        <li className="flex items-start gap-3">
                                            <div className="flex-shrink-0 mt-0.5">
                                                <div className="h-5 w-5 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span className="text-xs font-bold text-indigo-600">2</span>
                                                </div>
                                            </div>
                                            <p className="text-sm text-gray-600">
                                                You will receive an email notification once your account is approved.
                                            </p>
                                        </li>
                                        <li className="flex items-start gap-3">
                                            <div className="flex-shrink-0 mt-0.5">
                                                <div className="h-5 w-5 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span className="text-xs font-bold text-indigo-600">3</span>
                                                </div>
                                            </div>
                                            <p className="text-sm text-gray-600">
                                                After approval, you can log in and access all available courses.
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                            )}

                            {/* Sign Out Button */}
                            <div className="mt-8">
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    className="w-full inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition-colors"
                                >
                                    <svg className="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                    </svg>
                                    Sign Out
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Contact Support Footer */}
                    <div className="mt-6 text-center">
                        <p className="text-sm text-gray-500">
                            Need help?{' '}
                            <a
                                href="mailto:support@example.com"
                                className="font-medium text-indigo-600 hover:text-indigo-500"
                            >
                                Contact Support
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </>
    );
}
