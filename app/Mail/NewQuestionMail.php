<?php

namespace App\Mail;

use App\Models\QuestionAndAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewQuestionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public QuestionAndAnswer $question) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Fatwa Question Submitted – ' . $this->question->question_id);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-question');
    }
}
