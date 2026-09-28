<?php

namespace App\Services;

use App\Models\Product;
use App\Models\TachePlanifie;
use App\Models\ZoomMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarService
{
    public function index(Request $request)
    {
        $language = $request->get('lang', 'fr');

        app()->setLocale($language);

        $courses = Product::with(['variations' => function ($query) {
            $query->where('langue', 'en');
        }])->where('statut', 1)->orderBy('created_at', 'desc')->get();

        $coursesData = $courses->map(function ($course) {
            $variation = $course->variations->first();

            return [
                'id' => $course->id,
                'title' => $variation ? $variation->name : 'Course Title',
                'description' => $variation ? $variation->short_description : '',
                'type' => $course->type_course,
                'price' => $course->prix,
                'is_online' => $course->is_online,
                'is_classroom' => $course->is_classroom,
                'is_stage' => $course->is_stage,
                'langue' => 'en'
            ];
        });

        return view('calendar.index', compact('coursesData', 'language'));
    }

    public function getEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        if (!$start || !$end) {
            $year = $request->get('year', date('Y'));
            $month = $request->get('month', date('m'));
            $start = $year . '-' . $month . '-01';
            $end = date('Y-m-t', strtotime($start));
        }

        $tasks = TachePlanifie::with(['course', 'student'])
            ->whereBetween('date_time', [$start, $end])
            ->orderBy('date_time')
            ->get();

        $zoomMeetings = ZoomMeeting::with(['product', 'creator'])
            ->whereBetween('start_time', [$start, $end])
            ->whereIn('status', ['scheduled', 'completed'])
            ->orderBy('start_time')
            ->get();

        $events = $tasks->map(function ($task) {
            $title = $task->message;

            if ($task->course) {
                $title = "[{$task->course->name}] " . $title;
            }
            if ($task->student) {
                $title .= " - {$task->student->first_name} {$task->student->last_name}";
            }

            $type = $this->determineEventType($task);

            return [
                'id' => 'task_' . $task->id,
                'title' => $title,
                'date' => $task->date_time->format('Y-m-d'),
                'time' => $task->date_time->format('H:i'),
                'type' => $type,
                'color' => $this->getEventColor($type, $task->priority),
                'task_id' => $task->id,
                'status' => $task->status,
                'priority' => $task->priority,
                'course_id' => $task->course_id,
                'student_id' => $task->student_id,
                'course_name' => $task->course ? $task->course->titre : null,
                'student_name' => $task->student ? $task->student->first_name . ' ' . $task->student->last_name : null
            ];
        });

        $zoomEvents = $zoomMeetings->map(function ($meeting) {
            $courseName = $meeting->product ? $meeting->product->titre : 'N/A';
            $creatorName = $meeting->creator ? $meeting->creator->name : 'Admin';

            return [
                'id' => 'zoom_' . $meeting->id,
                'title' => "🎥 Zoom: " . $meeting->topic,
                'date' => $meeting->start_time->format('Y-m-d'),
                'time' => $meeting->start_time->format('H:i'),
                'type' => 'meeting',
                'color' => '#fa709a',
                'zoom_meeting_id' => $meeting->id,
                'zoom_id' => $meeting->zoom_meeting_id,
                'status' => $meeting->status,
                'duration' => $meeting->duration,
                'course_id' => $meeting->product_id,
                'course_name' => $courseName,
                'join_url' => $meeting->join_url,
                'password' => $meeting->password,
                'moderator_email' => $meeting->moderator_email,
                'created_by' => $creatorName,
                'agenda' => $meeting->agenda,
            ];
        });

        $events = $events->concat($zoomEvents);
        $events = $events->concat($this->getSampleEvents($start, $end));

        return response()->json($events->toArray());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable|string',
            'course_id' => 'required|exists:products,id',
            'student_id' => 'nullable|exists:students,id',
            'priority' => 'nullable|in:low,medium,high'
        ]);

        $dateTime = $request->date;
        if ($request->time) {
            $dateTime .= ' ' . $request->time;
        } else {
            $dateTime .= ' 09:00:00';
        }

        $priority = $request->priority ?? $this->getDefaultPriority($request->title);

        $enrolledStudents = DB::table('product_students')
            ->where('product_id', $request->course_id)
            ->where('is_active', 1)
            ->pluck('student_id');

        $createdTasks = [];

        if ($enrolledStudents->count() > 0) {
            foreach ($enrolledStudents as $studentId) {
                $task = TachePlanifie::create([
                    'course_id' => $request->course_id,
                    'student_id' => $studentId,
                    'message' => $request->title,
                    'date_time' => $dateTime,
                    'status' => 'pending',
                    'priority' => $priority,
                    'notes' => null
                ]);
                $createdTasks[] = $task;
            }

            $message = "Tâche planifiée ajoutée avec succès pour {$enrolledStudents->count()} étudiant(s) inscrit(s)";
        } else {
            $task = TachePlanifie::create([
                'course_id' => $request->course_id,
                'student_id' => null,
                'message' => $request->title,
                'date_time' => $dateTime,
                'status' => 'pending',
                'priority' => $priority,
                'notes' => 'Aucun étudiant inscrit dans ce cours'
            ]);
            $createdTasks[] = $task;
            $message = 'Tâche planifiée ajoutée avec succès (aucun étudiant inscrit dans ce cours)';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'tasks' => $createdTasks,
            'students_count' => $enrolledStudents->count()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable|string',
            'course_id' => 'required|exists:products,id',
            'student_id' => 'nullable|exists:students,id',
            'priority' => 'nullable|in:low,medium,high'
        ]);

        if (str_starts_with($id, 'task_')) {
            $taskId = str_replace('task_', '', $id);
            $task = TachePlanifie::findOrFail($taskId);

            $dateTime = $request->date;
            if ($request->time) {
                $dateTime .= ' ' . $request->time;
            } else {
                $dateTime .= ' 09:00:00';
            }

            $task->update([
                'course_id' => $request->course_id,
                'student_id' => $request->student_id,
                'message' => $request->title,
                'date_time' => $dateTime,
                'priority' => $request->priority ?? $task->priority,
                'notes' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tâche planifiée mise à jour avec succès',
                'task' => $task
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Événement mis à jour avec succès'
        ]);
    }

    public function destroy($id)
    {
        if (str_starts_with($id, 'task_')) {
            $taskId = str_replace('task_', '', $id);
            $task = TachePlanifie::findOrFail($taskId);
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tâche planifiée supprimée avec succès'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Événement supprimé avec succès'
        ]);
    }

    public function markCompleted($id)
    {
        if (str_starts_with($id, 'task_')) {
            $taskId = str_replace('task_', '', $id);
            $task = TachePlanifie::findOrFail($taskId);
            $task->markAsCompleted();

            return response()->json([
                'success' => true,
                'message' => 'Tâche marquée comme terminée'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tâche non trouvée'
        ], 404);
    }

    public function getStatistics()
    {
        $stats = [
            'total' => TachePlanifie::count(),
            'pending' => TachePlanifie::pending()->count(),
            'completed' => TachePlanifie::completed()->count(),
            'overdue' => TachePlanifie::pending()->where('date_time', '<', now())->count(),
            'high_priority' => TachePlanifie::byPriority('high')->count(),
            'this_month' => TachePlanifie::whereMonth('date_time', now()->month)
                ->whereYear('date_time', now()->year)
                ->count()
        ];

        return response()->json($stats);
    }

    private function determineEventType($task)
    {
        $message = strtolower($task->message);

        if (str_contains($message, 'quiz') || str_contains($message, 'examen')) {
            return 'quiz';
        }
        if (str_contains($message, 'stage') || str_contains($message, 'révision')) {
            return 'review';
        }
        if (str_contains($message, 'certificat')) {
            return 'certificate';
        }
        if (str_contains($message, 'réunion') || str_contains($message, 'meeting')) {
            return 'meeting';
        }
        if (str_contains($message, 'vidéo') || str_contains($message, 'video')) {
            return 'exam';
        }

        return 'other';
    }

    private function getEventColor($type, $priority = 'medium')
    {
        $colors = [
            'quiz' => '#667eea',
            'exam' => '#f093fb',
            'review' => '#4facfe',
            'certificate' => '#43e97b',
            'meeting' => '#fa709a',
            'other' => '#a8edea'
        ];

        $color = $colors[$type] ?? '#a8edea';

        if ($priority === 'high') {
            $color = $this->darkenColor($color, 20);
        } elseif ($priority === 'low') {
            $color = $this->lightenColor($color, 20);
        }

        return $color;
    }

    private function darkenColor($color, $percent)
    {
        $num = hexdec(str_replace('#', '', $color));
        $amt = round(2.55 * $percent);
        $red = ($num >> 16) - $amt;
        $green = ($num >> 8 & 0x00FF) - $amt;
        $blue = ($num & 0x0000FF) - $amt;

        return '#' . str_pad(dechex(max(0, $red)), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex(max(0, $green)), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex(max(0, $blue)), 2, '0', STR_PAD_LEFT);
    }

    private function lightenColor($color, $percent)
    {
        $num = hexdec(str_replace('#', '', $color));
        $amt = round(2.55 * $percent);
        $red = ($num >> 16) + $amt;
        $green = ($num >> 8 & 0x00FF) + $amt;
        $blue = ($num & 0x0000FF) + $amt;

        return '#' . str_pad(dechex(min(255, $red)), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex(min(255, $green)), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex(min(255, $blue)), 2, '0', STR_PAD_LEFT);
    }

    private function getSampleEvents($start, $end)
    {
        return collect([
            [
                'id' => 'sample_1',
                'title' => 'Quiz Session - AI Basics',
                'date' => date('Y-m-d', strtotime('+5 days')),
                'time' => '10:00',
                'type' => 'quiz',
                'color' => '#667eea',
                'status' => 'pending',
                'priority' => 'medium'
            ],
            [
                'id' => 'sample_2',
                'title' => 'Student Stage Review',
                'date' => date('Y-m-d', strtotime('+10 days')),
                'time' => '14:00',
                'type' => 'review',
                'color' => '#4facfe',
                'status' => 'pending',
                'priority' => 'high'
            ]
        ])->filter(function ($event) use ($start, $end) {
            return $event['date'] >= $start && $event['date'] <= $end;
        });
    }

    private function getDefaultPriority($message)
    {
        $message = strtolower($message);

        if (str_contains($message, 'examen') || str_contains($message, 'certificat') || str_contains($message, 'urgent')) {
            return 'high';
        }
        if (str_contains($message, 'quiz') || str_contains($message, 'révision') || str_contains($message, 'important')) {
            return 'medium';
        }

        return 'low';
    }
}
