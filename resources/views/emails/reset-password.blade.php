<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f8fafc; padding-bottom: 40px; }
        .content { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; margin-top: 40px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .header { background-color: #ffffff; padding: 40px; text-align: center; border-bottom: 1px solid #f1f5f9; }
        .body { padding: 40px; }
        .footer { padding: 24px; text-align: center; color: #64748b; font-size: 12px; }
        .button { display: inline-block; background-color: #0ea5e9; color: #ffffff !important; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; font-size: 14px; box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3); }
        h1 { font-weight: 900; letter-spacing: -0.05em; color: #020617; font-size: 30px; margin-bottom: 8px; margin-top: 0; }
        p { color: #475569; line-height: 1.6; font-size: 16px; margin-bottom: 24px; }
        .badge { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; display: inline-block; }
    </style>
</head>
<body>
    <div class="wrapper">
        <center>
            <div class="content">
                <div class="header">
                    <img src="{{ $message->embed(public_path('logo.jpg')) }}" alt="QuickJHA" style="height: 60px; width: auto;">
                </div>
                <div class="body">
                    <div style="text-align: center;">
                        <span class="badge">Security Notification</span>
                        <h1>Reset Your Password</h1>
                        <p>You are receiving this email because we received a password reset request for your account. This link will expire in {{ $expire }} minutes.</p>
                        
                        <div style="margin: 40px 0;">
                            <a href="{{ $url }}" class="button">Reset Password</a>
                        </div>
                        
                        <p style="font-size: 14px; color: #64748b;">If you did not request a password reset, no further action is required.</p>
                    </div>
                </div>
                <div class="footer">
                    &copy; {{ date('Y') }} QuickJHA. Professional Safety Solutions.<br>
                    <span style="font-size: 10px; opacity: 0.5;">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="{{ $url }}" style="color: #0ea5e9;">{{ $url }}</a></span>
                </div>
            </div>
        </center>
    </div>
</body>
</html>
