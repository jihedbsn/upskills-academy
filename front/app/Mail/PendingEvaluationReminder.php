<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\Student;
use App\Mail\Traits\HandlesStudentName;

class PendingEvaluationReminder extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $student;
    public $pendingCourses;
    public $pendingCount;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, $pendingCourses)
    {
        $this->student = $student;
        $this->pendingCourses = $pendingCourses;
        $this->pendingCount = $pendingCourses->count();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->pendingCount === 1
            ? 'Please Rate Your Completed Course'
            : "Please Rate Your {$this->pendingCount} Completed Courses";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pending-evaluation-reminder',
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
