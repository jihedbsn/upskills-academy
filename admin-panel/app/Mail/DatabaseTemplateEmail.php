<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;
use App\Models\EmailTemplate;

class DatabaseTemplateEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $templateType;
    protected $templateStatus;
    protected $variables;
    protected $fallbackSubject;

    /**
     * Create a new message instance.
     */
    public function __construct(string $templateType, string $templateStatus, array $variables = [], string $fallbackSubject = 'Notification')
    {
        $this->templateType = $templateType;
        $this->templateStatus = $templateStatus;
        $this->variables = $variables;
        $this->fallbackSubject = $fallbackSubject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $templateService = new EmailTemplateService();
        $template = $templateService->getTemplate($this->templateType, $this->templateStatus);
        
        if ($template) {
            $rendered = $templateService->renderTemplate($template, $this->variables);
            $subject = $rendered['subject'];
        } else {
            $subject = $this->fallbackSubject;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $templateService = new EmailTemplateService();
        $template = $templateService->getTemplate($this->templateType, $this->templateStatus);
        
        if ($template) {
            $rendered = $templateService->renderTemplate($template, $this->variables);
            
            return new Content(
                htmlString: $rendered['content']
            );
        } else {
            // Fallback vers une vue simple
            return new Content(
                view: 'emails.fallback',
                with: $this->variables
            );
        }
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}