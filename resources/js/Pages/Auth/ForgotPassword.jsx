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
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
.animate-fade-in-up-delay-1 { animation: fadeInUp 0.6s ease-out 0.1s forwards; opacity: 0; }
.animate-fade-in-up-delay-2 { animation: fadeInUp 0.6s ease-out 0.2s forwards; opacity: 0; }
.animate-fade-in-up-delay-3 { animation: fadeInUp 0.6s ease-out 0.3s forwards; opacity: 0; }
.animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }
.input-focus-glow:focus { box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15); border-color: #4F46E5; }
.btn-hover:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4); }
.btn-hover:active { transform: translateY(0); }
`;

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('password.email'));
    };

    return (
        <>
            <Head title="Forgot Password" />
            <style>{css}</style>

            <div className="flex min-h-screen">
                {/* Left Panel */}
                <div className="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 animate-gradient">
                    <div className="absolute inset-0 overflow-hidden">
                        <div className="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-xl animate-float1" />
                        <div className="absolute bottom-32 right-20 w-96 h-96 bg-purple-400/10 rounded-full blur-2xl animate-float2" />
                        <div className="absolute bottom-20 left-16 w-32 h-32 bg-white/5 rounded-full animate-pulse-slow" />
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
                                Forgot your<br />
                                <span className="text-indigo-200">password?</span>
                            </h1>
                        </div>

                        <div className="animate-fade-in-up-delay-2">
                            <p className="text-lg text-indigo-100/80 max-w-md leading-relaxed">
                                No worries. We&apos;ll send you a secure link to reset your password and get you back on track.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Right Panel */}
                <div className="w-full lg:w-1/2 xl:w-[45%] flex flex-col">
                    <div className="lg:hidden bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-center">
                        <div className="flex items-center justify-center gap-3 mb-3">
                            <div className="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                            </div>
                            <span className="text-xl font-bold text-white">MOH Learning</span>
                        </div>
                        <p className="text-indigo-100 text-sm">Reset your password</p>
                    </div>

                    <div className="flex-1 flex items-center justify-center px-6 sm:px-12 lg:px-16 py-12">
                        <div className="w-full max-w-md">
                            <div className="animate-fade-in-up">
                                <div className="mb-6 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg className="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <h1 className="text-3xl font-bold text-gray-900 mb-2">Reset Password</h1>
                                <p className="text-gray-500 mb-8">
                                    Enter your email address and we&apos;ll send you a link to reset your password.
                                </p>
                            </div>

                            {status && (
                                <div className="animate-fade-in-up mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                                    <div className="flex items-center gap-2">
                                        <svg className="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <p className="text-sm font-medium text-green-700">{status}</p>
                                    </div>
                                </div>
                            )}

                            <form onSubmit={submit} className="space-y-5">
                                <div className="animate-fade-in-up-delay-1">
                                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1.5">
                                        Email Address
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        autoFocus
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="input-focus-glow w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 outline-none"
                                        placeholder="you@example.com"
                                    />
                                    {errors.email && <p className="mt-1.5 text-sm text-red-600">{errors.email}</p>}
                                </div>

                                <div className="animate-fade-in-up-delay-2">
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
                                                Sending...
                                            </span>
                                        ) : (
                                            'Email Password Reset Link'
                                        )}
                                    </button>
                                </div>
                            </form>

                            <div className="animate-fade-in-up-delay-3 mt-8 text-center">
                                <Link href={route('login')} className="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors inline-flex items-center gap-1">
                                    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                    </svg>
                                    Back to Sign In
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
