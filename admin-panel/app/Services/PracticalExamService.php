<?php

namespace App\Services;

use App\Mail\PracticalExamGraded;
use App\Models\EmailLog;
use App\Models\Notification;
use App\Models\PracticalExamAttempt;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PracticalExamService
{
    public function index(Request $request)
    {
        $query = PracticalExamAttempt::with(['user', 'product', 'trainingCase', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc');

        if ($request->filled('course_id')) {
            $query->where('product_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $attempts = $query->paginate(20);

        $courses = Product::where('has_practical_exam', true)
            ->orderBy('video')
            ->get();

        $pendingCount = PracticalExamAttempt::where('status', 'pending_review')->count();

        return view('practical-exams.index', compact('attempts', 'courses', 'pendingCount'));
    }

    public function show(int $attemptId)
    {
        $attempt = PracticalExamAttempt::with(['user', 'product', 'trainingCase', 'trainingCaseFile', 'reviewer'])
            ->findOrFail($attemptId);

        return view('practical-exams.grade', compact('attempt'));
    }

    public function grade(Request $request, int $attemptId)
    {
        $request->validate([
            'status'        => 'required|in:passed,failed',
            'admin_comment' => 'required|string|max:1000',
        ], [
            'status.required'        => 'Please select a decision (Pass or Fail).',
            'status.in'              => 'Invalid decision selected.',
            'admin_comment.required' => 'Please provide feedback for the student.',
            'admin_comment.max'      => 'Feedback cannot exceed 1000 characters.',
        ]);

        $attempt = PracticalExamAttempt::findOrFail($attemptId);

        $attempt->update([
            'status'        => $request->status,
            'admin_comment' => $request->admin_comment,
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
        ]);

        try {
            $student   = $attempt->user;
            $product   = $attempt->product;
            $isPassed  = $request->status === 'passed';
            $actionUrl = env('USER_URL', 'http://localhost:8000') . '/student-dashboard/practical-exams';

            Notification::notifyStudent(
                $student->id,
                $isPassed ? 'practical_exam_passed' : 'practical_exam_failed',
                $isPassed ? 'Practical Exam Passed' : 'Practical Exam Result',
                $isPassed
                    ? 'Congratulations! You passed the practical exam for ' . $product->titre . '.'
                    : 'Your practical exam for ' . $product->titre . ' has been reviewed. Check the feedback to improve.',
                $actionUrl,
                [
                    'product_id'     => $product->id,
                    'attempt_number' => $attempt->attempt_number,
                    'feedback'       => $request->admin_comment,
                ],
                null,
                $isPassed ? 'success' : 'warning',
                true
            );

            Mail::to($student->email)->send(new PracticalExamGraded($attempt));

            EmailLog::logSent(
                $student->email,
                'practical_exam_graded',
                'Practical Exam Result: ' . $product->titre,
                $student->id,
                ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                'PracticalExamAttempt',
                $attempt->id
            );
        } catch (\Exception $e) {
            Log::error('Failed to send practical exam grade notification: ' . $e->getMessage());

            EmailLog::logFailed(
                $student->email ?? 'unknown',
                'practical_exam_graded',
                'Practical Exam Result: ' . ($product->titre ?? 'N/A'),
                $e->getMessage(),
                $student->id ?? null,
                ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                'PracticalExamAttempt',
                $attempt->id ?? null
            );
        }

        $successMessage = $request->status === 'passed'
            ? 'Student has been marked as PASSED. Notification sent.'
            : 'Student has been marked as FAILED. They can retry the exam.';

        return redirect()->route('practical-exams.index')->with('success', $successMessage);
    }

    public function getStats()
    {
        $stats = [
            'total'       => PracticalExamAttempt::count(),
            'pending'     => PracticalExamAttempt::where('status', 'pending')->count(),
            'passed'      => PracticalExamAttempt::where('status', 'passed')->count(),
            'failed'      => PracticalExamAttempt::where('status', 'failed')->count(),
            'in_progress' => PracticalExamAttempt::where('status', 'in_progress')->count(),
        ];

        return response()->json($stats);
    }
}
