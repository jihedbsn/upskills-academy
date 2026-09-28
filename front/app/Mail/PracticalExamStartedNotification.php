<?php

namespace App\Mail;

use App\Models\PracticalExamAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PracticalExamStartedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $attempt;
    public $student;
    public $course;
    public $trainingCase;

    /**
     * Create a new message instance.
     */
    public function __construct(PracticalExamAttempt $attempt)
    {
        $this->attempt = $attempt;
        $this->student = $attempt->user;
        $this->course = $attempt->product;
        $this->trainingCase = $attempt->trainingCase;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Practical Exam Has Started - ' . $this->course->titre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.practical-exam-started',
            with: [
                'studentName' => $this->student->first_name . ' ' . $this->student->last_name,
                'courseName' => $this->course->titre,
                'attemptNumber' => $this->attempt->attempt_number,
                'trainingCaseName' => $this->trainingCase ? ($this->trainingCase->title ?? $this->trainingCase->name ?? 'N/A') : 'N/A',
                'startedAt' => $this->attempt->created_at->format('F d, Y \a\t g:i A'),
                'examUrl' => env('USER_URL', 'http://localhost:8000') . '/student-dashboard/courses/' . $this->course->id . '/practical-exam',
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
