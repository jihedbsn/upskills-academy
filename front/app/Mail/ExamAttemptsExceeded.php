<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Product;
use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class ExamAttemptsExceeded extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $student;
    public $product;
    public $quiz;
    public $attemptsMade;
    public $renewalPrice;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, Product $product, Quiz $quiz, int $attemptsMade)
    {
        $this->student = $student;
        $this->product = $product;
        $this->quiz = $quiz;
        $this->attemptsMade = $attemptsMade;
        $this->renewalPrice = $product->renewal_price ?? 50.00;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Exam Attempts Limit Reached - Renewal Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-attempts-exceeded',
            with: [
                'student_name' => $this->getStudentName($this->student),
                'product_name' => $this->product->titre ?? 'Course',
                'quiz_name' => $this->quiz->titre ?? 'Exam',
                'attempts_made' => $this->attemptsMade,
                'renewal_price' => number_format($this->renewalPrice, 2),
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
