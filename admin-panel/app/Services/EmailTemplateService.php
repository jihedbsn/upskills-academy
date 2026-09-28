<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmailTemplateService
{
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('type')->orderBy('status')->paginate(15);

        return view('email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('email-templates.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:email_templates,name',
            'type' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::TYPES)),
            'status' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::STATUSES)),
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'text_content' => 'nullable|string',
            'description' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $content = $request->input('content');
            EmailTemplate::create([
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'subject' => $request->subject,
                'content' => $content,
                'description' => $request->description,
                'variables' => $request->variables ?? [],
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('email-templates.index')
                ->with('success', 'Email template created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error creating template: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return view('email-templates.show', compact('emailTemplate'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
            'type' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::TYPES)),
            'status' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::STATUSES)),
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'text_content' => 'nullable|string',
            'description' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $content = $request->input('content');
            $emailTemplate->update([
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'subject' => $request->subject,
                'content' => $content,
                'description' => $request->description,
                'variables' => $request->variables ?? [],
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('email-templates.index')
                ->with('success', 'Email template updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error updating template: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        try {
            $emailTemplate->delete();
            return redirect()->route('email-templates.index')
                ->with('success', 'Email template deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error deleting template: ' . $e->getMessage()]);
        }
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        $sampleData = [
            'student_name' => 'John Doe',
            'student_first_name' => 'John',
            'course_name' => 'English Teaching Certification',
            'quiz_name' => 'Final Assessment',
            'score' => '85.5',
            'submission_date' => now()->format('m/d/Y'),
            'validation_date' => now()->format('m/d/Y'),
            'admin_notes' => 'Excellent work! Keep up the good effort.',
            'video_link' => 'https://example.com/video',
            'video_description' => 'Demonstration of advanced techniques'
        ];

        $renderedContent = $emailTemplate->renderContent($sampleData);
        $renderedSubject = $emailTemplate->renderSubject($sampleData);

        return view('email-templates.preview', compact('emailTemplate', 'renderedContent', 'renderedSubject'));
    }

    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        try {
            $emailTemplate->update(['is_active' => !$emailTemplate->is_active]);

            $status = $emailTemplate->is_active ? 'activated' : 'deactivated';
            return redirect()->back()
                ->with('success', "Template {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error updating template status: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtenir un template par type et statut
     */
    public function getTemplate(string $type, string $status): ?EmailTemplate
    {
        try {
            return EmailTemplate::active()
                ->where('type', $type)
                ->where('status', $status)
                ->first();
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération du template: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Rendre le contenu d'un template avec les variables
     */
    public function renderTemplate(EmailTemplate $template, array $variables = []): array
    {
        try {
            $renderedSubject = $this->replaceVariables($template->subject, $variables);

            // Si le template a un contenu texte personnalisé, l'utiliser
            if ($template->text_content) {
                $renderedContent = $this->renderTemplateWithCustomText($template, $variables);
            } else {
                $renderedContent = $this->replaceVariables($template->content, $variables);
            }

            return [
                'subject' => $renderedSubject,
                'content' => $renderedContent
            ];
        } catch (\Exception $e) {
            Log::error("Erreur lors du rendu du template: {$e->getMessage()}");
            return [
                'subject' => 'Email Notification',
                'content' => $this->getFallbackContent($variables)
            ];
        }
    }

    /**
     * Rendre le template HTML avec le contenu texte personnalisé
     */
    public function renderTemplateWithCustomText(EmailTemplate $template, array $variables): string
    {
        $htmlContent = $template->content;
        $textContent = $template->text_content;

        // Remplacer les variables dans le contenu HTML
        $htmlContent = $this->replaceVariables($htmlContent, $variables);

        // Intégrer le contenu texte personnalisé dans le HTML
        // Pour l'instant, on utilise une approche simple
        // Dans une version plus avancée, on pourrait mapper des zones spécifiques

        return $htmlContent;
    }

    /**
     * Remplacer les variables dans le texte
     */
    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Convertir la valeur en string si ce n'est pas déjà le cas
            $stringValue = is_object($value) ? (string) $value : $value;
            $text = str_replace('{{' . $key . '}}', $stringValue, $text);
        }

        return $text;
    }

    /**
     * Obtenir un contenu de fallback en cas d'erreur
     */
    private function getFallbackContent(array $variables): string
    {
        $studentName = $variables['student_name'] ?? $variables['student_first_name'] ?? 'Student';
        $courseName = $variables['course_name'] ?? 'Course';

        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Notification</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; margin: -20px -20px 20px -20px; }
                .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; text-align: center; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Upskills Academy</h1>
                </div>
                <p>Dear ' . $studentName . ',</p>
                <p>This is a notification regarding your progress in ' . $courseName . '.</p>
                <p>Best regards,<br>The Upskills Academy Team</p>
                <div class="footer">
                    © ' . date('Y') . ' Upskills Academy. All rights reserved.
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Préparer les variables communes pour tous les types d'emails
     */
    public function prepareCommonVariables($student, $product, $additionalData = []): array
    {
        $variables = [
            'student_name' => $student->first_name . ' ' . $student->last_name,
            'student_first_name' => $student->first_name,
            'course_name' => $product->name_en ?? $product->name_ar ?? 'Course',
            'submission_date' => now()->format('m/d/Y'),
            'validation_date' => now()->format('m/d/Y'),
        ];

        // Fusionner avec les données additionnelles
        return array_merge($variables, $additionalData);
    }

    /**
     * Préparer les variables pour les quiz
     */
    public function prepareQuizVariables($resultatQuiz): array
    {
        $quizType = $resultatQuiz->quiz->type->id == 1 ? 'Exam' : 'Quiz';

        return $this->prepareCommonVariables(
            $resultatQuiz->student,
            $resultatQuiz->product,
            [
                'quiz_name' => $resultatQuiz->quiz->name_en ?? $resultatQuiz->quiz->name_ar ?? 'Quiz',
                'score' => number_format($resultatQuiz->score, 1),
                'quizType' => $quizType,
            ]
        );
    }

    /**
     * Préparer les variables pour les stages
     */
    public function prepareStageVariables($studentStageCourse): array
    {
        return $this->prepareCommonVariables(
            $studentStageCourse->student,
            $studentStageCourse->product,
            [
                'submission_date' => $studentStageCourse->submitted_at ? $studentStageCourse->submitted_at->format('m/d/Y') : 'N/A',
                'validation_date' => $studentStageCourse->validated_at ? $studentStageCourse->validated_at->format('m/d/Y') : now()->format('m/d/Y'),
                'admin_notes' => $studentStageCourse->admin_notes ?? '',
            ]
        );
    }

    /**
     * Préparer les variables pour les examens vidéo
     */
    public function prepareVideoExamVariables($studentVideoExam): array
    {
        return $this->prepareCommonVariables(
            $studentVideoExam->student,
            $studentVideoExam->product,
            [
                'submission_date' => $studentVideoExam->submitted_at ? $studentVideoExam->submitted_at->format('m/d/Y') : 'N/A',
                'video_link' => $studentVideoExam->lien ?? '',
                'video_description' => $studentVideoExam->video_description ?? '',
            ]
        );
    }

    /**
     * Préparer les variables pour les succès étudiants
     */
    public function prepareStudentSuccessVariables($studentSuccess): array
    {
        return $this->prepareCommonVariables(
            $studentSuccess->student,
            $studentSuccess->product,
            [
                'submission_date' => $studentSuccess->submitted_at ? $studentSuccess->submitted_at->format('m/d/Y') : 'N/A',
                'validation_date' => $studentSuccess->validated_at ? $studentSuccess->validated_at->format('m/d/Y') : now()->format('m/d/Y'),
                'admin_notes' => $studentSuccess->admin_notes ?? '',
                'video_link' => $studentSuccess->lien_video ?? '',
            ]
        );
    }
}
