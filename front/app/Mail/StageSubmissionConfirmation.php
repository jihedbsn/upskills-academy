<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentStageCourse;

class StageSubmissionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $studentStageCourse;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentStageCourse $studentStageCourse)
    {
        $this->studentStageCourse = $studentStageCourse;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Stage Submission Received - Under Review',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stage-submission-confirmation',
            with: [
                'student' => $this->studentStageCourse->student,
                'student_name' => $this->studentStageCourse->student->first_name . ' ' . $this->studentStageCourse->student->last_name,
                'course_name' => ($this->studentStageCourse->product && $this->studentStageCourse->product->current_variation) ? $this->studentStageCourse->product->current_variation->name : ($this->studentStageCourse->product->titre ?? 'Course'),
                'submission_date' => $this->studentStageCourse->submitted_at ? $this->studentStageCourse->submitted_at->format('F d, Y H:i') : now()->format('F d, Y H:i'),
                'dashboard_url' => config('app.user_url') . '/student-dashboard/courses/' . $this->studentStageCourse->product_id,
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
