import { useState, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';

const css = `
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
`;

const FAQ_SECTIONS = [
    {
        id: 'getting-started',
        title: 'Getting Started',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        ),
        items: [
            {
                q: 'What is the MOH Learning Portal (mohlearn)?',
                a: 'The MOH Learning Portal is the official digital training platform of the Ministry of Health of Trinidad & Tobago. It provides access to professional development courses, clinical applications training, and certification programs for healthcare workers and approved external partners.',
            },
            {
                q: 'Who can register for an account?',
                a: (
                    <>
                        There are two types of accounts:
                        <ul className="mt-2 ml-4 list-disc space-y-1">
                            <li><strong>MOH Staff</strong> — Employees of the Ministry of Health who register using their <code className="rounded bg-gray-100 px-1.5 py-0.5 text-sm">@health.gov.tt</code> email. Staff accounts require approval by a Course Administrator.</li>
                            <li><strong>External Users</strong> — Healthcare partners, NGOs, and other approved organizations. External users register using their work or personal email and may request access to specific courses.</li>
                        </ul>
                    </>
                ),
            },
            {
                q: 'How do I register?',
                a: (
                    <>
                        Click the <strong>Register</strong> button on the homepage or login page, then complete the form with your name, email, department/organization, date of birth, and a strong password. You must be 18 or older to register and must accept the Terms and Conditions.
                    </>
                ),
            },
            {
                q: 'Why does my account need approval?',
                a: 'MOH staff accounts are reviewed by a Course Administrator to confirm eligibility and prevent unauthorized access to internal training. You will receive an email once your account is approved. External users may also need approval for specific courses depending on the course enrollment settings.',
            },
        ],
    },
    {
        id: 'logging-in',
        title: 'Logging In & Account Security',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        ),
        items: [
            {
                q: 'How do I log in?',
                a: 'Click the Sign In link on the homepage and enter your registered email and password. Your login is always used on mohlearn — never directly on the Moodle site.',
            },
            {
                q: 'What is OTP verification?',
                a: 'For your security, a one-time password (OTP) is emailed to your registered address during sign-in or the first-time verification. Enter the 6-digit code on the verification screen to complete login. If you do not receive the email, check your spam folder or click "Resend Code".',
            },
            {
                q: 'I forgot my password — what do I do?',
                a: (
                    <>
                        On the login page, click <strong>Forgot password?</strong>, enter your registered email, and follow the link sent to your inbox to set a new password. The reset link expires after a short time for security.
                    </>
                ),
            },
            {
                q: 'How do I change my password after logging in?',
                a: 'Go to Profile → Settings → Change Password. You will need to enter your current password and a new one that meets the strength requirements.',
            },
            {
                q: 'Password requirements',
                a: 'Your password must be at least 12 characters long and include uppercase, lowercase, numbers, and special characters. It cannot contain your name or email. A strength indicator helps you choose a strong password.',
            },
        ],
    },
    {
        id: 'courses',
        title: 'Finding & Accessing Courses',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
        ),
        items: [
            {
                q: 'How do I browse available courses?',
                a: (
                    <>
                        After logging in, use the <strong>Courses</strong> or <strong>Catalog</strong> link in the navigation to see every course available to you. Courses are filtered by your role — MOH staff see internal courses, external users see externally-available courses, and some courses are open to both.
                    </>
                ),
            },
            {
                q: 'How do I enrol in a course?',
                a: (
                    <>
                        Open the course page and click <strong>Enrol</strong>. There are two enrolment types:
                        <ul className="mt-2 ml-4 list-disc space-y-1">
                            <li><strong>Open Enrolment</strong> — You gain access immediately.</li>
                            <li><strong>Approval Required</strong> — Your request is sent to a Course Administrator. You will be notified by email once approved.</li>
                        </ul>
                    </>
                ),
            },
            {
                q: 'Where can I see my enrolled courses?',
                a: 'Go to My Learning (or My Courses) from the navigation menu. You will see all courses you are currently enrolled in, along with your progress.',
            },
            {
                q: 'How do I check the status of a pending access request?',
                a: 'Go to My Requests from the navigation menu to see all your submitted course access requests and their current status (pending, approved, or denied).',
            },
            {
                q: 'How do I launch a course?',
                a: 'From your course page, click Access Course. You will be automatically signed into Moodle where the course content is delivered. You do not need a separate Moodle password.',
            },
        ],
    },
    {
        id: 'moodle-sso',
        title: 'Moodle & Single Sign-On (SSO)',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
        ),
        items: [
            {
                q: 'What is Single Sign-On (SSO)?',
                a: 'SSO means you only need to log in once on mohlearn, and you will be automatically signed into the Moodle learning platform when accessing courses. You never have to remember a separate Moodle password.',
            },
            {
                q: 'Why does Moodle redirect me to mohlearn?',
                a: 'All Moodle access is secured through mohlearn. If you try to go directly to the Moodle site, you will be redirected to the mohlearn login page. After signing in, you will be sent back to Moodle automatically.',
            },
            {
                q: 'Can I use the Moodle mobile app?',
                a: (
                    <>
                        Yes. Download the official Moodle app from the App Store or Google Play, enter the site URL <code className="rounded bg-gray-100 px-1.5 py-0.5 text-sm">https://learnabouthealth.hin.gov.tt</code>, and tap Sign In. The app will open a browser window where you sign in with your mohlearn email and password. Once authenticated, the app receives your access token automatically.
                    </>
                ),
            },
            {
                q: 'How do I log out of both systems?',
                a: 'Clicking Sign Out on mohlearn or on Moodle will log you out of both platforms at the same time.',
            },
        ],
    },
    {
        id: 'profile',
        title: 'Profile & Settings',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
        ),
        items: [
            {
                q: 'How do I update my personal information?',
                a: 'Go to Profile → Settings to update your name, department, and other details. Some fields may be locked once set (such as your date of birth) and will require an administrator to change.',
            },
            {
                q: 'How do I upload a profile photo?',
                a: 'In Profile Settings, click the photo placeholder or Upload Photo and select an image file. Supported formats include JPG and PNG.',
            },
            {
                q: 'How do I view my notifications?',
                a: 'Click the bell icon in the top navigation bar to see recent notifications — these include enrolment approvals, course announcements, and system alerts.',
            },
            {
                q: 'How do I delete my account?',
                a: 'Account deletion can be requested from Profile → Settings. Please note that deleting your account removes your learning history and is permanent. Contact the Helpdesk if you need assistance.',
            },
        ],
    },
    {
        id: 'certificates',
        title: 'Certificates & Progress',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
            </svg>
        ),
        items: [
            {
                q: 'How do I earn a certificate?',
                a: 'Certificates are issued automatically once you complete all required activities in a course — this may include watching lessons, completing assignments, and passing the final assessment.',
            },
            {
                q: 'Where can I download my certificate?',
                a: 'When you finish a course, open the course page in Moodle and look for the Certificate block. Click to download your PDF. Completed certificates are also listed under My Learning.',
            },
            {
                q: 'How do I track my progress?',
                a: 'The My Learning page shows a progress bar for each enrolled course. You can also open any course to see which activities are complete and which are pending.',
            },
        ],
    },
    {
        id: 'technical',
        title: 'Technical Issues',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
            </svg>
        ),
        items: [
            {
                q: 'I cannot log in — what should I check?',
                a: (
                    <ol className="mt-2 ml-4 list-decimal space-y-1">
                        <li>Make sure you are using the email address you registered with.</li>
                        <li>Check that Caps Lock is off and your password is entered correctly.</li>
                        <li>If you were recently registered, confirm your account has been approved.</li>
                        <li>Try clearing your browser cache or opening a private/incognito window.</li>
                        <li>Use Forgot password if you are unsure of your password.</li>
                    </ol>
                ),
            },
            {
                q: 'The page is not loading or looks broken',
                a: 'Refresh the page (Ctrl+F5 / Cmd+Shift+R). Make sure your browser is up to date — we recommend the latest version of Google Chrome, Microsoft Edge, Firefox, or Safari. Disable any browser extensions that may block scripts.',
            },
            {
                q: 'I am not receiving emails (OTP, password reset, notifications)',
                a: (
                    <>
                        Check your Junk/Spam folder first. If nothing is there, confirm that your email address is correct in Profile → Settings and that your organisation's mail server is not blocking messages from <code className="rounded bg-gray-100 px-1.5 py-0.5 text-sm">@health.gov.tt</code>. Contact the Helpdesk if the issue persists.
                    </>
                ),
            },
            {
                q: 'Course content will not play or load',
                a: 'Make sure your browser allows audio/video playback and that you have a stable internet connection. Try a different browser if the problem continues. Some course videos are large and may take a moment to buffer.',
            },
            {
                q: 'My session keeps expiring',
                a: 'For security, mohlearn automatically signs you out after a period of inactivity. Use the Remember me option at login to stay signed in longer on trusted devices.',
            },
        ],
    },
    {
        id: 'support',
        title: 'Support & Contact',
        icon: (
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
            </svg>
        ),
        items: [
            {
                q: 'How do I get help?',
                a: (
                    <>
                        The MOH Helpdesk is available to assist with any technical or account issue.
                        <ul className="mt-3 space-y-2">
                            <li className="flex items-start gap-2">
                                <span className="mt-0.5 text-indigo-600">Email:</span>
                                <a href="mailto:helpdesk@health.gov.tt" className="font-medium text-indigo-600 hover:text-indigo-500">helpdesk@health.gov.tt</a>
                            </li>
                            <li className="flex items-start gap-2">
                                <span className="mt-0.5 text-indigo-600">Phone:</span>
                                <span className="font-medium">1-868-217-4664</span>
                            </li>
                        </ul>
                    </>
                ),
            },
            {
                q: 'What information should I include when contacting support?',
                a: (
                    <ul className="mt-2 ml-4 list-disc space-y-1">
                        <li>Your registered email address</li>
                        <li>The page or course you were using when the issue occurred</li>
                        <li>A screenshot of any error message (if possible)</li>
                        <li>The browser and device you are using</li>
                    </ul>
                ),
            },
            {
                q: 'What are the support hours?',
                a: 'The MOH Helpdesk operates during regular Ministry of Health working hours, Monday to Friday. Emails sent outside these hours will be responded to on the next working day.',
            },
        ],
    },
];

