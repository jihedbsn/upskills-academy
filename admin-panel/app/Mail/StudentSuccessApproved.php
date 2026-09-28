<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentSuccess;
use App\Mail\Traits\HandlesStudentName;

class StudentSuccessApproved extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $studentSuccess;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentSuccess $studentSuccess)
    {
        $this->studentSuccess = $studentSuccess;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Final Success has been Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-success-approved',
            with: [
                'student' => $this->studentSuccess->student,
                'student_name' => $this->getStudentName($this->studentSuccess->student),
                'course_name' => $this->studentSuccess->product->current_variation ? $this->studentSuccess->product->current_variation->name : $this->studentSuccess->product->titre,
                'completion_date' => $this->studentSuccess->created_at->format('F d, Y'),
                'product' => $this->studentSuccess->product,
                'studentSuccess' => $this->studentSuccess,
                'dashboard_url' => config('app.user_url') . '/student-dashboard/courses/' . $this->studentSuccess->product_id,
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
