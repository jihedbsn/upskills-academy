<?php

namespace App\Mail;

use App\Models\InternalMessage;
use App\Support\StudentFrontendUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InternalMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $internalMessage;
    public $student;
    public $preview;
    public $messageId;

    /** Locale segment for inbox link when mail is queued (avoids worker default locale). */
    public ?string $linkLocale = null;

    /**
     * Create a new message instance.
     *
     * @param  object|null  $student  Row shape from DB query or Student model-like (id, email, …).
     * @param  string|null  $linkLocale  en|ar|fr captured when sending so queued workers use correct URL segment.
     */
    public function __construct($messageId, $student, ?string $linkLocale = null)
    {
        $this->messageId = $messageId;
        $this->student = $student;
        $this->linkLocale = $linkLocale;

        // Load message fresh from database
        $this->internalMessage = InternalMessage::find($messageId);
        $this->preview = $this->internalMessage ? mb_substr(strip_tags($this->internalMessage->body), 0, 100) : '';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Message: ' . ($this->internalMessage->subject ?? ''),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.internal-message-notification',
            with: [
                'inboxUrl' => $this->inboxUrl(),
            ],
        );
    }

    private function inboxUrl(): string
    {
        $locale = $this->linkLocale ?? app()->getLocale();
        $locale = in_array($locale, ['en', 'ar', 'fr'], true) ? $locale : 'en';

        return StudentFrontendUrl::localized($locale, 'student-dashboard/messages');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->internalMessage && !empty($this->internalMessage->attachments)) {
            foreach ($this->internalMessage->attachments as $attachment) {
                $path = storage_path('app/public/' . $attachment['path']);
                if (file_exists($path)) {
                    $attachments[] = Attachment::fromPath($path)
                        ->as($attachment['name']);
                }
            }
        }

        return $attachments;
    }
}
