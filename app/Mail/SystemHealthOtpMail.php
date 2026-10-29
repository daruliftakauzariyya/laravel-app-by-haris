<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemHealthOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $ipAddress;
    public string $userAgent;
    public int $expiresInMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $ipAddress, string $userAgent, int $expiresInMinutes = 5)
    {
        $this->otp = $otp;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SECURITY ALERT: Verification OTP for System Health & Diagnostics Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.system-health-otp',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

