THANK YOU FOR CONTACTING HEAC
==============================

Dear {{ $inquiry->name }},

Thank you for reaching out to the Higher Education Accreditation Commission (HEAC). 
We have successfully received your message and appreciate you taking the time to contact us.

YOUR MESSAGE SUMMARY
--------------------
Subject: {{ $inquiry->subject }}
Submitted: {{ $inquiry->created_at->format('F j, Y \a\t g:i A') }}
Reference ID: #{{ $inquiry->id }}

Our team will review your inquiry and respond as soon as possible. We typically respond to 
inquiries within 1-2 business days. If your matter is urgent, please feel free to call us 
directly at the contact number listed on our website.

NEED IMMEDIATE ASSISTANCE?
--------------------------
Phone: +1 (234) 567-8900
Office Hours: Monday - Friday, 9:00 AM - 5:00 PM

In the meantime, you may find answers to common questions in our FAQ section 
or explore our research publications at {{ config('app.url') }}/research

Best regards,
The HEAC Team
Higher Education Accreditation Commission

---
Higher Education Accreditation Commission
123 Education Street, City, State 12345
Email: info@heac.org
Website: {{ config('app.url') }}

This is an automated response. Please do not reply directly to this email.
If you did not submit this inquiry, please contact us immediately.
