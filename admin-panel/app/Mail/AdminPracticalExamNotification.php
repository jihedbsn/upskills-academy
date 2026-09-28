<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PracticalExamAttempt;

class AdminPracticalExamNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $attempt;

    /**
     * Create a new message instance.
     */
    public function __construct(PracticalExamAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Practical Exam Submission - Requires Review',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-practical-exam-notification',
            with: [
                'student_name' => $this->attempt->user->first_name . ' ' . $this->attempt->user->last_name,
                'student_email' => $this->attempt->user->email,
                'course_name' => $this->attempt->product->titre,
                'training_case_name' => $this->attempt->trainingCase->name,
                'attempt_number' => $this->attempt->attempt_number,
                'submission_date' => $this->attempt->submitted_at ? $this->attempt->submitted_at->format('F d, Y H:i') : now()->format('F d, Y H:i'),
                'exam_type' => $this->attempt->video_url ? 'Online' : 'Classroom',
                'grading_url' => route('practical-exams.show', $this->attempt->id),
            ]
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
