@extends('emails.layout')

@section('title', 'New Enrollment Request - MOH Learning')
@section('header-subtitle', 'New Enrollment Request')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">New Enrollment Request</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        A new enrollment request has been submitted and requires your approval.
    </p>

    <!-- Student Details -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">
        <tr>
            <td style="background-color:#EFF6FF;border-left:4px solid #3B82F6;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#1D4ED8;margin:0 0 8px 0;">Student Details</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Email:</strong> {{ $user->email }}</p>
                @if($user->department)
                <p style="font-size:13px;color:#1F2937;margin:0;"><strong>Department:</strong> {{ $user->department }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Course Information -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#F0FDF4;border-left:4px solid #22C55E;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#166534;margin:0 0 8px 0;">Course Information</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Course:</strong> {{ $course->title }}</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Requested:</strong> {{ $enrollment->created_at->format('F j, Y g:i A') }}</p>
                <p style="font-size:13px;color:#1F2937;margin:0;"><strong>Status:</strong> Pending Approval</p>
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
                            <a href="{{ $approvalUrl }}" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Review Enrollment Request
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        Please review and approve or deny this enrollment request at your earliest convenience.
    </p>
@endsection
