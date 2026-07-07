<?php

namespace App\Mail;

use App\Models\QuestionAndAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnswerSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public QuestionAndAnswer $answer) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Mufti Answer Submitted for Review – ' . $this->answer->question_id);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.answer-submitted');
    }
}
