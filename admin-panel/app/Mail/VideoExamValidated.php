<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentVideoExam;
use App\Mail\Traits\HandlesStudentName;

class VideoExamValidated extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $studentVideoExam;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentVideoExam $studentVideoExam)
    {
        $this->studentVideoExam = $studentVideoExam;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Video Exam has been Validated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.video-exam-validated',
            with: [
                'student' => $this->studentVideoExam->student,
                'student_name' => $this->getStudentName($this->studentVideoExam->student),
                'course_name' => ($this->studentVideoExam->product && $this->studentVideoExam->product->current_variation) ? $this->studentVideoExam->product->current_variation->name : ($this->studentVideoExam->product->titre ?? 'Course'),
                'video_exam_title' => 'Video Exam Submission',
                'validation_date' => $this->studentVideoExam->updated_at ? $this->studentVideoExam->updated_at->format('F d, Y') : now()->format('F d, Y'),
                'product' => $this->studentVideoExam->product,
                'videoExam' => $this->studentVideoExam,
                'dashboard_url' => config('app.user_url') . '/student-dashboard/courses/' . $this->studentVideoExam->product_id,
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
