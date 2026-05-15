@extends('emails.layout')

@section('title', 'New Account Request - MOH Learning')
@section('header-subtitle', 'Account Request Awaiting Approval')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">New Account Request</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        A new account request has been submitted and is awaiting your approval.
    </p>

    <!-- Applicant Details -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">
        <tr>
            <td style="background-color:#EFF6FF;border-left:4px solid #3B82F6;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#1D4ED8;margin:0 0 8px 0;">Applicant Details</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Name:</strong> {{ $accountRequest->first_name }} {{ $accountRequest->last_name }}</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Email:</strong> {{ $accountRequest->email }}</p>
                @if($accountRequest->department)
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Department:</strong> {{ $accountRequest->department }}</p>
                @endif
                @if($accountRequest->organization)
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Organization:</strong> {{ $accountRequest->organization }}</p>
                @endif
                <p style="font-size:13px;color:#1F2937;margin:0;"><strong>Type:</strong> {{ $accountRequest->request_type === 'moh_staff' ? 'MOH Staff' : 'External User' }}</p>
            </td>
        </tr>
    </table>

    <!-- Request Info -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FEF3C7;border-left:4px solid #F59E0B;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#92400E;margin:0 0 8px 0;">Action Required</p>
                <p style="font-size:13px;color:#78350F;margin:0 0 4px 0;">Submitted: {{ $accountRequest->created_at->format('F j, Y g:i A') }}</p>
                <p style="font-size:13px;color:#78350F;margin:0;">Please review this request and approve or reject it at your earliest convenience.</p>
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
                            <a href="{{ $reviewUrl }}" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Review Account Request
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        You are receiving this email because you are an administrator on MOH Learning.
    </p>
@endsection
