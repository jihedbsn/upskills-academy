<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Product;
use App\Models\Book;
use App\Models\Order;
use App\Models\CourseRating;
use App\Models\Ticket;
use App\Models\StudentSuccess;
use App\Models\ZoomMeeting;
use App\Models\CourseSession;
use App\Models\ResultatQuiz;
use App\Mail\AdminNewTicketNotification;
use App\Services\PointsService;
use App\Support\QuizSubmissionStatus;

class StudentDashboardService
{
    /**
     * Get pending evaluations for the authenticated student
     */
    private function getPendingEvaluations()
    {
        $student = Auth::user();
        if (!$student) {
            return ['count' => 0, 'courses' => collect()];
        }

        // Get completed courses (success = 1) that haven't been rated yet
        $completedCourses = StudentSuccess::with(['product'])
            ->where('student_id', $student->id)
            ->where('success', 1) // Only successful completions
            ->get();

        // Filter out courses that have already been rated
        $pendingCourses = $completedCourses->filter(function($success) use ($student) {
            return !CourseRating::where('student_id', $student->id)
                                ->where('product_id', $success->product_id)
                                ->exists();
        });

        return [
            'count' => $pendingCourses->count(),
            'courses' => $pendingCourses->pluck('product')->filter()
        ];
    }

    /**
     * Get unread notifications for the authenticated student
     */
    private function getUnreadNotifications()
    {
        $student = Auth::user();
        if (!$student) {
            return ['count' => 0, 'notifications' => collect()];
        }

        $notifications = \App\Models\Notification::where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $student->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'count' => $notifications->count(),
            'notifications' => $notifications
        ];
    }
    /**
     * Display the student dashboard index page
     */
    public function index()
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            // Pour le test, utiliser l'étudiant ID 1
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
            Log::info("Using test student for dashboard: {$student->full_name} (ID: {$student->id})");
        }

        // Statistiques réelles basées sur les vraies données (seulement cours actifs et visibles)
        $enrolledCourses = $student->productAccess()
            ->with('product')
            ->whereHas('product', function($query) {
                $query->where('statut', 1)
                    ->where('is_listed', 1);
            })
            ->get();
        $studentSuccesses = \App\Models\StudentSuccess::where('student_id', $student->id)->get();
        $quizHistory = \App\Models\HistoriqueQuiz::where('student_id', $student->id)->get();
        $orders = $student->orders()->with(['product', 'book'])->get();
        $tickets = \App\Models\Ticket::where('student_id', $student->id)->get();

        $stats = [
            'enrolled_courses' => $enrolledCourses->count(),
            'completed_courses' => $studentSuccesses->where('success', 1)->count(),
            'pending_certificates' => $studentSuccesses->where('success', 0)->count(),
            'total_quiz_attempts' => $quizHistory->whereNull('question_id')->count(), // Résumés seulement
            'successful_quizzes' => $quizHistory->whereNull('question_id')->where('success', true)->count(),
            'total_spent' => $orders->where('payment_success', true)->sum('price'),
            'total_orders' => $orders->count(),
            'successful_orders' => $orders->where('payment_success', true)->count(),
            'open_tickets' => $tickets->where('ticket_iscomplet', false)->count(),
            'resolved_tickets' => $tickets->where('ticket_iscomplet', true)->count(),
            'average_quiz_score' => $quizHistory->whereNull('question_id')->avg('score_percentage') ?? 0,
            'study_hours' => $quizHistory->sum('time_spent') / 3600 // Conversion en heures
        ];

        // Cours récents avec statut
        $recentCourses = $enrolledCourses->take(5)->map(function($enrollment) use ($studentSuccesses, $student) {
            $success = $studentSuccesses->where('product_id', $enrollment->product_id)->first();
            return [
                'product' => $enrollment->product,
                'enrolled_at' => $enrollment->created_at,
                'status' => $success ? ($success->success == 1 ? 'completed' : 'pending') : 'in_progress',
                'progress' => $this->calculateCourseProgress($enrollment->product_id, $student->id)
            ];
        });

        // Activité récente combinée
        $recentActivity = collect();

        // Ajouter les quiz récents
        $recentQuizzes = $quizHistory->whereNull('question_id')->sortByDesc('completed_at')->take(3);
        foreach ($recentQuizzes as $quiz) {
            $answeredCount = $quizHistory
                ->where('quiz_id', $quiz->quiz_id)
                ->where('product_id', $quiz->product_id)
                ->where('attempt_number', $quiz->attempt_number)
                ->whereNotNull('question_id')
                ->count();

            $submissionStatus = QuizSubmissionStatus::resolve(
                $quiz->started_at,
                $quiz->completed_at,
                (int) $quiz->total_questions,
                $quiz->time_spent,
                $answeredCount
            );

            $recentActivity->push([
                'type' => 'quiz',
                'title' => $quiz->success ? __('messages.quiz_completed') : __('messages.quiz_attempted'),
                'description' => $quiz->product->titre ?? __('messages.course_quiz'),
                'date' => $quiz->completed_at,
                'icon' => $quiz->success ? 'bi-check-circle text-success' : 'bi-clock text-warning',
                'score' => $quiz->score_percentage,
                'submission_status' => $submissionStatus,
                'submission_status_label' => QuizSubmissionStatus::label($submissionStatus),
                'submission_status_badge' => QuizSubmissionStatus::badgeClass($submissionStatus),
            ]);
        }

        $unfinishedAttempts = ResultatQuiz::with(['product', 'quiz' => fn ($query) => $query->withCount('questions')])
            ->where('student_id', $student->id)
            ->whereNotNull('started_at')
            ->whereNull('completed_at')
            ->orderByDesc('started_at')
            ->get()
            ->map(fn (ResultatQuiz $attempt) => $this->mapUnfinishedQuizAttempt($attempt));

        // Ajouter les commandes récentes
        $recentOrders = $orders->sortByDesc('created_at')->take(3);
        foreach ($recentOrders as $order) {
            $description = __('messages.unknown_item');
            if ($order->product) {
                $description = $order->product->titre;
            } elseif ($order->book) {
                $description = $order->book->title;
            }

            $recentActivity->push([
                'type' => 'order',
                'title' => __('messages.purchase_completed'),
                'description' => $description,
                'date' => $order->created_at,
                'icon' => 'bi-credit-card text-primary',
                'amount' => $order->price,
                'status' => $order->payment_success ? 'success' : 'failed'
            ]);
        }

        // Ajouter les tickets récents
        $recentTickets = $tickets->sortByDesc('created_at')->take(2);
        foreach ($recentTickets as $ticket) {
            $recentActivity->push([
                'type' => 'ticket',
                'title' => __('messages.ticket_created'),
                'description' => $ticket->sujet,
                'date' => $ticket->created_at,
                'icon' => $ticket->ticket_iscomplet ? 'bi-check-circle text-success' : 'bi-ticket text-warning',
                'status' => $ticket->ticket_iscomplet ? 'resolved' : 'open'
            ]);
        }

        // Trier par date et prendre les 8 plus récents
        $recentActivity = $recentActivity->sortByDesc('date')->take(8);

        // Progression mensuelle pour graphiques (simplifiée)
        $monthlyProgress = collect([
            ['month' => 'Sep 2024', 'quiz_attempts' => 1, 'certificates' => 0],
            ['month' => 'Oct 2024', 'quiz_attempts' => 2, 'certificates' => 1],
            ['month' => 'Nov 2024', 'quiz_attempts' => 1, 'certificates' => 0],
            ['month' => 'Dec 2024', 'quiz_attempts' => 3, 'certificates' => 2],
            ['month' => 'Jan 2025', 'quiz_attempts' => 2, 'certificates' => 1],
            ['month' => now()->format('M Y'), 'quiz_attempts' => $stats['total_quiz_attempts'], 'certificates' => $stats['completed_courses']]
        ]);

        // Cours recommandés (non inscrits)
        $enrolledCourseIds = $enrolledCourses->pluck('product_id');
        $recommendedCourses = \App\Models\Product::whereNotIn('id', $enrolledCourseIds)
            ->where('statut', 1)
            ->where('is_listed', 1)
            ->with(['category', 'teacher'])
            ->take(3)
            ->get();

        // Prochaines échéances (simplifiées)
        $upcomingDeadlines = collect();

        // Get pending evaluations for notifications
        $evaluationData = $this->getPendingEvaluations();
        $pendingEvaluationsCount = $evaluationData['count'];
        $pendingEvaluations = $evaluationData['courses'];

        // Get unread notifications
        $notificationData = $this->getUnreadNotifications();
        $unreadNotificationsCount = $notificationData['count'];
        $unreadNotifications = $notificationData['notifications'];

        // Get Zoom meetings for student's enrolled courses
        $upcomingZoomMeetings = ZoomMeeting::forStudent($student->id)
            ->upcoming()
            ->with('product')
            ->limit(5)
            ->get();

        $pastZoomMeetings = ZoomMeeting::forStudent($student->id)
            ->past()
            ->with('product')
            ->limit(5)
            ->get();

        // Get upcoming course sessions for enrolled courses
        $upcomingCourseSessions = CourseSession::forStudent($student->id)
            ->upcoming()
            ->with('product')
            ->limit(5)
            ->get();

        // Get customer points balance
        $pointsService = new PointsService();
        $pointsBalance = $pointsService->getPointsBalance($student->id);
        $pointsHistory = $pointsService->getPointsHistory($student->id, 5);

        return view('student-dashboard.pages.index', compact(
            'stats',
            'recentCourses',
            'recentActivity',
            'unfinishedAttempts',
            'monthlyProgress',
            'recommendedCourses',
            'upcomingDeadlines',
            'pendingEvaluationsCount',
            'pendingEvaluations',
            'unreadNotificationsCount',
            'unreadNotifications',
            'upcomingZoomMeetings',
            'pastZoomMeetings',
            'upcomingCourseSessions',
            'pointsBalance',
            'pointsHistory'
        ));
    }

    /**
     * Display the course schedule page
     */
    public function courseSchedule()
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
        }

        // Get all course sessions for enrolled courses
        $allSessions = CourseSession::forStudent($student->id)
            ->with('product')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        // Separate upcoming and past sessions
        $upcomingSessions = $allSessions->filter(function($session) {
            /** @var \App\Models\CourseSession $session */
            return $session->isUpcoming() || $session->isToday();
        });

        $pastSessions = $allSessions->filter(function($session) {
            /** @var \App\Models\CourseSession $session */
            return $session->isPast() && !$session->isToday();
        })->reverse(); // Most recent first

        // Get pending evaluations for notifications
        $evaluationData = $this->getPendingEvaluations();
        $pendingEvaluationsCount = $evaluationData['count'];
        $pendingEvaluations = $evaluationData['courses'];

        return view('student-dashboard.pages.course-schedule', compact(
            'upcomingSessions',
            'pastSessions',
            'pendingEvaluationsCount',
            'pendingEvaluations'
        ));
    }

    /**
     * Display the calendar page with all sessions and zoom meetings
     */
    public function calendar()
    {
        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
        }

        // Get course sessions
        $courseSessions = CourseSession::forStudent($student->id)
            ->with('product')
            ->get();

        // Get zoom meetings
        $zoomMeetings = \App\Models\ZoomMeeting::forStudent($student->id)
            ->with('product')
            ->get();

        // Get admin planned tasks/events assigned to this student
        $plannedTasks = collect();
        if (Schema::hasTable('tache_planifies')) {
            try {
                $taskColumns = Schema::getColumnListing('tache_planifies');

                if (in_array('student_id', $taskColumns) && in_array('date_time', $taskColumns)) {
                    $query = DB::table('tache_planifies as tp')
                        ->leftJoin('products as p', 'p.id', '=', 'tp.course_id')
                        ->leftJoin('product_variations as pv', function ($join) {
                            $join->on('pv.products_id', '=', 'p.id')
                                ->where('pv.langue', '=', app()->getLocale());
                        })
                        ->where('tp.student_id', $student->id);

                    if (in_array('status', $taskColumns)) {
                        $query->whereIn('tp.status', ['pending', 'completed']);
                    }

                    $selects = [
                        'tp.id',
                        'tp.message',
                        'tp.date_time',
                        'tp.course_id',
                        'pv.name as course_variation_name',
                        DB::raw("COALESCE(pv.name, CONCAT('Course #', tp.course_id)) as course_title"),
                    ];

                    if (in_array('status', $taskColumns)) {
                        $selects[] = 'tp.status';
                    }
                    if (in_array('priority', $taskColumns)) {
                        $selects[] = 'tp.priority';
                    }
                    if (in_array('notes', $taskColumns)) {
                        $selects[] = 'tp.notes';
                    }

                    $plannedTasks = $query
                        ->select($selects)
                        ->orderBy('tp.date_time')
                        ->get();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to load planned tasks for student calendar', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
                $plannedTasks = collect();
            }
        }

        // Get pending evaluations for notifications
        $evaluationData = $this->getPendingEvaluations();
        $pendingEvaluationsCount = $evaluationData['count'];
        $pendingEvaluations = $evaluationData['courses'];

        return view('student-dashboard.pages.calendar', compact(
            'courseSessions',
            'zoomMeetings',
            'plannedTasks',
            'pendingEvaluationsCount',
            'pendingEvaluations'
        ));
    }

    /**
     * Display the courses page
     */
    public function coursesIndex()
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
            Log::info("Using test student for courses: {$student->full_name} (ID: {$student->id})");
        }

        // Cours inscrits - retourner les ProductStudent avec les relations (seulement actifs et visibles)
        $enrolledProductStudents = $student->productAccess()
            ->with(['product.category', 'product.teacher'])
            ->whereNotNull('product_id') // S'assurer que product_id n'est pas null
            ->whereHas('product', function($query) {
                $query->where('statut', 1)
                    ->where('is_listed', 1);
            })
            ->get()
            ->filter(function($item) {
                return $item && $item->product; // Filtrer les éléments null
            });

        // Statistiques des cours basées sur StudentSuccess
        $studentSuccesses = \App\Models\StudentSuccess::where('student_id', $student->id)
            ->with('certificates')
            ->get();

        $courseStats = [
            'total_enrolled' => $enrolledProductStudents->count(),
            'completed' => $studentSuccesses->where('success', 1)->count(), // Succès validé
            'pending' => $studentSuccesses->where('success', 0)->count(), // En attente
            'rejected' => $studentSuccesses->where('success', -1)->count(), // Refusé
            'in_progress' => $enrolledProductStudents->count() - $studentSuccesses->count(), // Pas encore de StudentSuccess
            'total_hours' => $student->quizResults()->sum('score') * 0.5
        ];

        // Cours disponibles (non inscrits)
        $enrolledCourseIds = $enrolledProductStudents->pluck('product_id');
        $availableCourses = Product::whereNotIn('id', $enrolledCourseIds)
            ->with(['category', 'teacher'])
            ->where('statut', 1)
            ->where('is_listed', 1)
            ->take(6)
            ->get();

        // Get pending evaluations for notifications
        $evaluationData = $this->getPendingEvaluations();
        $pendingEvaluationsCount = $evaluationData['count'];
        $pendingEvaluations = $evaluationData['courses'];

        return view('student-dashboard.pages.courses', compact(
            'enrolledProductStudents',
            'courseStats',
            'availableCourses',
            'studentSuccesses',
            'pendingEvaluationsCount',
            'pendingEvaluations'
        ));
    }

    /**
     * Display the quiz and exam history page with detailed question responses
     */
    public function historyIndex(Request $request)
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            // Pour le test, utiliser l'étudiant ID 1
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found',
                    'debug' => 'Auth::user() returned null and Student ID 1 not found'
                ], 401);
            }
            Log::info("Using test student: {$student->full_name} (ID: {$student->id})");
        }

        // Récupérer l'historique des quiz avec les détails des réponses
        $query = \App\Models\HistoriqueQuiz::with(['product', 'quiz'])
            ->where('student_id', $student->id)
            ->whereNull('question_id') // Seulement les résumés de tentative
            ->orderBy('completed_at', 'desc');

        // Filtres
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('quiz_type')) {
            $query->where('quiz_type', $request->quiz_type);
        }

        if ($request->filled('success')) {
            $query->where('success', $request->success == '1');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        $historique = $query->paginate(15);

        // Debug: Afficher le nombre d'enregistrements trouvés
        Log::info("History page debug - Student ID: {$student->id}, Records found: " . $historique->count());

        foreach ($historique as $attempt) {
            $responses = $this->resolveAttemptResponsesForHistory($student->id, $attempt);
            $attempt->responses = $responses;
            $this->applySubmissionStatus($attempt, $responses->count());
        }

        $unfinishedQuery = ResultatQuiz::with(['product', 'quiz' => fn ($query) => $query->withCount('questions')])
            ->where('student_id', $student->id)
            ->whereNotNull('started_at')
            ->whereNull('completed_at')
            ->orderByDesc('started_at');

        if ($request->filled('product_id')) {
            $unfinishedQuery->where('product_id', $request->product_id);
        }

        if ($request->filled('quiz_type')) {
            $unfinishedQuery->whereHas('quiz', function ($query) use ($request) {
                $query->where('type_id', $request->quiz_type);
            });
        }

        $unfinishedAttempts = $unfinishedQuery->get()->map(fn (ResultatQuiz $attempt) => $this->mapUnfinishedQuizAttempt($attempt));

        // Statistiques
        $stats = \App\Models\HistoriqueQuiz::getStudentStats($student->id);

        // Cours disponibles pour le filtre
        $enrolledProducts = $student->productAccess()
            ->with(['product'])
            ->get()
            ->pluck('product');

        return view('student-dashboard.pages.history', compact('historique', 'stats', 'enrolledProducts', 'unfinishedAttempts'));
    }

    /**
     * Display a specific course
     */
    public function courseShow($id)
    {
        $student = Auth::user();
        $course = Product::with(['category', 'teacher', 'studies', 'quizzes'])->findOrFail($id);

        // Vérifier si l'étudiant a accès au cours
        $hasAccess = $student->productAccess()
            ->where('product_id', $id)
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('statut', 1)
                    ->where('is_listed', 1);
            })
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('student-dashboard.courses.index')
                ->with('error', 'You do not have access to this course.');
        }

        $blockResponse = app(BlockStudentCourseService::class)->denyIfBlocked((int) $id, app()->getLocale());
        if ($blockResponse) {
            return $blockResponse;
        }

        // Progression du cours
        $progress = 0;
        $totalLessons = $course->studies->count();
        $completedLessons = 0; // À implémenter avec un modèle de progression

        if ($totalLessons > 0) {
            $progress = ($completedLessons / $totalLessons) * 100;
        }

        // Résultats des quiz
        $quizResults = $student->quizResults()
            ->where('product_id', $id)
            ->with('quiz')
            ->get();

        return view('student-dashboard.pages.courses', compact('course', 'progress', 'quizResults'));
    }

    /**
     * Display the zoom meetings page
     */
    public function zoomMeetingsIndex()
    {
        $student = Auth::user();

        if (!$student) {
            return redirect()->route('login');
        }

        // Get upcoming Zoom meetings
        $upcomingMeetings = ZoomMeeting::forStudent($student->id)
            ->upcoming()
            ->with('product')
            ->get();

        // Get past Zoom meetings
        $pastMeetings = ZoomMeeting::forStudent($student->id)
            ->past()
            ->with('product')
            ->get();

        // Get pending evaluations for notifications
        $evaluationData = $this->getPendingEvaluations();
        $pendingEvaluationsCount = $evaluationData['count'];

        return view('student-dashboard.pages.zoom-meetings', compact(
            'upcomingMeetings',
            'pastMeetings',
            'pendingEvaluationsCount'
        ));
    }

    /**
     * Display the books page
     */
    public function booksIndex()
    {
        $student = Auth::user();

        // Commandes de livres achetés
        $purchasedBookOrders = $student->orders()
            ->whereNotNull('book_id')
            ->where('payment_success', true)
            ->with('book')
            ->get();

        // Statistiques des livres
        $bookStats = [
            'total_books' => $purchasedBookOrders->count()
        ];

        return view('student-dashboard.pages.books', compact('purchasedBookOrders', 'bookStats'));
    }

    /**
     * Display a specific book
     */
    public function bookShow($id)
    {
        $student = Auth::user();
        $book = Book::findOrFail($id);

        // Vérifier si l'étudiant a acheté le livre
        $hasPurchased = $student->orders()
            ->where('book_id', $id)
            ->where('payment_success', true)
            ->exists();

        if (!$hasPurchased) {
            return redirect()->route('student-dashboard.books.index')
                ->with('error', 'You do not have access to this book.');
        }

        return view('student-dashboard.pages.books', compact('book'));
    }

    /**
     * Display the certificates page
     */
    public function certificatesIndex(Request $request)
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            // Pour le test, utiliser l'étudiant ID 1
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
            Log::info("Using test student for certificates: {$student->full_name} (ID: {$student->id})");
        }

        // Récupérer les certificats depuis student_successes (only those with generated certificates)
        $query = \App\Models\StudentSuccess::with(['product', 'certificates'])
            ->where('student_id', $student->id)
            ->where('success', 1) // Seulement les certificats approuvés
            ->whereHas('certificates') // Only show if certificate has been generated
            ->orderBy('validated_at', 'desc');

        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('success', 1);
            } elseif ($request->status === 'pending') {
                $query->where('success', 0);
            } elseif ($request->status === 'rejected') {
                $query->where('success', -1);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('validated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('validated_at', '<=', $request->date_to);
        }

        $certificates = $query->paginate(12);

        // Debug: Afficher le nombre de certificats trouvés
        Log::info("Certificates page debug - Student ID: {$student->id}, Certificates found: " . $certificates->count());

        // Statistiques des certificats
        $allCertificates = \App\Models\StudentSuccess::where('student_id', $student->id)->get();
        $approvedWithCertificates = \App\Models\StudentSuccess::where('student_id', $student->id)
            ->where('success', 1)
            ->whereHas('certificates')
            ->count();

        $certificateStats = [
            'total_certificates' => $approvedWithCertificates,
            'pending_certificates' => 0, // Don't show pending certificates
            'rejected_certificates' => $allCertificates->where('success', -1)->count(),
            'this_month' => \App\Models\StudentSuccess::where('student_id', $student->id)
                ->where('success', 1)
                ->whereHas('certificates')
                ->where('validated_at', '>=', now()->startOfMonth())
                ->count(),
            'this_year' => \App\Models\StudentSuccess::where('student_id', $student->id)
                ->where('success', 1)
                ->whereHas('certificates')
                ->where('validated_at', '>=', now()->startOfYear())
                ->count(),
            'total_courses_completed' => $allCertificates->where('success', 1)->count()
        ];

        // Cours disponibles pour le filtre
        $enrolledProducts = $student->productAccess()
            ->with(['product'])
            ->get()
            ->pluck('product');

        return view('student-dashboard.pages.certificates', compact('certificates', 'certificateStats', 'enrolledProducts'));
    }

    /**
     * Display a specific certificate
     */
    public function certificateShow($id)
    {
        $student = Auth::user();

        // Récupérer le StudentSuccess de l'étudiant
        $studentSuccess = \App\Models\StudentSuccess::where('id', $id)
            ->where('student_id', $student->id)
            ->where('success', 1)
            ->with(['product', 'certificates'])
            ->firstOrFail();

        // Récupérer le certificat associé
        $certifStudent = $studentSuccess->certificates()->first();

        if (!$certifStudent || !$certifStudent->file_path) {
            return redirect()->route('student-dashboard.certificates.index')
                ->with('error', __('messages.certificate_not_found'));
        }

        // Construire l'URL du certificat avec FILE_URL
        $fileUrl = $certifStudent->file_url;

        // Rediriger vers l'URL du certificat pour téléchargement
        return redirect($fileUrl);
    }

    /**
     * Display the payment page
     */
    public function payment()
    {
        $student = Auth::user();

        // Historique des paiements
        $transactions = $student->orders()
            ->with(['product', 'book'])
            ->latest('created_at')
            ->get();

        // Statistiques des paiements
        $paymentStats = [
            'total_spent' => $transactions->where('payment_success', true)->sum('price'),
            'total_orders' => $transactions->count(),
            'successful_orders' => $transactions->where('payment_success', true)->count(),
            'this_month' => $transactions->where('created_at', '>=', now()->startOfMonth())->sum('price')
        ];

        return view('student-dashboard.pages.payment', compact('transactions', 'paymentStats'));
    }

    /**
     * Display payment history
     */
    public function paymentHistory()
    {
        $student = Auth::user();

        // Get transactions with coupon usage information
        $transactions = $student->orders()
            ->with(['product', 'book'])
            ->select('orders.*')
            ->latest('created_at')
            ->paginate(10);

        // Add coupon information to each transaction
        foreach ($transactions as $transaction) {
            $couponUsage = \App\Models\CouponUsageLog::where('order_id', $transaction->id)
                ->with('coupon')
                ->first();

            if ($couponUsage) {
                // Order has coupon - price is already discounted, show original price
                $transaction->coupon_used = $couponUsage->coupon->code;
                $transaction->discount_amount = $couponUsage->discount_amount;
                $transaction->original_price = $couponUsage->original_price;
                $transaction->final_price = $transaction->price; // Current price is final price
            } else {
                // No coupon used
                $transaction->coupon_used = null;
                $transaction->discount_amount = 0;
                $transaction->original_price = $transaction->price;
                $transaction->final_price = $transaction->price;
            }
        }

        // Calculate payment statistics including coupon savings
        $allTransactions = $student->orders()->get();
        $totalSavings = 0;
        $totalActualSpent = 0;

        foreach ($allTransactions as $order) {
            $couponUsage = \App\Models\CouponUsageLog::where('order_id', $order->id)->first();
            if ($couponUsage) {
                $totalSavings += $couponUsage->discount_amount;
            }
            $totalActualSpent += $order->price; // Final price after discount
        }

        $paymentStats = [
            'total_spent' => $totalActualSpent, // Amount actually paid
            'total_savings' => $totalSavings,
            'successful_orders' => $student->orders()->where('payment_success', true)->count(), // Match view expectation
            'successful_payments' => $student->orders()->where('payment_success', true)->count(),
            'total_orders' => $student->orders()->count(), // Add total orders for view calculation
            'this_month' => $student->orders()
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('price')
        ];

        return view('student-dashboard.pages.payment', compact('transactions', 'paymentStats'));
    }

    /**
     * Display student orders with payment status
     */
    public function orders()
    {
        $student = Auth::user();

        $orders = $student->orders()
            ->with(['product', 'book'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Group orders by created_at date and payment_method for bank transfer batches
        $orderGroups = $orders->groupBy(function($order) {
            return $order->created_at->format('Y-m-d H:i') . '_' . $order->payment_method;
        });

        return view('student-dashboard.pages.orders', compact('orders', 'orderGroups'));
    }

    /**
     * Display special offers / coupons for the student
     */
    public function specialOffers()
    {
        $student = Auth::user();
        $customerType = $this->determineCustomerTypeForOffers();

        // Get public coupons (exclude COMEBACK coupons - those are only for abandoned cart emails)
        $coupons = \App\Models\Coupon::where('is_public', true)
            ->where('is_active', true)
            ->where('code', 'NOT LIKE', 'COMEBACK%')
            ->where(function($q) {
                $q->whereNull('date_debut')->orWhere('date_debut', '<=', \Carbon\Carbon::now());
            })
            ->where(function($q) {
                $q->whereNull('date_fin')->orWhere('date_fin', '>=', \Carbon\Carbon::now());
            })
            ->where(function($q) use ($customerType) {
                $q->where('customer_type', 'all')->orWhere('customer_type', $customerType);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')->orWhereRaw('usage_count < usage_limit');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Filter by per-user limits if authenticated
        if ($student) {
            $coupons->getCollection()->transform(function($coupon) use ($student) {
                $coupon->can_use = $coupon->canBeUsedByUser($student);
                return $coupon;
            });
        }

        return view('student-dashboard.pages.special-offers', compact('coupons', 'customerType'));
    }

    /**
     * Determine customer type for special offers
     */
    private function determineCustomerTypeForOffers()
    {
        if (!Auth::check()) {
            return 'new';
        }

        $student = Auth::user();
        $hasOrders = \App\Models\Order::where('student_id', $student->id)
            ->where('payment_status', 'approved')
            ->exists();

        return $hasOrders ? 'returning' : 'new';
    }

    /**
     * Display the support page
     */
    public function support()
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            // Pour le test, utiliser l'étudiant ID 1
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
            Log::info("Using test student for support: {$student->full_name} (ID: {$student->id})");
        }

        // Tickets de support
        $tickets = \App\Models\Ticket::where('student_id', $student->id)
            ->with(['responses.student'])
            ->latest('created_at')
            ->get();

        // Debug: Afficher le nombre de tickets trouvés
        Log::info("Support page debug - Student ID: {$student->id}, Tickets found: " . $tickets->count());

        // Statistiques des tickets
        $supportStats = [
            'total_tickets' => $tickets->count(),
            'open_tickets' => $tickets->where('ticket_iscomplet', false)->count(),
            'resolved_tickets' => $tickets->where('ticket_iscomplet', true)->count(),
            'this_month' => $tickets->where('created_at', '>=', now()->startOfMonth())->count()
        ];

        return view('student-dashboard.pages.support', compact('tickets', 'supportStats'));
    }

    /**
     * Display tickets
     */
    public function tickets()
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
            Log::info("Using test student for tickets: {$student->full_name} (ID: {$student->id})");
        }

        $tickets = $student->tickets()
            ->with(['responses.student'])
            ->latest('created_at')
            ->paginate(10);

        // Statistiques des tickets
        $allTickets = $student->tickets()->get();
        $supportStats = [
            'total_tickets' => $allTickets->count(),
            'open_tickets' => $allTickets->where('ticket_iscomplet', false)->count(),
            'resolved_tickets' => $allTickets->where('ticket_iscomplet', true)->count(),
            'this_month' => $allTickets->where('created_at', '>=', now()->startOfMonth())->count()
        ];

        return view('student-dashboard.pages.support', compact('tickets', 'supportStats'));
    }

    /**
     * Create a new ticket
     */
    public function createTicket()
    {
        $student = Auth::user();

        // Debug: Vérifier l'authentification
        if (!$student) {
            $student = \App\Models\Student::find(1);
            if (!$student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found'
                ], 401);
            }
            Log::info("Using test student for create ticket: {$student->full_name} (ID: {$student->id})");
        }

        // Tickets de support
        $tickets = \App\Models\Ticket::where('student_id', $student->id)
            ->with(['responses.student'])
            ->latest('created_at')
            ->get();

        // Statistiques des tickets
        $supportStats = [
            'total_tickets' => $tickets->count(),
            'open_tickets' => $tickets->where('ticket_iscomplet', false)->count(),
            'resolved_tickets' => $tickets->where('ticket_iscomplet', true)->count(),
            'this_month' => $tickets->where('created_at', '>=', now()->startOfMonth())->count()
        ];

        return view('student-dashboard.pages.support', compact('tickets', 'supportStats'));
    }

    /**
     * Store a new ticket
     */
    public function storeTicket(Request $request)
    {
        $request->validate([
            'sujet' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
        }

        $ticket = \App\Models\Ticket::create([
            'student_id' => $student->id,
            'sujet' => $request->sujet,
            'ticket_iscomplet' => false
        ]);

        // Créer la première réponse (message initial)
        \App\Models\ResponseTicket::create([
            'ticket_id' => $ticket->id,
            'student_id' => $student->id,
            'message' => $request->message,
            'isAdmin' => false
        ]);

        // Send email notification to admin only
        try {
            // Reload ticket with relationships for email
            $ticketWithRelations = \App\Models\Ticket::with(['student', 'responses'])->find($ticket->id);

            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');

            // Send to admin
            Mail::to($adminEmail)->send(
                new AdminNewTicketNotification($ticketWithRelations)
            );

            // Student confirmation email is disabled
            // if ($student && $student->email) {
            //     Mail::to($student->email)->send(
            //         new AdminNewTicketNotification($ticketWithRelations)
            //     );
            // }
        } catch (\Exception $e) {
            Log::error('Admin ticket notification email error: ' . $e->getMessage());
        }

        // Create in-app notification for all admins
        try {
            \App\Models\Notification::notifyAllAdmins(
                \App\Models\Notification::TYPE_TICKET,
                'New Support Ticket',
                $student->first_name . ' ' . $student->last_name . ' created a new ticket: "' . $ticket->sujet . '"',
                config('app.url') . '/tickets/' . $ticket->id,
                ['ticket_id' => $ticket->id, 'student_id' => $student->id, 'subject' => $ticket->sujet],
                '🎫',
                'red'
            );
        } catch (\Exception $e) {
            Log::error('Admin ticket notification creation error: ' . $e->getMessage());
        }

        return redirect()->route('student-dashboard.support.index')
            ->with('success', __('messages.ticket_created_successfully'));
    }

    /**
     * Store a ticket reply
     */
    public function storeTicketReply(Request $request, $locale, $ticketId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
        }

        $ticket = \App\Models\Ticket::where('id', $ticketId)
            ->where('student_id', $student->id)
            ->with('student')
            ->firstOrFail();

        \App\Models\ResponseTicket::create([
            'ticket_id' => $ticket->id,
            'student_id' => $student->id,
            'message' => $request->message,
            'isAdmin' => false
        ]);

        // Send email notification to admin
        try {
            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(
                new \App\Mail\StudentTicketResponse($ticket, $request->message)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage());
        }

        return redirect()->route('student-dashboard.support.index')
                        ->with('success', __('messages.reply_sent_successfully'));
    }

    /**
     * Get ticket responses
     */
    public function getTicketResponses($locale, $ticketId)
    {
        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
        }

        $ticket = \App\Models\Ticket::where('id', $ticketId)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $responses = \App\Models\ResponseTicket::where('ticket_id', $ticketId)
            ->with('student')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'responses' => $responses,
            'ticket' => $ticket
        ]);
    }

    /**
     * Close a ticket
     */
    public function closeTicket(Request $request, $locale, $ticketId)
    {
        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
        }

        $ticket = \App\Models\Ticket::where('id', $ticketId)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $ticket->update(['ticket_iscomplet' => true]);

        return redirect()->route('student-dashboard.support.index')
            ->with('success', __('messages.ticket_closed_successfully'));
    }


    /**
     * Display the profile page
     */
    public function profile()
    {
        $student = Auth::user();

        // Statistiques du profil
        $profileStats = [
            'courses_enrolled' => $student->productAccess()->count(),
            'certificates_earned' => $student->quizResults()->where('success', true)->count(),
            'total_orders' => $student->orders()->count(),
            'member_since' => $student->created_at->diffForHumans()
        ];

        return view('student-dashboard.pages.profile', compact('student', 'profileStats'));
    }

    /**
     * Update student profile photo
     */
    public function updatePhoto(Request $request)
    {
        Log::info('Photo upload request received', [
            'upload_type' => $request->upload_type,
            'has_file' => $request->hasFile('photo'),
            'gravatar_email' => $request->gravatar_email,
            'all_data' => $request->all()
        ]);

        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
        }

        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gravatar_email' => 'nullable|email',
            'upload_type' => 'required|in:file,gravatar'
        ]);

        try {
            if ($request->upload_type === 'file' && $request->hasFile('photo')) {
                Log::info('Processing file upload');
                // Handle file upload
                $file = $request->file('photo');
                $filename = 'student_' . $student->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                Log::info('Generated filename: ' . $filename);

                // Ensure directory exists - créer les répertoires si nécessaire
                $uploadDir = public_path('uploads');
                $uploadPath = public_path('uploads/students');

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                Log::info('Upload path: ' . $uploadPath);
                Log::info('Directory exists: ' . (file_exists($uploadPath) ? 'yes' : 'no'));
                Log::info('Directory writable: ' . (is_writable($uploadPath) ? 'yes' : 'no'));

                // Store file in public/uploads/students/
                $file->move($uploadPath, $filename);

                Log::info('File moved successfully');

                // Update student photo - utiliser la colonne 'image' au lieu de 'photo'
                $student->update([
                    'image' => $filename,
                    'gravatar_email' => null // Effacer Gravatar si on upload un fichier
                ]);

                $photoUrl = asset('uploads/students/' . $filename);

                Log::info('Photo URL generated: ' . $photoUrl);

            } elseif ($request->upload_type === 'gravatar' && $request->gravatar_email) {
                Log::info('Processing Gravatar upload');
                // Handle Gravatar
                $email = $request->gravatar_email;
                $hash = md5(strtolower(trim($email)));
                $photoUrl = "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=200";

                // Update student with Gravatar email - effacer l'image locale
                $student->update([
                    'gravatar_email' => $email,
                    'image' => null // Effacer l'image locale si on utilise Gravatar
                ]);

                Log::info('Gravatar URL generated: ' . $photoUrl);

            } else {
                Log::warning('Invalid upload request', [
                    'upload_type' => $request->upload_type,
                    'has_file' => $request->hasFile('photo'),
                    'gravatar_email' => $request->gravatar_email
                ]);

                return response()->json([
                    'success' => false,
                    'message' => __('messages.please_select_photo_or_email')
                ], 400);
            }

            Log::info('Photo upload successful');

            return response()->json([
                'success' => true,
                'message' => __('messages.photo_uploaded_successfully'),
                'photo_url' => $photoUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Photo upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('messages.photo_upload_failed')
            ], 500);
        }
    }

    /**
     * Get transaction details for modal
     */
    public function getTransactionDetails($transactionId)
    {
        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
        }

        $transaction = $student->orders()
            ->with(['product', 'book'])
            ->findOrFail($transactionId);

        // Prepare transaction data
        $transactionData = [
            'id' => $transaction->id,
            'created_at' => $transaction->created_at,
            'price' => $transaction->price,
            'payment_success' => $transaction->payment_success,
            'payment_method' => $transaction->payment_method,
            'transaction_id' => $transaction->transaction_id,
            'item_name' => $transaction->getItemName(),
            'item_type' => $transaction->getItemType(),
        ];

        // Add item-specific details
        if ($transaction->isForCourse() && $transaction->product) {
            $transactionData['item_description'] = $transaction->product->description;
            $transactionData['item_instructor'] = $transaction->product->instructor ?? 'N/A';
            $transactionData['item_duration'] = $transaction->product->duree ?? 'N/A';
        } elseif ($transaction->isForBook() && $transaction->book) {
            $transactionData['item_description'] = $transaction->book->description;
            $transactionData['item_author'] = $transaction->book->auteur ?? 'N/A';
            $transactionData['item_pages'] = $transaction->book->pages ?? 'N/A';
        }

        return response()->json([
            'success' => true,
            'transaction' => $transactionData
        ]);
    }

    /**
     * Download receipt for transaction
     */
    public function downloadReceipt($transactionId)
    {
        $student = Auth::user();

        if (!$student) {
            $student = \App\Models\Student::find(1);
        }

        $transaction = $student->orders()
            ->with(['product', 'book'])
            ->findOrFail($transactionId);

        if (!$transaction->payment_success) {
            return redirect()->back()->with('error', 'Receipt not available for pending transactions.');
        }

        // For now, return a simple text receipt
        // In a real application, you would generate a PDF
        $receiptContent = "RECEIPT\n";
        $receiptContent .= "Transaction ID: {$transaction->id}\n";
        $receiptContent .= "Date: {$transaction->created_at->format('Y-m-d H:i:s')}\n";
        $receiptContent .= "Item: {$transaction->getItemName()}\n";
        $receiptContent .= "Amount: $" . number_format($transaction->price, 2) . "\n";
        $receiptContent .= "Payment Method: " . ($transaction->payment_method ?? 'N/A') . "\n";
        $receiptContent .= "Status: " . ($transaction->payment_success ? 'Completed' : 'Pending') . "\n";

        return response($receiptContent)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="receipt-' . $transaction->id . '.txt"');
    }

    /**
     * Update student profile information
     */
    public function updateProfile(Request $request)
    {
        $student = Auth::user();

        $request->validate([
            'first_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
            'last_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'birthdate' => 'nullable|date|before:today',
        ], [
            'first_name.regex' => 'First name must contain only English letters, spaces, hyphens, and apostrophes.',
            'last_name.regex' => 'Last name must contain only English letters, spaces, hyphens, and apostrophes.',
        ]);

        $student->update($request->only(['first_name', 'last_name', 'phone', 'country', 'gender', 'birthdate']));

        return response()->json([
            'success' => true,
            'message' => __('login.profile_updated_successfully'),
            'student' => $student
        ]);
    }

    public function settingsLanguage()
    {
        $student = Auth::user();

        return view('student-dashboard.pages.settings', compact('student'));
    }

    /**
     * Deactivate (block) student account
     * The student can contact admin to reactivate their account.
     */
    public function deleteAccount(Request $request)
    {
        $student = Auth::user();

        // Validate password confirmation
        $request->validate([
            'password' => 'required|string',
        ]);

        // Verify the password
        if (!Hash::check($request->password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => __('student_dashboard.incorrect_password'),
            ], 422);
        }

        try {
            // Block the student account
            $student->update([
                'is_blocked' => true,
                'block_reason' => __('student_dashboard.self_deactivation_reason'),
                'blocked_at' => now(),
            ]);

            // Logout the user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => __('student_dashboard.account_deactivated_successfully'),
                'redirect' => url('/'),
            ]);
        } catch (\Exception $e) {
            Log::error('Account deactivation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('student_dashboard.account_deactivation_failed'),
            ], 500);
        }
    }

    /**
     * Calculate course progress percentage
     */
    private function calculateCourseProgress($productId, $studentId)
    {
        $product = \App\Models\Product::find($productId);
        if (!$product) return 0;

        $totalQuizzes = $product->quizzes()->where('type_id', 2)->count(); // Quiz normaux
        $completedQuizzes = \App\Models\HistoriqueQuiz::where('student_id', $studentId)
            ->where('product_id', $productId)
            ->where('quiz_type', 2)
            ->where('success', true)
            ->whereNull('question_id')
            ->count();

        // Ajouter l'examen si terminé
        $examCompleted = \App\Models\HistoriqueQuiz::where('student_id', $studentId)
            ->where('product_id', $productId)
            ->where('quiz_type', 1)
            ->where('success', true)
            ->whereNull('question_id')
            ->exists();

        if ($examCompleted) $completedQuizzes++;

        $totalQuizzes++; // Inclure l'examen dans le total

        if ($totalQuizzes == 0) return 0;

        return round(($completedQuizzes / $totalQuizzes) * 100);
    }

    /**
     * Get monthly progress data for charts
     */
    private function getMonthlyProgress($studentId)
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $quizAttempts = \App\Models\HistoriqueQuiz::where('student_id', $studentId)
                ->whereBetween('completed_at', [$monthStart, $monthEnd])
                ->whereNull('question_id')
                ->count();

            $certificatesEarned = \App\Models\StudentSuccess::where('student_id', $studentId)
                ->whereBetween('validated_at', [$monthStart, $monthEnd])
                ->where('success', 1)
                ->count();

            $months->push([
                'month' => $date->format('M Y'),
                'quiz_attempts' => $quizAttempts,
                'certificates' => $certificatesEarned
            ]);
        }

        return $months;
    }

    /**
     * Get upcoming deadlines and important dates
     */
    private function getUpcomingDeadlines($studentId)
    {
        $deadlines = collect();

        // Quiz en attente (tentatives récentes échouées)
        $failedQuizzes = \App\Models\HistoriqueQuiz::where('student_id', $studentId)
            ->where('success', false)
            ->whereNull('question_id')
            ->with(['product', 'quiz'])
            ->latest('completed_at')
            ->take(3)
            ->get();

        foreach ($failedQuizzes as $quiz) {
            $deadlines->push([
                'type' => 'quiz_retry',
                'title' => __('messages.retry_quiz'),
                'description' => $quiz->product->titre . ' - ' . $quiz->quiz->name_en,
                'date' => $quiz->completed_at->addDays(1), // Peut reprendre le lendemain
                'priority' => 'medium',
                'icon' => 'bi-arrow-clockwise text-warning'
            ]);
        }

        // Certificats en attente
        $pendingCertificates = \App\Models\StudentSuccess::where('student_id', $studentId)
            ->where('success', 0)
            ->with('product')
            ->get();

        foreach ($pendingCertificates as $cert) {
            $deadlines->push([
                'type' => 'pending_certificate',
                'title' => __('messages.pending_certificate'),
                'description' => $cert->product->titre,
                'date' => $cert->submitted_at ? $cert->submitted_at->addDays(7) : now()->addDays(1),
                'priority' => 'high',
                'icon' => 'bi-clock text-info'
            ]);
        }

        return $deadlines->sortBy('date')->take(5);
    }

    private function resolveAttemptResponsesForHistory(int $studentId, $attempt)
    {
        $responses = \App\Models\HistoriqueQuiz::with(['question.reponses', 'response'])
            ->where('student_id', $studentId)
            ->where('quiz_id', $attempt->quiz_id)
            ->where('product_id', $attempt->product_id)
            ->where('attempt_number', $attempt->attempt_number)
            ->whereNotNull('question_id')
            ->orderBy('id')
            ->get();

        if ($responses->isNotEmpty()) {
            return $responses;
        }

        return \App\Models\ResponseQuiz::with(['question.reponses', 'reponse'])
            ->where('student_id', $studentId)
            ->where('quiz_id', $attempt->quiz_id)
            ->where('product_id', $attempt->product_id)
            ->orderBy('id')
            ->get();
    }

    private function applySubmissionStatus($attempt, int $answeredCount): void
    {
        $status = QuizSubmissionStatus::resolve(
            $attempt->started_at,
            $attempt->completed_at,
            (int) $attempt->total_questions,
            $attempt->time_spent,
            $answeredCount
        );

        $attempt->submission_status = $status;
        $attempt->submission_status_label = QuizSubmissionStatus::label($status);
        $attempt->submission_status_badge = QuizSubmissionStatus::badgeClass($status);
    }

    private function mapUnfinishedQuizAttempt(ResultatQuiz $attempt): array
    {
        $totalQuestions = max(1, (int) ($attempt->quiz?->questions_count ?? 10));
        $status = QuizSubmissionStatus::resolve(
            $attempt->started_at,
            null,
            $totalQuestions,
            null,
            0
        );

        return [
            'resultat' => $attempt,
            'total_questions' => $totalQuestions,
            'submission_status' => $status,
            'submission_status_label' => QuizSubmissionStatus::label($status),
            'submission_status_badge' => QuizSubmissionStatus::badgeClass($status),
        ];
    }
}
