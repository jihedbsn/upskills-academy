<?php

namespace App\Mail;

use App\Models\ZoomMeeting;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ZoomMeetingFollowUp extends Mailable
{
    use Queueable, SerializesModels;

    public $meeting;
    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct(ZoomMeeting $meeting, Student $student)
    {
        $this->meeting = $meeting;
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zoom Meeting Follow-Up: ' . $this->meeting->topic,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.zoom-meeting-followup',
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
