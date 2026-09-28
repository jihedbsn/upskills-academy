<?php

namespace App\Mail;

use App\Models\PracticalExamAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PracticalExamSubmittedToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $attempt;
    public $student;
    public $course;
    public $isRetake;

    /**
     * Create a new message instance.
     */
    public function __construct(PracticalExamAttempt $attempt, $isRetake = false)
    {
        $this->attempt = $attempt;
        $this->student = $attempt->user;
        $this->course = $attempt->product;
        $this->isRetake = $isRetake;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isRetake
            ? 'Practical Exam Retake Submitted - Review Required'
            : 'New Practical Exam Submission - Review Required';

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
            view: 'emails.practical-exam-submitted-admin',
            with: [
                'studentName' => $this->student->first_name . ' ' . $this->student->last_name,
                'studentEmail' => $this->student->email,
                'courseName' => $this->course->titre,
                'attemptNumber' => $this->attempt->attempt_number,
                'submittedAt' => $this->attempt->submitted_at->format('F d, Y H:i'),
                'videoUrl' => $this->attempt->video_url,
                'isRetake' => $this->isRetake,
                'reviewUrl' => env('ADMIN_URL') . '/practical-exams/' . $this->attempt->id . '/grade',
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
