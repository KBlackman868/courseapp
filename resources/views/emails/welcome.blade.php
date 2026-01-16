<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Welcome - {{ config('app.name', 'MOH LMS') }}</title>
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
        }
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 20px;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
            padding: 32px 40px;
            text-align: center;
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
        .welcome-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 6px 16px;
            border-radius: 20px;
            margin-bottom: 12px;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }
        .subgreeting {
            color: #059669;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 24px;
        }
        .message {
            color: #4b5563;
            font-size: 15px;
            margin-bottom: 28px;
            line-height: 1.7;
        }
        .credentials-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
        }
        .credentials-title {
            color: #166534;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .credential-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #bbf7d0;
        }
        .credential-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .credential-label {
            color: #166534;
            font-size: 13px;
            font-weight: 600;
            width: 100px;
            flex-shrink: 0;
        }
        .credential-value {
            color: #14532d;
            font-size: 14px;
            font-family: 'SF Mono', SFMono-Regular, Consolas, 'Liberation Mono', Menlo, monospace;
            word-break: break-all;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 28px;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.4);
        }
        .btn:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
        }
        .steps-section {
            margin-bottom: 28px;
        }
        .steps-title {
            color: #111827;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }
        .step-number {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #059669, #10b981);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .step-content {
            padding-top: 4px;
        }
        .step-title {
            color: #111827;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .step-desc {
            color: #6b7280;
            font-size: 13px;
        }
        .security-notice {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .security-notice h4 {
            color: #92400e;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .security-notice ul {
            color: #a16207;
            font-size: 13px;
            margin: 0;
            padding-left: 20px;
        }
        .security-notice li {
            margin-bottom: 4px;
        }
        .help-text {
            color: #6b7280;
            font-size: 13px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .footer {
            background-color: #f9fafb;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-divider {
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #059669, #10b981);
            margin: 0 auto 16px;
            border-radius: 2px;
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
        .footer-link {
            color: #059669;
            text-decoration: none;
            font-size: 12px;
        }
        @media only screen and (max-width: 560px) {
            .wrapper {
                padding: 20px 12px;
            }
            .header, .content, .footer {
                padding: 24px 20px;
            }
            .credential-row {
                flex-direction: column;
            }
            .credential-label {
                width: 100%;
                margin-bottom: 4px;
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
                                <span style="font-size: 28px;">🎉</span>
                            </div>
                        </td>
                    </tr>
                </table>
                <span class="welcome-badge">Welcome Aboard</span>
                <h1>{{ config('app.name', 'Ministry of Health') }}</h1>
                <p>Learning Management System</p>
            </div>

            <div class="content">
                <p class="greeting">Hello {{ $user->first_name }} {{ $user->last_name }}!</p>
                <p class="subgreeting">Your account has been successfully created</p>

                <p class="message">
                    Welcome to the Ministry of Health Learning Management System. We're excited to have you join our learning community. Below are your account credentials to get started.
                </p>

                <div class="credentials-box">
                    <div class="credentials-title">
                        <span style="font-size: 16px;">🔑</span>
                        Your Login Credentials
                    </div>
                    <div class="credential-row">
                        <span class="credential-label">Email:</span>
                        <span class="credential-value">{{ $user->email }}</span>
                    </div>
                    <div class="credential-row">
                        <span class="credential-label">Password:</span>
                        <span class="credential-value">{{ $password }}</span>
                    </div>
                    @if($user->department)
                    <div class="credential-row">
                        <span class="credential-label">Department:</span>
                        <span class="credential-value">{{ $user->department }}</span>
                    </div>
                    @endif
                </div>

                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center">
                            <a href="{{ $loginUrl }}" class="btn" style="color: #ffffff;">
                                Login to Your Account
                            </a>
                        </td>
                    </tr>
                </table>

                <div class="steps-section">
                    <p class="steps-title">Getting Started</p>

                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <p class="step-title">Verify Your Email</p>
                            <p class="step-desc">Enter the verification code sent to your email</p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <p class="step-title">Complete Your Profile</p>
                            <p class="step-desc">Add your profile photo and additional information</p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <p class="step-title">Browse Courses</p>
                            <p class="step-desc">Explore available training modules and programs</p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <p class="step-title">Start Learning</p>
                            <p class="step-desc">Enroll in courses and begin your learning journey</p>
                        </div>
                    </div>
                </div>

                <div class="security-notice">
                    <h4>🔒 Security Reminder</h4>
                    <ul>
                        <li>Change your password after your first login</li>
                        <li>Keep your credentials secure and private</li>
                        <li>Never share your login details with anyone</li>
                    </ul>
                </div>

                <p class="help-text">
                    Need help? Contact our support team if you have any questions or need assistance getting started.
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
                <p style="margin-top: 12px;">
                    <a href="{{ $loginUrl }}" class="footer-link">{{ $loginUrl }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
