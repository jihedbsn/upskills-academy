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
use App\Mail\Traits\HandlesStudentName;

class StudentAccountCreated extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $student;
    public $product;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, Product $product, string $password)
    {
        $this->student = $student;
        $this->product = $product;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Upskills Academy - Your Account Details',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-account-created',
            with: [
                'student_name' => $this->getStudentName($this->student),
                'student_email' => $this->student->email,
                'student_password' => $this->password,
                'course_name' => $this->product->name_en ?? $this->product->name_ar ?? 'Course #' . $this->product->id,
                'login_url' => config('app.user_url') . '/login',
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
