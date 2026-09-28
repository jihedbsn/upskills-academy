<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TachePlanifie;

class ScheduledTask extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $variables;

    /**
     * Create a new message instance.
     */
    public function __construct(TachePlanifie $task, array $variables)
    {
        $this->task = $task;
        $this->variables = $variables;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $timing = $this->variables['timing'];
        $subject = $timing === 'today'
            ? "📅 Rappel: Tâche prévue aujourd'hui - {$this->variables['course_name']}"
            : "📋 Préparation: Tâche prévue demain - {$this->variables['course_name']}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.scheduled-task',
            with: [
                'task' => $this->task,
                'variables' => $this->variables,
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