function FaqItem({ item, isOpen, onToggle }) {
    return (
        <div className="border-b border-gray-200 last:border-b-0">
            <button
                type="button"
                onClick={onToggle}
                className="flex w-full items-center justify-between py-4 text-left transition-colors hover:text-indigo-600"
                aria-expanded={isOpen}
            >
                <span className="pr-4 text-base font-medium text-gray-900">{item.q}</span>
                <svg
                    className={`h-5 w-5 flex-shrink-0 text-gray-400 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}
                    fill="none"
                    viewBox="0 0 24 24"
                    strokeWidth="2"
                    stroke="currentColor"
                >
                    <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            {isOpen && (
                <div className="animate-fade-in-up pb-5 pr-8 text-sm leading-relaxed text-gray-600">
                    {item.a}
                </div>
            )}
        </div>
    );
}

export default function Faq() {
    const [query, setQuery] = useState('');
    const [openItems, setOpenItems] = useState({});

    const toggleItem = (key) => {
        setOpenItems((prev) => ({ ...prev, [key]: !prev[key] }));
    };

    const normalizedQuery = query.trim().toLowerCase();

    const filteredSections = useMemo(() => {
        if (!normalizedQuery) return FAQ_SECTIONS;
        return FAQ_SECTIONS
            .map((section) => {
                const items = section.items.filter((item) => {
                    const answerText = typeof item.a === 'string' ? item.a : JSON.stringify(item.a);
                    return (
                        item.q.toLowerCase().includes(normalizedQuery) ||
                        answerText.toLowerCase().includes(normalizedQuery)
                    );
                });
                return { ...section, items };
            })
            .filter((section) => section.items.length > 0);
    }, [normalizedQuery]);

    return (
        <>
            <Head title="Help & FAQ — MOH Learning Portal" />
            <style>{css}</style>

            <div className="min-h-screen bg-gray-50">
                {/* Header */}
                <header className="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800">
                    <div className="absolute inset-0 overflow-hidden opacity-30">
                        <div className="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white/10 blur-3xl" />
                        <div className="absolute -bottom-32 right-10 h-96 w-96 rounded-full bg-purple-400/20 blur-3xl" />
                    </div>

                    <div className="relative mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
                        <div className="flex items-center justify-between">
                            <Link href="/" className="flex items-center gap-3 text-white transition hover:opacity-90">
                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                                    <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                    </svg>
                                </div>
                                <span className="font-semibold">MOH Learning</span>
                            </Link>
                            <Link
                                href="/"
                                className="flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/20"
                            >
                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg>
                                Back to Home
                            </Link>
                        </div>

                        <div className="mx-auto max-w-3xl py-16 text-center sm:py-20">
                            <span className="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-white backdrop-blur-sm">
                                Help Centre
                            </span>
                            <h1 className="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl">
                                Frequently Asked Questions
                            </h1>
                            <p className="mx-auto mt-4 max-w-2xl text-lg text-indigo-100">
                                Everything you need to know about registering, signing in, enrolling in courses, and getting the most out of the MOH Learning Portal.
                            </p>

                            {/* Search */}
                            <div className="mx-auto mt-8 max-w-xl">
                                <div className="relative">
                                    <svg
                                        className="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        strokeWidth="2"
                                        stroke="currentColor"
                                    >
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    <input
                                        type="search"
                                        value={query}
                                        onChange={(e) => setQuery(e.target.value)}
                                        placeholder="Search FAQs..."
                                        className="w-full rounded-xl border-0 bg-white py-3 pl-12 pr-4 text-gray-900 shadow-lg outline-none ring-1 ring-white/30 transition placeholder:text-gray-400 focus:ring-2 focus:ring-white"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {/* Content */}
                <main className="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
                    <div className="grid gap-8 lg:grid-cols-[240px_1fr]">
                        {/* Table of Contents */}
                        <aside className="hidden lg:block">
                            <div className="sticky top-8">
                                <p className="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    On this page
                                </p>
                                <nav className="space-y-1">
                                    {FAQ_SECTIONS.map((section) => (
                                        <a
                                            key={section.id}
                                            href={`#${section.id}`}
                                            className="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 transition hover:bg-indigo-50 hover:text-indigo-600"
                                        >
                                            <span className="text-indigo-500">{section.icon}</span>
                                            <span>{section.title}</span>
                                        </a>
                                    ))}
                                </nav>
                            </div>
                        </aside>

                        {/* FAQ Sections */}
                        <div className="space-y-8">
                            {filteredSections.length === 0 ? (
                                <div className="rounded-2xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                                    <svg className="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    <h3 className="mt-4 text-lg font-semibold text-gray-900">No results found</h3>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Try a different search term, or contact the Helpdesk for help.
                                    </p>
                                </div>
                            ) : (
                                filteredSections.map((section) => (
                                    <section
                                        key={section.id}
                                        id={section.id}
                                        className="scroll-mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8"
                                    >
                                        <div className="mb-4 flex items-center gap-3 border-l-4 border-indigo-500 pl-4">
                                            <span className="text-indigo-600">{section.icon}</span>
                                            <h2 className="text-xl font-semibold text-gray-900 sm:text-2xl">
                                                {section.title}
                                            </h2>
                                        </div>
                                        <div>
                                            {section.items.map((item, idx) => {
                                                const key = `${section.id}-${idx}`;
                                                return (
                                                    <FaqItem
                                                        key={key}
                                                        item={item}
                                                        isOpen={!!openItems[key] || !!normalizedQuery}
                                                        onToggle={() => toggleItem(key)}
                                                    />
                                                );
                                            })}
                                        </div>
                                    </section>
                                ))
                            )}

                            {/* Still need help CTA */}
                            <section className="rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-purple-50 p-8 text-center shadow-sm">
                                <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white">
                                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" strokeWidth="1.8" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                    </svg>
                                </div>
                                <h3 className="text-xl font-semibold text-gray-900">Still need help?</h3>
                                <p className="mx-auto mt-2 max-w-md text-sm text-gray-600">
                                    Our Helpdesk team is ready to assist with any question the FAQ did not answer.
                                </p>
                                <div className="mt-5 flex flex-col items-center justify-center gap-3 sm:flex-row">
                                    <a
                                        href="mailto:helpdesk@health.gov.tt"
                                        className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                                    >
                                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                        Email Helpdesk
                                    </a>
                                    <a
                                        href="tel:+18682174664"
                                        className="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50"
                                    >
                                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                        </svg>
                                        1-868-217-4664
                                    </a>
                                </div>
                            </section>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-gray-200 bg-white py-8">
                    <div className="mx-auto flex max-w-5xl flex-col items-center justify-between gap-4 px-4 sm:flex-row sm:px-6 lg:px-8">
                        <p className="text-sm text-gray-500">
                            &copy; {new Date().getFullYear()} Ministry of Health, Trinidad &amp; Tobago.
                        </p>
                        <div className="flex gap-5">
                            <Link href="/terms" className="text-sm text-gray-500 hover:text-gray-700">Terms</Link>
                            <Link href="/privacy" className="text-sm text-gray-500 hover:text-gray-700">Privacy</Link>
                            <Link href="/login" className="text-sm text-gray-500 hover:text-gray-700">Sign In</Link>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
