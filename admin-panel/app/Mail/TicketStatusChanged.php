<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->newStatus = $ticket->ticket_iscomplet ? 'resolved' : 'reopened';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = $this->newStatus === 'resolved' ? 'Resolved' : 'Reopened';
        
        return new Envelope(
            subject: "Support Ticket #{$this->ticket->id} - Status Updated: {$statusText}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-status-changed',
            with: [
                'ticketId' => $this->ticket->id,
                'subject' => $this->ticket->sujet,
                'studentName' => $this->ticket->student->first_name . ' ' . $this->ticket->student->last_name,
                'newStatus' => $this->newStatus,
                'isResolved' => $this->ticket->ticket_iscomplet,
                'createdAt' => $this->ticket->created_at->format('F j, Y'),
                'updatedAt' => $this->ticket->updated_at->format('F j, Y g:i A'),
                'ticketUrl' => config('app.user_url') . '/student-dashboard/support',
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
