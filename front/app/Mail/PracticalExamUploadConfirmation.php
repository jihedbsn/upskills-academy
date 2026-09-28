<?php

namespace App\Mail;

use App\Models\PracticalExamAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PracticalExamUploadConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $attempt;
    public $student;
    public $course;

    /**
     * Create a new message instance.
     */
    public function __construct(PracticalExamAttempt $attempt)
    {
        $this->attempt = $attempt;
        $this->student = $attempt->user;
        $this->course = $attempt->product;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Practical Exam Submitted - Under Review',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.practical-exam-upload-confirmation',
            with: [
                'studentName' => $this->student->first_name . ' ' . $this->student->last_name,
                'courseName' => $this->course->titre,
                'attemptNumber' => $this->attempt->attempt_number,
                'videoUrl' => $this->attempt->video_url,
                'submittedAt' => $this->attempt->submitted_at ? $this->attempt->submitted_at->format('F j, Y \a\t g:i A') : now()->format('F j, Y \a\t g:i A'),
                'dashboardUrl' => env('USER_URL', 'http://localhost:8000') . '/student-dashboard/practical-exams',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
