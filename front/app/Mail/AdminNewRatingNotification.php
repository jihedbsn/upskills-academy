<?php

namespace App\Mail;

use App\Models\CourseRating;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewRatingNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $rating;
    protected $student;
    protected $product;
    protected $ratingDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(CourseRating $rating)
    {
        $this->rating = $rating;
        $this->student = $rating->student;
        $this->product = $rating->product;

        // Prepare rating details
        $this->ratingDetails = [
            'rating_id' => $rating->id,
            'rating_date' => $rating->created_at->format('F d, Y H:i'),
            'student_name' => $this->student->first_name . ' ' . $this->student->last_name,
            'student_email' => $this->student->email,
            'course_name' => $this->product->titre ?? $this->product->name ?? 'N/A',
            'rating_value' => $rating->rating,
            'comment_text' => $rating->commentaire ?? '',
            'is_approved' => $rating->is_approved ?? false,
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Rating - ' . ($this->product->titre ?? 'Course'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-rating',
            with: [
                'courseName' => $this->ratingDetails['course_name'],
                'studentName' => $this->ratingDetails['student_name'],
                'rating' => $this->ratingDetails['rating_value'],
                'date' => $this->ratingDetails['rating_date'],
                'status' => $this->ratingDetails['is_approved'] ? 'Approved (auto)' : 'Disabled',
                'comment' => $this->ratingDetails['comment_text'],
                'reviewUrl' => config('app.admin_url') . '/course-ratings/' . $this->ratingDetails['rating_id'],
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
