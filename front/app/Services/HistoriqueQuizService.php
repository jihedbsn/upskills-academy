<?php

namespace App\Services;

use App\Models\HistoriqueQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoriqueQuizService
{
    public function index(Request $request, $productId = null)
    {
        $studentId = Auth::id();

        $query = HistoriqueQuiz::with(['product', 'quiz', 'question', 'response'])
            ->where('student_id', $studentId)
            ->orderBy('completed_at', 'desc');

        if ($productId) {
            $query->where('product_id', $productId);
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

        $historique = $query->paginate(20);
        $stats = HistoriqueQuiz::getStudentStats($studentId, $productId);

        return view('student-dashboard.historique-quiz', compact('historique', 'stats', 'productId'));
    }

    public function getStudentHistory(Request $request, $studentId)
    {
        if (Auth::id() != $studentId && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = HistoriqueQuiz::with(['product', 'quiz'])
            ->where('student_id', $studentId);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('quiz_type')) {
            $query->where('quiz_type', $request->quiz_type);
        }

        $historique = $query->orderBy('completed_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $historique,
        ]);
    }

    public function getStudentStats(Request $request, $studentId)
    {
        if (Auth::id() != $studentId && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $productId = $request->get('product_id');
        $stats = HistoriqueQuiz::getStudentStats($studentId, $productId);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function getProductStats($productId)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = HistoriqueQuiz::getProductStats($productId);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function show($id)
    {
        $historique = HistoriqueQuiz::with(['product', 'quiz', 'question', 'response', 'student'])
            ->findOrFail($id);

        if ($historique->student_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('student-dashboard.historique-quiz-details', compact('historique'));
    }

    public function getQuizHistory($studentId, $quizId)
    {
        if (Auth::id() != $studentId && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $historique = HistoriqueQuiz::with(['product', 'quiz', 'question', 'response'])
            ->where('student_id', $studentId)
            ->where('quiz_id', $quizId)
            ->orderBy('attempt_number', 'desc')
            ->get();

        $groupedHistory = $historique->groupBy('attempt_number');

        return response()->json([
            'success' => true,
            'data' => $groupedHistory,
        ]);
    }

    public function getGlobalStats()
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_attempts' => HistoriqueQuiz::count(),
            'unique_students' => HistoriqueQuiz::distinct('student_id')->count(),
            'unique_courses' => HistoriqueQuiz::distinct('product_id')->count(),
            'success_rate' => HistoriqueQuiz::successful()->count() / max(HistoriqueQuiz::count(), 1) * 100,
            'average_score' => HistoriqueQuiz::avg('score_percentage'),
            'recent_activity' => HistoriqueQuiz::recent(7)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
