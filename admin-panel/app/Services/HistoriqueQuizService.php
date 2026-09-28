<?php

namespace App\Services;

use App\Models\HistoriqueQuiz;
use App\Models\Product;
use App\Models\Quiz;
use App\Models\Student;
use App\Support\QuizSubmissionStatus;
use Illuminate\Http\Request;

class HistoriqueQuizService
{
    public function index(Request $request)
    {
        $query = HistoriqueQuiz::with(['student', 'quiz', 'product.variations']);

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->student}%")
                    ->orWhere('last_name', 'like', "%{$request->student}%")
                    ->orWhere('email', 'like', "%{$request->student}%");
            });
        }

        if ($request->filled('course')) {
            $query->where('product_id', $request->course);
        }

        if ($request->filled('quiz')) {
            $query->where('quiz_id', $request->quiz);
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('success', true);
            } elseif ($request->status === 'failed') {
                $query->where('success', false);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        if ($request->filled('min_score')) {
            $query->where('score', '>=', $request->min_score);
        }

        $historiqueQuiz = $query->orderBy('completed_at', 'desc')->paginate(20);

        $students = Student::orderBy('first_name')->get();
        $courses = Product::with('variations')->orderBy('id')->get();
        $quizzes = Quiz::orderBy('name_en')->get();

        $stats = [
            'total' => HistoriqueQuiz::count(),
            'successful' => HistoriqueQuiz::where('success', true)->count(),
            'failed' => HistoriqueQuiz::where('success', false)->count(),
            'average_score' => HistoriqueQuiz::avg('score') ?? 0,
            'unique_students' => HistoriqueQuiz::distinct('student_id')->count('student_id'),
            'unique_quizzes' => HistoriqueQuiz::distinct('quiz_id')->count('quiz_id'),
            'today' => HistoriqueQuiz::whereDate('completed_at', today())->count(),
            'this_week' => HistoriqueQuiz::whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => HistoriqueQuiz::whereMonth('completed_at', now()->month)->count(),
        ];

        return view('historique-quiz.index', compact('historiqueQuiz', 'students', 'courses', 'quizzes', 'stats'));
    }

    public function show(HistoriqueQuiz $historiqueQuiz)
    {
        $historiqueQuiz->load(['student', 'quiz', 'product.variations']);

        $answeredCount = HistoriqueQuiz::where('student_id', $historiqueQuiz->student_id)
            ->where('quiz_id', $historiqueQuiz->quiz_id)
            ->where('product_id', $historiqueQuiz->product_id)
            ->where('attempt_number', $historiqueQuiz->attempt_number)
            ->whereNotNull('question_id')
            ->count();

        $submissionStatus = QuizSubmissionStatus::resolve(
            $historiqueQuiz->started_at,
            $historiqueQuiz->completed_at,
            (int) $historiqueQuiz->total_questions,
            $historiqueQuiz->time_spent,
            $answeredCount
        );

        return view('historique-quiz.show', compact('historiqueQuiz', 'submissionStatus', 'answeredCount'));
    }

    public function byStudent(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $query = HistoriqueQuiz::with(['quiz', 'product.variations'])
            ->where('student_id', $studentId);

        if ($request->filled('course')) {
            $query->where('product_id', $request->course);
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('success', true);
            } elseif ($request->status === 'failed') {
                $query->where('success', false);
            }
        }

        $historiqueQuiz = $query->orderBy('completed_at', 'desc')->paginate(20);

        $courses = Product::whereIn('id', function ($query) use ($studentId) {
            $query->select('product_id')
                ->from('historique_quizzes')
                ->where('student_id', $studentId);
        })->with('variations')->orderBy('id')->get();

        $stats = [
            'total' => HistoriqueQuiz::where('student_id', $studentId)->count(),
            'successful' => HistoriqueQuiz::where('student_id', $studentId)->where('success', true)->count(),
            'failed' => HistoriqueQuiz::where('student_id', $studentId)->where('success', false)->count(),
            'average_score' => HistoriqueQuiz::where('student_id', $studentId)->avg('score') ?? 0,
            'unique_quizzes' => HistoriqueQuiz::where('student_id', $studentId)->distinct('quiz_id')->count('quiz_id'),
            'unique_courses' => HistoriqueQuiz::where('student_id', $studentId)->distinct('product_id')->count('product_id'),
        ];

        return view('historique-quiz.by-student', compact('historiqueQuiz', 'student', 'courses', 'stats'));
    }

    public function byCourse(Request $request, $courseId)
    {
        $course = Product::with('variations')->findOrFail($courseId);

        $query = HistoriqueQuiz::with(['student', 'quiz'])
            ->where('product_id', $courseId);

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->student}%")
                    ->orWhere('last_name', 'like', "%{$request->student}%")
                    ->orWhere('email', 'like', "%{$request->student}%");
            });
        }

        if ($request->filled('quiz')) {
            $query->where('quiz_id', $request->quiz);
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('success', true);
            } elseif ($request->status === 'failed') {
                $query->where('success', false);
            }
        }

        $historiqueQuiz = $query->orderBy('completed_at', 'desc')->paginate(20);

        $quizzes = Quiz::whereIn('id', function ($query) use ($courseId) {
            $query->select('quiz_id')
                ->from('historique_quizzes')
                ->where('product_id', $courseId);
        })->orderBy('name_en')->get();

        $stats = [
            'total' => HistoriqueQuiz::where('product_id', $courseId)->count(),
            'successful' => HistoriqueQuiz::where('product_id', $courseId)->where('success', true)->count(),
            'failed' => HistoriqueQuiz::where('product_id', $courseId)->where('success', false)->count(),
            'average_score' => HistoriqueQuiz::where('product_id', $courseId)->avg('score') ?? 0,
            'unique_students' => HistoriqueQuiz::where('product_id', $courseId)->distinct('student_id')->count('student_id'),
            'unique_quizzes' => HistoriqueQuiz::where('product_id', $courseId)->distinct('quiz_id')->count('quiz_id'),
        ];

        return view('historique-quiz.by-course', compact('historiqueQuiz', 'course', 'quizzes', 'stats'));
    }

    public function statistics()
    {
        $stats = [
            'total_attempts' => HistoriqueQuiz::count(),
            'successful_attempts' => HistoriqueQuiz::where('success', true)->count(),
            'failed_attempts' => HistoriqueQuiz::where('success', false)->count(),
            'average_score' => HistoriqueQuiz::avg('score') ?? 0,
            'unique_students' => HistoriqueQuiz::distinct('student_id')->count('student_id'),
            'unique_quizzes' => HistoriqueQuiz::distinct('quiz_id')->count('quiz_id'),
            'unique_courses' => HistoriqueQuiz::distinct('product_id')->count('product_id'),
        ];

        $dailyStats = HistoriqueQuiz::selectRaw('DATE(completed_at) as date, COUNT(*) as attempts, AVG(score) as avg_score')
            ->where('completed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topStudents = HistoriqueQuiz::selectRaw('student_id, AVG(score) as avg_score, COUNT(*) as attempts')
            ->with('student')
            ->groupBy('student_id')
            ->having('attempts', '>=', 3)
            ->orderBy('avg_score', 'desc')
            ->limit(10)
            ->get();

        $topQuizzes = HistoriqueQuiz::selectRaw('quiz_id, COUNT(*) as attempts, AVG(score) as avg_score')
            ->with('quiz')
            ->groupBy('quiz_id')
            ->orderBy('attempts', 'desc')
            ->limit(10)
            ->get();

        return view('historique-quiz.statistics', compact('stats', 'dailyStats', 'topStudents', 'topQuizzes'));
    }
}
