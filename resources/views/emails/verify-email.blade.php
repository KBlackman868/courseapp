<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Verify Your Email — MOH Learning</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 8px 0 0; }
        .body { padding: 40px; }
        .body p { color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #ffffff !important; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; }
        .note { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #92400e; margin: 24px 0; }
        .footer { padding: 24px 40px; background: #f9fafb; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
        .footer a { color: #6366f1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>MOH Learning</h1>
                <p>Ministry of Health Trinidad and Tobago</p>
            </div>

            <div class="body">
                <p>Dear {{ $accountRequest->first_name }},</p>

                <p>Thank you for registering with MOH Learning. Please click the button below to verify your email address.</p>

                <div class="btn-wrap">
                    <a href="{{ $verificationUrl }}" class="btn">Verify Email Address</a>
                </div>

                <div class="note">
                    This link will expire in 24 hours. If the button doesn't work, copy and paste this URL into your browser:<br>
                    <a href="{{ $verificationUrl }}" style="word-break: break-all; color: #6366f1;">{{ $verificationUrl }}</a>
                </div>

                <p>If you did not create an account, please ignore this email.</p>
            </div>

            <div class="footer">
                <p><a href="mailto:helpdesk@health.gov.tt">helpdesk@health.gov.tt</a></p>
                <p>&copy; {{ date('Y') }} Ministry of Health Trinidad and Tobago</p>
            </div>
        </div>
    </div>
</body>
</html>
