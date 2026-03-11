import { useState, useMemo } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

const css = `
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes float1 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -40px) scale(1.05); }
    66% { transform: translate(-20px, 20px) scale(0.95); }
}
@keyframes float2 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-25px, 35px) scale(1.08); }
    66% { transform: translate(15px, -25px) scale(0.92); }
}
@keyframes float3 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(20px, 25px) scale(0.96); }
    66% { transform: translate(-30px, -15px) scale(1.04); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes pulse {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 0.8; }
}
.animate-gradient { background-size: 200% 200%; animation: gradientShift 8s ease infinite; }
.animate-float1 { animation: float1 7s ease-in-out infinite; }
.animate-float2 { animation: float2 9s ease-in-out infinite; }
.animate-float3 { animation: float3 11s ease-in-out infinite; }
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
.animate-fade-in-up-delay-1 { animation: fadeInUp 0.6s ease-out 0.1s forwards; opacity: 0; }
.animate-fade-in-up-delay-2 { animation: fadeInUp 0.6s ease-out 0.2s forwards; opacity: 0; }
.animate-fade-in-up-delay-3 { animation: fadeInUp 0.6s ease-out 0.3s forwards; opacity: 0; }
.animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }
.input-focus-glow:focus { box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15); border-color: #4F46E5; }
.btn-hover:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4); }
.btn-hover:active { transform: translateY(0); }
`;

function getPasswordStrength(password) {
    if (!password) return { score: 0, label: '', color: 'bg-gray-200' };
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    const levels = [
        { label: 'Very Weak', color: 'bg-red-500' },
        { label: 'Weak', color: 'bg-red-500' },
        { label: 'Fair', color: 'bg-orange-500' },
        { label: 'Good', color: 'bg-yellow-500' },
        { label: 'Strong', color: 'bg-green-500' },
        { label: 'Very Strong', color: 'bg-green-600' },
    ];
    return { score, ...levels[score] };
}

