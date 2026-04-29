<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Inquiry — QuickJHA</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f1f5f9;
            padding: 48px 20px;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
        }
        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 16px 16px 0 0;
            padding: 40px 48px;
            text-align: center;
        }
        .header-logo {
            height: 44px;
            width: auto;
            margin-bottom: 24px;
        }
        .badge {
            display: inline-block;
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.35);
            color: #38bdf8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 999px;
            margin-bottom: 16px;
        }
        .header h1 {
            color: #f8fafc;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.25;
            margin-bottom: 8px;
        }
        .header p {
            color: #94a3b8;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.6;
        }
        /* ── Body ── */
        .body {
            background: #ffffff;
            padding: 40px 48px;
        }
        /* ── Sender Cards ── */
        .cards-row {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin: 0 -12px 32px;
        }
        .card {
            display: table-cell;
            width: 50%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            vertical-align: top;
        }
        .card-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .card-icon {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #e0f2fe;
            border-radius: 8px;
            text-align: center;
            line-height: 30px;
            margin-bottom: 10px;
        }
        .card-value {
            font-size: 15px;
            color: #0f172a;
            font-weight: 700;
            line-height: 1.4;
            word-break: break-all;
        }
        .card-value a {
            color: #0284c7;
            text-decoration: none;
        }
        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 32px 0;
        }
        /* ── Subject ── */
        .section-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .subject-text {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            line-height: 1.3;
        }
        /* ── Message Box ── */
        .message-wrap {
            margin-top: 28px;
        }
        .message-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0ea5e9;
            border-radius: 0 12px 12px 0;
            padding: 24px 28px;
            margin-top: 10px;
        }
        .message-box p {
            font-size: 15px;
            color: #334155;
            line-height: 1.85;
            white-space: pre-wrap;
        }
        /* ── Reply CTA ── */
        .cta-wrap {
            text-align: center;
            margin-top: 36px;
            padding-top: 32px;
            border-top: 1px solid #f1f5f9;
        }
        .cta-btn {
            display: inline-block;
            background: #0ea5e9;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 14px 32px;
            border-radius: 10px;
        }
        /* ── Footer ── */
        .footer {
            background: #0f172a;
            border-radius: 0 0 16px 16px;
            padding: 28px 48px;
            text-align: center;
        }
        .footer-brand {
            color: #f1f5f9;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .footer-meta {
            color: #475569;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.6;
        }
        .footer-divider {
            border: none;
            border-top: 1px solid #1e293b;
            margin: 16px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <img src="{{ asset('logo.jpg') }}" alt="QuickJHA" class="header-logo" />
            <div class="badge">New Contact Inquiry</div>
            <h1>You've Got a New Message</h1>
            <p>Someone submitted the contact form on QuickJHA.<br>Details are below — reply directly to respond.</p>
        </div>

        {{-- Body --}}
        <div class="body">

            {{-- Sender Info Cards --}}
            <div class="cards-row">
                <div class="card">
                    <div class="card-label">From</div>
                    <div class="card-value">{{ $formData['name'] }}</div>
                </div>
                <div class="card">
                    <div class="card-label">Email Address</div>
                    <div class="card-value">
                        <a href="mailto:{{ $formData['email'] }}">{{ $formData['email'] }}</a>
                    </div>
                </div>
            </div>

            <hr class="divider">

            {{-- Subject --}}
            <div class="section-label">Subject</div>
            <div class="subject-text">{{ $formData['subject'] }}</div>

            {{-- Message --}}
            <div class="message-wrap">
                <div class="section-label">Message</div>
                <div class="message-box">
                    <p>{{ $formData['message'] }}</p>
                </div>
            </div>

            {{-- Reply CTA --}}
            <div class="cta-wrap">
                <a href="mailto:{{ $formData['email'] }}?subject=Re: {{ $formData['subject'] }}" class="cta-btn">
                    Reply to {{ $formData['name'] }}
                </a>
            </div>

        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-brand">QuickJHA</div>
            <hr class="footer-divider">
            <p class="footer-meta">
                This message was sent via the QuickJHA contact form<br>
                {{ now()->format('l, F jS Y \a\t g:i A T') }}
            </p>
        </div>

    </div>
</body>
</html>
