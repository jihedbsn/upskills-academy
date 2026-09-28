<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $code;
    public $student;
    public $locale;

    /**
     * Create a new message instance.
     */
    public function __construct($code, $student = null, $locale = null)
    {
        $this->code = $code;
        $this->student = $student;
        $this->locale = $locale ?? app()->getLocale() ?? config('app.locale', 'en');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Upskills Academy - Password Reset Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-code',
            with: [
                'code' => $this->code,
                'studentName' => $this->student ? $this->getStudentName($this->student) : null,
                'studentEmail' => $this->student ? $this->student->email : null,
                'verifyUrl' => $this->buildVerifyUrl(),
                'encodedEmail' => $this->student && $this->student->email
                    ? base64_encode($this->student->email)
                    : null,
            ],
        );
    }

    /**
     * Build the URL where the student enters the code.
     */
    protected function buildVerifyUrl(): string
    {
        if (!$this->student || !$this->student->email) {
            return url('/');
        }

        $encodedEmail = base64_encode($this->student->email);

        // Build the absolute URL with locale prefix
        return url(sprintf('%s/verify-code/%s', $this->locale, $encodedEmail));
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
