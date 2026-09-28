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
use App\Models\EmailLog;
use App\Mail\ScheduledTask;
use Carbon\Carbon;

class SendScheduledTaskEmails implements ShouldQueue
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
        Log::info('Starting scheduled task email job');

        try {
            // Récupérer les tâches à envoyer aujourd'hui
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();

            // Tâches pour aujourd'hui (rappel)
            $todayTasks = TachePlanifie::with(['student', 'course'])
                ->where('status', 'pending')
                ->whereDate('date_time', $today)
                ->whereHas('student') // Seulement les tâches avec des étudiants
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
                $this->sendTaskEmail($task, 'today');
                $emailsSent++;
            }

            // Envoyer les emails pour les tâches de demain
            foreach ($tomorrowTasks as $task) {
                $this->sendTaskEmail($task, 'tomorrow');
                $emailsSent++;
            }

            Log::info("Scheduled task email job completed. Emails sent: {$emailsSent}");

        } catch (\Exception $e) {
            Log::error("Error in scheduled task email job: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoyer un email pour une tâche spécifique
     */
    private function sendTaskEmail(TachePlanifie $task, string $timing): void
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
                'timing' => $timing,
                'timing_label' => $timing === 'today' ? 'aujourd\'hui' : 'demain'
            ];

            // Envoyer l'email
            Mail::to($task->student->email)
                ->send(new ScheduledTask($task, $variables));

            EmailLog::logSent(
                $task->student->email,
                'scheduled_task',
                'Scheduled Task: ' . ($task->course ? $task->course->titre : 'N/A'),
                $task->student->id,
                $task->student->first_name . ' ' . $task->student->last_name,
                'TachePlanifie',
                $task->id
            );

            Log::info("Email sent to {$task->student->email} for task {$task->id} ({$timing})");

        } catch (\Exception $e) {
            Log::error("Failed to send email for task {$task->id}: " . $e->getMessage());

            EmailLog::logFailed(
                $task->student->email ?? 'unknown',
                'scheduled_task',
                'Scheduled Task: ' . ($task->course ? $task->course->titre : 'N/A'),
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
