<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\File;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lire les templates existants depuis les fichiers
        $templates = [
            [
                'name' => 'quiz_passed_notification',
                'type' => 'quiz',
                'status' => 'validated',
                'subject' => 'Congratulations! You passed your {{quizType}}',
                'description' => 'Email sent when a student passes a quiz or exam',
                'variables' => ['student_name', 'student_first_name', 'quiz_name', 'course_name', 'score', 'quizType'],
                'file_path' => 'resources/views/emails/quiz-passed.blade.php'
            ],
            [
                'name' => 'stage_validated_notification',
                'type' => 'stage',
                'status' => 'validated',
                'subject' => 'Congratulations! Your Internship has been Validated',
                'description' => 'Email sent when an internship submission is validated',
                'variables' => ['student_name', 'student_first_name', 'course_name', 'submission_date', 'validation_date', 'admin_notes'],
                'file_path' => 'resources/views/emails/stage-validated.blade.php'
            ],
            [
                'name' => 'stage_rejected_notification',
                'type' => 'stage',
                'status' => 'rejected',
                'subject' => 'Your Internship Submission Requires Revision',
                'description' => 'Email sent when an internship submission needs revision',
                'variables' => ['student_name', 'student_first_name', 'course_name', 'submission_date', 'admin_notes'],
                'file_path' => 'resources/views/emails/stage-rejected.blade.php'
            ],
            [
                'name' => 'video_exam_validated_notification',
                'type' => 'video_exam',
                'status' => 'validated',
                'subject' => 'Congratulations! Your Video Exam has been Validated',
                'description' => 'Email sent when a video exam is validated',
                'variables' => ['student_name', 'student_first_name', 'course_name', 'submission_date', 'validation_date', 'video_link', 'video_description'],
                'file_path' => 'resources/views/emails/video-exam-validated.blade.php'
            ],
            [
                'name' => 'video_exam_rejected_notification',
                'type' => 'video_exam',
                'status' => 'rejected',
                'subject' => 'Your Video Exam Submission Requires Revision',
                'description' => 'Email sent when a video exam needs revision',
                'variables' => ['student_name', 'student_first_name', 'course_name', 'submission_date', 'video_link', 'video_description'],
                'file_path' => 'resources/views/emails/video-exam-rejected.blade.php'
            ],
            [
                'name' => 'student_success_approved_notification',
                'type' => 'student_success',
                'status' => 'approved',
                'subject' => 'Congratulations! Your Final Success has been Approved',
                'description' => 'Email sent when final student success is approved',
                'variables' => ['student_name', 'student_first_name', 'course_name', 'submission_date', 'validation_date', 'admin_notes', 'video_link'],
                'file_path' => 'resources/views/emails/student-success-approved.blade.php'
            ],
            [
                'name' => 'student_success_rejected_notification',
                'type' => 'student_success',
                'status' => 'requirements',
                'subject' => 'Additional Requirements for Course Completion',
                'description' => 'Email sent when additional requirements are needed for course completion',
                'variables' => ['student_name', 'student_first_name', 'course_name', 'submission_date', 'admin_notes', 'video_link'],
                'file_path' => 'resources/views/emails/student-success-rejected.blade.php'
            ],
            [
                'name' => 'clean_template_notification',
                'type' => 'custom',
                'status' => 'custom',
                'subject' => '{{subject}}',
                'description' => 'Clean template for custom emails',
                'variables' => ['student_name', 'subject', 'content'],
                'file_path' => 'resources/views/emails/clean-template.blade.php'
            ],
            [
                'name' => 'student_account_created_notification',
                'type' => 'student',
                'status' => 'account_created',
                'subject' => 'Welcome to Swedish Academy of Sport Training - Your Account Details',
                'description' => 'Email sent to new students with their login credentials',
                'variables' => ['student_name', 'student_email', 'student_password', 'course_name', 'login_url', 'website_url'],
                'file_path' => 'resources/views/emails/student-account-created.blade.php'
            ],
            [
                'name' => 'student_course_enrollment_notification',
                'type' => 'student',
                'status' => 'course_enrolled',
                'subject' => 'Course Enrollment Confirmation - {{course_name}}',
                'description' => 'Email sent to students when they are enrolled in a course',
                'variables' => ['student_name', 'student_email', 'course_name', 'enrollment_date', 'course_url', 'website_url'],
                'file_path' => 'resources/views/emails/student-course-enrollment.blade.php'
            ]
        ];

        foreach ($templates as $templateData) {
            // Vérifier si le template existe déjà
            $existingTemplate = EmailTemplate::where('name', $templateData['name'])->first();
            
            if (!$existingTemplate) {
                // Lire le contenu du fichier
                $filePath = base_path($templateData['file_path']);
                $content = '';
                
                if (File::exists($filePath)) {
                    $content = File::get($filePath);
                } else {
                    // Contenu par défaut si le fichier n'existe pas
                    $content = $this->getDefaultTemplate($templateData['type'], $templateData['status']);
                }

                EmailTemplate::create([
                    'name' => $templateData['name'],
                    'type' => $templateData['type'],
                    'status' => $templateData['status'],
                    'subject' => $templateData['subject'],
                    'content' => $content,
                    'variables' => $templateData['variables'],
                    'description' => $templateData['description'],
                    'is_active' => true
                ]);

                $this->command->info("Template '{$templateData['name']}' créé avec succès.");
            } else {
                $this->command->info("Template '{$templateData['name']}' existe déjà, ignoré.");
            }
        }
    }

    /**
     * Obtenir un template par défaut
     */
    private function getDefaultTemplate($type, $status): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e5e5e5;
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            padding: 25px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content-section {
            padding: 30px 25px;
            color: #333;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 20px 25px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Hello {{student_name}}!</h1>
            <p>Swedish Academy of Sport Training</p>
        </div>
        <div class="content-section">
            <p>Dear {{student_name}},</p>
            <p>This is a notification regarding your {{course_name}}.</p>
            <p>Best regards,<br>The Swedish Academy of Sport Training Team</p>
        </div>
        <div class="footer">
            © ' . date('Y') . ' Swedish Academy of Sport Training. All rights reserved.
        </div>
    </div>
</body>
</html>';
    }
}