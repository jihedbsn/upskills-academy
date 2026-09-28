<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class CourseEnrollmentEmail extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $course;
    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct($course, $student)
    {
        $this->course = $course;
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Inscription confirmée : ' . $this->course->titre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-enrollment',
            with: [
                'studentName' => $this->getStudentName($this->student),
            ],
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
