<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MuftiPendingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $mufti,
        public Collection $pendingQuestions
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⏳ Pending Fatwa Reminder — {$this->pendingQuestions->count()} question(s) awaiting your answer",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mufti-pending-reminder',
        );
    }
}
