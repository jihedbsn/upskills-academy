<?php

namespace App\Mail;

use App\Models\StudentStageCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewStageSubmissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $student;
    public $course;
    public $reviewUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentStageCourse $submission)
    {
        $this->submission = $submission;
        $this->student = $submission->student;
        $this->course = $submission->product;
        $this->reviewUrl = config('app.admin_url') . '/student-stage-courses/' . $submission->id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $studentName = $this->student ? ($this->student->first_name . ' ' . $this->student->last_name) : 'A student';

        return new Envelope(
            subject: 'New Stage Submission - ' . $studentName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-stage-submission',
            with: [
                'studentName' => $this->student ? ($this->student->first_name . ' ' . $this->student->last_name) : 'Unknown Student',
                'studentEmail' => $this->student->email ?? 'N/A',
                'courseName' => $this->course->titre ?? $this->course->variation_title ?? 'Unknown Course',
                'submittedAt' => $this->submission->submitted_at ? $this->submission->submitted_at->format('F d, Y H:i') : now()->format('F d, Y H:i'),
                'description' => $this->submission->description ?? 'No description provided',
                'hasFile1' => !empty($this->submission->file1),
                'hasFile2' => !empty($this->submission->file2),
                'reviewUrl' => $this->reviewUrl,
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
