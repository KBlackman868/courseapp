import { Head, Link } from '@inertiajs/react';

const css = `
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-gradient { background-size: 200% 200%; animation: gradientShift 8s ease infinite; }
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
.animate-fade-in-up-delay-1 { animation: fadeInUp 0.6s ease-out 0.15s forwards; opacity: 0; }
.animate-fade-in-up-delay-2 { animation: fadeInUp 0.6s ease-out 0.3s forwards; opacity: 0; }
.animate-float { animation: float 3s ease-in-out infinite; }
`;

const errorConfig = {
    401: {
        title: 'Unauthorized',
        description: 'You need to be logged in to access this page.',
        icon: (
            <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        ),
        color: 'from-amber-500 to-orange-600',
        bgAccent: 'bg-amber-50',
        textAccent: 'text-amber-600',
    },
    403: {
        title: 'Access Denied',
        description: 'You don\'t have permission to access this resource. Contact your administrator if you believe this is an error.',
        icon: (
            <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        ),
        color: 'from-red-500 to-rose-600',
        bgAccent: 'bg-red-50',
        textAccent: 'text-red-600',
    },
    404: {
        title: 'Page Not Found',
        description: 'The page you\'re looking for doesn\'t exist or has been moved.',
        icon: (
            <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        ),
        color: 'from-indigo-500 to-purple-600',
        bgAccent: 'bg-indigo-50',
        textAccent: 'text-indigo-600',
    },
    419: {
        title: 'Session Expired',
        description: 'Your session has expired. Please refresh the page and try again.',
        icon: (
            <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        ),
        color: 'from-yellow-500 to-amber-600',
        bgAccent: 'bg-yellow-50',
        textAccent: 'text-yellow-600',
    },
    429: {
        title: 'Too Many Requests',
        description: 'You\'ve made too many requests. Please wait a moment and try again.',
        icon: (
            <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
        ),
        color: 'from-orange-500 to-red-600',
        bgAccent: 'bg-orange-50',
        textAccent: 'text-orange-600',
    },
    500: {
        title: 'Server Error',
        description: 'Something went wrong on our end. Our team has been notified and is working on a fix.',
        icon: (
            <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.049.58.025 1.192-.14 1.743" />
            </svg>
        ),
        color: 'from-gray-600 to-gray-800',
        bgAccent: 'bg-gray-50',
        textAccent: 'text-gray-600',
    },
    503: {
        title: 'Service Unavailable',
        description: 'We\'re performing maintenance. Please check back shortly.',
        icon: (
            <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
            </svg>
        ),
        color: 'from-blue-500 to-cyan-600',
        bgAccent: 'bg-blue-50',
        textAccent: 'text-blue-600',
    },
};

const defaultConfig = {
    title: 'Something Went Wrong',
    description: 'An unexpected error occurred. Please try again.',
    icon: (
        <svg className="h-16 w-16" fill="none" viewBox="0 0 24 24" strokeWidth="1" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>
    ),
    color: 'from-gray-600 to-gray-800',
    bgAccent: 'bg-gray-50',
    textAccent: 'text-gray-600',
};

export default function Error({ status }) {
    const statusCode = status || 500;
    const config = errorConfig[statusCode] || defaultConfig;

    return (
        <>
            <Head title={`${statusCode} - ${config.title}`} />
            <style>{css}</style>

            <div className="min-h-screen bg-gray-50 flex flex-col">
                {/* Top gradient bar */}
                <div className={`h-1.5 bg-gradient-to-r ${config.color} animate-gradient`} />

                <div className="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div className="w-full max-w-lg text-center">
                        {/* Icon */}
                        <div className="animate-fade-in-up">
                            <div className={`inline-flex items-center justify-center w-28 h-28 rounded-full ${config.bgAccent} ${config.textAccent} mb-6 animate-float`}>
                                {config.icon}
                            </div>
                        </div>

                        {/* Status Code */}
                        <div className="animate-fade-in-up">
                            <p className={`text-8xl font-extrabold bg-gradient-to-r ${config.color} bg-clip-text text-transparent mb-2`}>
                                {statusCode}
                            </p>
                        </div>

                        {/* Title */}
                        <div className="animate-fade-in-up-delay-1">
                            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">
                                {config.title}
                            </h1>
                            <p className="text-base text-gray-500 max-w-md mx-auto leading-relaxed mb-8">
                                {config.description}
                            </p>
                        </div>

                        {/* Action Buttons */}
                        <div className="animate-fade-in-up-delay-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                            {statusCode === 419 ? (
                                <button
                                    onClick={() => window.location.reload()}
                                    className={`inline-flex items-center gap-2 rounded-lg bg-gradient-to-r ${config.color} px-6 py-3 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5`}
                                >
                                    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
                                    </svg>
                                    Refresh Page
                                </button>
                            ) : (
                                <>
                                    <Link
                                        href="/"
                                        className={`inline-flex items-center gap-2 rounded-lg bg-gradient-to-r ${config.color} px-6 py-3 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5`}
                                    >
                                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                                        </svg>
                                        Go Home
                                    </Link>
                                    <button
                                        onClick={() => window.history.back()}
                                        className="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-all duration-200"
                                    >
                                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                        </svg>
                                        Go Back
                                    </button>
                                </>
                            )}
                        </div>

                        {/* Footer */}
                        <div className="animate-fade-in-up-delay-2 mt-12">
                            <div className="flex items-center justify-center gap-2 text-sm text-gray-400">
                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                                </svg>
                                MOH Learning &mdash; Trinidad &amp; Tobago
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
