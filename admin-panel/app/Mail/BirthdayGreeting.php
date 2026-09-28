<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class BirthdayGreeting extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $student;
    public $cardVariant;
    public $birthdayImageUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, ?int $cardVariant = null)
    {
        $this->student = $student;

        // Calculate image based on year (cycles every 10 years)
        // 2025 = 1.jpeg, 2026 = 2.jpeg, ..., 2034 = 10.jpeg, 2035 = 1.jpeg, etc.
        $currentYear = date('Y');
        $imageNumber = (($currentYear - 2025) % 10) + 1;

        // Set card variant to the calculated image number
        $this->cardVariant = $cardVariant ?? $imageNumber;

        // Generate the full URL to the birthday greeting image
        $this->birthdayImageUrl = asset("birthdaygreetings/{$this->cardVariant}.jpeg");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Happy Birthday from Upskills Academy!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $coursesCompleted = $this->student->productAccess()->where('is_active', true)->count();
        $memberSinceYear = $this->student->created_at ? $this->student->created_at->format('Y') : date('Y');

        // Estimate learning hours based on courses (assuming ~10 hours per course)
        $learningHours = $coursesCompleted * 10;

        return new Content(
            view: 'emails.birthday-greeting',
            with: [
                'student_name' => $this->getStudentName($this->student),
                'member_since' => $memberSinceYear,
                'courses_completed' => $coursesCompleted,
                'learning_hours' => $learningHours,
                'birthday_image_url' => $this->birthdayImageUrl,
                'card_variant' => $this->cardVariant,
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
