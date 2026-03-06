@extends('emails.layout')

@section('title', 'Welcome - MOH Learning')
@section('header-subtitle', 'Welcome Aboard')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Hello {{ $user->first_name }} {{ $user->last_name }}!</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        Welcome to the Ministry of Health Learning Management System. We're excited to have you join our learning community.
    </p>

    <!-- Credentials Box -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#F0FDF4;border:1px solid #86EFAC;border-radius:12px;padding:20px;">
                <p style="font-size:14px;font-weight:600;color:#166534;text-transform:uppercase;letter-spacing:1px;margin:0 0 12px 0;">Your Login Credentials</p>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding:8px 0;border-bottom:1px dashed #BBF7D0;">
                            <span style="font-size:13px;font-weight:600;color:#166534;">Email:</span>
                            <span style="font-size:14px;color:#14532D;font-family:Consolas,'Liberation Mono',Menlo,monospace;padding-left:8px;">{{ $user->email }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;border-bottom:1px dashed #BBF7D0;">
                            <span style="font-size:13px;font-weight:600;color:#166534;">Password:</span>
                            <span style="font-size:14px;color:#14532D;font-family:Consolas,'Liberation Mono',Menlo,monospace;padding-left:8px;">{{ $password }}</span>
                        </td>
                    </tr>
                    @if($user->department)
                    <tr>
                        <td style="padding:8px 0;">
                            <span style="font-size:13px;font-weight:600;color:#166534;">Department:</span>
                            <span style="font-size:14px;color:#14532D;padding-left:8px;">{{ $user->department }}</span>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Login Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color:#4F46E5;border-radius:8px;">
                            <a href="{{ $loginUrl }}" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Login to Your Account
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Getting Started Steps -->
    <p style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px 0;">Getting Started</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="padding:8px 0;">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="width:28px;height:28px;background:linear-gradient(135deg,#4F46E5,#6366F1);color:#ffffff;font-size:13px;font-weight:700;border-radius:50%;text-align:center;vertical-align:middle;line-height:28px;">1</td>
                        <td style="padding-left:12px;">
                            <p style="font-size:14px;font-weight:600;color:#111827;margin:0;">Verify Your Email</p>
                            <p style="font-size:13px;color:#6B7280;margin:2px 0 0 0;">Enter the verification code sent to your email</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="width:28px;height:28px;background:linear-gradient(135deg,#4F46E5,#6366F1);color:#ffffff;font-size:13px;font-weight:700;border-radius:50%;text-align:center;vertical-align:middle;line-height:28px;">2</td>
                        <td style="padding-left:12px;">
                            <p style="font-size:14px;font-weight:600;color:#111827;margin:0;">Complete Your Profile</p>
                            <p style="font-size:13px;color:#6B7280;margin:2px 0 0 0;">Add your profile photo and additional information</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="width:28px;height:28px;background:linear-gradient(135deg,#4F46E5,#6366F1);color:#ffffff;font-size:13px;font-weight:700;border-radius:50%;text-align:center;vertical-align:middle;line-height:28px;">3</td>
                        <td style="padding-left:12px;">
                            <p style="font-size:14px;font-weight:600;color:#111827;margin:0;">Browse & Enroll</p>
                            <p style="font-size:13px;color:#6B7280;margin:2px 0 0 0;">Explore courses and begin your learning journey</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Security Warning -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FFFBEB;border-left:4px solid #F59E0B;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#92400E;margin:0 0 8px 0;">Security Reminder</p>
                <p style="font-size:13px;color:#A16207;margin:0;line-height:1.6;">
                    Change your password after your first login. Keep your credentials secure and never share your login details with anyone.
                </p>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        Need help? Contact our support team at
        <a href="mailto:helpdesk@health.gov.tt" style="color:#4F46E5;text-decoration:none;font-weight:500;">helpdesk@health.gov.tt</a>.
    </p>
@endsection
