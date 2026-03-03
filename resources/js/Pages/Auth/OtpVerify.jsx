import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function OtpVerify({ user, remainingResends }) {
    const [resending, setResending] = useState(false);
    const [resendMessage, setResendMessage] = useState('');

    const form = useForm({ otp: '' });

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post('/auth/otp/verify');
    };

    const handleResend = async () => {
        setResending(true);
        try {
            const response = await fetch('/auth/otp/resend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
            });
            const data = await response.json();
            setResendMessage(data.message || 'Code resent.');
        } catch {
            setResendMessage('Failed to resend code.');
        }
        setResending(false);
    };

    return (
        <>
            <Head title="Verify OTP" />
            <div className="min-h-screen flex items-center justify-center bg-gray-100">
                <div className="w-full max-w-md bg-white rounded-lg shadow-md p-8">
                    <h2 className="text-2xl font-bold text-center text-gray-900 mb-2">Verify Your Identity</h2>
                    <p className="text-center text-gray-600 mb-6">
                        Enter the 6-digit code sent to <strong>{user?.email}</strong>
                    </p>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <input
                                type="text"
                                maxLength={6}
                                value={form.data.otp}
                                onChange={(e) => form.setData('otp', e.target.value.replace(/\D/g, ''))}
                                className="block w-full text-center text-2xl tracking-[0.5em] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3"
                                placeholder="000000"
                                autoFocus
                            />
                            {form.errors.otp && (
                                <p className="mt-1 text-sm text-red-600">{form.errors.otp}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={form.processing || form.data.otp.length !== 6}
                            className="w-full rounded-md bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
                        >
                            {form.processing ? 'Verifying...' : 'Verify Code'}
                        </button>
                    </form>

                    <div className="mt-4 text-center">
                        {remainingResends > 0 ? (
                            <button
                                onClick={handleResend}
                                disabled={resending}
                                className="text-sm text-indigo-600 hover:text-indigo-500"
                            >
                                {resending ? 'Sending...' : 'Resend Code'}
                            </button>
                        ) : (
                            <p className="text-sm text-gray-500">No more resend attempts available.</p>
                        )}
                        {resendMessage && (
                            <p className="mt-1 text-sm text-green-600">{resendMessage}</p>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
