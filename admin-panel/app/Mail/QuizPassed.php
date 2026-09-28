<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ResultatQuiz;
use App\Services\EmailTemplateService;
use App\Mail\Traits\HandlesStudentName;

class QuizPassed extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $resultatQuiz;

    /**
     * Create a new message instance.
     */
    public function __construct(ResultatQuiz $resultatQuiz)
    {
        $this->resultatQuiz = $resultatQuiz;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $templateService = new EmailTemplateService();
        $template = $templateService->getTemplate('quiz', 'validated');

        if ($template) {
            $variables = $templateService->prepareQuizVariables($this->resultatQuiz);
            $rendered = $templateService->renderTemplate($template, $variables);
            $subject = $rendered['subject'];
        } else {
            // Fallback au comportement original
            $quizType = $this->resultatQuiz->quiz->type->id == 1 ? 'Exam' : 'Quiz';
            $subject = "Congratulations! You passed your {$quizType}";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $templateService = new EmailTemplateService();
        $template = $templateService->getTemplate('quiz', 'validated');

        if ($template) {
            $variables = $templateService->prepareQuizVariables($this->resultatQuiz);
            $rendered = $templateService->renderTemplate($template, $variables);

            return new Content(
                htmlString: $rendered['content']
            );
        } else {
            // Fallback au comportement original
            return new Content(
                view: 'emails.quiz-passed',
                with: [
                    'student' => $this->resultatQuiz->student,
                    'quiz' => $this->resultatQuiz->quiz,
                    'product' => $this->resultatQuiz->product,
                    'score' => $this->resultatQuiz->score,
                    'quizType' => $this->resultatQuiz->quiz->type->id == 1 ? 'Exam' : 'Quiz',
                    'dashboard_url' => config('app.user_url') . '/student-dashboard/courses/' . $this->resultatQuiz->product_id,
                ],
            );
        }
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
