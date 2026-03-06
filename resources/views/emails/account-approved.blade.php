@extends('emails.layout')

@section('title', 'Account Approved - MOH Learning')
@section('header-subtitle', 'Account Approved')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Dear {{ $user->first_name }} {{ $user->last_name }},</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        Congratulations! Your account has been approved by the <strong>Ministry of Health, Trinidad and Tobago</strong>.
    </p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        You can now access the MOH Learning platform to browse courses, enroll in training programmes, and track your professional development.
    </p>

    <!-- Login Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color:#4F46E5;border-radius:8px;">
                            <a href="{{ $loginUrl }}" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Log In to MOH Learning
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0 0 24px 0;">
        Log in with the <strong>email and password</strong> you provided during registration.
    </p>

    <!-- Security Warning -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FFFBEB;border-left:4px solid #F59E0B;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#92400E;margin:0 0 8px 0;">Security Reminder</p>
                <p style="font-size:13px;color:#A16207;margin:0;line-height:1.6;">
                    Keep your credentials safe. Do not share your login details with anyone. You are responsible for all activity on your account.
                </p>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        If you have any issues logging in, please contact us at
        <a href="mailto:helpdesk@health.gov.tt" style="color:#4F46E5;text-decoration:none;font-weight:500;">helpdesk@health.gov.tt</a>.
    </p>
@endsection
