<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $emailContent;

    public ?string $trackingPixelUrl = null;

    public ?string $trackingConfirmUrl = null;

    /** @var array<int, array{path?: string, name: string, data?: string, mime?: string}> */
    public array $fileAttachments = [];

    public ?string $generatedMessageId = null;

    public ?string $inReplyTo = null;

    /** @var array<int, string> */
    public array $referenceMessageIds = [];

    /**
     * Create a new message instance.
     *
     * @param  ?string  $trackingPixelUrl  Absolute URL for 1×1 open-tracking image (blocked by many clients).
     * @param  ?string  $trackingConfirmUrl  Absolute URL for click-to-confirm (redirect); works when images are off.
     * @param  array<int, array{path?: string, name: string, data?: string, mime?: string}>  $fileAttachments
     * @param  array<int, string>  $referenceMessageIds
     */
    public function __construct(
        string $subject,
        string $emailContent,
        ?string $trackingPixelUrl = null,
        ?string $trackingConfirmUrl = null,
        array $fileAttachments = [],
        ?string $generatedMessageId = null,
        ?string $inReplyTo = null,
        array $referenceMessageIds = []
    ) {
        $this->subject = $subject;
        $this->emailContent = $emailContent;
        $this->trackingPixelUrl = $trackingPixelUrl;
        $this->trackingConfirmUrl = $trackingConfirmUrl;
        $this->fileAttachments = $fileAttachments;
        $this->generatedMessageId = $generatedMessageId;
        $this->inReplyTo = $inReplyTo;
        $this->referenceMessageIds = $referenceMessageIds;
    }

    public function headers(): Headers
    {
        $text = [];

        if ($this->inReplyTo) {
            $text['In-Reply-To'] = trim($this->inReplyTo, '<>');
        }

        $messageId = $this->generatedMessageId
            ? trim($this->generatedMessageId, '<>')
            : null;

        $references = array_values(array_filter(array_map(
            fn ($id) => trim((string) $id, '<>'),
            $this->referenceMessageIds
        )));

        return new Headers(
            messageId: $messageId,
            references: $references,
            text: $text,
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.clean-template',
            with: [
                'content' => $this->emailContent,
                'subject' => $this->subject,
                'trackingPixelUrl' => $this->trackingPixelUrl,
                'trackingConfirmUrl' => $this->trackingConfirmUrl,
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

        foreach ($this->fileAttachments as $attachment) {
            $name = $attachment['name'] ?? 'attachment';

            if (! empty($attachment['data'])) {
                $data = $attachment['data'];
                $mime = $attachment['mime'] ?? 'application/octet-stream';

                $attachments[] = Attachment::fromData(fn () => $data, $name)
                    ->withMime($mime);

                continue;
            }

            if (! empty($attachment['path']) && file_exists($attachment['path'])) {
                $attachments[] = Attachment::fromPath($attachment['path'])
                    ->as($name);
            }
        }

        return $attachments;
    }
}
