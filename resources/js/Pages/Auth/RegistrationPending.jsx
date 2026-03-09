import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link } from '@inertiajs/react';

export default function RegistrationPending({ email, type }) {
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
                    Thank You for Registering!
                </h2>

                <p className="mb-4 text-gray-600">
                    Your account request has been submitted successfully.
                </p>

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

                {type === 'external' && (
                    <p className="mb-4 text-xs text-gray-500">
                        External user accounts require administrator approval before access is granted.
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
