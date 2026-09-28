<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewTicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ticketDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket)
    {
        // Ensure relationships are loaded
        $ticket->load(['student', 'responses']);

        $this->ticketDetails = [
            'ticket_id' => $ticket->id,
            'subject' => $ticket->sujet,
            'student_name' => ($ticket->student->first_name ?? '') . ' ' . ($ticket->student->last_name ?? ''),
            'student_email' => $ticket->student->email ?? 'N/A',
            'first_message' => $ticket->responses->first()->message ?? 'No message',
            'created_at' => $ticket->created_at->format('F j, Y g:i A'),
            'status' => $ticket->ticket_iscomplet ? 'Closed' : 'Open',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Support Ticket #' . $this->ticketDetails['ticket_id'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-ticket',
            with: [
                'ticketId' => $this->ticketDetails['ticket_id'],
                'subject' => $this->ticketDetails['subject'],
                'priority' => 'Normal',
                'status' => $this->ticketDetails['status'],
                'createdAt' => $this->ticketDetails['created_at'],
                'studentName' => $this->ticketDetails['student_name'],
                'studentEmail' => $this->ticketDetails['student_email'],
                'studentPhone' => null,
                'ticketMessage' => $this->ticketDetails['first_message'],
                'ticketUrl' => config('app.admin_url') . '/support-tickets/' . $this->ticketDetails['ticket_id'],
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
