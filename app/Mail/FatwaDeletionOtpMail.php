<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FatwaDeletionOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $questionId;
    public string $fatwaTitle;
    public string $adminName;
    public string $ipAddress;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $questionId, string $fatwaTitle, string $adminName, string $ipAddress)
    {
        $this->otp = $otp;
        $this->questionId = $questionId;
        $this->fatwaTitle = $fatwaTitle;
        $this->adminName = $adminName;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "SECURITY ALERT: OTP for Permanent Deletion of Fatwa #{$this->questionId}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.fatwa-deletion-otp',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
