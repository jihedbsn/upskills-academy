<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class CourseExpirationReminder extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $reminderDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($enrollment, $daysRemaining)
    {
        $product = $enrollment->product;

        $this->reminderDetails = [
            'student_name' => $this->getStudentName($enrollment->student),
            'course_name' => $product->current_variation ? $product->current_variation->name : $product->titre,
            'days_remaining' => $daysRemaining,
            'expiration_date' => $enrollment->expiration_date ? $enrollment->expiration_date->format('F d, Y') : 'Lifetime Access',
            'purchase_date' => $enrollment->date,
            'product_id' => $product->id,
            'enrollment_id' => $enrollment->id,
        ];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $days = $this->reminderDetails['days_remaining'];
        $subject = "Course Expiring Soon - {$days} Day" . ($days > 1 ? 's' : '') . " Remaining";

        return $this->subject($subject)
                    ->view('emails.course-expiration-reminder')
                    ->with([
                        'studentName' => $this->reminderDetails['student_name'],
                        'courseName' => $this->reminderDetails['course_name'],
                        'daysRemaining' => $this->reminderDetails['days_remaining'],
                        'expirationDate' => $this->reminderDetails['expiration_date'],
                        'enrollmentDate' => $this->reminderDetails['purchase_date'],
                        'progress' => null,
                        'courseUrl' => url('/courses/' . $this->reminderDetails['product_id']),
                    ]);
    }
}
