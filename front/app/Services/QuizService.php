<?php

namespace App\Services;

use App\Mail\ExamAttemptsExceeded;
use App\Models\HistoriqueQuiz;
use App\Models\OrderSpecifique;
use App\Models\Product;
use App\Models\Quiz;
use App\Models\ResponseQuiz;
use App\Models\ResultatQuiz;
use App\Models\StudentExamAttempt;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuizService
{
    public function start(Request $request, $locale, Product $product, Quiz $quiz)
    {
        $blockResponse = app(BlockStudentCourseService::class)->denyIfBlocked($product->id, $locale);
        if ($blockResponse) {
            return $blockResponse;
        }

        if (!Auth::user()->hasPurchasedProduct($product->id)) {
            $productStudent = Auth::user()->productAccess()
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->first();

            if ($productStudent && $productStudent->is_expired) {
                return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
                    ->with('error', __('messages.course_expired'));
            }

            return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
                ->with('error', __('messages.no_access_to_course'));
        }

        $blockReason = app(InstallmentService::class)->getQuizAccessBlockReason($product, $quiz, Auth::id());
        if ($blockReason !== null) {
            return $this->redirectQuizBlocked($blockReason, $product, $locale);
        }

        if ($quiz->type_id == 1) {
            $examAttempt = StudentExamAttempt::firstOrCreate(
                [
                    'student_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quiz_id' => $quiz->id,
                ],
                [
                    'attempts' => 0,
                    'is_blocked' => false,
                ]
            );

            if ($examAttempt->is_blocked) {
                return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
                    ->with('error', __('messages.exam_blocked_renewal_required'));
            }

            $maxExamAttempts = $product->max_exam_attempts ?? 3;
            if ($examAttempt->attempts >= $maxExamAttempts) {
                $examAttempt->update([
                    'is_blocked' => true,
                    'blocked_at' => now(),
                ]);

                if (!$examAttempt->renewal_email_sent_at) {
                    try {
                        Mail::to(Auth::user()->email)->send(
                            new ExamAttemptsExceeded(Auth::user(), $product, $quiz, $examAttempt->attempts)
                        );
                        $examAttempt->update(['renewal_email_sent_at' => now()]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send exam attempts exceeded email', [
                            'student_id' => Auth::id(),
                            'product_id' => $product->id,
                            'quiz_id' => $quiz->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
                    ->with('error', __('messages.exam_max_attempts_exceeded', ['price' => $product->renewal_price ?? 50]));
            }
        }

        $resultatQuiz = ResultatQuiz::where('student_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('quiz_id', $quiz->id)
            ->first();

        $maxAttempts = $quiz->max_attempts;

        if ($resultatQuiz && $resultatQuiz->success) {
            return redirect()->route('quiz.result', ['product' => $product->id, 'quiz' => $quiz->id])
                ->with('info', __('messages.quiz_already_completed'));
        }

        if ($quiz->type_id != 1 && !is_null($maxAttempts) && $resultatQuiz && $resultatQuiz->attempts >= $maxAttempts) {
            return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
                ->with('error', __('messages.quiz_max_attempts_reached', ['count' => $maxAttempts]));
        }

        $resultatQuiz = ResultatQuiz::where('student_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->first();

        if ($resultatQuiz) {
            $resultatQuiz->update([
                'product_id' => $product->id,
                'score' => 0,
                'success' => false,
                'attempts' => $resultatQuiz->attempts + 1,
                'started_at' => now(),
                'completed_at' => null,
            ]);
        } else {
            $resultatQuiz = ResultatQuiz::create([
                'student_id' => Auth::id(),
                'product_id' => $product->id,
                'quiz_id' => $quiz->id,
                'score' => 0,
                'success' => false,
                'attempts' => 1,
                'started_at' => now(),
            ]);
        }

        $quizWithPivot = $product->quizzes()
            ->where('quiz_id', $quiz->id)
            ->with(['questions.reponses'])
            ->first();

        if (!$quizWithPivot) {
            return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
                ->with('error', __('messages.quiz_not_found'));
        }

        $configuredQuestionCount = optional($quizWithPivot->pivot)->nb_question_affiche;
        $configuredQuestionCount = $configuredQuestionCount !== null ? (int) $configuredQuestionCount : null;
        $useOwnQuestions = optional($quizWithPivot->pivot)->use_own_questions ?? false;

        if ($quiz->type_id == 1) {
            if ($useOwnQuestions) {
                $allQuestions = $quizWithPivot->questions ?? new EloquentCollection();
                $allQuestionsCollection = new EloquentCollection($allQuestions->all());
                $questionLimit = $this->resolveQuestionLimit(
                    $configuredQuestionCount,
                    $allQuestionsCollection->count(),
                    min(10, $allQuestionsCollection->count())
                );

                if ($questionLimit === 0) {
                    return redirect()->route('courses.show', $product)
                        ->with('error', 'Aucune question disponible dans cet examen. Veuillez ajouter des questions ou décocher "Use Own Questions".');
                }

                $selectedQuestions = $this->pickRandomQuestions($allQuestionsCollection, $questionLimit);
                $quiz->setRelation('questions', $selectedQuestions);
            } else {
                $courseQuizzes = $product->quizzes()
                    ->where('type_id', 2)
                    ->with(['questions.reponses'])
                    ->get();

                $allQuestions = $courseQuizzes->flatMap(function ($courseQuiz) {
                    return $courseQuiz->questions;
                });

                $allQuestionsCollection = new EloquentCollection($allQuestions->all());
                $questionLimit = $this->resolveQuestionLimit(
                    $configuredQuestionCount,
                    $allQuestionsCollection->count(),
                    min(10, $allQuestionsCollection->count())
                );

                if ($questionLimit === 0) {
                    $examOwnQuestions = $quizWithPivot->questions ?? new EloquentCollection();
                    $examOwnQuestionsCollection = new EloquentCollection($examOwnQuestions->all());
                    $examOwnQuestionLimit = $this->resolveQuestionLimit(
                        $configuredQuestionCount,
                        $examOwnQuestionsCollection->count(),
                        min(10, $examOwnQuestionsCollection->count())
                    );

                    if ($examOwnQuestionLimit === 0) {
                        return redirect()->route('courses.show', $product)
                            ->with('error', 'Aucune question disponible pour cet examen. Ajoutez des questions à cet examen ou activez des quiz de cours.');
                    }

                    Log::warning('Exam started with own-questions fallback because course quiz bank is empty', [
                        'student_id' => Auth::id(),
                        'product_id' => $product->id,
                        'quiz_id' => $quiz->id,
                    ]);

                    $selectedQuestions = $this->pickRandomQuestions($examOwnQuestionsCollection, $examOwnQuestionLimit);
                } else {
                    $selectedQuestions = $this->pickRandomQuestions($allQuestionsCollection, $questionLimit);
                }
                $quiz->setRelation('questions', $selectedQuestions);
            }
        } else {
            $allQuestions = $quizWithPivot->questions ?? new EloquentCollection();
            $allQuestionsCollection = new EloquentCollection($allQuestions->all());
            $questionLimit = $this->resolveQuestionLimit(
                $configuredQuestionCount,
                $allQuestionsCollection->count(),
                min(10, $allQuestionsCollection->count())
            );

            if ($questionLimit === 0) {
                return redirect()->route('courses.show', $product)
                    ->with('error', __('messages.quiz_not_found'));
            }

            $selectedQuestions = $this->pickRandomQuestions($allQuestionsCollection, $questionLimit);
            $quiz->setRelation('questions', $selectedQuestions);
        }

        return view('quiz.start', compact('product', 'quiz', 'resultatQuiz'));
    }

    public function submit(Request $request, $locale, Product $product, Quiz $quiz)
    {
        Log::info('Quiz submit called', [
            'product_id' => $product->id,
            'quiz_id' => $quiz->id,
            'student_id' => Auth::id(),
            'answers' => $request->input('answers'),
        ]);

        if (!Auth::user()->hasPurchasedProduct($product->id)) {
            $productStudent = Auth::user()->productAccess()
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->first();

            if ($productStudent && $productStudent->is_expired) {
                return redirect()->route('courses.show', $product)
                    ->with('error', __('messages.course_expired'));
            }

            return redirect()->route('courses.show', $product)->with('error', __('messages.no_access_to_course'));
        }

        $submitBlockReason = app(InstallmentService::class)->getQuizAccessBlockReason($product, $quiz, Auth::id());
        if ($submitBlockReason !== null) {
            return $this->redirectQuizBlocked($submitBlockReason, $product, $locale);
        }

        $resultatQuiz = ResultatQuiz::where('student_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('quiz_id', $quiz->id)
            ->first();

        if (!$resultatQuiz) {
            return redirect()->route('courses.show', $product)->with('error', __('messages.quiz_not_started'));
        }

        $quizWithPivot = $product->quizzes()
            ->where('quiz_id', $quiz->id)
            ->withPivot(['nb_question_affiche', 'score_success', 'use_own_questions'])
            ->first();

        $configuredQuestionCount = optional($quizWithPivot->pivot)->nb_question_affiche;
        $configuredQuestionCount = $configuredQuestionCount !== null ? (int) $configuredQuestionCount : null;
        $successThreshold = $this->resolveSuccessThreshold(optional($quizWithPivot->pivot)->score_success);
        $useOwnQuestions = optional($quizWithPivot->pivot)->use_own_questions ?? false;

        if ($quiz->type_id == 1) {
            if ($useOwnQuestions) {
                $availableQuestionsCount = $quiz->questions()->count();
            } else {
                $availableQuestionsCount = $product->quizzes()
                    ->where('type_id', 2)
                    ->withCount('questions')
                    ->get()
                    ->sum('questions_count');
            }

            $expectedTotalQuestions = $this->resolveQuestionLimit(
                $configuredQuestionCount,
                $availableQuestionsCount,
                min(10, $availableQuestionsCount)
            );
        } else {
            $availableQuestionsCount = $quiz->questions()->count();
            $expectedTotalQuestions = $this->resolveQuestionLimit(
                $configuredQuestionCount,
                $availableQuestionsCount,
                min(10, $availableQuestionsCount)
            );
        }

        if ($expectedTotalQuestions === 0) {
            return redirect()->route('courses.show', $product)
                ->with('error', __('messages.quiz_not_found'));
        }

        $durationMinutes = max(1, $expectedTotalQuestions);
        $timeLimitSeconds = ((int) $durationMinutes) * 60;
        $startedAt = $resultatQuiz->started_at ?? now();
        $now = now();
        $hasExpired = $startedAt->diffInSeconds($now) > $timeLimitSeconds;

        ResponseQuiz::where('product_id', $product->id)
            ->where('student_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->delete();

        $answeredAnswers = $request->input('answers', []);
        $answeredAnswers = $this->mergeUnansweredQuestions(
            $answeredAnswers,
            $request->input('question_ids', [])
        );

        $correctAnswers = 0;
        $startTime = $startedAt;

        Log::info('Quiz submit - processing answers', [
            'quiz_id' => $quiz->id,
            'quiz_type_id' => $quiz->type_id,
            'use_own_questions' => $useOwnQuestions,
            'answered_count' => count(array_filter($answeredAnswers, fn ($value) => $value !== null && $value !== '')),
            'submitted_questions' => count($answeredAnswers),
            'expected_total' => $expectedTotalQuestions,
            'time_expired' => $hasExpired,
        ]);

        foreach ($answeredAnswers as $questionId => $responseId) {
            $question = $this->findQuestionForSubmission($quiz, $product, $questionId, $useOwnQuestions);

            if (!$question) {
                Log::warning('Question not found during quiz submission', [
                    'question_id' => $questionId,
                    'quiz_id' => $quiz->id,
                    'quiz_type_id' => $quiz->type_id,
                    'use_own_questions' => $useOwnQuestions,
                    'student_id' => Auth::id(),
                ]);
                continue;
            }

            $responseId = ($responseId !== null && $responseId !== '') ? (int) $responseId : null;
            $isCorrect = false;

            if ($responseId) {
                $isCorrect = $question->reponses()->where('id', $responseId)->where('is_correcte', true)->exists();

                if ($isCorrect) {
                    $correctAnswers++;
                }

                ResponseQuiz::create([
                    'product_id' => $product->id,
                    'student_id' => Auth::id(),
                    'quiz_id' => $quiz->id,
                    'question_id' => $questionId,
                    'response_id' => $responseId,
                    'is_correct' => $isCorrect,
                ]);
            }
        }

        $score = ($correctAnswers / $expectedTotalQuestions) * 100;
        $success = $score >= $successThreshold;
        $completedAt = $hasExpired
            ? $startedAt->copy()->addSeconds($timeLimitSeconds)
            : now();
        $timeSpent = $hasExpired
            ? $timeLimitSeconds
            : ($startTime ? $startTime->diffInSeconds($completedAt) : null);

        if ($quiz->type_id == 1) {
            $examAttempt = StudentExamAttempt::firstOrCreate(
                [
                    'student_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quiz_id' => $quiz->id,
                ],
                [
                    'attempts' => 0,
                    'is_blocked' => false,
                ]
            );

            $examAttempt->increment('attempts');
            $examAttempt->refresh();

            $maxExamAttempts = $product->max_exam_attempts ?? 3;

            Log::info('Exam attempt recorded', [
                'student_id' => Auth::id(),
                'product_id' => $product->id,
                'quiz_id' => $quiz->id,
                'attempts' => $examAttempt->attempts,
                'max_attempts' => $maxExamAttempts,
                'success' => $success,
                'score' => $score,
            ]);

            if ($examAttempt->attempts >= $maxExamAttempts && !$success) {
                $examAttempt->update([
                    'is_blocked' => true,
                    'blocked_at' => now(),
                ]);

                Log::info('Exam max attempts reached, blocking student', [
                    'student_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quiz_id' => $quiz->id,
                    'attempts' => $examAttempt->attempts,
                    'email_already_sent' => !is_null($examAttempt->renewal_email_sent_at),
                ]);

                if (!$examAttempt->renewal_email_sent_at) {
                    try {
                        Mail::to(Auth::user()->email)->send(
                            new ExamAttemptsExceeded(Auth::user(), $product, $quiz, $examAttempt->attempts)
                        );
                        $examAttempt->update(['renewal_email_sent_at' => now()]);

                        Log::info('Exam attempts exceeded email sent successfully', [
                            'student_id' => Auth::id(),
                            'product_id' => $product->id,
                            'quiz_id' => $quiz->id,
                            'attempts' => $examAttempt->attempts,
                            'email' => Auth::user()->email,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send exam attempts exceeded email', [
                            'student_id' => Auth::id(),
                            'product_id' => $product->id,
                            'quiz_id' => $quiz->id,
                            'email' => Auth::user()->email,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                } else {
                    Log::info('Exam attempts exceeded email already sent previously', [
                        'student_id' => Auth::id(),
                        'sent_at' => $examAttempt->renewal_email_sent_at,
                    ]);
                }
            }
        }

        $resultatQuiz->update([
            'score' => $score,
            'success' => $success,
            'completed_at' => $completedAt,
        ]);

        HistoriqueQuiz::updateOrCreate(
            [
                'student_id' => Auth::id(),
                'quiz_id' => $quiz->id,
                'attempt_number' => $resultatQuiz->attempts,
            ],
            [
                'product_id' => $product->id,
                'question_id' => null,
                'response_id' => null,
                'is_correct' => null,
                'quiz_type' => $quiz->type_id,
                'total_questions' => $expectedTotalQuestions,
                'correct_answers' => $correctAnswers,
                'score_percentage' => $score,
                'success' => $success,
                'time_spent' => $timeSpent,
                'started_at' => $startTime,
                'completed_at' => $completedAt,
            ]
        );

        foreach ($answeredAnswers as $questionId => $responseId) {
            $question = $this->findQuestionForSubmission($quiz, $product, $questionId, $useOwnQuestions);

            if (!$question) {
                Log::warning('Question not found for historique_quizzes', [
                    'question_id' => $questionId,
                    'quiz_id' => $quiz->id,
                    'student_id' => Auth::id(),
                    'attempt_number' => $resultatQuiz->attempts,
                ]);
                continue;
            }

            $responseId = ($responseId !== null && $responseId !== '') ? (int) $responseId : null;
            $isCorrect = false;

            if ($responseId) {
                $isCorrect = $question->reponses()->where('id', $responseId)->where('is_correcte', true)->exists();
            }

            HistoriqueQuiz::updateOrCreate(
                [
                    'student_id' => Auth::id(),
                    'quiz_id' => $quiz->id,
                    'attempt_number' => $resultatQuiz->attempts,
                    'question_id' => $questionId,
                ],
                [
                    'product_id' => $product->id,
                    'response_id' => $responseId,
                    'is_correct' => $isCorrect,
                    'quiz_type' => $quiz->type_id,
                    'total_questions' => $expectedTotalQuestions,
                    'correct_answers' => $correctAnswers,
                    'score_percentage' => $score,
                    'success' => $success,
                    'time_spent' => $timeSpent,
                    'started_at' => $startTime,
                    'completed_at' => $completedAt,
                ]
            );
        }

        if ($quiz->type_id == 1 && $success) {
            Log::info('Quiz passed - checking for StudentSuccess creation', [
                'quiz_id' => $quiz->id,
                'product_id' => $product->id,
                'student_id' => Auth::id(),
                'certificate_mode' => $product->certificate_generation_mode,
            ]);
            $this->checkAndCreateStudentSuccess($product, Auth::id());
        }

        $maxAttempts = $quiz->max_attempts;
        if ($quiz->type_id != 1 && !$success && !is_null($maxAttempts) && $resultatQuiz->attempts >= $maxAttempts) {
            try {
                Mail::to(Auth::user()->email)->send(new \App\Mail\QuizRetakeOpportunity($quiz, Auth::user(), $product));
                Log::info('Quiz retake opportunity email sent', [
                    'student_id' => Auth::id(),
                    'quiz_id' => $quiz->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send quiz retake opportunity email', [
                    'error' => $e->getMessage(),
                    'student_id' => Auth::id(),
                    'quiz_id' => $quiz->id,
                ]);
            }
        }

        Log::info('Quiz submit completed', [
            'score' => $score,
            'success' => $success,
            'total_questions' => $expectedTotalQuestions,
            'correct_answers' => $correctAnswers,
        ]);

        Log::info('Redirecting to quiz result', [
            'route' => 'quiz.result',
            'product_id' => $product->id,
            'quiz_id' => $quiz->id,
            'time_expired' => $hasExpired,
        ]);

        $redirect = redirect()->route('quiz.result', ['product' => $product->id, 'quiz' => $quiz->id]);

        if ($hasExpired) {
            $redirect->with('error', __('messages.time_expired'));
        }

        return $redirect;
    }

    public function result($locale, Product $product, Quiz $quiz)
    {
        Log::info('Quiz result called', [
            'product_id' => $product->id,
            'quiz_id' => $quiz->id,
            'student_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
        ]);

        if (!Auth::user()->hasPurchasedProduct($product->id)) {
            Log::warning('Student does not have access to product', [
                'student_id' => Auth::id(),
                'product_id' => $product->id,
            ]);

            $productStudent = Auth::user()->productAccess()
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->first();

            if ($productStudent && $productStudent->is_expired) {
                return redirect()->route('courses.show', $product)
                    ->with('error', __('messages.course_expired'));
            }

            return redirect()->route('courses.show', $product)->with('error', __('messages.no_access_to_course'));
        }

        $resultatQuiz = ResultatQuiz::where('student_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('quiz_id', $quiz->id)
            ->first();

        if (!$resultatQuiz) {
            return redirect()->route('courses.show', $product)->with('error', __('messages.quiz_not_found'));
        }

        $summary = HistoriqueQuiz::where('student_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('product_id', $product->id)
            ->where('attempt_number', $resultatQuiz->attempts)
            ->whereNull('question_id')
            ->orderByDesc('completed_at')
            ->first();

        $reponses = HistoriqueQuiz::with(['question.reponses', 'response'])
            ->where('student_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('product_id', $product->id)
            ->where('attempt_number', $resultatQuiz->attempts)
            ->whereNotNull('question_id')
            ->orderBy('id')
            ->get();

        if ($reponses->isEmpty()) {
            $reponses = ResponseQuiz::with(['question.reponses', 'reponse'])
                ->where('product_id', $product->id)
                ->where('student_id', Auth::id())
                ->where('quiz_id', $quiz->id)
                ->orderBy('id')
                ->get();
        }

        $totalQuestions = (int) ($summary?->total_questions ?: max($reponses->count(), 1));
        $correctAnswers = (int) ($summary?->correct_answers ?? $reponses->where('is_correct', true)->count());

        if ($quiz->type_id == 1) {
            $examQuestions = $product->getExamQuestions();
            $quiz->setRelation('questions', $examQuestions);
        }

        return view('quiz.result', compact('product', 'quiz', 'resultatQuiz', 'reponses', 'totalQuestions', 'correctAnswers'));
    }

    private function redirectQuizBlocked(string $reason, Product $product, string $locale): \Illuminate\Http\RedirectResponse
    {
        if ($reason === 'suspended') {
            return redirect()->route('student-dashboard.installments.suspended', ['locale' => $locale])
                ->with('warning', __('messages.suspended_access_message'));
        }

        if ($reason === 'final_exam_installments') {
            $installmentOrder = OrderSpecifique::where('student_id', Auth::id())
                ->where('product_id', $product->id)
                ->where('status', '!=', 'cancelled')
                ->first();

            Log::info('Final exam blocked - installments not fully paid', [
                'student_id' => Auth::id(),
                'product_id' => $product->id,
                'total_due' => $installmentOrder->total_due ?? 0,
            ]);

            return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
                ->with('error', __('messages.exam_blocked_installments', [
                    'default' => 'You must pay all installments before taking the final exam. Remaining balance: $' . number_format($installmentOrder->total_due ?? 0, 2),
                ]));
        }

        return redirect()->route('courses.show', ['locale' => $locale, 'product' => $product])
            ->with('error', __('messages.content_locked'));
    }

    private function checkAndCreateStudentSuccess(Product $product, $studentId)
    {
        $installmentOrder = OrderSpecifique::where('student_id', $studentId)
            ->where('product_id', $product->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($installmentOrder && !$installmentOrder->isAllPaid()) {
            Log::info('Certificate blocked - installments not fully paid', [
                'student_id' => $studentId,
                'product_id' => $product->id,
                'total_due' => $installmentOrder->total_due,
            ]);
            return;
        }

        if ($product->canStudentSubmitSuccess($studentId)) {
            if (!$product->is_exam_video) {
                $studentSuccess = \App\Models\StudentSuccess::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'lien_video' => null,
                        'success' => 1,
                        'submitted_at' => now(),
                        'validated_at' => now(),
                    ]
                );

                Log::info('StudentSuccess created automatically', [
                    'product_id' => $product->id,
                    'student_id' => $studentId,
                    'reason' => 'No video exam required',
                    'certificate_mode' => $product->certificate_generation_mode,
                ]);

                try {
                    $student = \App\Models\Student::find($studentId);
                    if ($student && $student->email) {
                        Mail::to($student->email)->send(
                            new \App\Mail\CourseCompletedNotification($studentSuccess, null)
                        );

                        Log::info('Course completion email sent successfully', [
                            'product_id' => $product->id,
                            'student_id' => $studentId,
                            'email' => $student->email,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send course completion email', [
                        'product_id' => $product->id,
                        'student_id' => $studentId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            } else {
                \App\Models\StudentSuccess::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'lien_video' => null,
                        'success' => 0,
                        'submitted_at' => null,
                    ]
                );

                Log::info('StudentSuccess created pending video exam', [
                    'product_id' => $product->id,
                    'student_id' => $studentId,
                    'reason' => 'Video exam required',
                ]);
            }
        }
    }

    private function resolveQuestionLimit(?int $configuredCount, int $availableCount, int $defaultFallback): int
    {
        if ($availableCount <= 0) {
            return 0;
        }

        if (!is_null($configuredCount) && $configuredCount > 0) {
            return min($configuredCount, $availableCount);
        }

        $fallback = $defaultFallback > 0 ? $defaultFallback : $availableCount;

        return min(max(1, $fallback), $availableCount);
    }

    private function resolveSuccessThreshold($configuredThreshold): float
    {
        if (!is_null($configuredThreshold) && $configuredThreshold > 0) {
            return (float) $configuredThreshold;
        }

        return 50.0;
    }

    private function pickRandomQuestions(EloquentCollection $questions, int $limit): EloquentCollection
    {
        if ($limit >= $questions->count()) {
            return new EloquentCollection($questions->all());
        }

        if ($limit <= 0) {
            return new EloquentCollection();
        }

        $shuffled = $questions->shuffle()->take($limit);

        return new EloquentCollection($shuffled->all());
    }

    private function mergeUnansweredQuestions(array $answeredAnswers, array $questionIds): array
    {
        foreach (array_unique(array_filter(array_map('intval', $questionIds))) as $questionId) {
            if (!array_key_exists($questionId, $answeredAnswers)
                && !array_key_exists((string) $questionId, $answeredAnswers)) {
                $answeredAnswers[$questionId] = null;
            }
        }

        return $answeredAnswers;
    }

    private function findQuestionForSubmission(Quiz $quiz, Product $product, $questionId, bool $useOwnQuestions): ?\App\Models\Question
    {
        if ($quiz->type_id == 1) {
            if ($useOwnQuestions) {
                return $quiz->questions()->find($questionId);
            }

            $quizQuizzes = $product->quizzes()->where('type_id', 2)->get();
            foreach ($quizQuizzes as $quizQuiz) {
                $question = $quizQuiz->questions()->find($questionId);
                if ($question) {
                    return $question;
                }
            }

            return null;
        }

        return $quiz->questions()->find($questionId);
    }
}
