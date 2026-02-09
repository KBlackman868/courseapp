@extends('components.layouts')

@section('title', 'Privacy Policy')

@section('content')
<div class="prose max-w-none">
    <p class="text-sm text-gray-500">Last updated: {{ date('F j, Y') }}</p>

    <p>
        The Ministry of Health, Trinidad and Tobago ("the Ministry", "we", "us") is committed to
        protecting the privacy and personal information of users of the MOH Learning Platform
        ("the Platform"). This Privacy Policy explains how we collect, use, store, disclose, and
        protect your information in accordance with the Data Protection Act (Chap. 22:04) of Trinidad
        and Tobago ("the DPA") and its General Privacy Principles.
    </p>

    <h2>1. Accountability</h2>
    <p>
        The Ministry of Health is the data controller responsible for the personal information
        collected through the Platform. Questions, concerns, or complaints about how your personal
        information is handled should be directed to:
    </p>
    <p>
        Ministry of Health, Trinidad and Tobago<br>
        Email: <a href="mailto:support@health.gov.tt">support@health.gov.tt</a>
    </p>

    <h2>2. Information We Collect</h2>

    <h3>2.1 Personal Data You Provide</h3>
    <p>When you register for an account or use the Platform, we may collect:</p>
    <ul>
        <li>First name and last name</li>
        <li>Email address</li>
        <li>Department and organisation</li>
        <li>Phone number (optional)</li>
        <li>Date of birth (to verify age eligibility)</li>
        <li>Password (stored in hashed form only)</li>
        <li>Profile photograph (if uploaded)</li>
    </ul>

    <h3>2.2 Usage Data Collected Automatically</h3>
    <p>When you use the Platform, we automatically collect:</p>
    <ul>
        <li>IP address</li>
        <li>Browser type and version (user agent)</li>
        <li>Pages visited and actions performed</li>
        <li>Date and time of access</li>
        <li>Referring URL</li>
    </ul>

    <h3>2.3 Cookies and Session Data</h3>
    <p>
        The Platform uses essential cookies for session management and security (CSRF protection).
        These cookies are strictly necessary for the Platform to function and do not track your
        browsing activity across other websites. For more details, see
        <a href="#cookies">Section 9: Cookies</a>.
    </p>

    <h2>3. Purpose of Collection</h2>
    <p>We collect and use your personal information for the following purposes:</p>
    <ul>
        <li><strong>Account Management:</strong> To create, maintain, and authenticate your account;</li>
        <li><strong>Course Delivery:</strong> To enrol you in courses and track your learning progress;</li>
        <li><strong>Communication:</strong> To send you system notifications, verification codes, and
            account-related communications;</li>
        <li><strong>Security:</strong> To monitor for unauthorised access, detect abuse, and maintain
            the integrity of the Platform;</li>
        <li><strong>Audit and Compliance:</strong> To maintain activity logs for accountability and
            regulatory compliance;</li>
        <li><strong>Platform Improvement:</strong> To understand usage patterns and improve the Platform.</li>
    </ul>
    <p>
        We will not use your personal information for purposes beyond those stated above without
        obtaining your consent or as otherwise permitted by law.
    </p>

    <h2>4. Consent</h2>
    <p>
        By registering for and using the Platform, you consent to the collection and use of your
        personal information as described in this Privacy Policy. Where we require your information
        for purposes not covered by this Policy, we will seek your explicit consent before proceeding.
    </p>
    <p>
        You may withdraw your consent at any time by contacting us or by deleting your account, subject
        to any legal obligations that require us to retain certain information.
    </p>

    <h2>5. Data Minimisation</h2>
    <p>
        We collect only the personal information that is necessary for the stated purposes. We do not
        collect sensitive personal information about your physical or mental health or condition through
        this Platform. The Platform is designed for educational course delivery and does not require
        health-condition data from users.
    </p>

    <h2>6. Data Retention</h2>
    <p>
        We retain your personal information only for as long as necessary to fulfil the purposes for
        which it was collected, or as required by law:
    </p>
    <ul>
        <li><strong>Account Data:</strong> Retained for the duration of your active account and for a
            reasonable period after account closure to comply with legal and audit requirements;</li>
        <li><strong>Activity Logs:</strong> Retained for ninety (90) days, after which they are
            automatically deleted. Logs required for ongoing investigations or legal proceedings may
            be retained longer as necessary;</li>
        <li><strong>Course Completion Records:</strong> Retained for the duration required by
            professional development and certification obligations.</li>
    </ul>

    <h2>7. Accuracy</h2>
    <p>
        We take reasonable steps to ensure the personal information we hold is accurate, complete, and
        up to date. You can update your personal information at any time through your
        <a href="{{ route('profile.show') }}">Profile</a> page. If you believe any information we hold
        about you is inaccurate, please contact us and we will correct it promptly.
    </p>

    <h2>8. Safeguards</h2>
    <p>We implement security measures to protect your personal information, including:</p>
    <ul>
        <li>Passwords are hashed using industry-standard algorithms (bcrypt) and are never stored in
            plain text;</li>
        <li>Session cookies are marked HTTP-only and use the SameSite attribute to prevent cross-site
            request forgery;</li>
        <li>CSRF tokens protect all state-changing requests;</li>
        <li>Rate limiting is applied to authentication endpoints to prevent brute-force attacks;</li>
        <li>Role-based access control restricts data access to authorised personnel only;</li>
        <li>Activity logging provides an audit trail of all significant actions.</li>
    </ul>
    <p>
        While we implement reasonable safeguards, no method of electronic storage or transmission is
        100% secure. We cannot guarantee absolute security but are committed to protecting your
        information using commercially reasonable measures.
    </p>

    <h2 id="cookies">9. Cookies</h2>
    <p>The Platform uses the following cookies:</p>
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Cookie</th>
                    <th>Type</th>
                    <th>Purpose</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Session Cookie</td>
                    <td>Essential / First-party</td>
                    <td>Maintains your login session</td>
                    <td>120 minutes</td>
                </tr>
                <tr>
                    <td>XSRF-TOKEN</td>
                    <td>Essential / First-party</td>
                    <td>Prevents cross-site request forgery attacks</td>
                    <td>120 minutes</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p>
        All cookies used by the Platform are essential for its operation. We do not use analytics,
        advertising, or social media tracking cookies. You can configure your browser to block cookies,
        but doing so may prevent the Platform from functioning correctly.
    </p>

    <h2>10. Transparency</h2>
    <p>
        This Privacy Policy is publicly available and accessible from every page of the Platform.
        We are committed to being open about our data practices. If we make material changes to this
        Policy, we will post a notice on the Platform and update the "Last updated" date above.
    </p>

    <h2>11. Third-Party Services and Cross-Border Disclosure</h2>
    <p>
        The Platform integrates with the following third-party services that may process your
        personal information:
    </p>
    <ul>
        <li><strong>Moodle LMS:</strong> Your name, email, and enrolment data are shared with our
            Moodle learning management system to deliver course content. This system is hosted within
            the Government's network infrastructure;</li>
        <li><strong>Bugsnag (Error Monitoring):</strong> Technical error data may be sent to Bugsnag
            for application monitoring. This service is hosted outside Trinidad and Tobago. We take
            steps to minimise personal information in error reports;</li>
        <li><strong>Email Service:</strong> Your email address is used to send verification codes,
            welcome messages, and system notifications through our email service provider.</li>
    </ul>
    <p>
        Where personal information is disclosed to service providers outside Trinidad and Tobago, we
        ensure that comparable safeguards are in place to protect your information, in accordance with
        the DPA's General Privacy Principles regarding cross-border disclosure.
    </p>

    <h2>12. Your Rights</h2>
    <p>In accordance with the DPA, you have the right to:</p>
    <ul>
        <li><strong>Access:</strong> Request information about what personal data we hold about you
            and how it is used;</li>
        <li><strong>Correction:</strong> Request that we correct inaccurate or incomplete personal
            information;</li>
        <li><strong>Challenge Compliance:</strong> Raise concerns about our compliance with this
            Policy and the DPA's General Privacy Principles.</li>
    </ul>
    <p>
        To exercise these rights, please contact us at
        <a href="mailto:support@health.gov.tt">support@health.gov.tt</a>.
        We will respond to your request within a reasonable time.
    </p>

    <h2>13. Children's Privacy</h2>
    <p>
        The Platform is not intended for use by persons under the age of eighteen (18). We do not
        knowingly collect personal information from persons under 18. If we become aware that we have
        collected personal information from a person under 18, we will take steps to delete that
        information promptly. If you believe a person under 18 has provided us with personal
        information, please contact us immediately.
    </p>

    <h2>14. Complaints</h2>
    <p>
        If you believe your privacy rights have been violated or you have a complaint about how your
        personal information has been handled, please contact us at
        <a href="mailto:support@health.gov.tt">support@health.gov.tt</a>.
        We will investigate your complaint and respond within a reasonable time. If you are not
        satisfied with our response, you may escalate your complaint to the relevant regulatory
        authority in Trinidad and Tobago.
    </p>

    <h2>15. Changes to This Policy</h2>
    <p>
        We may update this Privacy Policy from time to time to reflect changes in our practices or
        applicable laws. We will post the updated Policy on this page with a revised "Last updated"
        date. We encourage you to review this Policy periodically. Your continued use of the Platform
        after changes are posted constitutes your acceptance of the updated Policy.
    </p>

    <h2>16. Contact Information</h2>
    <p>If you have questions or concerns about this Privacy Policy, please contact:</p>
    <p>
        Ministry of Health, Trinidad and Tobago<br>
        Email: <a href="mailto:support@health.gov.tt">support@health.gov.tt</a>
    </p>
</div>
@endsection
