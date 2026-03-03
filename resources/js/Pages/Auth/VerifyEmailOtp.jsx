import { Head, Link, useForm } from '@inertiajs/react';

export default function VerifyEmailOtp({ user, canResend, secondsUntilResend }) {
    const initiateForm = useForm({});

    const handleInitiate = (e) => {
        e.preventDefault();
        initiateForm.post('/email/verify/initiate');
    };

    const handleResend = (e) => {
        e.preventDefault();
        initiateForm.post('/email/verification-notification');
    };

    return (
        <>
            <Head title="Verify Email" />
            <div className="min-h-screen flex items-center justify-center bg-gray-100">
                <div className="w-full max-w-md bg-white rounded-lg shadow-md p-8 text-center">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 mb-4">
                        <svg className="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <h2 className="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h2>
                    <p className="text-gray-600 mb-6">
                        We need to verify your email address <strong>{user?.email}</strong>. Click below to receive a verification code.
                    </p>

                    <form onSubmit={handleInitiate} className="mb-4">
                        <button
                            type="submit"
                            disabled={initiateForm.processing}
                            className="w-full rounded-md bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
                        >
                            {initiateForm.processing ? 'Sending...' : 'Send Verification Code'}
                        </button>
                    </form>

                    {canResend ? (
                        <form onSubmit={handleResend}>
                            <button type="submit" disabled={initiateForm.processing} className="text-sm text-indigo-600 hover:text-indigo-500">
                                Resend verification code
                            </button>
                        </form>
                    ) : secondsUntilResend > 0 ? (
                        <p className="text-sm text-gray-500">
                            You can request a new code in {secondsUntilResend} seconds.
                        </p>
                    ) : null}

                    <div className="mt-6">
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            className="text-sm text-gray-600 hover:text-gray-800"
                        >
                            Sign Out
                        </Link>
                    </div>
                </div>
            </div>
        </>
    );
}
