<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verification Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .otp-container {
            text-align: center;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
        }
        .otp-label {
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #ffffff;
            font-family: 'Courier New', monospace;
            background-color: rgba(255,255,255,0.1);
            padding: 15px 25px;
            border-radius: 6px;
            display: inline-block;
        }
        .expiry-notice {
            text-align: center;
            color: #dc2626;
            font-weight: 600;
            font-size: 14px;
            margin-top: 15px;
        }
        .security-notice {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 25px 0;
            border-radius: 0 6px 6px 0;
        }
        .security-notice h4 {
            color: #92400e;
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .security-notice ul {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
            font-size: 13px;
        }
        .security-notice li {
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .footer p {
            margin: 5px 0;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ministry of Health</h1>
            <p>Learning Management System</p>
        </div>
        
        <p class="greeting">Hello {{ $user->first_name ?? 'User' }},</p>
        
        <p>You are attempting to log in to the MOH Learning Management System. To complete your authentication, please enter the verification code below:</p>
        
        <div class="otp-container">
            <div class="otp-label">Your Verification Code</div>
            <div class="otp-code">{{ $otpCode }}</div>
            <div class="expiry-notice">⏱ Expires in {{ $expiryMinutes }} minutes</div>
        </div>
        
        <p>Enter this code on the verification page to complete your login.</p>
        
        <div class="security-notice">
            <h4>🔒 Security Notice</h4>
            <ul>
                <li>Never share this code with anyone</li>
                <li>MOH staff will never ask for this code</li>
                <li>If you didn't request this code, please ignore this email</li>
                <li>This code is valid for one-time use only</li>
            </ul>
        </div>
        
        <p>If you did not attempt to log in, please contact IT support immediately.</p>
        
        <div class="footer">
            <p><strong>Ministry of Health Trinidad and Tobago</strong></p>
            <p>Learning Management System</p>
            <p>This is an automated message. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
