<?php

namespace App\Mail;

use App\Models\ZoomMeeting;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ZoomMeetingUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $meeting;
    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct($meeting, Student $student)
    {
        $this->meeting = $meeting;
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $topic = is_object($this->meeting) ? $this->meeting->topic : $this->meeting['topic'];
        return new Envelope(
            subject: 'Zoom Meeting Updated: ' . $topic,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.zoom-meeting-updated',
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
