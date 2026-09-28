@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Quiz Result Details</h1>
                    <p class="text-muted mb-0">Result #{{ $resultatQuiz->id }} — {{ $attemptHistory->count() }} attempt(s)</p>
                </div>
                <a href="{{ route('admin.resultat-quizzes.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i> Back to List
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $scorePercentage }}%</h4>
                            <small>Latest Score</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $correctAnswers }}</h4>
                            <small>Correct (latest)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $incorrectAnswers }}</h4>
                            <small>Incorrect (latest)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $totalQuestions }}</h4>
                            <small>Total Questions</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="border rounded p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">
                                    <i class="fa fa-clipboard-list text-primary me-2"></i>
                                    {{ $resultatQuiz->quiz->type->name ?? 'Quiz' }}:
                                    {{ $resultatQuiz->quiz->name_en ?? 'Quiz #' . $resultatQuiz->quiz_id }}
                                </h5>
                                <p class="text-muted mb-1">
                                    <i class="fa fa-book me-1"></i>
                                    {{ $resultatQuiz->product->titre ?? 'Course not found' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $submissionStatusBadge }} mb-1">
                                    {{ $submissionStatusLabel }}
                                </span>
                                <br>
                                <span class="badge bg-{{ $resultatQuiz->success ? 'success' : 'danger' }} fs-5 mb-2">
                                    {{ $scorePercentage }}%
                                </span>
                                <br>
                                <span class="badge bg-{{ $resultatQuiz->success ? 'success' : 'danger' }}">
                                    {{ $resultatQuiz->success ? 'PASSED' : 'FAILED' }}
                                </span>
                            </div>
                        </div>

                        <div class="row py-3 bg-light rounded">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Student</small>
                                <strong>{{ $resultatQuiz->student->full_name ?? trim(($resultatQuiz->student->first_name ?? '') . ' ' . ($resultatQuiz->student->last_name ?? '')) }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $resultatQuiz->student->email }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Country</small>
                                <strong>{{ $resultatQuiz->student->country ?? 'Not specified' }}</strong>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">
                        <i class="fa fa-history me-2"></i>
                        All Attempts
                    </h5>

                    @forelse($attemptHistory as $attempt)
                        <div class="border rounded mb-4 p-4 {{ $attempt['is_current'] ? 'border-primary' : '' }}">
                            @if($attempt['is_current'])
                                <span class="badge bg-primary mb-2">Current result</span>
                            @endif

                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1">Attempt #{{ $attempt['attempt_number'] }}</h6>
                                    <small class="text-muted">
                                        @if($attempt['started_at'])
                                            Started: {{ $attempt['started_at']->format('d/m/Y H:i') }}
                                        @endif
                                        @if($attempt['completed_at'])
                                            | Completed: {{ $attempt['completed_at']->format('d/m/Y H:i') }}
                                        @endif
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $attempt['submission_status_badge'] }} mb-1">
                                        {{ $attempt['submission_status_label'] }}
                                    </span>
                                    <br>
                                    <span class="badge bg-{{ $attempt['success'] ? 'success' : 'danger' }} fs-6">
                                        {{ number_format((float) $attempt['score_percentage'], 1) }}%
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        @if($attempt['time_spent'])
                                            Duration: {{ gmdate('i:s', $attempt['time_spent']) }}
                                        @else
                                            Duration: --:--
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Total questions:</small>
                                    <strong class="ms-1">{{ $attempt['total_questions'] }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Correct:</small>
                                    <strong class="ms-1 text-success">{{ $attempt['correct_answers'] }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Incorrect:</small>
                                    <strong class="ms-1 text-danger">{{ max(0, $attempt['total_questions'] - $attempt['correct_answers']) }}</strong>
                                </div>
                            </div>

                            @if($attempt['submission_status'] === \App\Support\QuizSubmissionStatus::TIME_EXPIRED)
                                <div class="alert alert-warning py-2 mb-2">
                                    <i class="fa fa-clock-o me-1"></i>
                                    Time expired — partial answers were saved.
                                </div>
                            @elseif($attempt['submission_status'] === \App\Support\QuizSubmissionStatus::ABANDONED)
                                <div class="alert alert-secondary py-2 mb-2">
                                    <i class="fa fa-sign-out me-1"></i>
                                    Abandoned — submitted before completing all questions.
                                </div>
                            @endif

                            @include('resultat-quizzes.partials.question-responses', [
                                'responses' => $attempt['responses'],
                                'totalQuestions' => $attempt['total_questions'],
                                'attemptNumber' => $attempt['attempt_number'],
                                'expanded' => $attempt['is_current'],
                            ])
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            No attempt history found for this quiz.
                        </div>
                    @endforelse

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="{{ route('admin.resultat-quizzes.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.resultat-quizzes.edit', $resultatQuiz) }}" class="btn btn-warning">
                                <i class="fa fa-edit me-2"></i> Edit
                            </a>
                            <form action="{{ route('admin.resultat-quizzes.destroy', $resultatQuiz) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this result?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-trash me-2"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-success-light { background-color: rgba(25, 135, 84, 0.1) !important; }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.1) !important; }
.card { border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.accordion-button:not(.collapsed) { background-color: #f8f9fa; color: #212529; }
</style>
@endsection
