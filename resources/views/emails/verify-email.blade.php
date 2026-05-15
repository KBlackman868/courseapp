@extends('emails.layout')

@section('title', 'Verify Your Email — MOH Learning')
@section('header-subtitle', 'Email Verification')

@section('content')
    <p style="font-size:18px;font-weight:600;color:#111827;margin:0 0 16px 0;">Dear {{ $accountRequest->first_name }},</p>

    <p style="font-size:16px;color:#4B5563;line-height:1.6;margin:0 0 24px 0;">
        Thank you for registering with MOH Learning. Please click the button below to verify your email address.
    </p>

    <!-- Verify Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color:#4F46E5;border-radius:8px;">
                            <a href="{{ $verificationUrl }}" target="_blank" style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                Verify Email Address
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Expiry Warning -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#FEF3C7;border-left:4px solid #F59E0B;border-radius:0 8px 8px 0;padding:16px 20px;">
                <p style="font-size:14px;font-weight:600;color:#92400E;margin:0 0 8px 0;">Link Expires in 24 Hours</p>
                <p style="font-size:13px;color:#78350F;margin:0;line-height:1.6;">
                    If the button above doesn't work, copy and paste this URL into your browser:<br>
                    <a href="{{ $verificationUrl }}" style="word-break:break-all;color:#4F46E5;text-decoration:none;">{{ $verificationUrl }}</a>
                </p>
            </td>
        </tr>
    </table>

    <p style="font-size:14px;color:#6B7280;line-height:1.6;margin:0;padding-top:16px;border-top:1px solid #E5E7EB;">
        If you did not create an account, please ignore this email.
    </p>
@endsection
