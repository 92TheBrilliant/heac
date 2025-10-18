<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .field-value {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            color: #1f2937;
        }
        .message-box {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 15px;
            font-weight: 600;
        }
        .meta-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 New Contact Form Submission</h1>
    </div>

    <div class="content">
        <div class="field">
            <div class="field-label">From</div>
            <div class="field-value">
                <strong>{{ $inquiry->name }}</strong><br>
                <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>
                @if($inquiry->phone)
                    <br>{{ $inquiry->phone }}
                @endif
            </div>
        </div>

        <div class="field">
            <div class="field-label">Subject</div>
            <div class="field-value">{{ $inquiry->subject }}</div>
        </div>

        <div class="field">
            <div class="field-label">Message</div>
            <div class="message-box">{{ $inquiry->message }}</div>
        </div>

        <div class="meta-info">
            <strong>Submission Details:</strong><br>
            Submitted: {{ $inquiry->created_at->format('F j, Y \a\t g:i A') }}<br>
            IP Address: {{ $inquiry->ip_address }}<br>
            Inquiry ID: #{{ $inquiry->id }}
        </div>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/admin/contact-inquiries/{{ $inquiry->id }}" class="button">
                View in Admin Panel
            </a>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated notification from the HEAC website contact form.</p>
        <p>You can reply directly to this email to respond to {{ $inquiry->name }}.</p>
    </div>
</body>
</html>
