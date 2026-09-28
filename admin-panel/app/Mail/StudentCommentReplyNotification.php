<?php

namespace App\Mail;

use App\Models\ResponseDiscussion;
use App\Models\Discussion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentCommentReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $response;
    protected $discussion;
    protected $student;
    protected $product;
    protected $admin;
    protected $replyDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(ResponseDiscussion $response)
    {
        $this->response = $response;
        $this->discussion = $response->discussion;
        $this->student = $this->discussion->student;
        $this->product = $this->discussion->product;
        $this->admin = $response->admin;

        // Prepare reply details
        $this->replyDetails = [
            'reply_id' => $response->id,
            'reply_date' => $response->created_at->format('F d, Y H:i'),
            'student_name' => $this->student ? ($this->student->first_name . ' ' . $this->student->last_name) : 'Student',
            'admin_name' => $this->admin ? ($this->admin->first_name . ' ' . $this->admin->last_name) : 'Admin',
            'course_name' => $this->product->titre ?? $this->product->name ?? 'N/A',
            'original_comment' => $this->discussion->commentaire,
            'reply_text' => $response->reponse,
            'discussion_id' => $this->discussion->id,
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reply to Your Comment - ' . ($this->product->titre ?? 'Course'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-comment-reply',
            with: [
                'studentName' => $this->replyDetails['student_name'],
                'course' => $this->replyDetails['course_name'],
                'admin' => $this->replyDetails['admin_name'],
                'date' => $this->replyDetails['reply_date'],
                'originalComment' => $this->replyDetails['original_comment'],
                'reply' => $this->replyDetails['reply_text'],
                'discussionUrl' => config('app.user_url', env('USER_URL')) . '/discussions/' . $this->replyDetails['discussion_id'],
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
