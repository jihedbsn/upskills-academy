<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $emailSubject,
        public string $heading,
        public string $body,
        public ?string $ctaUrl = null,
        public ?string $ctaLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.referral-notification',
            with: [
                'recipientName' => $this->recipientName,
                'heading'       => $this->heading,
                'body'          => $this->body,
                'ctaUrl'        => $this->ctaUrl,
                'ctaLabel'      => $this->ctaLabel,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
