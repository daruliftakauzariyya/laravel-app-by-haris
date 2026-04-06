<?php

namespace App\Mail;

use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuestionAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Question $question) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Question Assigned to You – ' . $this->question->question_id);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.question-assigned');
    }
}
