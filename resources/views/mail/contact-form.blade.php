<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Message</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 40px 20px;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .header {
            background: #ffffff;
            padding: 40px 48px 30px;
            text-align: center;
            border-bottom: 2px solid #f1f5f9;
        }

        .logo {
            max-height: 40px;
            margin-bottom: 24px;
        }

        .header h1 {
            color: #0f172a;
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .header p {
            color: #64748b;
            font-size: 15px;
            margin: 8px 0 0;
            font-weight: 500;
        }

        .body {
            padding: 40px 48px;
            background: #ffffff;
        }

        .grid-fields {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .field-group {
            display: table-cell;
            width: 50%;
            padding-right: 20px;
        }

        .field-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .field-value {
            font-size: 15px;
            color: #0f172a;
            font-weight: 600;
            line-height: 1.5;
            word-break: break-all;
        }

        .divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 30px 0;
        }

        .message-container {
            margin-top: 10px;
        }

        .message-box {
            background: #f8fafc;
            border-left: 4px solid #0ea5e9;
            border-radius: 0 12px 12px 0;
            padding: 24px;
            margin-top: 12px;
        }

        .message-box p {
            font-size: 15px;
            color: #334155;
            line-height: 1.8;
            margin: 0;
            white-space: pre-wrap;
        }

        .footer {
            background: #f8fafc;
            padding: 30px 48px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
        }

        .footer p {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
            font-weight: 500;
        }

        .tag {
            display: inline-block;
            background: #e0f2fe;
            color: #0284c7;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 16px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            @if(file_exists(public_path('logo.svg')))
                <img src="{{ asset('logo.svg') }}" alt="QuickJHA Logo" class="logo">
            @else
                <h2 style="margin: 0 0 20px; color: #0ea5e9;">QuickJHA</h2>
            @endif
            <!-- <div class="tag">New Inquiry</div> -->
            <h1>Contact Form Submission</h1>
            <p>You've received a new message from the QuickJHA landing page.</p>
        </div>
        <div class="body">
            <div class="grid-fields">
                <div class="field-group">
                    <div class="field-label">Sender Name</div>
                    <div class="field-value">{{ $formData['name'] }}</div>
                </div>
                <div class="field-group">
                    <div class="field-label">Work Email</div>
                    <div class="field-value">
                        <a href="mailto:{{ $formData['email'] }}" style="color: #0ea5e9; text-decoration: none;">
                            {{ $formData['email'] }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="message-container">
                <div class="field-label">Subject</div>
                <div class="field-value" style="font-size: 18px;">{{ $formData['subject'] }}</div>

                <div style="margin-top: 24px;">
                    <div class="field-label">Message Details</div>
                    <div class="message-box">
                        <p>{{ $formData['message'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>This automated email was dispatched from QuickJHA · {{ now()->format('F jS, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>

</html>