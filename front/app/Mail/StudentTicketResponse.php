<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class StudentTicketResponse extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $ticketDetails;
    public $studentMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, string $message)
    {
        $this->ticketDetails = [
            'ticket_id' => $ticket->id,
            'subject' => $ticket->sujet,
            'student_name' => $this->getStudentName($ticket->student),
            'student_email' => $ticket->student->email,
            'created_at' => $ticket->created_at->format('F j, Y g:i A'),
            'status' => $ticket->ticket_iscomplet ? 'Resolved' : 'Open',
        ];
        
        $this->studentMessage = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Reply to Support Ticket #' . $this->ticketDetails['ticket_id'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-ticket-response',
            with: [
                'ticketId' => $this->ticketDetails['ticket_id'],
                'subject' => $this->ticketDetails['subject'],
                'status' => $this->ticketDetails['status'],
                'createdAt' => $this->ticketDetails['created_at'],
                'studentName' => $this->ticketDetails['student_name'],
                'studentEmail' => $this->ticketDetails['student_email'],
                'studentMessage' => $this->studentMessage,
                'ticketUrl' => env('ADMIN_APP_URL', 'http://localhost:8100') . '/admin/support-tickets/' . $this->ticketDetails['ticket_id'],
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
