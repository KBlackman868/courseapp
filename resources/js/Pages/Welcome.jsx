import { Head, Link } from '@inertiajs/react';

function getUserRole(user) {
    if (!user?.roles?.length) return 'moh_staff';
    const roleNames = user.roles.map((r) => r.name);
    if (roleNames.includes('superadmin')) return 'superadmin';
    if (roleNames.includes('admin')) return 'admin';
    if (roleNames.includes('course_admin')) return 'course_admin';
    if (roleNames.includes('external_staff')) return 'external_staff';
    return 'moh_staff';
}

export default function Welcome({ isAuthenticated, user, enrolledCourses, featuredCourses }) {
    const role = user ? getUserRole(user) : null;
    const isAdmin = role === 'superadmin' || role === 'admin';
    const isCourseAdmin = role === 'course_admin';

    const getDashboardRoute = () => {
        if (isAdmin || isCourseAdmin) return '/dashboard';
        return '/dashboard/learner';
    };

    const getDashboardLabel = () => {
        if (isAdmin) return 'Admin Dashboard';
        if (isCourseAdmin) return 'Course Management';
        return 'My Courses';
    };

    return (
        <>
            <Head title="Welcome - MOH Learning Portal" />
            <style>{`
                @keyframes blob {
                    0% { transform: translate(0px, 0px) scale(1); }
                    33% { transform: translate(30px, -50px) scale(1.1); }
                    66% { transform: translate(-20px, 20px) scale(0.9); }
                    100% { transform: translate(0px, 0px) scale(1); }
                }
                .animate-blob {
                    animation: blob 7s infinite;
                }
                .animation-delay-2000 {
                    animation-delay: 2s;
                }
                .animation-delay-4000 {
                    animation-delay: 4s;
                }
            `}</style>

            <div className="min-h-screen bg-white">
                {/* Navigation */}
                <nav className="fixed top-0 z-50 w-full border-b border-white/10 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 shadow-lg backdrop-blur-md">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <div className="flex items-center space-x-3">
                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20">
                                    <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                    </svg>
                                </div>
                                <div>
                                    <span className="text-lg font-bold text-white">MOH Learning Portal</span>
                                    <span className="ml-2 hidden rounded-full bg-white/20 px-2 py-0.5 text-xs text-white/80 sm:inline-block">
                                        Trinidad &amp; Tobago
                                    </span>
                                </div>
                            </div>
                            <div className="flex items-center space-x-3">
                                {isAuthenticated ? (
                                    <>
                                        <span className="hidden text-sm text-white/80 sm:inline">
                                            Welcome, {user?.name}
                                        </span>
                                        <Link
                                            href={getDashboardRoute()}
                                            className="rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/30"
                                        >
                                            {getDashboardLabel()}
                                        </Link>
                                    </>
                                ) : (
                                    <>
                                        <Link
                                            href="/login"
                                            className="rounded-lg px-4 py-2 text-sm font-medium text-white transition hover:bg-white/10"
                                        >
                                            Sign In
                                        </Link>
                                        <Link
                                            href="/register"
                                            className="rounded-lg bg-white px-4 py-2 text-sm font-medium text-indigo-600 transition hover:bg-gray-100"
                                        >
                                            Register
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <section className="relative min-h-screen overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 pt-16">
                    {/* Animated Blobs */}
                    <div className="absolute top-0 -left-4 h-72 w-72 rounded-full bg-purple-300 opacity-30 mix-blend-multiply blur-xl filter animate-blob"></div>
                    <div className="absolute top-0 -right-4 h-72 w-72 rounded-full bg-yellow-300 opacity-30 mix-blend-multiply blur-xl filter animate-blob animation-delay-2000"></div>
                    <div className="absolute -bottom-8 left-20 h-72 w-72 rounded-full bg-pink-300 opacity-30 mix-blend-multiply blur-xl filter animate-blob animation-delay-4000"></div>
                    <div className="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-300 opacity-20 mix-blend-multiply blur-xl filter animate-blob animation-delay-2000"></div>

                    <div className="relative z-10 flex min-h-screen items-center justify-center px-4 sm:px-6 lg:px-8">
                        <div className="mx-auto max-w-5xl text-center">
                            <div className="mb-6 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm text-white backdrop-blur-sm">
                                <svg className="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                                </svg>
                                Ministry of Health Digital Learning Initiative
                            </div>

                            <h1 className="mb-6 text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl md:text-6xl lg:text-7xl">
                                Strengthening Healthcare{' '}
                                <span className="bg-gradient-to-r from-yellow-200 to-yellow-100 bg-clip-text text-transparent">
                                    Workforce Capability
                                </span>
                            </h1>

                            <p className="mx-auto mb-10 max-w-2xl text-lg text-blue-100 sm:text-xl">
                                Empowering Trinidad &amp; Tobago's healthcare professionals with
                                world-class digital training and continuous professional development.
                            </p>

                            <div className="flex flex-col items-center justify-center gap-4 sm:flex-row">
                                {isAuthenticated ? (
                                    <>
                                        <Link
                                            href={getDashboardRoute()}
                                            className="group inline-flex items-center rounded-xl bg-white px-8 py-4 text-lg font-semibold text-indigo-600 shadow-xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-2xl"
                                        >
                                            {getDashboardLabel()}
                                            <svg className="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                            </svg>
                                        </Link>
                                        <Link
                                            href="/courses"
                                            className="inline-flex items-center rounded-xl border-2 border-white/30 px-8 py-4 text-lg font-semibold text-white backdrop-blur-sm transition-all duration-300 hover:border-white/60 hover:bg-white/10"
                                        >
                                            Browse Courses
                                        </Link>
                                    </>
                                ) : (
                                    <>
                                        <Link
                                            href="/register"
                                            className="group inline-flex items-center rounded-xl bg-white px-8 py-4 text-lg font-semibold text-indigo-600 shadow-xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-2xl"
                                        >
                                            Access Training Portal
                                            <svg className="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                            </svg>
                                        </Link>
                                        <Link
                                            href="/courses"
                                            className="inline-flex items-center rounded-xl border-2 border-white/30 px-8 py-4 text-lg font-semibold text-white backdrop-blur-sm transition-all duration-300 hover:border-white/60 hover:bg-white/10"
                                        >
                                            Explore Modules
                                        </Link>
                                    </>
                                )}
                            </div>

                            {/* Stats */}
                            <div className="mt-16 grid grid-cols-2 gap-4 sm:gap-8 lg:grid-cols-4">
                                {[
                                    { value: '24/7', label: 'Online Access' },
                                    { value: '100%', label: 'Digital Learning' },
                                    { value: 'Free', label: 'Self-Generated Certificates' },
                                    { value: 'Secure', label: 'MOH Managed Platform' },
                                ].map((stat) => (
                                    <div
                                        key={stat.label}
                                        className="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm sm:p-6"
                                    >
                                        <div className="text-2xl font-bold text-white sm:text-3xl">{stat.value}</div>
                                        <div className="mt-1 text-sm text-blue-200">{stat.label}</div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Wave Divider */}
                    <div className="absolute bottom-0 left-0 w-full">
                        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" className="w-full">
                            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white" />
                        </svg>
                    </div>
                </section>

                {/* Features Section */}
                <section className="bg-white py-20 sm:py-28">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="mb-16 text-center">
                            <span className="mb-4 inline-block rounded-full bg-indigo-100 px-4 py-1 text-sm font-semibold text-indigo-700">
                                Training Areas
                            </span>
                            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">
                                Comprehensive Healthcare Training
                            </h2>
                            <p className="mx-auto mt-4 max-w-2xl text-lg text-gray-600">
                                Our platform covers essential areas of professional development designed
                                specifically for healthcare workers across Trinidad &amp; Tobago.
                            </p>
                        </div>

                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {/* Standardized Training */}
                            <div className="group rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mb-5 inline-flex rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-3">
                                    <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">Standardized Training</h3>
                                <p className="text-gray-600">
                                    Nationally recognized training modules aligned with MOH standards and international healthcare best practices.
                                </p>
                            </div>

                            {/* Clinical Applications */}
                            <div className="group rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mb-5 inline-flex rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-3">
                                    <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">Clinical Applications</h3>
                                <p className="text-gray-600">
                                    Practical clinical training modules designed to enhance patient care delivery and medical procedures knowledge.
                                </p>
                            </div>

                            {/* Computer Literacy */}
                            <div className="group rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mb-5 inline-flex rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 p-3">
                                    <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">Computer Literacy</h3>
                                <p className="text-gray-600">
                                    Essential digital skills training to help healthcare workers navigate modern health information systems effectively.
                                </p>
                            </div>

                            {/* Professional Development */}
                            <div className="group rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mb-5 inline-flex rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 p-3">
                                    <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">Professional Development</h3>
                                <p className="text-gray-600">
                                    Career advancement pathways and leadership training for healthcare professionals at every level.
                                </p>
                            </div>

                            {/* Remote Access */}
                            <div className="group rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mb-5 inline-flex rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 p-3">
                                    <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864a11.04 11.04 0 0 1 2.649 2.648c.213.256.529.405.864.405H17.25M12.75 3.03l-1.5.75M12.75 3.03l1.5.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-8.25-4.5v.938m0 4.874V12m0 .188v.937m-3.375-3a2.625 2.625 0 1 0 0 5.25h.375a2.25 2.25 0 0 0 0-4.5H9.75a2.625 2.625 0 0 0 0 5.25" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">Remote Access</h3>
                                <p className="text-gray-600">
                                    Learn from anywhere across Trinidad &amp; Tobago with 24/7 access to all training materials and resources.
                                </p>
                            </div>

                            {/* Progress Tracking */}
                            <div className="group rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mb-5 inline-flex rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 p-3">
                                    <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">Progress Tracking</h3>
                                <p className="text-gray-600">
                                    Monitor your learning journey with detailed progress reports, completion certificates, and performance analytics.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Featured Training Modules */}
                {featuredCourses && featuredCourses.length > 0 && (
                    <section className="bg-gray-50 py-20 sm:py-28">
                        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div className="mb-16 text-center">
                                <span className="mb-4 inline-block rounded-full bg-purple-100 px-4 py-1 text-sm font-semibold text-purple-700">
                                    Featured
                                </span>
                                <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">
                                    Featured Training Modules
                                </h2>
                                <p className="mx-auto mt-4 max-w-2xl text-lg text-gray-600">
                                    Explore our most popular and recently updated training programs.
                                </p>
                            </div>

                            <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                                {featuredCourses.slice(0, 3).map((course) => (
                                    <div
                                        key={course.id}
                                        className="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                                    >
                                        <div className="relative bg-gradient-to-br from-indigo-500 to-purple-600 px-6 pb-12 pt-6">
                                            <div className="absolute right-4 top-4">
                                                {course.category && (
                                                    <span className="rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm">
                                                        {course.category}
                                                    </span>
                                                )}
                                            </div>
                                            <svg className="h-10 w-10 text-white/80" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>
                                        </div>
                                        <div className="flex flex-1 flex-col p-6">
                                            <h3 className="mb-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-600">
                                                {course.title}
                                            </h3>
                                            <p className="mb-4 flex-1 text-sm text-gray-600">
                                                {course.description
                                                    ? course.description.length > 120
                                                        ? course.description.substring(0, 120) + '...'
                                                        : course.description
                                                    : 'Explore this training module to enhance your healthcare skills and knowledge.'}
                                            </p>
                                            <div className="mt-auto">
                                                {isAuthenticated ? (
                                                    (isAdmin || isCourseAdmin) ? (
                                                        <Link
                                                            href={`/courses/${course.id}`}
                                                            className="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
                                                        >
                                                            Manage Course
                                                        </Link>
                                                    ) : (
                                                        <Link
                                                            href={`/courses/${course.id}`}
                                                            className="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
                                                        >
                                                            View Course
                                                        </Link>
                                                    )
                                                ) : (
                                                    <Link
                                                        href="/register"
                                                        className="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
                                                    >
                                                        Get Started
                                                    </Link>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="mt-12 text-center">
                                <Link
                                    href="/courses"
                                    className="inline-flex items-center rounded-xl border-2 border-indigo-600 px-8 py-3 text-base font-semibold text-indigo-600 transition hover:bg-indigo-600 hover:text-white"
                                >
                                    View All Courses
                                    <svg className="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </section>
                )}

                {/* Partners Section */}
                <section className="bg-white py-20 sm:py-28">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="mb-16 text-center">
                            <span className="mb-4 inline-block rounded-full bg-blue-100 px-4 py-1 text-sm font-semibold text-blue-700">
                                Partners
                            </span>
                            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">
                                Our Partners
                            </h2>
                            <p className="mx-auto mt-4 max-w-2xl text-lg text-gray-600">
                                Collaborating with leading organizations to deliver world-class healthcare training.
                            </p>
                        </div>

                        <div className="grid gap-8 sm:grid-cols-3">
                            {/* iTECH */}
                            <div className="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600">
                                    <svg className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">iTECH</h3>
                                <p className="text-sm text-gray-600">
                                    International Training and Education Center for Health - providing technical assistance and training solutions.
                                </p>
                            </div>

                            {/* HACU */}
                            <div className="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600">
                                    <svg className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">HACU</h3>
                                <p className="text-sm text-gray-600">
                                    Health Accounts Coordinating Unit - supporting workforce development and healthcare capacity building.
                                </p>
                            </div>

                            {/* ICT Division */}
                            <div className="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div className="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-indigo-600">
                                    <svg className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Zm-3 6h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-xl font-semibold text-gray-900">ICT Division</h3>
                                <p className="text-sm text-gray-600">
                                    Information and Communications Technology Division - enabling digital infrastructure and platform development.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Contact Section */}
                <section className="bg-gray-900 py-20 sm:py-28">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="mb-16 text-center">
                            <span className="mb-4 inline-block rounded-full bg-indigo-500/20 px-4 py-1 text-sm font-semibold text-indigo-300">
                                Contact
                            </span>
                            <h2 className="text-3xl font-bold text-white sm:text-4xl">
                                Get in Touch
                            </h2>
                            <p className="mx-auto mt-4 max-w-2xl text-lg text-gray-400">
                                Have questions about the MOH Learning Portal? Reach out to our support team.
                            </p>
                        </div>

                        <div className="grid gap-8 sm:grid-cols-3">
                            {/* Email */}
                            <div className="rounded-2xl border border-gray-700/50 bg-gray-800/50 p-8 text-center backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-600">
                                <div className="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-500/20">
                                    <svg className="h-7 w-7 text-indigo-400" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-lg font-semibold text-white">Email</h3>
                                <a
                                    href="mailto:lms@health.gov.tt"
                                    className="text-indigo-400 transition hover:text-indigo-300"
                                >
                                    lms@health.gov.tt
                                </a>
                            </div>

                            {/* Phone */}
                            <div className="rounded-2xl border border-gray-700/50 bg-gray-800/50 p-8 text-center backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-600">
                                <div className="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/20">
                                    <svg className="h-7 w-7 text-emerald-400" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-lg font-semibold text-white">Phone</h3>
                                <p className="text-gray-400">1-868-XXX-XXXX</p>
                            </div>

                            {/* Location */}
                            <div className="rounded-2xl border border-gray-700/50 bg-gray-800/50 p-8 text-center backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-600">
                                <div className="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-purple-500/20">
                                    <svg className="h-7 w-7 text-purple-400" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                </div>
                                <h3 className="mb-2 text-lg font-semibold text-white">Location</h3>
                                <p className="text-gray-400">
                                    Ministry of Health,<br />
                                    Trinidad and Tobago
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="border-t border-gray-200 bg-gray-50 py-12">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex flex-col items-center justify-between gap-6 sm:flex-row">
                            <div className="flex items-center space-x-3">
                                <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600">
                                    <svg className="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                    </svg>
                                </div>
                                <span className="text-sm font-semibold text-gray-700">MOH Learning Portal</span>
                            </div>

                            <div className="flex items-center space-x-6">
                                <Link
                                    href="/terms"
                                    className="text-sm text-gray-500 transition hover:text-gray-700"
                                >
                                    Terms of Service
                                </Link>
                                <Link
                                    href="/privacy"
                                    className="text-sm text-gray-500 transition hover:text-gray-700"
                                >
                                    Privacy Policy
                                </Link>
                            </div>

                            <p className="text-sm text-gray-500">
                                &copy; {new Date().getFullYear()} Ministry of Health. All rights reserved.
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
