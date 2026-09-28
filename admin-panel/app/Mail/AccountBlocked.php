<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountBlocked extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $blockReason;
    public $blockedAt;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $blockReason = null)
    {
        $this->student = $student;
        $this->blockReason = $blockReason;
        $this->blockedAt = now();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Blocked - Action Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account-blocked',
            with: [
                'studentName' => $this->getStudentName(),
                'blockReason' => $this->blockReason,
                'blockedAt' => $this->blockedAt,
            ],
        );
    }

    /**
     * Get student name based on locale
     */
    protected function getStudentName()
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return $this->student->name_ar ?? $this->student->name ?? $this->student->email;
        } elseif ($locale === 'fr') {
            return $this->student->name_fr ?? $this->student->name ?? $this->student->email;
        }

        return $this->student->name ?? $this->student->email;
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
