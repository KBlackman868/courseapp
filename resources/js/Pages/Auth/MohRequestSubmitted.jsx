import { Head, Link } from '@inertiajs/react';

export default function MohRequestSubmitted() {
    return (
        <>
            <Head title="Request Submitted" />
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 to-purple-50">
                <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8 text-center">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-4">
                        <svg className="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 className="text-2xl font-bold text-gray-900 mb-2">Request Submitted!</h2>
                    <p className="text-gray-600 mb-6">
                        Your MOH Staff account request has been submitted successfully.
                        A Course Administrator will review your request and you will receive an email
                        notification once your account is approved.
                    </p>
                    <div className="bg-indigo-50 rounded-lg p-4 text-left mb-6">
                        <h3 className="text-sm font-semibold text-indigo-900 mb-2">What happens next?</h3>
                        <ol className="text-sm text-indigo-700 space-y-1 list-decimal list-inside">
                            <li>An administrator will review your request</li>
                            <li>You will receive an email when approved</li>
                            <li>Once approved, you can log in with your credentials</li>
                        </ol>
                    </div>
                    <Link
                        href="/login"
                        className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        Back to Login
                    </Link>
                </div>
            </div>
        </>
    );
}
