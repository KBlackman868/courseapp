import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';

const sections = [
    { id: 'introduction', title: '1. Introduction' },
    { id: 'information-collected', title: '2. Information We Collect' },
    { id: 'how-we-use', title: '3. How We Use Your Information' },
    { id: 'data-storage', title: '4. Data Storage & Security' },
    { id: 'user-rights', title: '5. User Rights' },
    { id: 'cookies', title: '6. Cookies' },
    { id: 'changes', title: '7. Changes to This Policy' },
    { id: 'contact', title: '8. Contact' },
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

export default function Privacy() {
    const [showFloatingTop, setShowFloatingTop] = useState(false);

    useEffect(() => {
        const handleScroll = () => setShowFloatingTop(window.scrollY > 400);
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <>
            <Head title="Privacy Policy" />

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
                        <h1 className="text-3xl font-bold text-gray-900 tracking-tight">Privacy Policy</h1>
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
                                        The <strong>Ministry of Health, Trinidad and Tobago</strong> is committed to protecting the privacy
                                        of all users of the MOH Learning Platform.
                                    </p>
                                    <p>
                                        This Privacy Policy explains what personal data we collect, how we use it, how we store and protect
                                        it, and what rights you have regarding your information. By using the platform, you consent to the
                                        data practices described in this policy.
                                    </p>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 2 */}
                            <section id="information-collected">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    2. Information We Collect
                                </h2>
                                <div className="space-y-4 text-gray-600 leading-relaxed pl-5">
                                    <div>
                                        <h3 className="text-base font-semibold text-gray-800 mb-2">Personal Information</h3>
                                        <ul className="list-disc pl-5 space-y-1">
                                            <li>Full name (first name and last name)</li>
                                            <li>Email address</li>
                                            <li>Date of birth</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3 className="text-base font-semibold text-gray-800 mb-2">Account Information</h3>
                                        <ul className="list-disc pl-5 space-y-1">
                                            <li>User role (e.g., MOH Staff, External User)</li>
                                            <li>Enrollment status for courses</li>
                                            <li>Course progress and completion records</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3 className="text-base font-semibold text-gray-800 mb-2">Usage Data</h3>
                                        <ul className="list-disc pl-5 space-y-1">
                                            <li>Login times and session duration</li>
                                            <li>Pages visited within the platform</li>
                                            <li>Course completion data and assessment results</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3 className="text-base font-semibold text-gray-800 mb-2">Technical Data</h3>
                                        <ul className="list-disc pl-5 space-y-1">
                                            <li>Browser type and version</li>
                                            <li>IP address (collected for security purposes only)</li>
                                        </ul>
                                    </div>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 3 */}
                            <section id="how-we-use">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    3. How We Use Your Information
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>We use the information we collect for the following purposes:</p>
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>To provide access to courses and training materials</li>
                                        <li>To manage enrollment requests and approvals</li>
                                        <li>To track course progress and completion</li>
                                        <li>To communicate important updates about your account or courses</li>
                                        <li>To improve the platform and overall user experience</li>
                                    </ul>
                                    <div className="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                                        <p className="text-green-800 font-medium">
                                            We do NOT sell, rent, or share your personal information with third parties.
                                        </p>
                                    </div>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 4 */}
                            <section id="data-storage">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    4. Data Storage & Security
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>All data is stored securely on <strong>Ministry-managed servers</strong>.</li>
                                        <li>Access to personal data is restricted to <strong>authorized administrators</strong> only.</li>
                                        <li>We use <strong>encryption for data transmission</strong> (SSL/TLS) to protect your information in transit.</li>
                                        <li>We implement appropriate technical and organizational measures to protect against unauthorized access, alteration, disclosure, or destruction of your data.</li>
                                    </ul>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 5 */}
                            <section id="user-rights">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    5. User Rights
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>As a user of the MOH Learning Platform, you have the following rights:</p>
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>
                                            <strong>View and update your information:</strong> You can view and update your profile
                                            information at any time through your account settings.
                                        </li>
                                        <li>
                                            <strong>Request account deletion:</strong> You can request deletion of your account by
                                            contacting{' '}
                                            <a href="mailto:helpdesk@health.gov.tt" className="text-indigo-600 hover:text-indigo-800 font-medium">
                                                helpdesk@health.gov.tt
                                            </a>.
                                        </li>
                                        <li>
                                            <strong>Request a copy of your data:</strong> You may request a copy of the personal data
                                            we hold about you at any time.
                                        </li>
                                    </ul>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 6 */}
                            <section id="cookies">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    6. Cookies
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>The platform uses <strong>session cookies</strong> for authentication purposes. These cookies are essential for keeping you logged in while navigating the platform.</li>
                                        <li><strong>No third-party tracking cookies</strong> are used. We do not use cookies for advertising, analytics, or any other tracking purposes.</li>
                                    </ul>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 7 */}
                            <section id="changes">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    7. Changes to This Policy
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>
                                        The Ministry of Health may update this Privacy Policy at any time to reflect changes in our
                                        practices, legal requirements, or operational needs.
                                    </p>
                                    <p>
                                        Users will be notified of significant changes through the platform. We encourage you to review
                                        this policy periodically to stay informed about how we protect your data.
                                    </p>
                                </div>
                                <BackToTop />
                            </section>

                            {/* Section 8 */}
                            <section id="contact">
                                <h2 className="text-xl font-semibold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-4">
                                    8. Contact
                                </h2>
                                <div className="space-y-3 text-gray-600 leading-relaxed pl-5">
                                    <p>
                                        If you have any questions, concerns, or requests regarding this Privacy Policy or the handling
                                        of your personal data, please contact us:
                                    </p>
                                    <div className="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <p className="font-medium text-gray-900">Ministry of Health, Trinidad and Tobago</p>
                                        <p className="mt-1">
                                            Data protection inquiries:{' '}
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
                        <Link href="/terms" className="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View our Terms and Conditions &rarr;
                        </Link>
                    </div>
                </div>

                {/* Footer */}
                <footer className="border-t border-gray-200 bg-gray-50 py-6 text-center text-sm text-gray-500 mt-12">
                    <div className="flex flex-wrap justify-center gap-4 mb-2">
                        <Link href="/terms" className="hover:text-gray-700 hover:underline">Terms and Conditions</Link>
                        <span className="font-medium text-gray-700">Privacy Policy</span>
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
