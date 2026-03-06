<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', 'MOH Learning')</title>
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
        :root { color-scheme: light; supported-color-schemes: light; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; margin: 0 !important; border-radius: 0 !important; }
            .email-padding { padding: 24px 20px !important; }
            .header-padding { padding: 24px 20px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;">
    <!-- Wrapper table for full-width background -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6;">
        <tr>
            <td align="center" style="padding:40px 20px;">
                <!-- Main email container -->
                <table class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1),0 2px 4px -1px rgba(0,0,0,0.06);">
                    <!-- Header -->
                    <tr>
                        <td class="header-padding" style="background:linear-gradient(135deg,#4F46E5 0%,#6366F1 50%,#818CF8 100%);padding:32px 40px;text-align:center;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="width:44px;height:44px;background-color:rgba(255,255,255,0.2);border-radius:50%;text-align:center;vertical-align:middle;line-height:44px;">
                                                    <span style="font-size:20px;font-weight:700;color:#ffffff;">M</span>
                                                </td>
                                                <td style="padding-left:12px;">
                                                    <span style="font-size:20px;font-weight:700;color:#ffffff;letter-spacing:-0.5px;">MOH Learning</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @hasSection('header-subtitle')
                                <tr>
                                    <td align="center" style="padding-top:8px;">
                                        <span style="color:rgba(255,255,255,0.9);font-size:14px;">@yield('header-subtitle')</span>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="email-padding" style="padding:32px 40px;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#F9FAFB;padding:24px 40px;border-top:1px solid #E5E7EB;text-align:center;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <!-- Divider -->
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
                                            <tr>
                                                <td style="width:40px;height:3px;background:linear-gradient(90deg,#4F46E5,#6366F1);border-radius:2px;"></td>
                                            </tr>
                                        </table>
                                        <p style="font-size:14px;font-weight:600;color:#1F2937;margin:0 0 4px 0;">&copy; {{ date('Y') }} Ministry of Health, Trinidad and Tobago</p>
                                        <p style="font-size:12px;color:#9CA3AF;margin:0 0 4px 0;">MOH Learning Platform</p>
                                        <p style="font-size:12px;color:#9CA3AF;margin:0 0 4px 0;">
                                            <a href="mailto:helpdesk@health.gov.tt" style="color:#6366F1;text-decoration:none;">helpdesk@health.gov.tt</a>
                                        </p>
                                        <p style="font-size:11px;color:#D1D5DB;margin:12px 0 0 0;">This is an automated message. Please do not reply to this email.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
