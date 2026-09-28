<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class AccountBlocked extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $student;
    public $blockReason;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $blockReason = null)
    {
        $this->student = $student;
        $this->blockReason = $blockReason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Access Notification - Upskills Academy',
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
                'studentName' => $this->getStudentName($this->student),
                'blockReason' => $this->blockReason,
                'blockedAt' => $this->student->blocked_at,
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
