<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for contacting HEAC</title>
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
            padding: 40px 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            background: white;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .message-summary {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #2563eb;
            margin: 20px 0;
        }
        .message-summary h3 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .message-summary p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
            color: #1e40af;
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
        .footer a {
            color: #2563eb;
            text-decoration: none;
        }
        .social-links {
            margin-top: 15px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 5px;
            color: #6b7280;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✓ Message Received</h1>
        <p>Thank you for contacting HEAC</p>
    </div>

    <div class="content">
        <div class="greeting">
            Dear {{ $inquiry->name }},
        </div>

        <p>
            Thank you for reaching out to the Higher Education Accreditation Commission (HEAC). 
            We have successfully received your message and appreciate you taking the time to contact us.
        </p>

        <div class="message-summary">
            <h3>Your Message Summary</h3>
            <p><strong>Subject:</strong> {{ $inquiry->subject }}</p>
            <p><strong>Submitted:</strong> {{ $inquiry->created_at->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Reference ID:</strong> #{{ $inquiry->id }}</p>
        </div>

        <p>
            Our team will review your inquiry and respond as soon as possible. We typically respond to 
            inquiries within 1-2 business days. If your matter is urgent, please feel free to call us 
            directly at the contact number listed on our website.
        </p>

        <div class="info-box">
            <p><strong>📞 Need immediate assistance?</strong></p>
            <p>Phone: +1 (234) 567-8900</p>
            <p>Office Hours: Monday - Friday, 9:00 AM - 5:00 PM</p>
        </div>

        <p>
            In the meantime, you may find answers to common questions in our 
            <a href="{{ config('app.url') }}/faq" style="color: #2563eb; text-decoration: none;">FAQ section</a> 
            or explore our <a href="{{ config('app.url') }}/research" style="color: #2563eb; text-decoration: none;">research publications</a>.
        </p>

        <p style="margin-top: 30px;">
            Best regards,<br>
            <strong>The HEAC Team</strong><br>
            Higher Education Accreditation Commission
        </p>
    </div>

    <div class="footer">
        <p>
            <strong>Higher Education Accreditation Commission</strong><br>
            123 Education Street, City, State 12345<br>
            Email: <a href="mailto:info@heac.org">info@heac.org</a> | 
            Website: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
        </p>

        <div class="social-links">
            <a href="#">LinkedIn</a> | 
            <a href="#">Twitter</a> | 
            <a href="#">Facebook</a>
        </div>

        <p style="margin-top: 15px; font-size: 11px; color: #9ca3af;">
            This is an automated response. Please do not reply directly to this email.<br>
            If you did not submit this inquiry, please contact us immediately.
        </p>
    </div>
</body>
</html>
