<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactInquiryAutoReply;
use App\Mail\ContactInquiryNotification;
use App\Models\ContactInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the contact form
     */
    public function index(): View
    {
        return view('contact');
    }

    /**
     * Store contact form submission with validation and spam protection
     * 
     * Note: Honeypot and rate limiting are handled by middleware
     */
    public function store(ContactFormRequest $request): RedirectResponse
    {
        // Get validated data
        $validated = $request->validated();

        // Store inquiry in database
        $inquiry = ContactInquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? 'General Inquiry',
            'message' => $validated['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send email notification to admins
        $this->sendAdminNotification($inquiry);

        // Send auto-reply to submitter
        $this->sendAutoReply($inquiry);

        return redirect()->route('contact.index')
            ->with('success', 'Thank you for your message. We will get back to you soon.');
    }

    /**
     * Send email notification to administrators
     */
    protected function sendAdminNotification(ContactInquiry $inquiry): void
    {
        $adminEmail = config('mail.admin_email', 'admin@heac.example.com');

        Mail::to($adminEmail)->send(new ContactInquiryNotification($inquiry));
    }

    /**
     * Send auto-reply email to submitter
     */
    protected function sendAutoReply(ContactInquiry $inquiry): void
    {
        Mail::to($inquiry->email)->send(new ContactInquiryAutoReply($inquiry));
    }
}
