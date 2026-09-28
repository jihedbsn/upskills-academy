<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;
use App\Models\Product;

class StudentCourseEnrollment extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $product;
    public $enrollmentDate;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, Product $product, $enrollmentDate = null)
    {
        $this->student = $student;
        $this->product = $product;
        $this->enrollmentDate = $enrollmentDate ?? now();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $courseName = $this->product->name_en ?? $this->product->name_ar ?? 'Course #' . $this->product->id;

        return new Envelope(
            subject: 'Course Enrollment Confirmation - ' . $courseName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-course-enrollment',
            with: [
                'student_name' => $this->student->first_name . ' ' . $this->student->last_name,
                'student_email' => $this->student->email,
                'course_name' => $this->product->name_en ?? $this->product->name_ar ?? 'Course #' . $this->product->id,
                'enrollment_date' => $this->enrollmentDate->format('F j, Y'),
                'course_url' => config('app.user_url') . '/student-dashboard/courses/' . $this->product->id,
                'website_url' => config('app.user_url'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
