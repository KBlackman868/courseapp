@extends('emails.layout')

@section('title', 'Enrollment Request Received - MOH Learning')
@section('header-subtitle', 'Enrollment Request Received')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Dear {{ $enrollment->user->first_name }},</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        We have received your enrollment request for the following course:
    </p>

    <!-- Course Details -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#EFF6FF;border-left:4px solid #3B82F6;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:16px;font-weight:600;color:#1F2937;margin:0 0 4px 0;">{{ $enrollment->course->title }}</p>
                <p style="font-size:13px;color:#1D4ED8;margin:4px 0 0 0;">Status: <strong>Pending Approval</strong></p>
                <p style="font-size:13px;color:#6B7280;margin:4px 0 0 0;">Requested: {{ $enrollment->created_at->format('F j, Y g:i A') }}</p>
            </td>
        </tr>
    </table>

    <!-- What Happens Next -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FFFBEB;border-left:4px solid #F59E0B;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#92400E;margin:0 0 8px 0;">What Happens Next?</p>
                <p style="font-size:13px;color:#A16207;margin:0;line-height:1.6;">
                    Your enrollment request will be reviewed by our administrators. You will receive an email notification once your enrollment is approved. Typical processing time: 1-2 business days.
                </p>
            </td>
        </tr>
    </table>

    <!-- Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color:#4F46E5;border-radius:8px;">
                            <a href="{{ config('app.url', 'https://mohlearn.hin.gov.tt') }}/mycourses" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                View My Courses
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        If you have questions about your enrollment, contact us at
        <a href="mailto:helpdesk@health.gov.tt" style="color:#4F46E5;text-decoration:none;font-weight:500;">helpdesk@health.gov.tt</a>.
    </p>
@endsection
