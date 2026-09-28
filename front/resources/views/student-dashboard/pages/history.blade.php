@extends('student-dashboard.layouts.app')

@section('title', __('messages.history'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
                    <h1 class="h3 mb-0">{{ __('messages.history') }}</h1>
                    <p class="text-muted mb-0">{{ __('messages.quiz_exam_history_description') }}</p>
        </div>
    </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                    <div>
                                    <h4 class="mb-0">{{ $stats['total_attempts'] }}</h4>
                                    <small>{{ __('messages.total_attempts') }}</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-clipboard-data fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                    <div>
                                    <h4 class="mb-0">{{ $stats['success_rate'] }}%</h4>
                                    <small>{{ __('messages.success_rate') }}</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-trophy fs-2"></i>
                    </div>
                </div>
            </div>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['average_score'] }}%</h4>
                                    <small>{{ __('messages.average_score') }}</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-graph-up fs-2"></i>
                                </div>
                        </div>
                    </div>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                            <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['unique_courses'] }}</h4>
                                    <small>{{ __('messages.courses_taken') }}</small>
                                    </div>
                                <div class="align-self-center">
                                    <i class="bi bi-book fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('student-dashboard.history.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ __('messages.course') }}</label>
                                <select name="product_id" class="form-select">
                                    <option value="">{{ __('messages.all_courses') }}</option>
                                    @foreach($enrolledProducts as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->titre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('messages.quiz_type') }}</label>
                                <select name="quiz_type" class="form-select">
                                    <option value="">{{ __('messages.all_types') }}</option>
                                    <option value="1" {{ request('quiz_type') == '1' ? 'selected' : '' }}>{{ __('messages.exam') }}</option>
                                    <option value="2" {{ request('quiz_type') == '2' ? 'selected' : '' }}>{{ __('messages.quiz') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('messages.status') }}</label>
                                <select name="success" class="form-select">
                                    <option value="">{{ __('messages.all_status') }}</option>
                                    <option value="1" {{ request('success') == '1' ? 'selected' : '' }}>{{ __('messages.successful') }}</option>
                                    <option value="0" {{ request('success') == '0' ? 'selected' : '' }}>{{ __('messages.failed') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('messages.date_from') }}</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('messages.date_to') }}</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel"></i> {{ __('messages.filter') }}
                                </button>
                                <a href="{{ route('student-dashboard.history.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> {{ __('messages.clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tentatives en cours -->
            @if(isset($unfinishedAttempts) && $unfinishedAttempts->count() > 0)
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-hourglass-split me-2"></i>{{ __('messages.unfinished_quiz_attempts') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($unfinishedAttempts as $unfinished)
                            @php
                                $attempt = $unfinished['resultat'];
                            @endphp
                            <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                                <div>
                                    <strong>{{ $attempt->quiz->name_en ?? __('messages.quiz') }}</strong>
                                    <div class="text-muted small">{{ $attempt->product->titre ?? '' }}</div>
                                    <small class="text-muted">
                                        {{ __('messages.started_at') }}: {{ $attempt->started_at?->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $unfinished['submission_status_badge'] }} mb-2">
                                        {{ $unfinished['submission_status_label'] }}
                                    </span>
                                    <br>
                                    @if($unfinished['submission_status'] === \App\Support\QuizSubmissionStatus::IN_PROGRESS && $attempt->product && $attempt->quiz)
                                        <a href="{{ route('quiz.start', ['product' => $attempt->product, 'quiz' => $attempt->quiz]) }}" class="btn btn-sm btn-primary">
                                            {{ __('messages.continue_quiz') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Historique -->
            <div class="card">
                            <div class="card-body">
                    @if($historique->count() > 0)
                        @foreach($historique as $attempt)
                            <div class="border rounded mb-4 p-4">
                                <!-- En-tête de la tentative -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">
                                            {{ $attempt->quiz_type == 1 ? __('messages.exam') : __('messages.quiz') }}: 
                                            {{ $attempt->quiz->name_en ?? 'Quiz #' . $attempt->quiz_id }}
                                        </h5>
                                        <p class="text-muted mb-1">{{ $attempt->product->titre }}</p>
                                        <small class="text-muted">
                                            {{ __('messages.attempt') }} #{{ $attempt->attempt_number }} - 
                                            {{ $attempt->completed_at ? $attempt->completed_at->format('d/m/Y H:i') : __('messages.not_completed') }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $attempt->submission_status_badge ?? 'secondary' }} mb-1">
                                            {{ $attempt->submission_status_label ?? __('messages.quiz_status_completed') }}
                                        </span>
                                        <br>
                                        <span class="badge bg-{{ $attempt->success ? 'success' : 'danger' }} fs-6">
                                            {{ $attempt->score_percentage }}%
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $attempt->time_spent ? gmdate('i:s', $attempt->time_spent) : '--:--' }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Résumé -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">{{ __('messages.total_questions') }}:</small>
                                        <strong>{{ $attempt->total_questions }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">{{ __('messages.correct_answers') }}:</small>
                                        <strong>{{ $attempt->correct_answers }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">{{ __('messages.incorrect_answers') }}:</small>
                                        <strong>{{ $attempt->total_questions - $attempt->correct_answers }}</strong>
                                    </div>
                                </div>

                                @if(($attempt->submission_status ?? null) === \App\Support\QuizSubmissionStatus::TIME_EXPIRED)
                                    <div class="alert alert-warning py-2 mb-2">
                                        <i class="bi bi-clock-history me-1"></i>
                                        {{ __('messages.quiz_time_expired_partial_message') }}
                                    </div>
                                @elseif(($attempt->submission_status ?? null) === \App\Support\QuizSubmissionStatus::ABANDONED)
                                    <div class="alert alert-secondary py-2 mb-2">
                                        <i class="bi bi-box-arrow-right me-1"></i>
                                        {{ __('messages.quiz_abandoned_partial_message') }}
                                    </div>
                                @endif

                                @include('student-dashboard.partials.quiz-question-responses', [
                                    'responses' => $attempt->responses,
                                    'totalQuestions' => (int) $attempt->total_questions,
                                    'accordionId' => 'attempt' . $attempt->id,
                                ])
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $historique->links() }}
                        </div>
                    @else
                        @if(!isset($unfinishedAttempts) || $unfinishedAttempts->count() === 0)
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x fs-1 text-muted mb-3"></i>
                            <h4 class="text-muted">{{ __('messages.no_history_found') }}</h4>
                            <p class="text-muted">{{ __('messages.no_quiz_attempts_yet') }}</p>
                            <a href="{{ route('student-dashboard.courses.index') }}" class="btn btn-primary">
                                <i class="bi bi-book me-2"></i>{{ __('messages.browse_courses') }}
                        </a>
                    </div>
                        @endif
                @endif
            </div>
        </div>
        </div>
        </div>
</div>
@endsection

@push('styles')
<style>
.bg-light-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush