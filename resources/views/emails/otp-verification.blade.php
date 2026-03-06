@extends('emails.layout')

@section('title', 'Verification Code - MOH Learning')
@section('header-subtitle', 'Email Verification')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Hello {{ $user->first_name ?? 'there' }},</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        Please use the verification code below to complete your authentication.
    </p>

    <!-- OTP Code Box -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#4F46E5 0%,#6366F1 100%);border-radius:12px;padding:28px;text-align:center;">
                            <p style="color:rgba(255,255,255,0.9);font-size:12px;text-transform:uppercase;letter-spacing:2px;font-weight:600;margin:0 0 12px 0;">Your Verification Code</p>
                            <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 16px auto;">
                                <tr>
                                    <td style="background-color:rgba(255,255,255,0.15);border-radius:8px;padding:12px 24px;">
                                        <span style="font-size:36px;font-weight:700;letter-spacing:10px;color:#ffffff;font-family:Consolas,'Liberation Mono',Menlo,monospace;">{{ $otpCode }}</span>
                                    </td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                                <tr>
                                    <td style="background-color:rgba(255,255,255,0.2);border-radius:20px;padding:8px 16px;">
                                        <span style="color:#ffffff;font-size:13px;font-weight:500;">Expires in {{ $expiryMinutes ?? 10 }} minutes</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;margin:0 0 24px 0;">
        Enter this code on the verification page to continue. Do not share this code with anyone.
    </p>

    <!-- Security Notice -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FFFBEB;border-left:4px solid #F59E0B;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#92400E;margin:0 0 8px 0;">Security Notice</p>
                <p style="font-size:13px;color:#A16207;margin:0;line-height:1.8;">
                    &bull; Never share this code with anyone<br>
                    &bull; Our staff will never ask for this code<br>
                    &bull; This code is valid for one-time use only<br>
                    &bull; If you didn't request this, ignore this email
                </p>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        Didn't request this code? You can safely ignore this email. For account security concerns, contact
        <a href="mailto:helpdesk@health.gov.tt" style="color:#4F46E5;text-decoration:none;font-weight:500;">helpdesk@health.gov.tt</a>.
    </p>
@endsection
