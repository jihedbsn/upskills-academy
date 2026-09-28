<?php

namespace App\Mail;

use App\Models\Discussion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewCommentNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $discussion;
    protected $student;
    protected $product;
    protected $commentDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(Discussion $discussion)
    {
        $this->discussion = $discussion;
        $this->student = $discussion->student;
        $this->product = $discussion->product;

        // Prepare comment details
        $this->commentDetails = [
            'comment_id' => $discussion->id,
            'comment_date' => $discussion->created_at->format('F d, Y H:i'),
            'student_name' => $this->student ? ($this->student->first_name . ' ' . $this->student->last_name) : 'Unknown Student',
            'student_email' => $this->student->email ?? 'N/A',
            'course_name' => $this->product->titre ?? $this->product->name ?? 'N/A',
            'comment_text' => $discussion->commentaire,
            'is_approved' => $discussion->is_approved ?? false,
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Comment - ' . ($this->product->titre ?? 'Course'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-comment',
            with: [
                'course' => $this->commentDetails['course_name'],
                'student' => $this->commentDetails['student_name'],
                'date' => $this->commentDetails['comment_date'],
                'status' => $this->commentDetails['is_approved'] ? 'Approved' : 'Pending Review',
                'comment' => $this->commentDetails['comment_text'],
                'reviewUrl' => config('app.admin_url') . '/discussions/' . $this->commentDetails['comment_id'],
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
