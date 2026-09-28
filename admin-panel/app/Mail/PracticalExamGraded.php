<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PracticalExamAttempt;
use App\Mail\Traits\HandlesStudentName;

class PracticalExamGraded extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

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
        $isPassed = $this->attempt->status === 'passed';
        return new Envelope(
            subject: $isPassed ? 'You Passed the Practical Exam 🎉' : 'Practical Exam Result',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $isPassed = $this->attempt->status === 'passed';
        $reviewer = $this->attempt->reviewer;

        return new Content(
            view: 'emails.practical-exam-graded',
            with: [
                'student' => $this->attempt->user,
                'student_name' => $this->getStudentName($this->attempt->user),
                'course_name' => $this->attempt->product->titre,
                'training_case_name' => $this->attempt->trainingCase->name ?? $this->attempt->trainingCase->title ?? 'Training Case',
                'attempt_number' => $this->attempt->attempt_number,
                'status' => $this->attempt->status,
                'is_passed' => $isPassed,
                'admin_comment' => $this->attempt->admin_comment,
                'reviewer_name' => $reviewer ? ($reviewer->first_name . ' ' . $reviewer->last_name) : 'Instructor',
                'reviewed_at' => $this->attempt->reviewed_at ? $this->attempt->reviewed_at->format('F d, Y H:i') : now()->format('F d, Y H:i'),
                'exam_url' => env('USER_URL', 'http://localhost:8000') . '/student-dashboard/practical-exams',
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
