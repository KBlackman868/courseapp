import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';

export default function VerificationExpired({ reason = 'expired' }) {
    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('verification.resend'));
    };

    const messages = {
        expired: {
            title: 'Verification Link Expired',
            description: 'Your email verification link has expired. Links are valid for 24 hours.',
            showResend: true,
        },
        not_found: {
            title: 'Request Not Found',
            description: 'We could not find a registration request matching this link. It may have been deleted or already processed.',
            showResend: false,
        },
        invalid: {
            title: 'Invalid Verification Link',
            description: 'This verification link is invalid. Please try registering again.',
            showResend: false,
        },
        already_processed: {
            title: 'Already Processed',
            description: 'This registration request has already been processed.',
            showResend: false,
        },
    };

    const msg = messages[reason] || messages.expired;

    return (
        <GuestLayout>
            <Head title={msg.title} />

            <div className="text-center">
                <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                    <svg
                        className="h-8 w-8 text-red-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"
                        />
                    </svg>
                </div>

                <h2 className="mb-2 text-2xl font-bold text-gray-900">
                    {msg.title}
                </h2>

                <p className="mb-6 text-gray-600">
                    {msg.description}
                </p>

                {recentlySuccessful && (
                    <div className="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                        A new verification email has been sent! Please check your inbox.
                    </div>
                )}

                {msg.showResend && (
                    <form onSubmit={submit} className="mb-6 text-left">
                        <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                            Enter your email to resend the verification link
                        </label>
                        <div className="mt-2 flex gap-2">
                            <input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="your@email.com"
                                required
                            />
                            <button
                                type="submit"
                                disabled={processing}
                                className="shrink-0 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                            >
                                {processing ? 'Sending...' : 'Resend'}
                            </button>
                        </div>
                        <InputError message={errors.email} className="mt-2" />
                    </form>
                )}

                <div className="flex flex-col items-center gap-3">
                    <Link
                        href={route('register')}
                        className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Register Again
                    </Link>
                    <Link
                        href={route('login')}
                        className="text-sm text-gray-600 underline hover:text-gray-900"
                    >
                        Return to Sign In
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
