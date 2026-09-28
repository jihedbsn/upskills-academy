<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PracticalExamAttempt;

class AdminPracticalExamSubmitted extends Mailable
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
            subject: 'New Practical Exam Awaiting Review',
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
            view: 'emails.admin-practical-exam-submitted',
            with: [
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'student_email' => $student->email,
                'course_name' => $product->titre ?? 'Course',
                'training_case_name' => $trainingCase->title ?? $trainingCase->name ?? 'Training Case',
                'attempt_number' => $this->attempt->attempt_number,
                'video_url' => $this->attempt->video_url,
                'submitted_at' => $this->attempt->submitted_at,
                'exam_type' => $product->practical_exam_type,
                'grade_url' => config('app.admin_url') . '/practical-exams/' . $this->attempt->id . '/grade',
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
