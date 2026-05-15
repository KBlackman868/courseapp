@extends('emails.layout')

@section('title', 'New Course Access Request - MOH Learning')
@section('header-subtitle', 'Course Access Request Awaiting Approval')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">New Course Access Request</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        A user has requested access to a course and is awaiting your approval.
    </p>

    <!-- Requester Details -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">
        <tr>
            <td style="background-color:#EFF6FF;border-left:4px solid #3B82F6;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#1D4ED8;margin:0 0 8px 0;">Requester Details</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Email:</strong> {{ $user->email }}</p>
                @if($user->department)
                <p style="font-size:13px;color:#1F2937;margin:0;"><strong>Department:</strong> {{ $user->department }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Course Information -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">
        <tr>
            <td style="background-color:#F0FDF4;border-left:4px solid #22C55E;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#166534;margin:0 0 8px 0;">Course Information</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Course:</strong> {{ $course->title }}</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Requested:</strong> {{ $courseAccessRequest->created_at->format('F j, Y g:i A') }}</p>
                @if($courseAccessRequest->request_reason)
                <p style="font-size:13px;color:#1F2937;margin:0;"><strong>Reason:</strong> {{ $courseAccessRequest->request_reason }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Action Required -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FEF3C7;border-left:4px solid #F59E0B;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#92400E;margin:0 0 4px 0;">Action Required</p>
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
                                Review Course Access Request
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
