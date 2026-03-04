import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';

const sections = [
    { id: 'introduction', title: '1. Introduction' },
    { id: 'user-accounts', title: '2. User Accounts' },
    { id: 'acceptable-use', title: '3. Acceptable Use' },
    { id: 'enrollment-access', title: '4. Course Enrollment & Access' },
    { id: 'intellectual-property', title: '5. Intellectual Property' },
    { id: 'limitation-liability', title: '6. Limitation of Liability' },
    { id: 'contact', title: '7. Contact Information' },
];

function BackToTop() {
    return (
        <div className="mt-6 text-right">
            <button
                onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
                className="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 transition-colors"
            >
                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 15l7-7 7 7" />
                </svg>
                Back to top
            </button>
        </div>
    );
}

export default function Terms() {
    const [showFloatingTop, setShowFloatingTop] = useState(false);

    useEffect(() => {
        const handleScroll = () => setShowFloatingTop(window.scrollY > 400);
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <>
            <Head title="Terms and Conditions" />

            <div className="min-h-screen bg-gray-50">
                {/* Header */}
                <nav className="bg-white shadow-sm border-b border-gray-200">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                        <Link href="/" className="flex items-center gap-2">
                            <div className="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <span className="text-white font-bold text-xs">MOH</span>
                            </div>
                            <span className="text-lg font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                Learning
                            </span>
                        </Link>
                        <Link href="/" className="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                            &larr; Back to Home
                        </Link>
                    </div>
                </nav>

                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                    {/* Page Title */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900 tracking-tight">Terms and Conditions</h1>
                        <p className="mt-2 text-sm text-gray-500">Last updated: March 4, 2026</p>
                    </div>

                    {/* Main Card */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        {/* Table of Contents */}
                        <div className="bg-gray-50 border-b border-gray-200 px-6 sm:px-8 py-6">
                            <h2 className="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Table of Contents</h2>
                            <nav className="grid grid-cols-1 sm:grid-cols-2 gap-1">
                                {sections.map((section) => (
                                    <a
                                        key={section.id}
                                        href={`#${section.id}`}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            document.getElementById(section.id)?.scrollIntoView({ behavior: 'smooth' });
                                        }}
                                        className="text-sm text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-md px-3 py-1.5 transition-colors"
                                    >
                                        {section.title}
                                    </a>
                                ))}
                            </nav>
                        </div>

                        {/* Content */}
                        <div className="px-6 sm:px-8 py-8 space-y-10">
                            {/* Section 1 */}
                            <section id="introduction">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    1. Introduction
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>
                                        Welcome to the MOH Learning Platform, operated by the <strong>Ministry of Health, Trinidad and Tobago</strong>.
                                        By accessing and using this platform, you agree to be bound by these Terms and Conditions.
                                    </p>
                                    <p>
                                        The purpose of this platform is to provide training and educational resources to Ministry of Health
                                        staff and approved external users. These terms govern your use of all services, content, and features
                                        available through the platform.
                                    </p>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 2 */}
                            <section id="user-accounts">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    2. User Accounts
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>Users must be <strong>18 years of age or older</strong> to register for an account.</li>
                                        <li>
                                            Users are responsible for maintaining the confidentiality of their login credentials.
                                            You must not share your password or allow others to access your account.
                                        </li>
                                        <li>
                                            <strong>One account per person</strong> — sharing accounts is strictly prohibited.
                                            Each user must register with their own unique credentials.
                                        </li>
                                        <li>
                                            The Ministry reserves the right to <strong>suspend or terminate</strong> accounts that
                                            violate these terms, at any time and without prior notice.
                                        </li>
                                    </ul>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 3 */}
                            <section id="acceptable-use">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    3. Acceptable Use
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>The platform is intended for <strong>professional development and training purposes only</strong>. By using this platform, you agree that you will not:</p>
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>Share, redistribute, or download course content without explicit authorization.</li>
                                        <li>Attempt to access courses or areas of the platform that you are not enrolled in or authorized for.</li>
                                        <li>Engage in harassment, discrimination, or any form of inappropriate behaviour on the platform.</li>
                                        <li>Use the platform for any purpose other than professional development and training.</li>
                                    </ul>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 4 */}
                            <section id="enrollment-access">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    4. Course Enrollment & Access
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>Enrollment in certain courses may require <strong>approval from an administrator</strong>.</li>
                                        <li>The Ministry reserves the right to approve or deny enrollment requests at its discretion.</li>
                                        <li>Course availability may change without prior notice. Courses may be added, modified, or removed at any time.</li>
                                        <li>Completion certificates are issued at the discretion of course administrators and are subject to successful completion of all required course components.</li>
                                    </ul>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 5 */}
                            <section id="intellectual-property">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    5. Intellectual Property
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>
                                        All course content, materials, and resources available on this platform are the property of the
                                        <strong> Ministry of Health, Trinidad and Tobago</strong>, or their respective content creators.
                                    </p>
                                    <p>
                                        Unauthorized reproduction, distribution, modification, or use of any content from this platform
                                        is strictly prohibited and may result in legal action.
                                    </p>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 6 */}
                            <section id="limitation-liability">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    6. Limitation of Liability
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>
                                        The platform is provided on an <strong>&quot;as is&quot;</strong> basis. The Ministry of Health makes
                                        no guarantees regarding uninterrupted access to the platform or error-free operation of its services.
                                    </p>
                                    <p>
                                        To the maximum extent permitted by law, the Ministry shall not be liable for any direct, indirect,
                                        incidental, special, consequential, or punitive damages arising from your use of, or inability to
                                        use, the platform.
                                    </p>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 7 */}
                            <section id="contact">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    7. Contact Information
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>
                                        If you have any questions or concerns about these Terms and Conditions, please contact us:
                                    </p>
                                    <div className="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <p className="font-medium text-gray-900">Ministry of Health, Trinidad and Tobago</p>
                                        <p className="mt-1">
                                            Email:{' '}
                                            <a href="mailto:helpdesk@health.gov.tt" className="text-indigo-600 hover:text-indigo-800 font-medium">
                                                helpdesk@health.gov.tt
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <BackToTop />
                            </section>
                        </div>
                    </div>

                    {/* Related Link */}
                    <div className="mt-6 text-center">
                        <Link href="/privacy" className="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View our Privacy Policy &rarr;
                        </Link>
                    </div>
                </div>

                {/* Footer */}
                <footer className="border-t border-gray-200 bg-gray-50 py-6 text-center text-sm text-gray-500 mt-12">
                    <div className="flex flex-wrap justify-center gap-4 mb-2">
                        <span className="font-medium text-gray-700">Terms and Conditions</span>
                        <Link href="/privacy" className="hover:text-gray-700 hover:underline">Privacy Policy</Link>
                    </div>
                    <p>&copy; {new Date().getFullYear()} Ministry of Health Trinidad and Tobago. All rights reserved.</p>
                </footer>
            </div>

            {/* Floating Back to Top Button */}
            {showFloatingTop && (
                <button
                    onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
                    className="fixed bottom-6 right-6 z-50 flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-700 transition-all"
                    aria-label="Back to top"
                >
                    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
            )}
        </>
    );
}
