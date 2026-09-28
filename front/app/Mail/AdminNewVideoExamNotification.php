<?php

namespace App\Mail;

use App\Models\StudentVideoExam;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewVideoExamNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $student;
    public $course;
    public $reviewUrl;
    public $isResubmission;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentVideoExam $submission, bool $isResubmission = false)
    {
        $this->submission = $submission;
        $this->student = $submission->student;
        $this->course = $submission->product;
        $this->isResubmission = $isResubmission;
        $this->reviewUrl = config('app.admin_url') . '/student-video-exams/' . $submission->id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $studentName = $this->student ? ($this->student->first_name . ' ' . $this->student->last_name) : 'A student';
        $subject = $this->isResubmission
            ? 'Video Exam Re-Submission - ' . $studentName
            : 'New Video Exam Submission - ' . $studentName;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-video-exam',
            with: [
                'studentName' => $this->student ? ($this->student->first_name . ' ' . $this->student->last_name) : 'Unknown Student',
                'studentEmail' => $this->student->email ?? 'N/A',
                'courseName' => $this->course->titre ?? $this->course->variation_title ?? 'Unknown Course',
                'submittedAt' => $this->submission->submitted_at ? $this->submission->submitted_at->format('F d, Y H:i') : now()->format('F d, Y H:i'),
                'videoLink' => $this->submission->lien ?? 'No link provided',
                'description' => $this->submission->video_description ?? 'No description provided',
                'isResubmission' => $this->isResubmission,
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
