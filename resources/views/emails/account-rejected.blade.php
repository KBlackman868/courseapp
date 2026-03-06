@extends('emails.layout')

@section('title', 'Account Request Update - MOH Learning')
@section('header-subtitle', 'Account Request Update')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Dear {{ $accountRequest->first_name }} {{ $accountRequest->last_name }},</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        Thank you for your interest in the MOH Learning platform. After review, we are unable to approve your account request at this time.
    </p>

    @if($reason)
    <!-- Reason Box -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FEF2F2;border-left:4px solid #EF4444;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#991B1B;margin:0 0 8px 0;">Reason</p>
                <p style="font-size:14px;color:#B91C1C;margin:0;line-height:1.6;">{{ $reason }}</p>
            </td>
        </tr>
    </table>
    @endif

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        If you believe this was an error, or if you would like to resubmit your request with additional information, please contact us.
    </p>

    <!-- Contact Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color:#6B7280;border-radius:8px;">
                            <a href="mailto:helpdesk@health.gov.tt" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Contact Support
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        For assistance, email us at
        <a href="mailto:helpdesk@health.gov.tt" style="color:#4F46E5;text-decoration:none;font-weight:500;">helpdesk@health.gov.tt</a>.
    </p>
@endsection
