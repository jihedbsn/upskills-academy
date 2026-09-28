<?php

namespace App\Mail;

use App\Models\StudentSuccess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminManualCertificateNotification extends Mailable
{
    use Queueable, SerializesModels;

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
            subject: 'Action Required: Manual Certificate Generation - ' . $this->studentSuccess->student->full_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-manual-certificate',
            with: [
                'studentSuccess' => $this->studentSuccess,
                'studentName' => $this->studentSuccess->student->full_name,
                'studentEmail' => $this->studentSuccess->student->email,
                'courseName' => $this->studentSuccess->product->titre,
                'courseId' => $this->studentSuccess->product_id,
                'studentSuccessId' => $this->studentSuccess->id,
                'certificateManagementUrl' => route('certificate-management.show', $this->studentSuccess->id),
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
