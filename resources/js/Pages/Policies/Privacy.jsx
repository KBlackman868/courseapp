import { Head, Link } from '@inertiajs/react';

export default function Privacy() {
    return (
        <>
            <Head title="Privacy Policy" />

            <div className="min-h-screen bg-gray-50">
                <nav className="bg-white shadow-sm">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                        <Link href="/" className="text-xl font-bold text-blue-600">MOH Learning Portal</Link>
                        <Link href="/" className="text-sm text-gray-600 hover:text-blue-600">Back to Home</Link>
                    </div>
                </nav>

                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
                    <p className="text-sm text-gray-500 mb-8">Last updated: March 3, 2026</p>

                    <div className="prose prose-blue max-w-none bg-white rounded-lg shadow-sm p-8">
                        <p>
                            The Ministry of Health of the Republic of Trinidad and Tobago (&quot;the Ministry&quot;, &quot;we&quot;, &quot;us&quot;, or &quot;our&quot;)
                            is committed to protecting your personal data and respecting your privacy. This Privacy Policy explains
                            how we collect, use, store, and protect the personal information you provide when using the MOH
                            Learning Platform (&quot;the Platform&quot;).
                        </p>
                        <p>
                            This policy is aligned with the principles of the <strong>Data Protection Act, 2011</strong> of
                            the Republic of Trinidad and Tobago and applies to all users of the Platform.
                        </p>

                        <h2>1. Accountability</h2>
                        <p>
                            The Ministry of Health is the data controller responsible for your personal information collected
                            through this Platform. We are accountable for ensuring that all personal data is handled in compliance
                            with the Data Protection Act, 2011 and other applicable laws of Trinidad and Tobago.
                        </p>
                        <p>
                            Any questions or concerns regarding the handling of your personal data should be directed to our
                            designated contact at <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a>.
                        </p>

                        <h2>2. Information We Collect</h2>
                        <p>We collect the following types of personal information when you register for and use the Platform:</p>

                        <h3>Personal Identification Information</h3>
                        <ul>
                            <li>Full name (first name and last name)</li>
                            <li>Email address</li>
                            <li>Phone number</li>
                            <li>Date of birth</li>
                        </ul>

                        <h3>Professional Information</h3>
                        <ul>
                            <li>Job title / position</li>
                            <li>Department or unit</li>
                            <li>Regional Health Authority or affiliated institution</li>
                            <li>Professional registration number (where applicable)</li>
                        </ul>

                        <h3>Technical and Usage Information</h3>
                        <ul>
                            <li>IP address and browser type</li>
                            <li>Device information and operating system</li>
                            <li>Login timestamps and session duration</li>
                            <li>Pages visited and features used within the Platform</li>
                            <li>Course enrollment, progress, and completion data</li>
                            <li>Assessment scores and certification records</li>
                        </ul>

                        <h2>3. Purpose of Collection</h2>
                        <p>Your personal data is collected and used for the following purposes:</p>
                        <ul>
                            <li>Creating and managing your user account on the Platform.</li>
                            <li>Verifying your identity and eligibility to access the Platform.</li>
                            <li>Facilitating course enrollment, tracking learning progress, and issuing certifications.</li>
                            <li>Communicating with you about your account, courses, and Platform updates.</li>
                            <li>Generating anonymized and aggregated reports for administrative, planning, and policy purposes.</li>
                            <li>Ensuring Platform security, preventing unauthorized access, and investigating potential violations.</li>
                            <li>Complying with legal and regulatory obligations.</li>
                            <li>Improving the Platform&apos;s functionality, content, and user experience.</li>
                        </ul>

                        <h2>4. Consent</h2>
                        <p>
                            By registering for an account and using the Platform, you provide your informed consent for the
                            collection, use, and processing of your personal data as described in this Privacy Policy.
                        </p>
                        <p>
                            You may withdraw your consent at any time by contacting us
                            at <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a>.
                            Please be aware that withdrawing consent may result in the suspension or termination of your access
                            to the Platform, as certain data processing is essential for providing our services.
                        </p>

                        <h2>5. Data Minimisation</h2>
                        <p>
                            We only collect personal data that is directly necessary for the purposes identified in this policy.
                            We do not collect excessive or unnecessary information. Registration forms and data collection points
                            are designed to gather only the minimum information required to provide the Platform services effectively.
                        </p>

                        <h2>6. Data Retention</h2>
                        <p>
                            Your personal data will be retained for as long as your account remains active or as needed to provide
                            the Platform services. Specifically:
                        </p>
                        <ul>
                            <li><strong>Active accounts:</strong> Data is retained for the duration of your active use of the Platform.</li>
                            <li><strong>Inactive accounts:</strong> Accounts that have been inactive for an extended period may be subject to archival or deletion in accordance with our data retention policies.</li>
                            <li><strong>Course and certification records:</strong> Learning records, assessment results, and certifications may be retained for a longer period to support professional development records and regulatory compliance.</li>
                            <li><strong>Post-termination:</strong> Upon account deletion, personal data will be securely deleted or anonymized within a reasonable timeframe, except where retention is required by law.</li>
                        </ul>

                        <h2>7. Accuracy</h2>
                        <p>
                            We take reasonable steps to ensure that the personal data we hold about you is accurate, complete,
                            and up to date. You are responsible for providing accurate information during registration and for
                            updating your profile if your personal details change.
                        </p>
                        <p>
                            You may review and update your personal information at any time through your account profile settings.
                            If you believe any information we hold about you is inaccurate or incomplete, please contact us
                            at <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a>.
                        </p>

                        <h2>8. Safeguards</h2>
                        <p>
                            We implement appropriate technical, administrative, and organizational security measures to protect
                            your personal data from unauthorized access, disclosure, alteration, destruction, or loss. These
                            measures include:
                        </p>
                        <ul>
                            <li>Encryption of data in transit using TLS/SSL protocols.</li>
                            <li>Encryption of sensitive data at rest.</li>
                            <li>Access controls that restrict data access to authorized personnel only.</li>
                            <li>Regular security assessments and vulnerability testing.</li>
                            <li>Secure authentication mechanisms, including password hashing and session management.</li>
                            <li>Monitoring and logging of access to detect and respond to potential security incidents.</li>
                        </ul>
                        <p>
                            While we strive to protect your data, no method of electronic storage or transmission is completely
                            secure. We cannot guarantee absolute security but are committed to maintaining the highest practicable
                            standards.
                        </p>

                        <h2>9. Cookies</h2>
                        <p>
                            The Platform uses cookies that are strictly necessary for its operation. These cookies are essential
                            for enabling core functionality such as maintaining your login session and ensuring security. We do
                            not use cookies for advertising, tracking, or analytics purposes.
                        </p>

                        <div className="not-prose my-6 overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cookie Name</th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Purpose</th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td className="px-6 py-4 text-sm font-medium text-gray-900">Session Cookie</td>
                                        <td className="px-6 py-4 text-sm text-gray-600">Maintains your authenticated session while using the Platform. This cookie is essential for keeping you logged in as you navigate between pages.</td>
                                        <td className="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">Browser session</td>
                                    </tr>
                                    <tr>
                                        <td className="px-6 py-4 text-sm font-medium text-gray-900">XSRF-TOKEN</td>
                                        <td className="px-6 py-4 text-sm text-gray-600">Protects against cross-site request forgery (CSRF) attacks. This security cookie ensures that form submissions and requests originate from the Platform and not from malicious third-party sources.</td>
                                        <td className="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">Browser session</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p>
                            Because these cookies are strictly necessary for the Platform to function, they cannot be disabled.
                            By using the Platform, you consent to the use of these essential cookies.
                        </p>

                        <h2>10. Transparency</h2>
                        <p>
                            We are committed to being open and transparent about how we handle your personal data. This Privacy
                            Policy is publicly available on the Platform and describes our data practices in clear, accessible
                            language. If we make significant changes to our data handling practices, we will update this policy
                            and notify users as appropriate.
                        </p>

                        <h2>11. Third-Party Services</h2>
                        <p>
                            The Platform may integrate with or link to third-party services to support its educational functions.
                            Any personal data shared with third-party service providers is done so under appropriate data processing
                            agreements and safeguards.
                        </p>
                        <p>We require that third-party providers:</p>
                        <ul>
                            <li>Process your data only for the purposes specified by the Ministry.</li>
                            <li>Implement appropriate security measures to protect your data.</li>
                            <li>Not use your personal data for their own marketing or unrelated purposes.</li>
                            <li>Comply with applicable data protection laws.</li>
                        </ul>
                        <p>
                            We are not responsible for the privacy practices of external websites linked from the Platform.
                            We encourage you to review the privacy policies of any third-party sites you visit.
                        </p>

                        <h2>12. Your Rights</h2>
                        <p>
                            Under the Data Protection Act, 2011, you have the following rights in relation to your personal data:
                        </p>
                        <ul>
                            <li><strong>Right of Access:</strong> You have the right to request access to the personal data we hold about you and to receive a copy of that data.</li>
                            <li><strong>Right to Rectification:</strong> You have the right to request correction of any inaccurate or incomplete personal data.</li>
                            <li><strong>Right to Erasure:</strong> You may request the deletion of your personal data, subject to any legal obligations that require us to retain certain information.</li>
                            <li><strong>Right to Withdraw Consent:</strong> Where processing is based on your consent, you may withdraw that consent at any time.</li>
                            <li><strong>Right to Object:</strong> You have the right to object to the processing of your personal data in certain circumstances.</li>
                        </ul>
                        <p>
                            To exercise any of these rights, please contact us
                            at <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a>. We
                            will respond to your request within a reasonable timeframe in accordance with the law.
                        </p>

                        <h2>13. Children&apos;s Privacy</h2>
                        <p>
                            The Platform is not intended for use by individuals under the age of 18. We do not knowingly collect
                            personal data from children. If we become aware that we have inadvertently collected personal
                            information from a person under 18, we will take immediate steps to delete that information.
                        </p>
                        <p>
                            If you believe that a child under 18 has provided us with personal data, please contact us immediately
                            at <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a>.
                        </p>

                        <h2>14. Complaints</h2>
                        <p>
                            If you believe that your personal data has been handled in a manner that is not consistent with this
                            Privacy Policy or applicable data protection laws, you have the right to file a complaint.
                        </p>
                        <p>You may direct your complaint to:</p>
                        <ul>
                            <li><strong>The MOH Learning Platform team</strong> at <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a></li>
                            <li><strong>The Office of the Information Commissioner</strong> of Trinidad and Tobago, which is the authority responsible for overseeing compliance with the Data Protection Act, 2011.</li>
                        </ul>

                        <h2>15. Changes to This Policy</h2>
                        <p>
                            We may update this Privacy Policy from time to time to reflect changes in our practices, legal
                            requirements, or operational needs. When we make significant changes, we will:
                        </p>
                        <ul>
                            <li>Update the &quot;Last updated&quot; date at the top of this page.</li>
                            <li>Where appropriate, notify users through the Platform or via email.</li>
                        </ul>
                        <p>
                            Your continued use of the Platform after any changes to this policy constitutes acceptance of the
                            updated terms. We encourage you to review this policy periodically to stay informed about how we
                            protect your data.
                        </p>

                        <h2>16. Contact Information</h2>
                        <p>
                            If you have any questions, concerns, or requests regarding this Privacy Policy or the handling of
                            your personal data, please contact us:
                        </p>
                        <p>
                            Ministry of Health, Republic of Trinidad and Tobago<br />
                            Email: <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a>
                        </p>
                        <p>
                            We are committed to resolving any concerns you may have about our collection, use, or disclosure
                            of your personal data. Your trust is important to us, and we take our responsibility to protect
                            your privacy seriously.
                        </p>
                    </div>
                </div>

                <footer className="bg-white border-t mt-12 py-6">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
                        <p>&copy; {new Date().getFullYear()} Ministry of Health, Trinidad and Tobago. All rights reserved.</p>
                        <div className="mt-2 space-x-4">
                            <Link href="/terms-and-conditions" className="text-blue-600 hover:underline">Terms</Link>
                            <Link href="/privacy-policy" className="text-blue-600 hover:underline">Privacy Policy</Link>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
