<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\TachePlanifie;
use App\Models\Student;
use App\Models\Product;
use App\Mail\DatabaseTemplateEmail;
use App\Models\EmailLog;
use Carbon\Carbon;

class SendScheduledTaskEmailsWithTemplates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting scheduled task email job with database templates');

        try {
            // Récupérer les tâches à envoyer aujourd'hui
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();

            // Tâches pour aujourd'hui (rappel)
            $todayTasks = TachePlanifie::with(['student', 'course'])
                ->where('status', 'pending')
                ->whereDate('date_time', $today)
                ->whereHas('student')
                ->get();

            // Tâches pour demain (préparation)
            $tomorrowTasks = TachePlanifie::with(['student', 'course'])
                ->where('status', 'pending')
                ->whereDate('date_time', $tomorrow)
                ->whereHas('student')
                ->get();

            $emailsSent = 0;

            // Envoyer les emails pour les tâches d'aujourd'hui
            foreach ($todayTasks as $task) {
                $this->sendTaskEmailWithTemplate($task, 'today');
                $emailsSent++;
            }

            // Envoyer les emails pour les tâches de demain
            foreach ($tomorrowTasks as $task) {
                $this->sendTaskEmailWithTemplate($task, 'tomorrow');
                $emailsSent++;
            }

            Log::info("Scheduled task email job with templates completed. Emails sent: {$emailsSent}");

        } catch (\Exception $e) {
            Log::error("Error in scheduled task email job with templates: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoyer un email pour une tâche spécifique en utilisant les templates de la DB
     */
    private function sendTaskEmailWithTemplate(TachePlanifie $task, string $timing): void
    {
        try {
            if (!$task->student || !$task->student->email) {
                Log::warning("Task {$task->id} has no student or email");
                return;
            }

            // Préparer les variables pour le template
            $variables = [
                'student_name' => $task->student->first_name . ' ' . $task->student->last_name,
                'course_name' => $task->course ? $task->course->titre : 'Cours non spécifié',
                'task_message' => $task->message,
                'task_date' => $task->date_time->format('d/m/Y'),
                'task_time' => $task->date_time->format('H:i'),
                'task_priority' => $this->getPriorityLabel($task->priority),
            ];

            // Déterminer le type et statut du template
            $templateType = 'student';
            $templateStatus = $timing === 'today' ? 'task_reminder_today' : 'task_preparation_tomorrow';

            // Envoyer l'email en utilisant le système de templates existant
            Mail::to($task->student->email)
                ->send(new DatabaseTemplateEmail(
                    $templateType,
                    $templateStatus,
                    $variables,
                    $timing === 'today' ? 'Rappel de tâche' : 'Préparation de tâche'
                ));

            Log::info("Email sent to {$task->student->email} for task {$task->id} ({$timing}) using template");

            EmailLog::logSent(
                $task->student->email,
                'scheduled_task',
                ($timing === 'today' ? 'Task Reminder Today: ' : 'Task Preparation Tomorrow: ') . ($task->course ? $task->course->titre : 'N/A'),
                $task->student->id,
                $task->student->first_name . ' ' . $task->student->last_name,
                'TachePlanifie',
                $task->id
            );

        } catch (\Exception $e) {
            Log::error("Failed to send email for task {$task->id}: " . $e->getMessage());

            EmailLog::logFailed(
                $task->student->email ?? 'unknown',
                'scheduled_task',
                ($timing === 'today' ? 'Task Reminder Today: ' : 'Task Preparation Tomorrow: ') . ($task->course ? $task->course->titre : 'N/A'),
                $e->getMessage(),
                $task->student->id ?? null,
                ($task->student->first_name ?? '') . ' ' . ($task->student->last_name ?? ''),
                'TachePlanifie',
                $task->id
            );
        }
    }

    /**
     * Obtenir le libellé de priorité
     */
    private function getPriorityLabel(string $priority): string
    {
        return match($priority) {
            'high' => 'Haute',
            'medium' => 'Moyenne',
            'low' => 'Basse',
            default => 'Moyenne'
        };
    }
}
