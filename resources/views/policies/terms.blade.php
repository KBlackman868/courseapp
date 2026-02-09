@extends('components.layouts')

@section('title', 'Terms and Conditions')

@section('content')
<div class="prose max-w-none">
    <p class="text-sm text-gray-500">Last updated: {{ date('F j, Y') }}</p>

    <h2>1. Agreement and Acceptance</h2>
    <p>
        By accessing and using the MOH Learning Platform ("the Platform"), operated by the Ministry of Health,
        Trinidad and Tobago ("the Ministry"), you agree to be bound by these Terms and Conditions ("Terms"),
        our <a href="{{ route('privacy-policy') }}">Privacy Policy</a>, and all applicable laws and regulations
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
        for an account or using the Platform, you confirm that you are at least 18 years old. Persons under 18
        are not permitted to register for or use this Platform.
    </p>

    <h2>3. Purpose of the Platform</h2>
    <p>
        The MOH Learning Platform provides access to educational and professional development courses for
        healthcare professionals and other authorised users. The Platform is designed for learning and
        training purposes only.
    </p>

    <h2>4. Medical Disclaimer</h2>
    <div class="bg-warning/10 border border-warning rounded-lg p-4 my-4">
        <h3 class="text-warning mt-0">Important Notice</h3>
        <p>
            The content on this Platform is provided for <strong>general educational and informational purposes only</strong>.
            It does not constitute medical advice, diagnosis, or treatment, and does not create a clinician-patient
            or doctor-patient relationship between you and the Ministry of Health or any of its employees.
        </p>
        <p>
            <strong>Do not</strong> use information from this Platform as a substitute for professional medical advice.
            Always seek the advice of a qualified healthcare provider with any questions you may have regarding a
            medical condition.
        </p>
        <p>
            <strong>Do not delay</strong> seeking professional medical care because of content you have read or
            accessed on this Platform.
        </p>
        <p class="mb-0">
            <strong>In an emergency, contact local emergency services immediately:</strong>
        </p>
        <ul class="mt-2">
            <li>Ambulance: <strong>811</strong></li>
            <li>Police: <strong>999</strong></li>
            <li>Fire: <strong>990</strong></li>
        </ul>
    </div>

    <h2>5. Permitted Use</h2>
    <p>You are granted a limited, non-exclusive, non-transferable licence to:</p>
    <ul>
        <li>Access and use the Platform for personal, educational, and professional development purposes;</li>
        <li>View, download, and print course materials for your own non-commercial use, provided you retain
            all copyright and proprietary notices.</li>
    </ul>

    <h2>6. Prohibited Conduct</h2>
    <p>You agree that you will not:</p>
    <ul>
        <li>Attempt to gain unauthorised access to any part of the Platform, other users' accounts, or
            computer systems or networks connected to the Platform;</li>
        <li>Use any automated means, including bots, scrapers, or crawlers, to access the Platform at
            levels that could harm service availability;</li>
        <li>Introduce any viruses, malware, trojans, or other harmful code;</li>
        <li>Impersonate any person or entity, including Ministry officials or health authorities;</li>
        <li>Conduct denial-of-service attacks, credential stuffing, or any other form of abuse;</li>
        <li>Upload personal data about another person without proper authority;</li>
        <li>Use the Platform in any way that violates the laws of Trinidad and Tobago, including the
            Computer Misuse Act (Chap. 11:17);</li>
        <li>Reproduce, distribute, modify, or create derivative works from Platform content without
            prior written authorisation from the Ministry.</li>
    </ul>

    <h2>7. Account Responsibilities</h2>
    <p>
        If you create an account on the Platform, you are responsible for maintaining the confidentiality
        of your login credentials and for all activities that occur under your account. You agree to
        notify the Ministry immediately of any unauthorised use of your account.
    </p>
    <p>
        Passwords must meet the Platform's minimum security requirements, including a minimum of twelve (12)
        characters for standard accounts and fourteen (14) characters for high-risk administrative accounts.
    </p>

    <h2>8. Suspension and Termination</h2>
    <p>
        The Ministry reserves the right to suspend or terminate your access to the Platform, without prior
        notice, for any reason, including but not limited to a breach of these Terms, suspected unauthorised
        use, or conduct that the Ministry reasonably considers harmful to other users or the integrity of
        the Platform.
    </p>

    <h2>9. Intellectual Property</h2>
    <p>
        All content, logos, trademarks, and materials on the Platform are the property of the Ministry of
        Health, Trinidad and Tobago, or their respective owners. Content sourced from international
        organisations (such as the World Health Organization) is subject to the reuse and attribution
        requirements of those organisations.
    </p>
    <p>
        You may not use the Ministry's name, logo, or branding for endorsement purposes without prior
        written authorisation.
    </p>

    <h2>10. Third-Party Links and Content</h2>
    <p>
        The Platform may contain links to external websites, resources, or services operated by third
        parties. The Ministry does not control, endorse, or assume responsibility for the content,
        privacy practices, or availability of any third-party sites. Accessing third-party links is at
        your own risk.
    </p>

    <h2>11. Accuracy and Availability</h2>
    <p>
        The Ministry endeavours to ensure that the content on the Platform is accurate and up to date.
        However, public health information changes rapidly, and the Ministry does not warrant that
        content is free from errors or omissions. Content is provided on an "as correct at time of
        publication" basis.
    </p>
    <p>
        The Platform is provided on an <strong>"AS IS" and "AS AVAILABLE"</strong> basis. The Ministry
        makes no warranties, whether express or implied, regarding uninterrupted access, error-free
        operation, or that the Platform is free from viruses or other harmful components.
    </p>

    <h2>12. Limitation of Liability</h2>
    <p>
        To the maximum extent permitted by the laws of Trinidad and Tobago, the Ministry, its officers,
        employees, and agents shall not be liable for any indirect, incidental, special, consequential,
        or punitive damages, including but not limited to loss of data, loss of revenue, business
        interruption, or personal injury arising from:
    </p>
    <ul>
        <li>Your use of, or inability to use, the Platform;</li>
        <li>Any errors, omissions, or inaccuracies in content;</li>
        <li>Any unauthorised access to or alteration of your data;</li>
        <li>Any third-party content or conduct on the Platform.</li>
    </ul>

    <h2>13. Governing Law and Dispute Resolution</h2>
    <p>
        These Terms are governed by and construed in accordance with the laws of the Republic of Trinidad
        and Tobago. Any disputes arising under or in connection with these Terms shall first be raised
        through the contact channel provided below. If informal resolution is not achieved, disputes
        shall be subject to the exclusive jurisdiction of the courts of Trinidad and Tobago.
    </p>

    <h2>14. Electronic Transactions</h2>
    <p>
        In accordance with the Electronic Transactions Act (Chap. 22:05) of Trinidad and Tobago,
        electronic records, notices, and communications issued through the Platform have legal effect.
        Your click-through acceptance of these Terms, account registration, and any acknowledgements
        provided through the Platform constitute valid electronic transactions.
    </p>

    <h2>15. Contact Information</h2>
    <p>If you have questions about these Terms, please contact:</p>
    <p>
        Ministry of Health, Trinidad and Tobago<br>
        Email: <a href="mailto:support@health.gov.tt">support@health.gov.tt</a>
    </p>
</div>
@endsection
