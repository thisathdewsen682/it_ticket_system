<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'IT Job Management System' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            padding: 0;
            margin: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, {{ $headerColor ?? '#059669' }} 0%, {{ $headerColorDark ?? '#047857' }} 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin: 0;
        }
        .email-body {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        .greeting {
            font-size: 16px;
            color: #111827;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #059669;
        }
        .message {
            font-size: 15px;
            color: #374151;
            margin-bottom: 25px;
            line-height: 1.7;
        }
        .info-card {
            background: linear-gradient(to right, #f0fdf4, #ffffff);
            border-left: 4px solid {{ $accentColor ?? '#059669' }};
            border-radius: 8px;
            padding: 24px;
            margin: 25px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .info-card h3 {
            color: #059669;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 16px 0;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            min-width: 140px;
            font-size: 14px;
        }
        .info-value {
            color: #111827;
            font-size: 14px;
            flex: 1;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-high { background: #fee2e2; color: #991b1b; }
        .badge-normal { background: #dbeafe; color: #1e40af; }
        .badge-low { background: #d1fae5; color: #065f46; }
        .badge-urgent { background: #fef3c7; color: #92400e; }
        .alert-box {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }
        .alert-box p {
            color: #92400e;
            margin: 0;
            font-size: 14px;
        }
        .alert-box strong {
            color: #78350f;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            margin: 8px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .button-primary {
            background: #059669;
            color: #ffffff;
        }
        .button-success {
            background: #10b981;
            color: #ffffff;
        }
        .button-danger {
            background: #ef4444;
            color: #ffffff;
        }
        .button-secondary {
            background: #6b7280;
            color: #ffffff;
        }
        .description-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }
        .description-box p {
            color: #374151;
            margin: 0;
            font-size: 14px;
            white-space: pre-wrap;
        }
        .instructions-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }
        .instructions-box strong {
            color: #92400e;
            display: block;
            margin-bottom: 8px;
        }
        .instructions-box p {
            color: #78350f;
            margin: 0;
            font-size: 14px;
            white-space: pre-wrap;
        }
        .email-footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 8px 0;
        }
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 25px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-header {
                padding: 30px 20px;
            }
            .email-body {
                padding: 30px 20px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                margin-bottom: 4px;
            }
            .button {
                display: block;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            @yield('header')
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p><strong>IT Job Management System</strong></p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p style="margin-top: 16px; color: #9ca3af; font-size: 12px;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
