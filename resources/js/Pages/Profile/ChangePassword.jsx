import { Head, useForm, usePage } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import { ProfileNav } from '@/Components/ui/profile-nav';

function PasswordInput({ id, label, value, onChange, error, placeholder, autoComplete }) {
    const [show, setShow] = useState(false);
    return (
        <div>
            <label htmlFor={id} className="block text-sm font-medium text-gray-700 mb-1.5">{label}</label>
            <div className="relative">
                <input
                    id={id}
                    type={show ? 'text' : 'password'}
                    value={value}
                    onChange={onChange}
                    autoComplete={autoComplete}
                    placeholder={placeholder}
                    className="block w-full rounded-lg border-gray-300 px-4 py-2.5 pr-12 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                />
                <button type="button" onClick={() => setShow(!show)} tabIndex={-1} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    {show ? (
                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    ) : (
                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    )}
                </button>
            </div>
            {error && <p className="mt-1.5 text-sm text-red-600">{error}</p>}
        </div>
    );
}

function PasswordStrength({ password }) {
    const strength = useMemo(() => {
        if (!password) return { score: 0, label: '', color: 'bg-gray-200' };
        let score = 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (password.length >= 14) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        if (score <= 2) return { score: 1, label: 'Weak', color: 'bg-red-500' };
        if (score <= 3) return { score: 2, label: 'Fair', color: 'bg-yellow-500' };
        if (score <= 4) return { score: 3, label: 'Good', color: 'bg-blue-500' };
        return { score: 4, label: 'Strong', color: 'bg-green-500' };
    }, [password]);

    if (!password) return null;

    return (
        <div className="mt-2">
            <div className="flex gap-1">
                {[1, 2, 3, 4].map((i) => (
                    <div key={i} className={`h-1.5 flex-1 rounded-full transition-colors ${i <= strength.score ? strength.color : 'bg-gray-200'}`} />
                ))}
            </div>
            <p className={`mt-1 text-xs ${strength.score <= 1 ? 'text-red-600' : strength.score <= 2 ? 'text-yellow-600' : strength.score <= 3 ? 'text-blue-600' : 'text-green-600'}`}>
                {strength.label}
            </p>
        </div>
    );
}

export default function ChangePassword() {
    const { flash } = usePage().props;
    const [successMessage, setSuccessMessage] = useState('');

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const handlePasswordSubmit = (e) => {
        e.preventDefault();
        passwordForm.post(route('profile.password'), {
            onSuccess: () => {
                passwordForm.reset();
                setSuccessMessage('Password changed successfully.');
                setTimeout(() => setSuccessMessage(''), 4000);
            },
        });
    };

    return (
        <>
            <Head title="Change Password" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Change Password</h1>
                    <p className="mt-1 text-sm text-gray-500">Ensure your account stays secure with a strong password.</p>
                </div>

                <ProfileNav />

                {(successMessage || flash?.success) && (
                    <div className="rounded-lg bg-green-50 border border-green-200 p-4">
                        <div className="flex items-center gap-3">
                            <svg className="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <p className="text-sm font-medium text-green-800">{successMessage || flash.success}</p>
                        </div>
                    </div>
                )}

                <div className="rounded-lg bg-white shadow">
                    <div className="px-6 py-5 border-b border-gray-200">
                        <h3 className="text-base font-semibold text-gray-900">Update Password</h3>
                        <p className="mt-1 text-sm text-gray-500">Minimum 12-14 characters required. Use a mix of uppercase, lowercase, numbers, and special characters.</p>
                    </div>
                    <form onSubmit={handlePasswordSubmit} className="px-6 py-5">
                        <div className="max-w-lg space-y-4">
                            <PasswordInput
                                id="current_password"
                                label="Current Password"
                                value={passwordForm.data.current_password}
                                onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                error={passwordForm.errors.current_password}
                                placeholder="Enter current password"
                                autoComplete="current-password"
                            />
                            <div>
                                <PasswordInput
                                    id="new_password"
                                    label="New Password"
                                    value={passwordForm.data.password}
                                    onChange={(e) => passwordForm.setData('password', e.target.value)}
                                    error={passwordForm.errors.password}
                                    placeholder="Min. 14 characters"
                                    autoComplete="new-password"
                                />
                                <PasswordStrength password={passwordForm.data.password} />
                            </div>
                            <PasswordInput
                                id="password_confirmation"
                                label="Confirm New Password"
                                value={passwordForm.data.password_confirmation}
                                onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                error={passwordForm.errors.password_confirmation}
                                placeholder="Re-enter new password"
                                autoComplete="new-password"
                            />
                        </div>
                        <div className="mt-6 flex justify-end">
                            <button
                                type="submit"
                                disabled={passwordForm.processing}
                                className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-50 transition-colors"
                            >
                                {passwordForm.processing ? (
                                    <><svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>Updating...</>
                                ) : 'Update Password'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
