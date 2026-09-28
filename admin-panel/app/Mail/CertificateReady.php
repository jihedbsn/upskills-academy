<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentSuccess;
use App\Models\CertifStudent;
use App\Mail\Traits\HandlesStudentName;

class CertificateReady extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $studentSuccess;
    public $certificate;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentSuccess $studentSuccess, CertifStudent $certificate)
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
            subject: '🎓 Congratulations! Your Certificate is Ready - Course Completed Successfully',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate-ready',
            with: [
                'student' => $this->studentSuccess->student,
                'student_name' => $this->getStudentName($this->studentSuccess->student),
                'course_name' => $this->studentSuccess->product->current_variation ? $this->studentSuccess->product->current_variation->name : $this->studentSuccess->product->titre,
                'completion_date' => $this->studentSuccess->created_at ? $this->studentSuccess->created_at->format('F d, Y') : now()->format('F d, Y'),
                'certificate_number' => $this->certificate->serial_number ?? 'N/A',
                'product' => $this->studentSuccess->product,
                'studentSuccess' => $this->studentSuccess,
                'certificate' => $this->certificate,
                'certificate_url' => config('app.user_url') . '/student-dashboard/certificates/' . $this->certificate->id,
                'downloadUrl' => config('app.user_url') . '/student-successes/' . $this->studentSuccess->id . '/download-certificate',
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
        if ($this->certificate->file_path) {
            $filePath = public_path($this->certificate->file_path);

            if (file_exists($filePath)) {
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($filePath)
                    ->as('Certificate_' . $this->certificate->serial_number . '.' . $extension)
                    ->withMime(mime_content_type($filePath));
            }
        }

        return $attachments;
    }
}
