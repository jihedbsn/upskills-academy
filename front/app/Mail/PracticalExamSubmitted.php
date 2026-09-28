<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PracticalExamAttempt;

class PracticalExamSubmitted extends Mailable
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
            subject: 'Practical Exam Submission Confirmed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $student = $this->attempt->user;
        $product = $this->attempt->product;
        $trainingCase = $this->attempt->trainingCase;

        return new Content(
            view: 'emails.practical-exam-submitted',
            with: [
                'student' => $student,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'course_name' => $product->titre ?? 'Course',
                'training_case_name' => $trainingCase->title ?? $trainingCase->name ?? 'Training Case',
                'attempt_number' => $this->attempt->attempt_number,
                'video_url' => $this->attempt->video_url,
                'submitted_at' => $this->attempt->submitted_at,
                'exam_url' => route('student.practical-exam.show', ['locale' => app()->getLocale(), 'product' => $product->id]),
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
