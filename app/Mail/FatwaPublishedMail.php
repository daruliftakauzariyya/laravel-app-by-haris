<?php

namespace App\Mail;

use App\Models\QuestionAndAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FatwaPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public QuestionAndAnswer $fatwa) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Question Has Been Answered – ' . $this->fatwa->question_id);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.fatwa-published');
    }
}
