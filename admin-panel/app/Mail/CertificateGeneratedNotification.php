<?php

namespace App\Mail;

use App\Models\CertifStudent;
use App\Models\StudentSuccess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CertificateGeneratedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate;
    public $studentSuccess;

    /**
     * Create a new message instance.
     */
    public function __construct(CertifStudent $certificate, StudentSuccess $studentSuccess)
    {
        $this->certificate = $certificate;
        $this->studentSuccess = $studentSuccess;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Certificate is Ready! - ' . $this->studentSuccess->product->titre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.certificate-generated',
            with: [
                'certificate' => $this->certificate,
                'studentSuccess' => $this->studentSuccess,
                'studentName' => $this->studentSuccess->student->full_name,
                'courseName' => $this->studentSuccess->product->titre,
                'serialNumber' => $this->certificate->serial_number,
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
        $attachments = [];

        // Attach certificate file if it exists
        if ($this->certificate->file_path && file_exists(public_path($this->certificate->file_path))) {
            $attachments[] = Attachment::fromPath(public_path($this->certificate->file_path))
                ->as('certificate_' . $this->certificate->serial_number . '.png')
                ->withMime('image/png');
        }

        return $attachments;
    }
}
