<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class CourseExpired extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $courseDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($enrollment)
    {
        $product = $enrollment->product;

        $this->courseDetails = [
            'student_name' => $this->getStudentName($enrollment->student),
            'course_name' => $product->current_variation ? $product->current_variation->name : $product->titre,
            'expired_date' => $enrollment->expiration_date ? $enrollment->expiration_date->format('Y-m-d') : now()->format('Y-m-d'),
            'purchase_date' => $enrollment->date,
            'extension_price' => 50, // $50 extension option
            'enrollment_id' => $enrollment->id,
            'product_id' => $product->id,
        ];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Course Access Expired - Extension Available')
                    ->view('emails.course-expired')
                    ->with([
                        'studentName' => $this->courseDetails['student_name'],
                        'courseName' => $this->courseDetails['course_name'],
                        'expirationDate' => $this->courseDetails['expired_date'],
                        'enrollmentDate' => $this->courseDetails['purchase_date'],
                        'progress' => null,
                        'extensionAvailable' => true,
                        'extensionPeriod' => '30 days',
                        'extensionPrice' => $this->courseDetails['extension_price'],
                        'extensionUrl' => url('/courses/' . $this->courseDetails['product_id'] . '/extend'),
                    ]);
    }
}
