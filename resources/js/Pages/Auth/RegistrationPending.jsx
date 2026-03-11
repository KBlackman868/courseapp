import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link } from '@inertiajs/react';

export default function RegistrationPending({ email, type, verified = false, resent = false, error = null }) {
    return (
        <GuestLayout>
            <Head title="Registration Submitted" />

            <div className="text-center">
                <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                    <svg
                        className="h-8 w-8 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>

                <h2 className="mb-2 text-2xl font-bold text-gray-900">
                    {verified ? 'Email Verified!' : 'Thank You for Registering!'}
                </h2>

                {resent && (
                    <div className="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                        A new verification email has been sent to <strong>{email}</strong>.
                    </div>
                )}

                {error && (
                    <div className="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        {error}
                    </div>
                )}

                <p className="mb-4 text-gray-600">
                    {verified
                        ? 'Your email has been verified successfully.'
                        : 'Your account request has been submitted successfully.'}
                </p>

                {!verified && (
                    <div className="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4 text-left">
                        <h3 className="mb-2 font-semibold text-blue-800">
                            Next Step: Verify Your Email
                        </h3>
                        <p className="text-sm text-blue-700">
                            We've sent a verification email to <strong>{email}</strong>.
                            Please check your inbox and click the verification link within 24 hours.
                        </p>
                        <p className="mt-2 text-xs text-blue-600">
                            Don't see it? Check your spam or junk folder.
                        </p>
                    </div>
                )}

                {verified && type === 'external' && (
                    <div className="mb-6 rounded-lg bg-amber-50 border border-amber-200 p-4 text-left">
                        <h3 className="mb-2 font-semibold text-amber-800">
                            What happens next?
                        </h3>
                        <ul className="space-y-2 text-sm text-amber-700">
                            <li className="flex items-start">
                                <span className="mr-2 mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-200 text-xs font-bold text-amber-800">
                                    1
                                </span>
                                An administrator will review your registration request.
                            </li>
                            <li className="flex items-start">
                                <span className="mr-2 mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-200 text-xs font-bold text-amber-800">
                                    2
                                </span>
                                You will receive an email at <strong>{email}</strong> once your account is approved.
                            </li>
                            <li className="flex items-start">
                                <span className="mr-2 mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-200 text-xs font-bold text-amber-800">
                                    3
                                </span>
                                After approval, you can sign in with the password you created during registration.
                            </li>
                        </ul>
                    </div>
                )}

                {verified && type === 'moh_staff' && !error && (
                    <div className="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-left">
                        <h3 className="mb-2 font-semibold text-green-800">
                            Account Activated
                        </h3>
                        <p className="text-sm text-green-700">
                            Your MOH staff account has been activated. You can now sign in with your credentials.
                        </p>
                    </div>
                )}

                {verified && type === 'moh_staff' && error && (
                    <div className="mb-6 rounded-lg bg-amber-50 border border-amber-200 p-4 text-left">
                        <h3 className="mb-2 font-semibold text-amber-800">
                            What happens next?
                        </h3>
                        <p className="text-sm text-amber-700">
                            Your email has been verified, but your account could not be activated automatically.
                            An administrator has been notified and will activate your account shortly.
                            You will be able to sign in once your account is activated.
                        </p>
                    </div>
                )}

                {type === 'external' && !verified && (
                    <p className="mb-4 text-xs text-gray-500">
                        External user accounts require email verification and administrator approval before access is granted.
                    </p>
                )}

                <Link
                    href={route('login')}
                    className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Return to Sign In
                </Link>

                <div className="mt-4">
                    <Link
                        href={route('home')}
                        className="text-sm text-gray-600 underline hover:text-gray-900"
                    >
                        Go to Homepage
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
