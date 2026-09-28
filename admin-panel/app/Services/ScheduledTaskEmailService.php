<?php

namespace App\Services;

use App\Models\TachePlanifie;
use App\Mail\DatabaseTemplateEmail;
use App\Jobs\SendScheduledTaskEmails;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ScheduledTaskEmailService
{
    public function index(): array
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $todayTasks = TachePlanifie::with(['student', 'course'])
            ->where('status', 'pending')
            ->whereDate('date_time', $today)
            ->whereHas('student')
            ->get();

        $tomorrowTasks = TachePlanifie::with(['student', 'course'])
            ->where('status', 'pending')
            ->whereDate('date_time', $tomorrow)
            ->whereHas('student')
            ->get();

        $stats = [
            'today_tasks'              => $todayTasks->count(),
            'tomorrow_tasks'           => $tomorrowTasks->count(),
            'total_students_today'     => $todayTasks->pluck('student_id')->unique()->count(),
            'total_students_tomorrow'  => $tomorrowTasks->pluck('student_id')->unique()->count(),
        ];

        return compact('todayTasks', 'tomorrowTasks', 'stats');
    }

    public function sendEmails(Request $request)
    {
        $request->validate([
            'timing'    => 'required|in:today,tomorrow,both',
            'test_mode' => 'boolean',
        ]);

        try {
            if ($request->test_mode) {
                $result = $this->testEmailProcess($request->timing);
                return response()->json([
                    'success' => true,
                    'message' => 'Test effectué avec succès',
                    'data'    => $result,
                ]);
            } else {
                SendScheduledTaskEmails::dispatch();
                return response()->json([
                    'success' => true,
                    'message' => 'Job d\'envoi d\'emails lancé avec succès',
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error in manual email sending: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi des emails: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tache_planifies,id',
            'email'   => 'required|email',
        ]);

        try {
            $task = TachePlanifie::with(['student', 'course'])->findOrFail($request->task_id);

            $variables = [
                'student_name'  => $task->student->first_name . ' ' . $task->student->last_name,
                'course_name'   => $task->course ? $task->course->titre : 'Cours non spécifié',
                'task_message'  => $task->message,
                'task_date'     => $task->date_time->format('d/m/Y'),
                'task_time'     => $task->date_time->format('H:i'),
                'task_priority' => $this->getPriorityLabel($task->priority),
            ];

            $timing         = $task->date_time->isToday() ? 'today' : 'tomorrow';
            $templateType   = 'student';
            $templateStatus = $timing === 'today' ? 'task_reminder_today' : 'task_preparation_tomorrow';

            Mail::to($request->email)
                ->send(new DatabaseTemplateEmail(
                    $templateType,
                    $templateStatus,
                    $variables,
                    $timing === 'today' ? 'Rappel de tâche' : 'Préparation de tâche'
                ));

            EmailLog::logSent(
                $request->email,
                'scheduled_task_test',
                'Test Email - Scheduled Task',
                $task->student->id ?? null,
                ($task->student->first_name ?? '') . ' ' . ($task->student->last_name ?? ''),
                'TachePlanifie',
                $task->id
            );

            return response()->json([
                'success' => true,
                'message' => "Email de test envoyé à {$request->email}",
            ]);
        } catch (\Exception $e) {
            Log::error("Error sending test email: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email de test: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function testEmailProcess(string $timing): array
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $todayTasks    = collect();
        $tomorrowTasks = collect();

        if ($timing === 'today' || $timing === 'both') {
            $todayTasks = TachePlanifie::with(['student', 'course'])
                ->where('status', 'pending')
                ->whereDate('date_time', $today)
                ->whereHas('student')
                ->get();
        }

        if ($timing === 'tomorrow' || $timing === 'both') {
            $tomorrowTasks = TachePlanifie::with(['student', 'course'])
                ->where('status', 'pending')
                ->whereDate('date_time', $tomorrow)
                ->whereHas('student')
                ->get();
        }

        $mapTask = fn ($task) => [
            'id'           => $task->id,
            'student_name' => $task->student->first_name . ' ' . $task->student->last_name,
            'student_email'=> $task->student->email,
            'message'      => $task->message,
            'course_name'  => $task->course ? $task->course->titre : 'N/A',
            'date_time'    => $task->date_time->format('d/m/Y H:i'),
            'priority'     => $task->priority,
        ];

        return [
            'today_tasks'    => $todayTasks->map($mapTask),
            'tomorrow_tasks' => $tomorrowTasks->map($mapTask),
            'total_emails'   => $todayTasks->count() + $tomorrowTasks->count(),
        ];
    }

    private function getPriorityLabel(string $priority): string
    {
        return match ($priority) {
            'high'   => 'Haute',
            'medium' => 'Moyenne',
            'low'    => 'Basse',
            default  => 'Moyenne',
        };
    }
}
