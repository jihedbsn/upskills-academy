<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentStageCourse;
use App\Mail\Traits\HandlesStudentName;

class StageValidated extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

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
            subject: 'Congratulations! Your Internship has been Validated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stage-validated',
            with: [
                'student' => $this->studentStageCourse->student,
                'student_name' => $this->getStudentName($this->studentStageCourse->student),
                'course_name' => ($this->studentStageCourse->product && $this->studentStageCourse->product->current_variation) ? $this->studentStageCourse->product->current_variation->name : ($this->studentStageCourse->product->titre ?? 'Course'),
                'product' => $this->studentStageCourse->product,
                'stageCourse' => $this->studentStageCourse,
                'stage_title' => 'Internship Submission',
                'validation_date' => $this->studentStageCourse->updated_at ? $this->studentStageCourse->updated_at->format('F d, Y') : now()->format('F d, Y'),
                'dashboard_url' => config('app.user_url') . '/student-dashboard/courses/' . $this->studentStageCourse->product_id,
                'approval_message' => $this->studentStageCourse->approval_message,
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
