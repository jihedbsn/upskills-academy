<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminTicketResponse extends Mailable
{
    use Queueable, SerializesModels;

    public $ticketDetails;
    public $adminMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, string $message)
    {
        $this->ticketDetails = [
            'ticket_id' => $ticket->id,
            'subject' => $ticket->sujet,
            'student_name' => $ticket->student->first_name . ' ' . $ticket->student->last_name,
            'student_email' => $ticket->student->email,
            'created_at' => $ticket->created_at->format('F j, Y g:i A'),
            'status' => $ticket->ticket_iscomplet ? 'Resolved' : 'Open',
        ];

        $this->adminMessage = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Response to Your Support Ticket #' . $this->ticketDetails['ticket_id'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-ticket-response',
            with: [
                'ticketId' => $this->ticketDetails['ticket_id'],
                'subject' => $this->ticketDetails['subject'],
                'status' => $this->ticketDetails['status'],
                'createdAt' => $this->ticketDetails['created_at'],
                'studentName' => $this->ticketDetails['student_name'],
                'adminMessage' => $this->adminMessage,
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
