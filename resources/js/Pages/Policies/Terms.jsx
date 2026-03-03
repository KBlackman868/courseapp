import { Head, Link } from '@inertiajs/react';

export default function Terms() {
    return (
        <>
            <Head title="Terms and Conditions" />

            <div className="min-h-screen bg-gray-50">
                <nav className="bg-white shadow-sm">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                        <Link href="/" className="text-xl font-bold text-blue-600">MOH Learning Portal</Link>
                        <Link href="/" className="text-sm text-gray-600 hover:text-blue-600">Back to Home</Link>
                    </div>
                </nav>

                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">Terms and Conditions</h1>
                    <p className="text-sm text-gray-500 mb-8">Last updated: March 3, 2026</p>

                    <div className="prose prose-blue max-w-none bg-white rounded-lg shadow-sm p-8">
                        <h2>1. Agreement and Acceptance</h2>
                        <p>
                            By accessing and using the MOH Learning Platform (&quot;the Platform&quot;), operated by the Ministry of Health,
                            Trinidad and Tobago (&quot;the Ministry&quot;), you agree to be bound by these Terms and Conditions (&quot;Terms&quot;),
                            our <Link href="/privacy-policy" className="text-blue-600 hover:underline">Privacy Policy</Link>, and all applicable laws and regulations
                            of the Republic of Trinidad and Tobago.
                        </p>
                        <p>
                            The Ministry reserves the right to update or modify these Terms at any time. Changes will be posted on
                            this page with an updated revision date. Your continued use of the Platform after changes are posted
                            constitutes your acceptance of the revised Terms.
                        </p>

                        <h2>2. Eligibility</h2>
                        <p>
                            The Platform is intended for use by persons who are eighteen (18) years of age or older. By registering
                            for an account or using the Platform, you confirm that you are at least 18 years old.
                        </p>

                        <h2>3. Purpose of the Platform</h2>
                        <p>
                            The MOH Learning Platform provides access to educational and professional development courses for
                            healthcare professionals and other authorised users. The Platform is designed for learning and
                            training purposes only.
                        </p>

                        <h2>4. Medical Disclaimer</h2>
                        <div className="bg-yellow-50 border border-yellow-300 rounded-lg p-4 my-4">
                            <h3 className="text-yellow-800 mt-0 font-bold">Important Notice</h3>
                            <p>
                                The content on this Platform is provided for <strong>general educational and informational purposes only</strong>.
                                It does not constitute medical advice, diagnosis, or treatment.
                            </p>
                            <p><strong>Do not</strong> use information from this Platform as a substitute for professional medical advice.</p>
                            <p><strong>Do not delay</strong> seeking professional medical care because of content you have read on this Platform.</p>
                            <p className="mb-0"><strong>In an emergency, contact local emergency services immediately:</strong></p>
                            <ul className="mt-2">
                                <li>Ambulance: <strong>811</strong></li>
                                <li>Police: <strong>999</strong></li>
                                <li>Fire: <strong>990</strong></li>
                            </ul>
                        </div>

                        <h2>5. Permitted Use</h2>
                        <p>You are granted a limited, non-exclusive, non-transferable licence to:</p>
                        <ul>
                            <li>Access and use the Platform for personal, educational, and professional development purposes;</li>
                            <li>View, download, and print course materials for your own non-commercial use.</li>
                        </ul>

                        <h2>6. Prohibited Conduct</h2>
                        <p>You agree that you will not:</p>
                        <ul>
                            <li>Attempt to gain unauthorised access to any part of the Platform;</li>
                            <li>Use any automated means to access the Platform at levels that could harm service availability;</li>
                            <li>Introduce any viruses, malware, trojans, or other harmful code;</li>
                            <li>Impersonate any person or entity;</li>
                            <li>Conduct denial-of-service attacks or any other form of abuse;</li>
                            <li>Use the Platform in any way that violates the laws of Trinidad and Tobago.</li>
                        </ul>

                        <h2>7. Account Responsibilities</h2>
                        <p>
                            If you create an account, you are responsible for maintaining the confidentiality
                            of your login credentials and for all activities that occur under your account.
                        </p>

                        <h2>8. Suspension and Termination</h2>
                        <p>
                            The Ministry reserves the right to suspend or terminate your access to the Platform, without prior
                            notice, for any reason including breach of these Terms.
                        </p>

                        <h2>9. Intellectual Property</h2>
                        <p>
                            All content, logos, trademarks, and materials on the Platform are the property of the Ministry of
                            Health, Trinidad and Tobago, or their respective owners.
                        </p>

                        <h2>10. Third-Party Links and Content</h2>
                        <p>
                            The Platform may contain links to external websites. The Ministry does not control or assume
                            responsibility for the content of any third-party sites.
                        </p>

                        <h2>11. Accuracy and Availability</h2>
                        <p>
                            The Platform is provided on an <strong>&quot;AS IS&quot; and &quot;AS AVAILABLE&quot;</strong> basis. The Ministry
                            makes no warranties regarding uninterrupted access or error-free operation.
                        </p>

                        <h2>12. Limitation of Liability</h2>
                        <p>
                            To the maximum extent permitted by law, the Ministry shall not be liable for any indirect,
                            incidental, special, consequential, or punitive damages arising from your use of the Platform.
                        </p>

                        <h2>13. Governing Law and Dispute Resolution</h2>
                        <p>
                            These Terms are governed by the laws of the Republic of Trinidad and Tobago. Disputes shall be
                            subject to the exclusive jurisdiction of the courts of Trinidad and Tobago.
                        </p>

                        <h2>14. Electronic Transactions</h2>
                        <p>
                            In accordance with the Electronic Transactions Act (Chap. 22:05), electronic records and
                            communications issued through the Platform have legal effect.
                        </p>

                        <h2>15. Contact Information</h2>
                        <p>If you have questions about these Terms, please contact:</p>
                        <p>
                            Ministry of Health, Trinidad and Tobago<br />
                            Email: <a href="mailto:support@health.gov.tt" className="text-blue-600">support@health.gov.tt</a>
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
