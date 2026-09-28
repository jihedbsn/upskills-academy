<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentSuccess;
use App\Models\CertifStudent;
use App\Mail\Traits\HandlesStudentName;

class CourseCompletedNotification extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $studentSuccess;
    public $certificate;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentSuccess $studentSuccess, ?CertifStudent $certificate = null)
    {
        $this->studentSuccess = $studentSuccess;
        $this->certificate = $certificate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations - Course Completed Successfully',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-completed',
            with: [
                'studentName' => $this->getStudentName($this->studentSuccess->student),
                'courseName' => $this->studentSuccess->product->titre ?? $this->studentSuccess->product->name,
                'completionDate' => $this->studentSuccess->created_at->format('F d, Y'),
                'finalScore' => $this->studentSuccess->score ?? null,
                'certificateAvailable' => $this->certificate !== null,
                'certificateUrl' => $this->certificate ? url('/student-dashboard/certificates/' . $this->certificate->id) : null,
                'catalogUrl' => url('/courses'),
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
        $attachments = [];

        // Attach the certificate PDF/PNG if it exists
        if ($this->certificate && $this->certificate->file_path) {
            $filePath = public_path($this->certificate->file_path);

            if (file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath)
                    ->as('Certificate_' . $this->certificate->serial_number . '.' . pathinfo($filePath, PATHINFO_EXTENSION))
                    ->withMime(mime_content_type($filePath));
            }
        }

        return $attachments;
    }
}
