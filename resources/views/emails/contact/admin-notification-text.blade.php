NEW CONTACT FORM SUBMISSION
============================

From: {{ $inquiry->name }}
Email: {{ $inquiry->email }}
@if($inquiry->phone)
Phone: {{ $inquiry->phone }}
@endif

Subject: {{ $inquiry->subject }}

Message:
--------
{{ $inquiry->message }}

Submission Details:
-------------------
Submitted: {{ $inquiry->created_at->format('F j, Y \a\t g:i A') }}
IP Address: {{ $inquiry->ip_address }}
Inquiry ID: #{{ $inquiry->id }}

View in Admin Panel: {{ config('app.url') }}/admin/contact-inquiries/{{ $inquiry->id }}

---
This is an automated notification from the HEAC website contact form.
You can reply directly to this email to respond to {{ $inquiry->name }}.
