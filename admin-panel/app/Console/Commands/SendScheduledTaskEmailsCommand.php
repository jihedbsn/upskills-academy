<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\TachePlanifie;
use App\Models\Student;
use App\Models\Product;
use App\Services\OutboundEmailLogger;
use Carbon\Carbon;

class SendScheduledTaskEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-emails {--test : Run in test mode without actually sending emails} {--limit=100 : Maximum number of emails to send per execution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled task reminder emails to students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== LARAVEL EMAIL SCHEDULER STARTED ===');
        $this->info('Starting scheduled task email process...');
        
        Log::info('=== LARAVEL EMAIL SCHEDULER STARTED ===');
        Log::info('Starting scheduled task email command at ' . now()->format('Y-m-d H:i:s'));

        try {
            // Récupérer toutes les tâches non envoyées avec limite
            $limit = $this->option('limit');
            
            $tasksToSend = TachePlanifie::with(['student', 'course'])
                ->whereNull('is_send') // Seulement les tâches non envoyées
                ->whereHas('student', function($query) {
                    $query->whereNotNull('email'); // Seulement si l'étudiant a un email
                })
                ->orderBy('date_time', 'asc') // Par ordre chronologique
                ->limit($limit)
                ->get();

            $emailsSent = 0;

            $this->info("Found {$tasksToSend->count()} tasks to send emails for");
            $this->info("Current time: " . now()->format('Y-m-d H:i:s'));
            
            Log::info("Found {$tasksToSend->count()} tasks to send emails for");
            Log::info("Current time: " . now()->format('Y-m-d H:i:s'));
            
            if ($tasksToSend->count() > 0) {
                $this->info("Tasks found:");
                Log::info("Tasks found:");
                foreach ($tasksToSend as $task) {
                    $this->line("  - {$task->student->first_name} {$task->student->last_name} - {$task->message} ({$task->date_time->format('d/m/Y H:i')})");
                    Log::info("  - {$task->student->first_name} {$task->student->last_name} - {$task->message} ({$task->date_time->format('d/m/Y H:i')})");
                }
            } else {
                $this->info("No tasks found to send emails for");
                Log::info("No tasks found to send emails for");
            }

            if ($this->option('test')) {
                $this->info('Running in TEST mode - no emails will be sent');
                
                foreach ($tasksToSend as $task) {
                    $this->line("  - {$task->student->first_name} {$task->student->last_name} - {$task->message} ({$task->date_time->format('d/m/Y H:i')})");
                }
            } else {
                $this->info('Running in PRODUCTION mode - emails will be sent');
                
                // Envoyer les emails pour toutes les tâches non envoyées
                foreach ($tasksToSend as $task) {
                    $this->sendTaskEmail($task);
                    $emailsSent++;
                    $this->line("  ✓ Email sent to {$task->student->first_name} {$task->student->last_name} for task: {$task->message}");
                }
            }

            $this->info("Total emails processed: {$tasksToSend->count()}");
            
            Log::info("Scheduled task email command completed. Emails sent: {$emailsSent}");

        } catch (\Exception $e) {
            $this->error("Error in scheduled task email command: " . $e->getMessage());
            Log::error("Error in scheduled task email command: " . $e->getMessage());
            throw $e;
        }

        $this->info('Process completed.');
    }

    /**
     * Envoyer un email pour une tâche spécifique
     */
    private function sendTaskEmail(TachePlanifie $task): void
    {
        try {
            if (!$task->student || !$task->student->email) {
                Log::warning("Task {$task->id} has no student or email");
                return;
            }

            $to_name = $task->student->first_name . ' ' . $task->student->last_name;
            $to_email = $task->student->email;
            $course_name = $task->course ? $task->course->titre : 'Cours non spécifié';
            $task_date = $task->date_time->format('d/m/Y');
            $task_time = $task->date_time->format('H:i');
            
            // Déterminer le timing basé sur la date
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
            $timing = 'other';
            $timing_label = 'prochainement';
            
            if ($task->date_time->isSameDay($today)) {
                $timing = 'today';
                $timing_label = 'aujourd\'hui';
            } elseif ($task->date_time->isSameDay($tomorrow)) {
                $timing = 'tomorrow';
                $timing_label = 'demain';
            }

            $data = [
                'student_name' => $to_name,
                'course_name' => $course_name,
                'task_message' => $task->message,
                'task_date' => $task_date,
                'task_time' => $task_time,
                'timing' => $timing,
                'timing_label' => $timing_label
            ];

            $subject = "📅 Rappel: Tâche prévue {$timing_label} - {$course_name}";

            Mail::send('emails.scheduled-task', $data, function($message) use ($to_name, $to_email, $subject) {
                $message->to($to_email, $to_name)
                    ->subject($subject);
                $message->from('no-reply@up-skills.academy', 'Upskills Academy');
            });

            OutboundEmailLogger::logSent(
                $to_email,
                'scheduled_task',
                $subject,
                $task->student->id,
                $to_name,
                'TachePlanifie',
                $task->id
            );

            // Marquer la tâche comme envoyée
            $task->markAsSent();

            Log::info("Email sent to {$to_email} for task {$task->id} ({$timing})");

        } catch (\Exception $e) {
            OutboundEmailLogger::logFailed(
                $task->student->email ?? '',
                'scheduled_task',
                $subject ?? 'Scheduled Task Reminder',
                $e->getMessage(),
                $task->student->id ?? null,
                ($task->student->first_name ?? '') . ' ' . ($task->student->last_name ?? ''),
                'TachePlanifie',
                $task->id
            );

            Log::error("Failed to send email for task {$task->id}: " . $e->getMessage());
            $this->error("Failed to send email for task {$task->id}: " . $e->getMessage());
        }
    }
}