@extends('layouts.app')

@section('styles')
<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var courseUrl = "{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $product->id]) }}";
  try {
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function() {
      window.location.replace(courseUrl);
    });
  } catch (e) {
    // Fallback: no history API support
    window.onpopstate = function() { window.location.replace(courseUrl); };
  }
});
</script>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <!-- Status Message - Top Banner -->
            @if($resultatQuiz->success)
                <div class="status-message-card mb-4">
                    <div class="status-success">
                        <div class="status-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="status-content">
                            <h4>{{ __('messages.congratulations') }}!</h4>
                            <p>{{ __('messages.quiz_passed_message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quiz Result Header -->
            <div class="result-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="result-title">
                            <h2 class="text-gradient mb-2">{{ __('messages.results') }}</h2>
                            <p class="text-muted mb-0">{{ $product->titre }} - {{ $quiz->titre }}</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="result-stats">
                            <div class="completion-date">
                                <div class="date-label">{{ __('messages.completed_at') }}</div>
                                <div class="date-value">{{ $resultatQuiz->completed_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Result Content -->
            <div class="result-content">
                <div class="row">
                    <!-- Main Result Area -->
                    <div class="col-lg-8">
                        <div class="result-main">
                            <!-- Score Summary -->
                            <div class="score-summary-card mb-4">
                                <div class="score-header">
                                    <div class="score-circle {{ $resultatQuiz->success ? 'success' : 'failed' }}">
                                        <div class="score-number">{{ number_format($resultatQuiz->score, 1) }}%</div>
                                        <div class="score-label">
                                            @if($resultatQuiz->success)
                                                <i class="fas fa-check-circle"></i>
                                                {{ __('messages.passed') }}
                                            @else
                                                <i class="fas fa-times-circle"></i>
                                                {{ __('messages.failed') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="score-details">
                                    <div class="details-grid">
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-sync-alt"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">{{ __('messages.attempt') }}</div>
                                                <div class="detail-value">{{ $resultatQuiz->attempts }}/{{ $quiz->type_id == 1 ? ($product->max_exam_attempts ?? 3) : ($quiz->max_attempts ?? '∞') }}</div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-list-ol"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">{{ __('messages.points') }}</div>
                                                <div class="detail-value">{{ isset($correctAnswers) ? $correctAnswers : ($reponses->where('is_correct', true)->count()) }} / {{ isset($totalQuestions) ? $totalQuestions : $reponses->count() }}</div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-stopwatch"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">{{ __('messages.time_taken') }}</div>
                                                <div class="detail-value">
                                                    @if($resultatQuiz->started_at && $resultatQuiz->completed_at)
                                                        @php
                                                            $totalSeconds = $resultatQuiz->started_at->diffInSeconds($resultatQuiz->completed_at);
                                                            $minutes = floor($totalSeconds / 60);
                                                            $seconds = $totalSeconds % 60;
                                                        @endphp
                                                        {{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}
                                                    @else
                                                        {{ __('messages.not_available') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">{{ __('messages.completed_at') }}</div>
                                                <div class="detail-value">{{ $resultatQuiz->completed_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Results -->
                            <div class="detailed-results-card">
                                <div class="results-header">
                                    <h4>{{ __('messages.detailed_results') }}</h4>
                                    <div class="results-summary">
                                        <span class="correct-count">
                                            <i class="fas fa-check-circle text-success"></i>
                                            {{ $correctAnswers ?? $reponses->where('is_correct', true)->count() }} {{ __('messages.correct') }}
                                        </span>
                                        <span class="incorrect-count">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            {{ max(0, ($totalQuestions ?? $reponses->count()) - ($correctAnswers ?? $reponses->where('is_correct', true)->count())) }} {{ __('messages.incorrect') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="questions-review">
                                    @foreach($reponses as $index => $reponse)
                                        @php
                                            $questionText = optional($reponse->question)->question ?? __('messages.question');
                                            $answerModel = $reponse->response ?? $reponse->reponse ?? null;
                                            $hasAnswer = !empty($reponse->response_id);
                                            $selectedAnswerText = $answerModel?->reponse ?? __('messages.no_answer_provided');
                                            $correctAnswer = $reponse->question
                                                ? ($reponse->question->reponses->firstWhere('is_correcte', true)
                                                    ?? $reponse->question->reponses()->where('is_correcte', true)->first())
                                                : null;
                                            $correctAnswerText = $correctAnswer?->reponse ?? __('messages.not_available');
                                        @endphp
                                        <div class="question-review-card {{ $reponse->is_correct ? 'correct' : 'incorrect' }}">
                                            <div class="question-header">
                                                <div class="question-number">{{ $index + 1 }}</div>
                                                <div class="question-content">
                                                    <div class="question-text">{{ $questionText }}</div>
                                                </div>
                                                <div class="question-status">
                                                    @if($reponse->is_correct)
                                                        <i class="fas fa-check-circle"></i>
                                                    @else
                                                        <i class="fas fa-times-circle"></i>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="answer-details">
                                                <div class="answer-section">
                                                    <div class="answer-label">{{ __('messages.your_answer') }}:</div>
                                                    <div class="answer-value {{ $reponse->is_correct ? 'correct' : 'incorrect' }}">
                                                        {{ $selectedAnswerText }}
                                                    </div>
                                                </div>
                                                @if(!$reponse->is_correct)
                                                    <div class="answer-section">
                                                        <div class="answer-label">{{ __('messages.correct_answer') }}:</div>
                                                        <div class="answer-value correct">
                                                            {{ $correctAnswerText }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons mt-4">
                                <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $product->id]) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    {{ __('messages.back_to_course') }}
                                </a>

                                @if(!$resultatQuiz->success && ($quiz->type_id == 2 || ($quiz->type_id == 1 && $resultatQuiz->attempts < 1)))
                                    <a href="{{ route('quiz.start', ['product' => $product, 'quiz' => $quiz]) }}" class="btn btn-primary">
                                        <i class="fas fa-sync-alt"></i>
                                        {{ __('messages.retake_quiz') }}
                                    </a>
                                @endif

                                @if($resultatQuiz->success)
                                    <a href="{{ route('student-dashboard.index') }}" class="btn btn-success">
                                        <i class="fas fa-trophy"></i>
                                        {{ __('messages.go_to_dashboard') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Result Sidebar -->
                    <div class="col-lg-4">
                        <div class="result-sidebar">
                            <!-- Action Buttons -->
                            <div class="action-buttons-card mb-4">
                                <div class="card-header-modern">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>{{ __('messages.next_steps') }}</span>
                                </div>
                                <div class="card-body-modern">
                                    <div class="action-buttons">
                                        <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $product->id]) }}" class="btn-modern btn-primary-modern">
                                            <i class="fas fa-arrow-left"></i>
                                            <span>{{ __('messages.back_to_course') }}</span>
                                        </a>

                                        @if(!$resultatQuiz->success && ($quiz->type_id == 2 || ($quiz->type_id == 1 && $resultatQuiz->attempts < 1)))
                                            <a href="{{ route('quiz.start', ['product' => $product, 'quiz' => $quiz]) }}" class="btn-modern btn-warning-modern">
                                                <i class="fas fa-redo"></i>
                                                <span>{{ __('messages.retake_quiz') }}</span>
                                            </a>
                                        @endif

                                        <a href="{{ route('student-dashboard.index') }}" class="btn-modern btn-secondary-modern">
                                            <i class="fas fa-home"></i>
                                            <span>{{ __('messages.go_to_dashboard') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Statistics -->
                            <div class="stats-card">
                                <div class="card-header-modern">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>{{ __('messages.quiz_statistics') }}</span>
                                </div>
                                <div class="card-body-modern">
                                    <div class="stats-grid">
                                        <div class="stat-item">
                                            <div class="stat-icon">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-value">{{ isset($totalQuestions) ? $totalQuestions : $reponses->count() }}</div>
                                                <div class="stat-label">{{ __('messages.total_questions') }}</div>
                                            </div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-icon correct">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-value">{{ $correctAnswers ?? $reponses->where('is_correct', true)->count() }}</div>
                                                <div class="stat-label">{{ __('messages.correct_answers') }}</div>
                                            </div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-icon incorrect">
                                                <i class="fas fa-times-circle"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-value">{{ max(0, ($totalQuestions ?? $reponses->count()) - ($correctAnswers ?? $reponses->where('is_correct', true)->count())) }}</div>
                                                <div class="stat-label">{{ __('messages.incorrect_answers') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Design System - Inspired by mon-template */
:root {
    --primary-color: #0071ae;
    --primary-dark: #005a8a;
    --secondary-color: #FCD116;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    --light-color: #f8fafc;
    --dark-color: #1e293b;
    --border-radius: 16px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Result Header */
.result-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
}

.text-gradient {
    background: linear-gradient(135deg, #ffffff, #e2e8f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.completion-date {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
}

.date-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.date-value {
    font-size: 1.125rem;
    font-weight: bold;
}

/* Score Summary Card */
.score-summary-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    margin-bottom: 2rem;
}

.score-header {
    background: linear-gradient(135deg, var(--light-color), #e2e8f0);
    padding: 2rem;
    text-align: center;
}

.score-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: var(--shadow-lg);
    transition: var(--transition);
}

.score-circle.success {
    background: linear-gradient(135deg, var(--success-color), #059669);
}

.score-circle.failed {
    background: linear-gradient(135deg, var(--danger-color), #dc2626);
}

.score-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
    margin-bottom: 0.5rem;
}

.score-label {
    font-size: 1rem;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.score-details {
    padding: 2rem;
}

.details-grid {
    display: grid;
    gap: 1.5rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-color);
    border-radius: 12px;
    transition: var(--transition);
}

.detail-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.detail-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.detail-content {
    flex: 1;
}

.detail-label {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1.125rem;
    font-weight: bold;
    color: var(--dark-color);
}

/* Status Message Card */
.status-message-card {
    margin-bottom: 2rem;
}

.status-success, .status-failed {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
}

.status-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 1px solid var(--success-color);
}

.status-failed {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 1px solid var(--danger-color);
}

.status-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.status-success .status-icon {
    background: var(--success-color);
    color: white;
}

.status-failed .status-icon {
    background: var(--danger-color);
    color: white;
}

.status-content h4 {
    margin: 0 0 0.5rem 0;
    font-weight: 600;
}

.status-content p {
    margin: 0;
    opacity: 0.9;
}

/* Detailed Results Card */
.detailed-results-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.results-header {
    background: linear-gradient(135deg, var(--light-color), #e2e8f0);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.results-header h4 {
    margin: 0;
    font-weight: 600;
    color: var(--dark-color);
}

.results-summary {
    display: flex;
    gap: 1rem;
}

.correct-count, .incorrect-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.correct-count {
    color: var(--success-color);
}

.incorrect-count {
    color: var(--danger-color);
}

.questions-review {
    padding: 2rem;
}

.question-review-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: var(--transition);
}

.question-review-card.correct {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-color: var(--success-color);
}

.question-review-card.incorrect {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    border-color: var(--danger-color);
}

.question-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.question-number {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1rem;
    flex-shrink: 0;
}

.question-content {
    flex: 1;
}

.question-text {
    font-size: 1.125rem;
    font-weight: 500;
    color: var(--dark-color);
    line-height: 1.6;
}

.question-status {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.question-review-card.correct .question-status {
    color: var(--success-color);
}

.question-review-card.incorrect .question-status {
    color: var(--danger-color);
}

.answer-details {
    margin-left: 3rem;
}

.answer-section {
    margin-bottom: 1rem;
}

.answer-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--secondary-color);
    margin-bottom: 0.5rem;
}

.answer-value {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-weight: 500;
}

.answer-value.correct {
    background: var(--success-color);
    color: white;
}

.answer-value.incorrect {
    background: var(--danger-color);
    color: white;
}

/* Result Sidebar */
.result-sidebar {
    position: sticky;
    top: 2rem;
}

.action-buttons-card, .stats-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.card-header-modern {
    background: linear-gradient(135deg, var(--light-color), #e2e8f0);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--dark-color);
}

.card-body-modern {
    padding: 1.5rem;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.action-buttons .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.action-buttons .btn-secondary {
    background: var(--primary-color) !important;
    color: white !important;
    border: none !important;
}

.action-buttons .btn-secondary:hover {
    background: var(--primary-dark) !important;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.action-buttons .btn-primary {
    background: var(--warning-color) !important;
    color: white !important;
    border: none !important;
}

.action-buttons .btn-primary:hover {
    background: #d97706 !important;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.btn-primary-modern {
    background: var(--primary-color);
    color: white !important;
}

.btn-primary-modern:hover {
    background: var(--primary-dark);
    color: white !important;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-warning-modern {
    background: var(--warning-color);
    color: var(--dark-color) !important;
    font-weight: 600;
}

.btn-warning-modern:hover {
    background: #d97706;
    color: var(--dark-color) !important;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-secondary-modern {
    background: var(--secondary-color);
    color: var(--dark-color) !important;
    font-weight: 600;
}

.btn-secondary-modern:hover {
    background: #e5b800;
    color: var(--dark-color) !important;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-color);
    border-radius: 12px;
    transition: var(--transition);
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.stat-icon.correct {
    background: var(--success-color);
}

.stat-icon.incorrect {
    background: var(--danger-color);
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--dark-color);
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
}

/* Responsive */
@media (max-width: 768px) {
    .result-header {
        padding: 1.5rem;
    }

    .score-circle {
        width: 120px;
        height: 120px;
    }

    .score-number {
        font-size: 2rem;
    }

    .results-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .question-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .answer-details {
        margin-left: 0;
    }
}
</style>
@endsection
