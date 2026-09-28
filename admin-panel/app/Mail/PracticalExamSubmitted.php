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

class PracticalExamSubmitted extends Mailable
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
        return new Envelope(
            subject: 'Practical Exam Submitted Successfully',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.practical-exam-submitted',
            with: [
                'student' => $this->attempt->user,
                'student_name' => $this->getStudentName($this->attempt->user),
                'course_name' => $this->attempt->product->titre,
                'training_case_name' => $this->attempt->trainingCase->name,
                'attempt_number' => $this->attempt->attempt_number,
                'submission_date' => $this->attempt->submitted_at ? $this->attempt->submitted_at->format('F d, Y H:i') : now()->format('F d, Y H:i'),
                'exam_type' => $this->attempt->video_url ? 'Online' : 'Classroom',
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
