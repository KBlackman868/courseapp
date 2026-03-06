@extends('emails.layout')

@section('title', 'Moodle Account Created - MOH Learning')
@section('header-subtitle', 'Moodle Account Created')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Dear {{ $user->first_name }},</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        Your Moodle Learning Management System account has been successfully created!
    </p>

    <!-- Account Details -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#F0FDF4;border-left:4px solid #22C55E;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#166534;margin:0 0 8px 0;">Your Account Details</p>
                <p style="font-size:13px;color:#1F2937;margin:0 0 4px 0;"><strong>Username:</strong> {{ $user->email }}</p>
                <p style="font-size:13px;color:#1F2937;margin:0;"><strong>Moodle User ID:</strong> {{ $user->moodle_user_id }}</p>
            </td>
        </tr>
    </table>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        You can now access all your enrolled courses through the Moodle platform.
    </p>

    <!-- Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color:#4F46E5;border-radius:8px;">
                            <a href="{{ $moodleUrl }}" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Access Moodle LMS
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        If you have trouble accessing your account, contact us at
        <a href="mailto:helpdesk@health.gov.tt" style="color:#4F46E5;text-decoration:none;font-weight:500;">helpdesk@health.gov.tt</a>.
    </p>
@endsection
