<?php

namespace App\Mail;

use App\Models\CourseSession;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseSessionCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $session;
    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct($session, Student $student)
    {
        $this->session = $session;
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = is_object($this->session) ? $this->session->title : $this->session['title'];
        return new Envelope(
            subject: 'Session Cancelled: ' . $title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-session-cancelled',
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