function calculateAge(dateString) {
    if (!dateString) return null;
    const today = new Date();
    const birth = new Date(dateString);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

export default function Register() {
    const { data, setData, post, processing, errors, setError, clearErrors, reset } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        department: '',
        date_of_birth: '',
        password: '',
        password_confirmation: '',
        terms: false,
    });
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);
    const [ageError, setAgeError] = useState('');
    const [termsError, setTermsError] = useState('');

    const strength = useMemo(() => getPasswordStrength(data.password), [data.password]);

    const maxDate = useMemo(() => {
        const d = new Date();
        d.setFullYear(d.getFullYear() - 18);
        return d.toISOString().split('T')[0];
    }, []);

    const submit = (e) => {
        e.preventDefault();
        setAgeError('');
        setTermsError('');

        const age = calculateAge(data.date_of_birth);
        if (age === null || age < 18) {
            setAgeError('You must be 18 or older to register.');
            return;
        }

        if (!data.terms) {
            setTermsError('You must accept the Terms and Conditions to register.');
            return;
        }

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <>
            <Head title="Register" />
            <style>{css}</style>

            <div className="flex min-h-screen">
                {/* Left Panel - Branding */}
                <div className="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 animate-gradient">
                    <div className="absolute inset-0 overflow-hidden">
                        <div className="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-xl animate-float1" />
                        <div className="absolute bottom-32 right-20 w-96 h-96 bg-purple-400/10 rounded-full blur-2xl animate-float2" />
                        <div className="absolute top-1/2 left-1/3 w-64 h-64 bg-indigo-300/10 rounded-full blur-xl animate-float3" />
                        <div className="absolute bottom-20 left-16 w-32 h-32 bg-white/5 rounded-full animate-pulse-slow" />
                        <div className="absolute top-32 right-32 w-20 h-20 bg-white/5 rounded-full animate-pulse-slow" style={{ animationDelay: '1.5s' }} />
                    </div>

                    <div className="relative z-10 flex flex-col justify-center px-12 xl:px-20">
                        <div className="animate-fade-in-up">
                            <div className="flex items-center gap-4 mb-10">
                                <div className="h-14 w-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg">
                                    <svg className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 className="text-2xl font-bold text-white">MOH Learning</h2>
                                    <p className="text-indigo-200 text-sm">Trinidad &amp; Tobago</p>
                                </div>
                            </div>
                        </div>

                        <div className="animate-fade-in-up-delay-1">
                            <h1 className="text-4xl xl:text-5xl font-bold text-white leading-tight mb-6">
                                Start your<br />
                                <span className="text-indigo-200">learning journey</span>
                            </h1>
                        </div>

                        <div className="animate-fade-in-up-delay-2">
                            <p className="text-lg text-indigo-100/80 max-w-md leading-relaxed mb-10">
                                Join the Ministry of Health&apos;s learning platform and gain access to professional development courses designed for healthcare professionals.
                            </p>
                        </div>

                        <div className="animate-fade-in-up-delay-3 space-y-4">
                            <div className="flex items-center gap-3">
                                <div className="h-8 w-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                                    <svg className="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                </div>
                                <span className="text-indigo-100">Access 50+ professional courses</span>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="h-8 w-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                                    <svg className="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                </div>
                                <span className="text-indigo-100">Track your learning progress</span>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="h-8 w-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                                    <svg className="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                </div>
                                <span className="text-indigo-100">Earn certificates of completion</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Panel - Form */}
                <div className="w-full lg:w-1/2 xl:w-[45%] flex flex-col">
                    {/* Mobile header */}
                    <div className="lg:hidden bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-center">
                        <div className="flex items-center justify-center gap-3 mb-3">
                            <div className="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                            </div>
                            <span className="text-xl font-bold text-white">MOH Learning</span>
                        </div>
                        <p className="text-indigo-100 text-sm">Create your account to get started</p>
                    </div>

                    <div className="flex-1 flex items-center justify-center px-6 sm:px-12 lg:px-16 py-8 lg:py-12">
                        <div className="w-full max-w-md">
                            <div className="animate-fade-in-up">
                                <h1 className="text-3xl font-bold text-gray-900 mb-2">Join MOH Learning</h1>
                                <p className="text-gray-500 mb-8">Create your account to start your learning journey</p>
                            </div>

                            <form onSubmit={submit} className="space-y-5">
                                {/* First Name & Last Name */}
                                <div className="animate-fade-in-up-delay-1 grid grid-cols-2 gap-4">
                                    <div>
                                        <label htmlFor="first_name" className="block text-sm font-medium text-gray-700 mb-1.5">
                                            First Name
                                        </label>
                                        <input
                                            id="first_name"
                                            type="text"
                                            name="first_name"
                                            value={data.first_name}
                                            autoComplete="given-name"
                                            autoFocus
                                            required
                                            minLength={2}
                                            onChange={(e) => setData('first_name', e.target.value)}
                                            className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                            placeholder="John"
                                        />
                                        {errors.first_name && <p className="mt-1.5 text-sm text-red-600">{errors.first_name}</p>}
                                    </div>
                                    <div>
                                        <label htmlFor="last_name" className="block text-sm font-medium text-gray-700 mb-1.5">
                                            Last Name
                                        </label>
                                        <input
                                            id="last_name"
                                            type="text"
                                            name="last_name"
                                            value={data.last_name}
                                            autoComplete="family-name"
                                            required
                                            minLength={2}
                                            onChange={(e) => setData('last_name', e.target.value)}
                                            className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                            placeholder="Doe"
                                        />
                                        {errors.last_name && <p className="mt-1.5 text-sm text-red-600">{errors.last_name}</p>}
                                    </div>
                                </div>

                                {/* Email */}
                                <div className="animate-fade-in-up-delay-1">
                                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1.5">
                                        Email Address
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        autoComplete="username"
                                        required
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                        placeholder="you@example.com"
                                    />
                                    {errors.email && <p className="mt-1.5 text-sm text-red-600">{errors.email}</p>}
                                </div>

                                {/* Department / Organization */}
                                <div className="animate-fade-in-up-delay-2">
                                    <label htmlFor="department" className="block text-sm font-medium text-gray-700 mb-1.5">
                                        Department / Organization
                                    </label>
                                    <input
                                        id="department"
                                        type="text"
                                        name="department"
                                        value={data.department}
                                        onChange={(e) => setData('department', e.target.value)}
                                        className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                        placeholder="e.g. Insect Vector Control, Ministry of Health"
                                        required
                                    />
                                    {errors.department && <p className="mt-1.5 text-sm text-red-600">{errors.department}</p>}
                                </div>

                                {/* Date of Birth */}
                                <div className="animate-fade-in-up-delay-2">
                                    <label htmlFor="date_of_birth" className="block text-sm font-medium text-gray-700 mb-1.5">
                                        Date of Birth
                                    </label>
                                    <input
                                        id="date_of_birth"
                                        type="date"
                                        name="date_of_birth"
                                        value={data.date_of_birth}
                                        max={maxDate}
                                        required
                                        onChange={(e) => {
                                            setData('date_of_birth', e.target.value);
                                            setAgeError('');
                                        }}
                                        className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                    />
                                    {(ageError || errors.date_of_birth) && (
                                        <p className="mt-1.5 text-sm text-red-600">{ageError || errors.date_of_birth}</p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-400">You must be 18 or older to register</p>
                                </div>

                                {/* Password */}
                                <div className="animate-fade-in-up-delay-2">
                                    <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1.5">
                                        Password
                                    </label>
                                    <div className="relative">
                                        <input
                                            id="password"
                                            type={showPassword ? 'text' : 'password'}
                                            name="password"
                                            value={data.password}
                                            autoComplete="new-password"
                                            required
                                            minLength={12}
                                            onChange={(e) => setData('password', e.target.value)}
                                            className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                            placeholder="Min. 14 characters"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                            tabIndex={-1}
                                        >
                                            {showPassword ? (
                                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            ) : (
                                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            )}
                                        </button>
                                    </div>
                                    {/* Strength indicator */}
                                    {data.password && (
                                        <div className="mt-2">
                                            <div className="flex gap-1 mb-1">
                                                {[1, 2, 3, 4, 5].map((level) => (
                                                    <div
                                                        key={level}
                                                        className={`h-1.5 flex-1 rounded-full transition-all duration-300 ${
                                                            level <= strength.score ? strength.color : 'bg-gray-200'
                                                        }`}
                                                    />
                                                ))}
                                            </div>
                                            <p className={`text-xs ${
                                                strength.score <= 1 ? 'text-red-600' :
                                                strength.score <= 2 ? 'text-orange-600' :
                                                strength.score <= 3 ? 'text-yellow-600' :
                                                'text-green-600'
                                            }`}>
                                                {strength.label}
                                            </p>
                                        </div>
                                    )}
                                    {errors.password && <p className="mt-1.5 text-sm text-red-600">{errors.password}</p>}
                                </div>

                                {/* Confirm Password */}
                                <div className="animate-fade-in-up-delay-3">
                                    <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-1.5">
                                        Confirm Password
                                    </label>
                                    <div className="relative">
                                        <input
                                            id="password_confirmation"
                                            type={showConfirm ? 'text' : 'password'}
                                            name="password_confirmation"
                                            value={data.password_confirmation}
                                            autoComplete="new-password"
                                            required
                                            onChange={(e) => setData('password_confirmation', e.target.value)}
                                            className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                            placeholder="Re-enter your password"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowConfirm(!showConfirm)}
                                            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                            tabIndex={-1}
                                        >
                                            {showConfirm ? (
                                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            ) : (
                                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            )}
                                        </button>
                                    </div>
                                    {errors.password_confirmation && <p className="mt-1.5 text-sm text-red-600">{errors.password_confirmation}</p>}
                                </div>

                                {/* Terms & Conditions */}
                                <div className="animate-fade-in-up-delay-3">
                                    <div className="flex items-start gap-3">
                                        <input
                                            id="terms_accepted"
                                            type="checkbox"
                                            checked={data.terms}
                                            onChange={(e) => {
                                                setData('terms', e.target.checked);
                                                setTermsError('');
                                            }}
                                            className="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <label htmlFor="terms_accepted" className="text-sm text-gray-600">
                                            I have read and agree to the{' '}
                                            <a
                                                href="/terms"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="font-medium text-indigo-600 hover:text-indigo-500 underline"
                                            >
                                                Terms and Conditions
                                            </a>{' '}
                                            and{' '}
                                            <a
                                                href="/privacy"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="font-medium text-indigo-600 hover:text-indigo-500 underline"
                                            >
                                                Privacy Policy
                                            </a>
                                        </label>
                                    </div>
                                    {termsError && <p className="mt-1.5 text-sm text-red-600">{termsError}</p>}
                                </div>

                                {/* Submit */}
                                <div className="animate-fade-in-up-delay-3">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="btn-hover w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:transform-none"
                                    >
                                        {processing ? (
                                            <span className="flex items-center justify-center gap-2">
                                                <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                                </svg>
                                                Creating account...
                                            </span>
                                        ) : (
                                            'Create Account'
                                        )}
                                    </button>
                                </div>
                            </form>

                            <div className="animate-fade-in-up-delay-3 mt-8 text-center">
                                <p className="text-sm text-gray-500">
                                    Already have an account?{' '}
                                    <Link href={route('login')} className="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                                        Sign In
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
