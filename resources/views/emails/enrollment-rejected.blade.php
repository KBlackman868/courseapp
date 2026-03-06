@extends('emails.layout')

@section('title', 'Enrollment Update - MOH Learning')
@section('header-subtitle', 'Enrollment Update')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Dear {{ $user->first_name }},</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        We regret to inform you that your enrollment request for the following course has not been approved at this time.
    </p>

    <!-- Course Details -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FEF2F2;border-left:4px solid #EF4444;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:16px;font-weight:600;color:#1F2937;margin:0 0 4px 0;">{{ $course->title }}</p>
                @if($reason)
                <p style="font-size:13px;color:#B91C1C;margin:8px 0 0 0;"><strong>Reason:</strong> {{ $reason }}</p>
                @endif
            </td>
        </tr>
    </table>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        You may browse other available courses on the platform or contact us for more information.
    </p>

    <!-- Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color:#4F46E5;border-radius:8px;">
                            <a href="{{ config('app.url', 'https://mohlearn.hin.gov.tt') }}/catalog" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Browse Courses
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        For assistance, contact us at
        <a href="mailto:helpdesk@health.gov.tt" style="color:#4F46E5;text-decoration:none;font-weight:500;">helpdesk@health.gov.tt</a>.
    </p>
@endsection
