<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\PracticalExamAttempt;

class PracticalExamStarted extends Mailable
{
    use Queueable, SerializesModels;

    public $attempt;

    /**
     * Create a new message instance.
     */
    public function __construct(PracticalExamAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Practical Exam Has Started',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $student = $this->attempt->user;
        $product = $this->attempt->product;
        $trainingCase = $this->attempt->trainingCase;

        return new Content(
            view: 'emails.practical-exam-started',
            with: [
                'student' => $student,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'course_name' => $product->titre ?? 'Course',
                'training_case_name' => $trainingCase->title ?? $trainingCase->name ?? 'Training Case',
                'attempt_number' => $this->attempt->attempt_number,
                'exam_type' => $product->practical_exam_type,
                'exam_url' => route('student.practical-exam.show', ['locale' => app()->getLocale(), 'product' => $product->id]),
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
        $attachments = [];

        // Attach the training case file if it exists (stored in admin project)
        if ($this->attempt->trainingCaseFile && $this->attempt->trainingCaseFile->file_path) {
            // Files are in admin storage, need to use admin path
            $adminStoragePath = dirname(storage_path()) . '/../admin/storage/app/public/' . $this->attempt->trainingCaseFile->file_path;

            if (file_exists($adminStoragePath)) {
                $fileName = $this->attempt->trainingCaseFile->file_name ?? basename($this->attempt->trainingCaseFile->file_path);

                $attachments[] = Attachment::fromPath($adminStoragePath)
                    ->as($fileName);
            }
        }

        return $attachments;
    }
}
