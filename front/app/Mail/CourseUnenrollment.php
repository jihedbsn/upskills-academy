<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class CourseUnenrollment extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $student;
    public $product;
    public $enrollment;
    public $courseName;
    public $expirationDate;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $product, $enrollment)
    {
        $this->student = $student;
        $this->product = $product;
        $this->enrollment = $enrollment;
        $this->courseName = $product->current_variation
            ? $product->current_variation->name
            : $product->titre;
        $this->expirationDate = $enrollment->expiration_date
            ? $enrollment->expiration_date->format('Y-m-d')
            : now()->format('Y-m-d');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Course Unenrollment Notice - ' . $this->courseName)
                    ->view('emails.course-unenrollment')
                    ->with([
                        'studentName' => $this->getStudentName($this->student),
                        'courseName' => $this->courseName,
                        'expirationDate' => $this->expirationDate,
                        'enrollmentDate' => $this->enrollment->date,
                        'gracePeriodDays' => 30,
                        'supportEmail' => config('mail.from.address', 'support@up-skills.academy'),
                    ]);
    }
}
