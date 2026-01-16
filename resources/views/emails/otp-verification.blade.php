<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Verification Code - {{ config('app.name', 'MOH LMS') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        :root {
            color-scheme: light;
            supported-color-schemes: light;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 20px;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
            padding: 32px 40px;
            text-align: center;
        }
        .header-icon {
            width: 64px;
            height: 64px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-top: 4px;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
        }
        .message {
            color: #4b5563;
            font-size: 15px;
            margin-bottom: 32px;
            line-height: 1.7;
        }
        .otp-box {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            border-radius: 12px;
            padding: 28px;
            text-align: center;
            margin-bottom: 24px;
        }
        .otp-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .otp-code {
            font-size: 38px;
            font-weight: 700;
            letter-spacing: 10px;
            color: #ffffff;
            font-family: 'SF Mono', SFMono-Regular, Consolas, 'Liberation Mono', Menlo, monospace;
            background-color: rgba(255, 255, 255, 0.15);
            padding: 12px 24px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 16px;
        }
        .expiry {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 20px;
        }
        .expiry svg {
            width: 16px;
            height: 16px;
        }
        .instruction {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .security-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .security-box h4 {
            color: #92400e;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .security-box ul {
            color: #a16207;
            font-size: 13px;
            margin: 0;
            padding-left: 20px;
        }
        .security-box li {
            margin-bottom: 6px;
        }
        .security-box li:last-child {
            margin-bottom: 0;
        }
        .help-text {
            color: #6b7280;
            font-size: 13px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .help-text a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .footer {
            background-color: #f9fafb;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-brand {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
            margin-bottom: 4px;
        }
        .footer-text {
            color: #9ca3af;
            font-size: 12px;
            margin: 4px 0;
        }
        .footer-divider {
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #1e40af, #3b82f6);
            margin: 16px auto;
            border-radius: 2px;
        }
        @media only screen and (max-width: 520px) {
            .wrapper {
                padding: 20px 12px;
            }
            .header {
                padding: 24px 20px;
            }
            .content {
                padding: 24px 20px;
            }
            .otp-code {
                font-size: 28px;
                letter-spacing: 6px;
            }
            .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center">
                            <div style="width: 64px; height: 64px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; margin: 0 auto 16px; line-height: 64px;">
                                <span style="font-size: 28px;">🔐</span>
                            </div>
                        </td>
                    </tr>
                </table>
                <h1>{{ config('app.name', 'Ministry of Health') }}</h1>
                <p>Learning Management System</p>
            </div>

            <div class="content">
                <p class="greeting">Hello {{ $user->first_name ?? 'there' }},</p>

                <p class="message">
                    @if(session('registration_pending') ?? false)
                        Thank you for registering! To complete your account setup, please enter the verification code below.
                    @else
                        You're attempting to sign in to your account. Please use the verification code below to complete your authentication.
                    @endif
                </p>

                <div class="otp-box">
                    <div class="otp-label">Your Verification Code</div>
                    <div class="otp-code">{{ $otpCode }}</div>
                    <div class="expiry">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Expires in {{ $expiryMinutes ?? 10 }} minutes
                    </div>
                </div>

                <p class="instruction">
                    Enter this code on the verification page to continue. Do not share this code with anyone.
                </p>

                <div class="security-box">
                    <h4>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Security Notice
                    </h4>
                    <ul>
                        <li>Never share this code with anyone</li>
                        <li>Our staff will never ask for this code</li>
                        <li>This code is valid for one-time use only</li>
                        <li>If you didn't request this, ignore this email</li>
                    </ul>
                </div>

                <p class="help-text">
                    Didn't request this code? You can safely ignore this email. If you're concerned about your account security, please contact our support team.
                </p>
            </div>

            <div class="footer">
                <div class="footer-divider"></div>
                <p class="footer-brand">{{ config('app.name', 'Ministry of Health Trinidad and Tobago') }}</p>
                <p class="footer-text">Learning Management System</p>
                <p class="footer-text" style="margin-top: 12px;">
                    This is an automated message. Please do not reply to this email.
                </p>
                <p class="footer-text">
                    &copy; {{ date('Y') }} All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
