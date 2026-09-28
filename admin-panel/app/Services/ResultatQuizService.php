<?php

namespace App\Services;

use App\Models\ResultatQuiz;
use App\Models\Product;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\Notification;
use App\Models\StudentSuccess;
use App\Models\HistoriqueQuiz;
use App\Models\ResponseQuiz;
use App\Support\QuizSubmissionStatus;
use App\Mail\QuizPassed;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ResultatQuizService
{
    public function index(Request $request): array
    {
        $query = ResultatQuiz::with(['student', 'product', 'quiz.type']);

        if ($request->filled('success')) {
            $query->where('success', $request->success);
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        if ($request->filled('quiz')) {
            $query->where('quiz_id', $request->quiz);
        }

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->student . '%')
                  ->orWhere('last_name', 'like', '%' . $request->student . '%')
                  ->orWhere('email', 'like', '%' . $request->student . '%');
            });
        }

        if ($request->filled('date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('completed_at', $request->date)
                    ->orWhere(function ($inner) use ($request) {
                        $inner->whereNull('completed_at')
                            ->whereDate('updated_at', $request->date);
                    });
            });
        }

        $results = $query
            ->orderByRaw('COALESCE(completed_at, updated_at, created_at) DESC')
            ->paginate(15);

        $this->attachLastQuizDates($results);

        foreach ($results as $result) {
            $resolved = $this->resolveResultResponses($result);
            $responses = $resolved['responses'];

            $result->responses = $responses;
            $result->total_questions = $resolved['totalQuestions'];
            $result->correct_answers = $resolved['correctAnswers'];
        }

        $products = Product::all();

        if ($request->filled('product')) {
            $quizzes = Quiz::whereHas('products', function ($q) use ($request) {
                $q->where('products.id', $request->product);
            })->get();
        } else {
            $quizzes = Quiz::all();
        }

        $stats = [
            'total'         => ResultatQuiz::count(),
            'successful'    => ResultatQuiz::where('success', true)->count(),
            'failed'        => ResultatQuiz::where('success', false)->count(),
            'average_score' => ResultatQuiz::avg('score') ?? 0,
        ];

        return compact('results', 'products', 'quizzes', 'stats');
    }

    public function getCreateData(): array
    {
        return [
            'students' => Student::all(),
            'products' => Product::all(),
            'quizzes'  => Quiz::all(),
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'quiz_id'    => 'required|exists:quizzes,id',
            'score'      => 'required|numeric|min:0|max:100',
            'success'    => 'boolean',
            'attempts'   => 'required|integer|min:1|max:10',
        ]);

        $hasAccess = DB::table('product_students')
            ->where('product_id', $request->product_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if (!$hasAccess) {
            return back()->withErrors(['error' => 'Cet étudiant n\'a pas accès à ce cours.']);
        }

        $existingResult = ResultatQuiz::where('student_id', $request->student_id)
            ->where('product_id', $request->product_id)
            ->where('quiz_id', $request->quiz_id)
            ->first();

        if ($existingResult) {
            return back()->withErrors(['error' => 'Un résultat existe déjà pour cet étudiant et ce quiz.']);
        }

        $success = $request->has('success') ? $request->success : ($request->score >= 50);

        ResultatQuiz::create([
            'student_id' => $request->student_id,
            'product_id' => $request->product_id,
            'quiz_id'    => $request->quiz_id,
            'score'      => $request->score,
            'success'    => $success,
            'attempts'   => $request->attempts,
        ]);

        return redirect()->route('admin.resultat-quizzes.index')->with('success', 'Résultat créé avec succès.');
    }

    public function show(ResultatQuiz $resultatQuiz): array
    {
        $resultatQuiz->load(['student', 'product', 'quiz.type']);

        $attemptHistory = $this->buildAttemptHistory($resultatQuiz);

        if ($resultatQuiz->started_at && !$resultatQuiz->completed_at) {
            $hasCurrentSummary = $attemptHistory->contains(
                fn (array $attempt) => (int) $attempt['attempt_number'] === (int) $resultatQuiz->attempts
            );

            if (!$hasCurrentSummary) {
                $totalQuestions = max(1, (int) ($resultatQuiz->quiz?->questions()->count() ?? 10));
                $attemptHistory->prepend([
                    'summary' => null,
                    'attempt_number' => $resultatQuiz->attempts,
                    'responses' => collect(),
                    'total_questions' => $totalQuestions,
                    'correct_answers' => 0,
                    'score_percentage' => $resultatQuiz->score ?? 0,
                    'success' => (bool) $resultatQuiz->success,
                    'time_spent' => null,
                    'completed_at' => null,
                    'started_at' => $resultatQuiz->started_at,
                    'submission_status' => QuizSubmissionStatus::IN_PROGRESS,
                    'submission_status_label' => QuizSubmissionStatus::label(QuizSubmissionStatus::IN_PROGRESS),
                    'submission_status_badge' => QuizSubmissionStatus::badgeClass(QuizSubmissionStatus::IN_PROGRESS),
                    'is_current' => true,
                ]);
            }
        }

        $currentAttempt = $attemptHistory->firstWhere('is_current', true)
            ?? $attemptHistory->first();

        $responses = $currentAttempt['responses'] ?? collect();
        $totalQuestions = (int) ($currentAttempt['total_questions'] ?? 0);
        $correctAnswers = (int) ($currentAttempt['correct_answers'] ?? 0);
        $incorrectAnswers = $responses->where('is_correct', false)->count();
        $scorePercentage = $currentAttempt['score_percentage'] ?? ($resultatQuiz->score ?? 0);
        $submissionStatus = $currentAttempt['submission_status'] ?? QuizSubmissionStatus::COMPLETED;
        $submissionStatusLabel = $currentAttempt['submission_status_label'] ?? QuizSubmissionStatus::label($submissionStatus);
        $submissionStatusBadge = $currentAttempt['submission_status_badge'] ?? QuizSubmissionStatus::badgeClass($submissionStatus);

        return compact(
            'resultatQuiz',
            'responses',
            'totalQuestions',
            'correctAnswers',
            'incorrectAnswers',
            'scorePercentage',
            'attemptHistory',
            'submissionStatus',
            'submissionStatusLabel',
            'submissionStatusBadge'
        );
    }

    /**
     * All completed attempts for this student / course / quiz (same logic as user history).
     */
    private function buildAttemptHistory(ResultatQuiz $resultatQuiz): Collection
    {
        $summariesQuery = HistoriqueQuiz::where('student_id', $resultatQuiz->student_id)
            ->where('quiz_id', $resultatQuiz->quiz_id)
            ->whereNull('question_id');

        if ($resultatQuiz->product_id) {
            $summariesQuery->where(function ($query) use ($resultatQuiz) {
                $query->where('product_id', $resultatQuiz->product_id)
                    ->orWhereNull('product_id');
            });
        }

        $summaries = $summariesQuery
            ->orderByDesc('attempt_number')
            ->orderByDesc('completed_at')
            ->get()
            ->unique('attempt_number')
            ->values();

        if ($summaries->isEmpty()) {
            $resolved = $this->resolveResultResponses($resultatQuiz);
            $responses = $resolved['responses'];
            $answeredCount = $responses->count();
            $totalQuestions = max((int) $resolved['totalQuestions'], 1);
            $status = QuizSubmissionStatus::resolve(
                $resultatQuiz->started_at,
                $resultatQuiz->completed_at,
                $totalQuestions,
                null,
                $answeredCount > 0 ? $answeredCount : null
            );

            return collect([[
                'summary' => null,
                'attempt_number' => $resultatQuiz->attempts,
                'responses' => $responses,
                'total_questions' => $resolved['totalQuestions'],
                'correct_answers' => $resolved['correctAnswers'],
                'score_percentage' => $resultatQuiz->score ?? 0,
                'success' => (bool) $resultatQuiz->success,
                'time_spent' => null,
                'completed_at' => $resultatQuiz->completed_at ?? $resultatQuiz->updated_at,
                'started_at' => $resultatQuiz->started_at,
                'submission_status' => $status,
                'submission_status_label' => QuizSubmissionStatus::label($status),
                'submission_status_badge' => QuizSubmissionStatus::badgeClass($status),
                'is_current' => true,
            ]]);
        }

        return $summaries->map(function (HistoriqueQuiz $summary) use ($resultatQuiz) {
            $responses = HistoriqueQuiz::with(['question.reponses', 'response'])
                ->where('student_id', $summary->student_id)
                ->where('quiz_id', $summary->quiz_id)
                ->where('attempt_number', $summary->attempt_number)
                ->whereNotNull('question_id')
                ->when($summary->product_id || $resultatQuiz->product_id, function ($query) use ($summary, $resultatQuiz) {
                    $productId = $summary->product_id ?? $resultatQuiz->product_id;
                    $query->where(function ($inner) use ($productId) {
                        $inner->where('product_id', $productId)
                            ->orWhereNull('product_id');
                    });
                })
                ->orderBy('id')
                ->get();

            if ($responses->isEmpty() && $summary->attempt_number === $resultatQuiz->attempts) {
                $responses = ResponseQuiz::with(['question.reponses', 'reponse'])
                    ->where('student_id', $resultatQuiz->student_id)
                    ->where('quiz_id', $resultatQuiz->quiz_id)
                    ->where('product_id', $resultatQuiz->product_id)
                    ->orderBy('id')
                    ->get();
            }

            $answeredCount = $responses->count();
            $status = QuizSubmissionStatus::resolve(
                $summary->started_at,
                $summary->completed_at,
                (int) ($summary->total_questions ?: max($answeredCount, 1)),
                $summary->time_spent,
                $answeredCount
            );

            return [
                'summary' => $summary,
                'attempt_number' => $summary->attempt_number,
                'responses' => $responses,
                'total_questions' => (int) $summary->total_questions,
                'correct_answers' => (int) $summary->correct_answers,
                'score_percentage' => $summary->score_percentage,
                'success' => (bool) $summary->success,
                'time_spent' => $summary->time_spent,
                'completed_at' => $summary->completed_at,
                'started_at' => $summary->started_at,
                'submission_status' => $status,
                'submission_status_label' => QuizSubmissionStatus::label($status),
                'submission_status_badge' => QuizSubmissionStatus::badgeClass($status),
                'is_current' => (int) $summary->attempt_number === (int) $resultatQuiz->attempts,
            ];
        });
    }

    /**
     * Load question-level answers for a result (same attempt as user dashboard).
     */
    private function resolveResultResponses(ResultatQuiz $resultatQuiz): array
    {
        $summaryQuery = HistoriqueQuiz::where('student_id', $resultatQuiz->student_id)
            ->where('quiz_id', $resultatQuiz->quiz_id)
            ->where('attempt_number', $resultatQuiz->attempts)
            ->whereNull('question_id');

        if ($resultatQuiz->product_id) {
            $summaryQuery->where('product_id', $resultatQuiz->product_id);
        }

        $summary = $summaryQuery->orderByDesc('completed_at')->first();

        if (!$summary) {
            $summary = HistoriqueQuiz::where('student_id', $resultatQuiz->student_id)
                ->where('quiz_id', $resultatQuiz->quiz_id)
                ->where('attempt_number', $resultatQuiz->attempts)
                ->whereNull('question_id')
                ->orderByDesc('completed_at')
                ->first();
        }

        $attemptNumber = $summary?->attempt_number ?? $resultatQuiz->attempts;

        $historiqueResponses = HistoriqueQuiz::with(['question.reponses', 'response'])
            ->where('student_id', $resultatQuiz->student_id)
            ->where('quiz_id', $resultatQuiz->quiz_id)
            ->where('attempt_number', $attemptNumber)
            ->whereNotNull('question_id')
            ->when($resultatQuiz->product_id, function ($query) use ($resultatQuiz) {
                $query->where(function ($inner) use ($resultatQuiz) {
                    $inner->where('product_id', $resultatQuiz->product_id)
                        ->orWhereNull('product_id');
                });
            })
            ->orderBy('id')
            ->get();

        if ($historiqueResponses->isNotEmpty()) {
            return [
                'responses' => $historiqueResponses,
                'totalQuestions' => (int) ($summary?->total_questions ?: $historiqueResponses->count()),
                'correctAnswers' => (int) ($summary?->correct_answers ?? $historiqueResponses->where('is_correct', true)->count()),
            ];
        }

        $responseQuizRows = ResponseQuiz::with(['question.reponses', 'reponse'])
            ->where('student_id', $resultatQuiz->student_id)
            ->where('quiz_id', $resultatQuiz->quiz_id)
            ->where('product_id', $resultatQuiz->product_id)
            ->orderBy('id')
            ->get();

        return [
            'responses' => $responseQuizRows,
            'totalQuestions' => (int) ($summary?->total_questions ?: max($responseQuizRows->count(), 1)),
            'correctAnswers' => (int) ($summary?->correct_answers ?? $responseQuizRows->where('is_correct', true)->count()),
        ];
    }

    public function getEditData(ResultatQuiz $resultatQuiz): array
    {
        return [
            'resultatQuiz' => $resultatQuiz,
            'students'     => Student::all(),
            'products'     => Product::all(),
            'quizzes'      => Quiz::all(),
        ];
    }

    public function update(Request $request, ResultatQuiz $resultatQuiz)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'quiz_id'    => 'required|exists:quizzes,id',
            'score'      => 'required|numeric|min:0|max:100',
            'success'    => 'boolean',
            'attempts'   => 'required|integer|min:1|max:10',
        ]);

        $hasAccess = DB::table('product_students')
            ->where('product_id', $request->product_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if (!$hasAccess) {
            return back()->withErrors(['error' => 'Cet étudiant n\'a pas accès à ce cours.']);
        }

        $success         = $request->has('success') ? $request->success : ($request->score >= 50);
        $wasNotSuccessful = !$resultatQuiz->success;
        $willBeSuccessful = $success;

        $resultatQuiz->update([
            'student_id' => $request->student_id,
            'product_id' => $request->product_id,
            'quiz_id'    => $request->quiz_id,
            'score'      => $request->score,
            'success'    => $success,
            'attempts'   => $request->attempts,
        ]);

        $this->syncStudentExamAttempts(
            (int) $request->student_id,
            (int) $request->product_id,
            (int) $request->quiz_id,
            (int) $request->attempts
        );

        if ($wasNotSuccessful && $willBeSuccessful) {
            try {
                $resultatQuiz->load(['student', 'product', 'quiz.type']);
                Mail::to($resultatQuiz->student->email)->send(new QuizPassed($resultatQuiz));

                \App\Models\EmailLog::logSent(
                    $resultatQuiz->student->email,
                    'quiz_passed',
                    'Quiz Passed: ' . ($resultatQuiz->product->titre ?? 'N/A'),
                    $resultatQuiz->student->id,
                    ($resultatQuiz->student->first_name ?? '') . ' ' . ($resultatQuiz->student->last_name ?? ''),
                    'ResultatQuiz',
                    $resultatQuiz->id
                );

                return redirect()->route('admin.resultat-quizzes.index')
                    ->with('success', 'Result updated successfully and notification email sent to the student.');
            } catch (\Exception $e) {
                \App\Models\EmailLog::logFailed(
                    $resultatQuiz->student->email ?? 'unknown',
                    'quiz_passed',
                    'Quiz Passed: ' . ($resultatQuiz->product->titre ?? 'N/A'),
                    $e->getMessage(),
                    $resultatQuiz->student->id ?? null,
                    ($resultatQuiz->student->first_name ?? '') . ' ' . ($resultatQuiz->student->last_name ?? ''),
                    'ResultatQuiz',
                    $resultatQuiz->id
                );

                return redirect()->route('admin.resultat-quizzes.index')
                    ->with('success', 'Result updated successfully.')
                    ->with('warning', 'Error sending email: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.resultat-quizzes.index')->with('success', 'Result updated successfully.');
    }

    public function destroy(ResultatQuiz $resultatQuiz)
    {
        try {
            $resultatQuiz->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.']);
        }
    }

    public function adminUpdateSuccess(Request $request, ResultatQuiz $resultatQuiz)
    {
        $request->validate(['success' => 'required|boolean']);

        try {
            $wasNotSuccessful = !$resultatQuiz->success;
            $willBeSuccessful = $request->success;

            $resultatQuiz->update(['success' => $request->success]);

            if ($wasNotSuccessful && $willBeSuccessful) {
                try {
                    $resultatQuiz->load(['student', 'product', 'quiz.type']);
                    Mail::to($resultatQuiz->student->email)->send(new QuizPassed($resultatQuiz));

                    \App\Models\EmailLog::logSent(
                        $resultatQuiz->student->email,
                        'quiz_passed',
                        'Quiz Passed: ' . ($resultatQuiz->product->titre ?? 'N/A'),
                        $resultatQuiz->student->id,
                        ($resultatQuiz->student->first_name ?? '') . ' ' . ($resultatQuiz->student->last_name ?? ''),
                        'ResultatQuiz',
                        $resultatQuiz->id
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Status updated successfully and notification email sent to the student.',
                    ]);
                } catch (\Exception $e) {
                    \App\Models\EmailLog::logFailed(
                        $resultatQuiz->student->email ?? 'unknown',
                        'quiz_passed',
                        'Quiz Passed: ' . ($resultatQuiz->product->titre ?? 'N/A'),
                        $e->getMessage(),
                        $resultatQuiz->student->id ?? null,
                        ($resultatQuiz->student->first_name ?? '') . ' ' . ($resultatQuiz->student->last_name ?? ''),
                        'ResultatQuiz',
                        $resultatQuiz->id
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Status updated successfully.',
                        'warning' => 'Error sending email: ' . $e->getMessage(),
                    ]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status.']);
        }
    }

    public function resetAttempts(ResultatQuiz $resultatQuiz)
    {
        try {
            $resultatQuiz->update(['attempts' => 0]);

            $this->syncStudentExamAttempts(
                (int) $resultatQuiz->student_id,
                (int) $resultatQuiz->product_id,
                (int) $resultatQuiz->quiz_id,
                0
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la réinitialisation.']);
        }
    }

    public function markAsSuccess(ResultatQuiz $resultatQuiz)
    {
        try {
            DB::beginTransaction();

            $resultatQuiz->update([
                'score'    => 100,
                'success'  => true,
                'attempts' => $resultatQuiz->attempts + 1,
            ]);

            $existingSuccess = StudentSuccess::where('student_id', $resultatQuiz->student_id)
                ->where('product_id', $resultatQuiz->product_id)
                ->first();

            if (!$existingSuccess) {
                $studentSuccess = StudentSuccess::create([
                    'student_id'   => $resultatQuiz->student_id,
                    'product_id'   => $resultatQuiz->product_id,
                    'success'      => 1,
                    'admin_notes'  => 'Auto-approved: Quiz passed with 100% score',
                    'validated_at' => now(),
                ]);

                $student = Student::find($resultatQuiz->student_id);
                $product = Product::find($resultatQuiz->product_id);
                if ($student && $product) {
                    Notification::notifyAllAdmins(
                        Notification::TYPE_EVALUATION,
                        'Exam Passed - ' . $student->first_name . ' ' . $student->last_name,
                        $student->first_name . ' ' . $student->last_name . ' passed the exam for "' . $product->title . '"',
                        route('admin.student-successes.show', $studentSuccess->id),
                        ['student_success_id' => $studentSuccess->id, 'exam_type' => 'quiz'],
                        '✅',
                        'green',
                        true
                    );
                }
            } else {
                if ($existingSuccess->success == 0) {
                    $existingSuccess->update([
                        'success'      => 1,
                        'admin_notes'  => 'Auto-approved: Quiz passed with 100% score',
                        'validated_at' => now(),
                    ]);

                    $student = Student::find($resultatQuiz->student_id);
                    $product = Product::find($resultatQuiz->product_id);
                    if ($student && $product) {
                        Notification::notifyAllAdmins(
                            Notification::TYPE_EVALUATION,
                            'Exam Passed - ' . $student->first_name . ' ' . $student->last_name,
                            $student->first_name . ' ' . $student->last_name . ' passed the exam for "' . $product->title . '"',
                            route('admin.student-successes.show', $existingSuccess->id),
                            ['student_success_id' => $existingSuccess->id, 'exam_type' => 'quiz'],
                            '✅',
                            'green',
                            true
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz marked as 100% success and student success created/updated!',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function syncStudentExamAttempts(int $studentId, int $productId, int $quizId, int $attempts): void
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz || (int) $quiz->type_id !== 1) {
            return;
        }

        $maxExamAttempts = Product::where('id', $productId)->value('max_exam_attempts') ?? 3;
        $isBlocked       = $attempts >= (int) $maxExamAttempts;

        $existingAttempt = DB::table('student_exam_attempts')
            ->where('student_id', $studentId)
            ->where('product_id', $productId)
            ->where('quiz_id', $quizId)
            ->first();

        $updateData = [
            'attempts'   => $attempts,
            'is_blocked' => $isBlocked,
            'updated_at' => now(),
        ];

        if ($isBlocked) {
            $updateData['blocked_at'] = $existingAttempt && $existingAttempt->is_blocked
                ? $existingAttempt->blocked_at
                : now();
        } else {
            $updateData['blocked_at']               = null;
            $updateData['renewal_email_sent_at']    = null;
        }

        if ($existingAttempt) {
            DB::table('student_exam_attempts')
                ->where('student_id', $studentId)
                ->where('product_id', $productId)
                ->where('quiz_id', $quizId)
                ->update($updateData);
            return;
        }

        DB::table('student_exam_attempts')->insert(array_merge($updateData, [
            'student_id' => $studentId,
            'product_id' => $productId,
            'quiz_id'    => $quizId,
            'created_at' => now(),
        ]));
    }

    /**
     * Set last_quiz_at on each result (last submission, not record creation date).
     */
    private function attachLastQuizDates($results): void
    {
        if ($results->isEmpty()) {
            return;
        }

        $historiqueDates = HistoriqueQuiz::query()
            ->select('student_id', 'quiz_id', 'product_id', DB::raw('MAX(completed_at) as last_completed_at'))
            ->whereNull('question_id')
            ->where(function ($query) use ($results) {
                foreach ($results as $result) {
                    $query->orWhere(function ($inner) use ($result) {
                        $inner->where('student_id', $result->student_id)
                            ->where('quiz_id', $result->quiz_id)
                            ->where('product_id', $result->product_id);
                    });
                }
            })
            ->groupBy('student_id', 'quiz_id', 'product_id')
            ->get()
            ->keyBy(fn ($row) => "{$row->student_id}-{$row->quiz_id}-{$row->product_id}");

        foreach ($results as $result) {
            $key = "{$result->student_id}-{$result->quiz_id}-{$result->product_id}";
            $historiqueDate = $historiqueDates->get($key)?->last_completed_at;

            $result->last_quiz_at = $result->completed_at
                ?? ($historiqueDate ? Carbon::parse($historiqueDate) : null)
                ?? $result->updated_at
                ?? $result->created_at;
        }
    }
}
