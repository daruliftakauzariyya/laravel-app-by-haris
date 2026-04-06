<?php

namespace App\Mail;

use App\Models\Fatwa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FatwaPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Fatwa $fatwa) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Question Has Been Answered – ' . $this->fatwa->question->question_id);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.fatwa-published');
    }
}
