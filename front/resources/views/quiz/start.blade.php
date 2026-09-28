@extends('layouts.app')

@section('styles')
<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
@php
    $isRtlLocale = app()->getLocale() === 'ar';
@endphp
<div class="container-fluid quiz-page-container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <!-- Quiz Header -->
            <div class="quiz-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="quiz-title">
                            <h2 class="text-gradient mb-2">{{ $quiz->titre }}</h2>
                            <p class="text-muted mb-0">{{ $product->titre }}</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="quiz-stats">
                            <div class="timer-card">
                                <div class="timer-icon">
                                    <i class="fas fa-stopwatch"></i>
                                </div>
                                <div class="timer-content">
                                    <div class="timer-label">{{ __('messages.time_remaining') }}</div>
                                    @php $durationMinutes = max(1, $quiz->questions->count()); @endphp
                                    <div class="timer-value" id="timer">{{ sprintf('%02d:%02d', (int) $durationMinutes, 0) }}</div>
                                </div>
                            </div>
                            <div class="progress-info mt-2">
                                <span id="current-question">1</span> / <span id="total-questions">{{ $quiz->questions->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Content -->
            <div class="quiz-content">
                <div class="row g-0">
                    <!-- Main Quiz Area -->
                    <div class="col-lg-8">
                        <div class="quiz-main">


                            @if($resultatQuiz->attempts > 1)
                                <div class="alert-modern alert-warning-modern mb-4">
                                    <div class="alert-icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="alert-content">
                                        @if($quiz->type_id == 1)
                                            <strong>{{ __('messages.exam_attempt') }} {{ $resultatQuiz->attempts }}/1</strong>
                                            <p class="mb-0">{{ __('messages.exam_single_attempt_warning') }}</p>
                                        @else
                                            <strong>{{ __('messages.quiz_attempt') }} {{ $resultatQuiz->attempts }}</strong>
                                            <p class="mb-0">{{ __('messages.quiz_unlimited_attempts') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Questions Container -->
                            <form action="{{ route('quiz.submit', ['product' => $product, 'quiz' => $quiz]) }}" method="POST" id="quiz-form">
                                @csrf
                                @foreach($quiz->questions as $question)
                                    <input type="hidden" name="question_ids[]" value="{{ $question->id }}">
                                @endforeach
                                <div class="questions-container">
                                    @foreach($quiz->questions as $index => $question)
                                        <div class="question-item {{ $index === 0 ? 'active' : 'd-none' }}" id="question-{{ $question->id }}" data-question="{{ $index + 1 }}">
                                            <div class="question-card">
                                                <div class="question-header">
                                                    <div class="question-number">
                                                        <span>{{ $index + 1 }}</span>
                                                    </div>
                                                    <div class="question-title">
                                                        <h4>{{ __('messages.question') }} {{ $index + 1 }}</h4>
                                                    </div>
                                                </div>
                                                <div class="question-content">
                                                    <div class="question-text">
                                                        <p>{{ $question->question }}</p>
                                                    </div>
                                                    <div class="answers-container">
                                                        @foreach($question->reponses as $reponse)
                                                            <div class="answer-option">
                                                                <input type="radio"
                                                                       name="answers[{{ $question->id }}]"
                                                                       id="answer-{{ $reponse->id }}"
                                                                       value="{{ $reponse->id }}"
                                                                       class="answer-input">
                                                                <label for="answer-{{ $reponse->id }}" class="answer-label">
                                                                    <span class="answer-text">{{ $reponse->reponse }}</span>
                                                                    <span class="answer-check">
                                                                        <i class="fas fa-check"></i>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Quiz Controls -->
                                <div class="quiz-controls">
                                    <div class="control-buttons">
                                        <div class="control-group">
                                            <button type="button" id="prev-btn" class="btn-modern btn-secondary-modern" disabled>
                                                <i class="fas {{ $isRtlLocale ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                                                <span>{{ __('messages.previous') }}</span>
                                            </button>
                                            <button type="button" id="next-btn" class="btn-modern btn-primary-modern">
                                                <i class="fas {{ $isRtlLocale ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                                                <span>{{ __('messages.next') }}</span>
                                            </button>
                                            <button type="submit" id="submit-btn" class="btn-modern btn-success-modern d-none">
                                                <i class="fas fa-check-circle"></i>
                                                <span>{{ __('messages.submit_quiz') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quiz Sidebar -->
                    <div class="col-lg-4">
                        <div class="quiz-sidebar">
                            <!-- Quiz Info -->
                            <div class="sidebar-card mb-4">
                                <div class="card-header-modern">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>{{ __('messages.quiz_info') }}</span>
                                </div>
                                <div class="card-body-modern">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <div class="info-content">
                                                <div class="info-value">{{ $quiz->questions->count() }}</div>
                                                <div class="info-label">{{ __('messages.questions') }}</div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-stopwatch"></i>
                                            </div>
                                            <div class="info-content">
                                                <div class="info-value">{{ (int) $durationMinutes }}</div>
                                                <div class="info-label">{{ __('messages.total_minutes') }}</div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-trophy"></i>
                                            </div>
                                            <div class="info-content">
                                                <div class="info-value">{{ $quiz->questions->count() }}</div>
                                                <div class="info-label">{{ __('messages.points') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Question Navigation -->
                            <div class="sidebar-card">
                                <div class="card-header-modern">
                                    <i class="fas fa-compass"></i>
                                    <span>{{ __('messages.navigation') }}</span>
                                </div>
                                <div class="card-body-modern">
                                    <div class="question-dots">
                                        @foreach($quiz->questions as $index => $question)
                                            <span class="question-dot {{ $index === 0 ? 'active' : '' }}" data-question="{{ $index + 1 }}">
                                                {{ $index + 1 }}
                                            </span>
                                        @endforeach
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
/* Modern Design System */
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

/* Quiz Page Container */
.quiz-page-container {
    margin-top: 8%;
    padding: 2rem 1rem;
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

/* Quiz Content Structure */
.quiz-content {
    margin-top: 2rem;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.quiz-main {
    padding: 2rem;
}

.quiz-sidebar {
    background: #f8fafc;
    padding: 2rem;
    border-left: 1px solid #e2e8f0;
}

/* Quiz Header */
.quiz-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.quiz-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.text-gradient {
    background: linear-gradient(135deg, #ffffff, #e2e8f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    z-index: 1;
}

.quiz-title {
    position: relative;
    z-index: 1;
}

.quiz-stats {
    position: relative;
    z-index: 1;
}

.timer-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.timer-icon {
    font-size: 1.5rem;
    opacity: 0.8;
}

.timer-content {
    flex: 1;
}

.timer-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.timer-value {
    font-size: 1.5rem;
    font-weight: bold;
}

.progress-info {
    font-size: 0.875rem;
    opacity: 0.8;
}

/* Quiz Content */
.quiz-content {
    margin-top: 2rem;
}

/* Modern Cards */
.instruction-card, .sidebar-card {
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



/* Alert Modern */
.alert-modern {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    border: 1px solid;
}

.alert-warning-modern {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-color: var(--warning-color);
    color: #92400e;
}

.alert-icon {
    font-size: 1.25rem;
    margin-top: 0.125rem;
}

.alert-content {
    flex: 1;
}

.alert-content strong {
    display: block;
    margin-bottom: 0.5rem;
}

/* Question Card */
.question-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    margin-bottom: 2rem;
}

.question-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.question-number {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.25rem;
}

.question-title h4 {
    margin: 0;
    font-weight: 600;
}

.question-content {
    padding: 2rem;
}

.question-text p {
    font-size: 1.125rem;
    line-height: 1.7;
    color: var(--dark-color);
    margin-bottom: 2rem;
}

/* Answer Options */
.answers-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.answer-option {
    position: relative;
}

.answer-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.answer-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    background: var(--light-color);
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.answer-label::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    opacity: 0;
    transition: var(--transition);
    z-index: 1;
}

.answer-text {
    position: relative;
    z-index: 2;
    font-weight: 500;
    color: var(--dark-color);
    transition: var(--transition);
}

.answer-check {
    position: relative;
    z-index: 2;
    width: 24px;
    height: 24px;
    border: 2px solid #cbd5e1;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.answer-check i {
    font-size: 0.75rem;
    color: white;
    opacity: 0;
    transition: var(--transition);
}

/* Answer Input States */
.answer-input:checked + .answer-label {
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
}

.answer-input:checked + .answer-label::before {
    opacity: 1;
}

.answer-input:checked + .answer-label .answer-text {
    color: var(--dark-color);
}

.answer-input:checked + .answer-label .answer-check {
    border-color: white;
    background: white;
}

.answer-input:checked + .answer-label .answer-check i {
    opacity: 1;
    color: var(--primary-color);
}

.answer-label:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Quiz Controls */
.quiz-controls {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.control-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.control-group {
    display: flex;
    gap: 1rem;
}

/* Modern Buttons */
.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.btn-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-modern:hover::before {
    left: 100%;
}

.btn-primary-modern {
    background: var(--secondary-color);
    color: #1f2937;
}

.btn-primary-modern:hover {
    background: #eab308;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-secondary-modern {
    background: #e2e8f0;
    color: #334155;
}

.btn-secondary-modern:hover {
    background: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

[dir="rtl"] .control-group {
    flex-direction: row-reverse;
}

.btn-info-modern {
    background: var(--info-color);
    color: white;
}

.btn-info-modern:hover {
    background: #0891b2;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-success-modern {
    background: var(--success-color);
    color: white;
}

.btn-success-modern:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Sidebar */
.quiz-sidebar {
    position: sticky;
    top: 2rem;
}

/* Info Grid */
.info-grid {
    display: grid;
    gap: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-color);
    border-radius: 12px;
    transition: var(--transition);
}

.info-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.info-icon {
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

.info-content {
    flex: 1;
}

.info-value {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--dark-color);
}

.info-label {
    font-size: 0.875rem;
    color: #333333;
    font-weight: 500;
}

/* Navigation */
.navigation-controls {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.nav-buttons {
    display: flex;
    gap: 0.5rem;
}

.nav-btn {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 12px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-btn:hover:not(:disabled) {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.question-dots {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(40px, 1fr));
    gap: 0.5rem;
}

.question-dot {
    width: 40px;
    height: 40px;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.875rem;
}

.question-dot:hover {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.question-dot.active {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
    box-shadow: var(--shadow-md);
}

.question-dot.answered {
    border-color: var(--success-color);
    background: var(--success-color);
    color: white;
}

/* Modal Modern */
.modal-content-modern {
    border-radius: var(--border-radius);
    border: none;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.modal-header-modern {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
    font-weight: 600;
}

.btn-close-modern {
    background: none;
    border: none;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: var(--transition);
}

.btn-close-modern:hover {
    background: rgba(255, 255, 255, 0.1);
}

.modal-body-modern {
    padding: 2rem;
}

.modal-footer-modern {
    padding: 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Preview Items */
.preview-item {
    background: var(--light-color);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: var(--transition);
}

.preview-item.answered {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border-color: var(--success-color);
}

.preview-item.not-answered {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border-color: var(--danger-color);
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-page-container {
        margin-top: 5%;
        padding: 1rem 0.5rem;
    }

    .quiz-header {
        padding: 1.5rem;
    }

    .quiz-main {
        padding: 1.5rem;
    }

    .quiz-sidebar {
        padding: 1.5rem;
        border-left: none;
        border-top: 1px solid #e2e8f0;
    }

    .question-content {
        padding: 1.5rem;
    }

    .control-buttons {
        flex-direction: column;
        gap: 1rem;
    }

    .control-group {
        width: 100%;
        justify-content: center;
    }

    .question-dots {
        grid-template-columns: repeat(auto-fit, minmax(35px, 1fr));
    }

    .question-dot {
        width: 35px;
        height: 35px;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .quiz-page-container {
        margin-top: 3%;
        padding: 0.5rem;
    }

    .quiz-header {
        padding: 1rem;
    }

    .quiz-main {
        padding: 1rem;
    }

    .quiz-sidebar {
        padding: 1rem;
    }
}

/* Animation for question transitions */
.question-item {
    transition: var(--transition);
}

.question-item.active {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* SweetAlert2 Custom Styling */
.swal2-popup {
    border-radius: 20px;
    padding: 30px;
}

.swal2-title {
    color: #1e293b;
    font-size: 24px;
    font-weight: 700;
}

.swal2-html-container {
    color: #475569;
    font-size: 16px;
}

.swal2-confirm {
    background: linear-gradient(135deg, var(--primary-color), #005a8a) !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 30px !important;
    font-weight: 600 !important;
    font-size: 16px !important;
    box-shadow: 0 4px 15px rgba(0, 113, 174, 0.3) !important;
    transition: all 0.3s ease !important;
}

.swal2-confirm:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0, 113, 174, 0.4) !important;
}

.swal2-cancel {
    background: white !important;
    color: #64748b !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 12px 30px !important;
    font-weight: 600 !important;
    font-size: 16px !important;
    transition: all 0.3s ease !important;
}

.swal2-cancel:hover {
    border-color: #cbd5e1 !important;
    background: #f1f5f9 !important;
    transform: translateY(-1px) !important;
}

.swal2-actions {
    gap: 15px !important;
}

.swal2-icon.swal2-warning {
    border-color: #f59e0b !important;
    color: #f59e0b !important;
}

.swal2-icon.swal2-question {
    border-color: var(--primary-color) !important;
    color: var(--primary-color) !important;
}
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentQuestion = 1;
const totalQuestions = {{ $quiz->questions->count() }};
const durationMinutes = {{ (int) max(1, $quiz->questions->count()) }};
let timer = null;
let timerRunning = false;
let timeLeft = durationMinutes * 60; // total time in seconds
let answers = {};
let beforeUnloadHandlerRef = null;
let popstateHandlerRef = null;
let isAutoSubmitting = false; // Flag pour soumission automatique
let quizSubmitted = false;

function markQuizSubmitted() {
    quizSubmitted = true;
}

function syncAnswersToForm() {
    Object.entries(answers).forEach(([questionId, responseId]) => {
        const radio = document.querySelector(`input[name="answers[${questionId}]"][value="${responseId}"]`);
        if (radio) {
            radio.checked = true;
        }
    });
}

function buildQuizSubmitFormData() {
    saveCurrentAnswer();
    const formData = new FormData();
    const token = document.querySelector('#quiz-form input[name="_token"]')?.value;
    if (token) {
        formData.append('_token', token);
    }
    Object.entries(answers).forEach(([questionId, responseId]) => {
        formData.append(`answers[${questionId}]`, responseId);
    });
    document.querySelectorAll('#quiz-form input[name="question_ids[]"]').forEach((input) => {
        formData.append('question_ids[]', input.value);
    });
    return formData;
}

function sendQuizLeaveBeacon() {
    if (quizSubmitted || isAutoSubmitting) {
        return;
    }
    saveCurrentAnswer();
    if (Object.keys(answers).length === 0) {
        return;
    }
    const quizForm = document.getElementById('quiz-form');
    if (!quizForm || typeof navigator.sendBeacon !== 'function') {
        return;
    }
    markQuizSubmitted();
    navigator.sendBeacon(quizForm.action, buildQuizSubmitFormData());
}

window.addEventListener('pagehide', sendQuizLeaveBeacon);

// Initialize quiz
document.addEventListener('DOMContentLoaded', function() {
    console.log('Quiz initialized with', totalQuestions, 'questions');
    showQuestion(1);
    startTimer();
    loadAnswers();

    // Protection contre la navigation arrière
    enableNavigationGuards();
});

// Timer functions
function startTimer() {
    if (timerRunning) return;
    timerRunning = true;
    timer = setInterval(function() {
        timeLeft--;
        updateTimerDisplay();

        if (timeLeft <= 0) {
            clearInterval(timer);
            autoNextQuestion();
        }
    }, 1000);
}

function stopTimer() {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
    timerRunning = false;
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    const timerElement = document.getElementById('timer');
    if (timerElement) {
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}

function resetTimer() {
    stopTimer();
    timeLeft = totalQuestions * 60; // Reset to total time
    updateTimerDisplay();
    startTimer();
}

// Navigation functions
function showQuestion(questionNumber) {
    console.log('Showing question:', questionNumber);

    // Hide all questions
    document.querySelectorAll('.question-item').forEach(item => {
        item.classList.add('d-none');
        item.classList.remove('active');
    });

    // Show current question
    const currentQuestionElement = document.querySelector(`[data-question="${questionNumber}"]`);
    if (currentQuestionElement) {
        currentQuestionElement.classList.remove('d-none');
        currentQuestionElement.classList.add('active');
        console.log('Question element found and shown');
    } else {
        console.log('Question element not found for:', questionNumber);
        return; // Exit if question not found
    }

    // Update dots
    document.querySelectorAll('.question-dot').forEach(dot => {
        dot.classList.remove('active');
    });
    const activeDot = document.querySelector(`.question-dot[data-question="${questionNumber}"]`);
    if (activeDot) {
        activeDot.classList.add('active');
    }

    // Update counter
    const counterElement = document.getElementById('current-question');
    if (counterElement) {
        counterElement.textContent = questionNumber;
    }

    // Update navigation buttons
    updateNavigation();

    // Load saved answers
    loadAnswersForQuestion(questionNumber);
}

function updateNavigation() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');

    console.log('Updating navigation - current question:', currentQuestion, 'total:', totalQuestions);

    if (prevBtn) {
        prevBtn.disabled = currentQuestion === 1;
        console.log('Previous button disabled:', prevBtn.disabled);
    }

    if (nextBtn && submitBtn) {
        if (currentQuestion === totalQuestions) {
            // Dernière question - masquer Next et afficher Submit
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
            console.log('Last question - showing submit button');
        } else {
            // Pas la dernière question - afficher Next et masquer Submit
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
            console.log('Not last question - showing next button');
        }
    }
}

function nextQuestion() {
    console.log('Next button clicked, current question:', currentQuestion, 'total:', totalQuestions);

    if (currentQuestion < totalQuestions) {
        saveCurrentAnswer();
        currentQuestion++;
        showQuestion(currentQuestion);
    }
}

function prevQuestion() {
    console.log('Previous button clicked, current question:', currentQuestion);

    if (currentQuestion > 1) {
        saveCurrentAnswer();
        currentQuestion--;
        showQuestion(currentQuestion);
    }
}

function autoNextQuestion() {
    console.log('Time expired, auto-submitting quiz');

    // Arrêter le timer immédiatement
    stopTimer();

    // Afficher un message à l'utilisateur
    const timerElement = document.getElementById('timer');
    if (timerElement) {
        timerElement.textContent = '00:00';
        timerElement.style.color = '#ef4444';
    }

    // Sauvegarder toutes les réponses de toutes les questions
    for (let i = 1; i <= totalQuestions; i++) {
        const questionElement = document.querySelector(`[data-question="${i}"]`);
        if (questionElement) {
            const selectedAnswer = questionElement.querySelector('input[type="radio"]:checked');
            if (selectedAnswer) {
                const questionId = selectedAnswer.name.match(/\[(\d+)\]/)[1];
                answers[questionId] = selectedAnswer.value;
            }
        }
    }

    // Marquer comme soumission automatique
    isAutoSubmitting = true;
    markQuizSubmitted();

    // Désactiver les gardes de navigation
    disableNavigationGuards();

    // Soumettre directement le formulaire sans alerte
    const quizForm = document.getElementById('quiz-form');
    if (quizForm) {
        syncAnswersToForm();
        console.log('Submitting quiz form automatically');
        quizForm.submit();
    }
}

// Answer management
function saveCurrentAnswer() {
    const currentQuestionElement = document.querySelector(`[data-question="${currentQuestion}"]`);
    if (!currentQuestionElement) return;

    const selectedAnswer = currentQuestionElement.querySelector('input[type="radio"]:checked');

    if (selectedAnswer) {
        const questionId = selectedAnswer.name.match(/\[(\d+)\]/)[1];
        answers[questionId] = selectedAnswer.value;
        markQuestionAsAnswered(currentQuestion);
    }
}

function loadAnswers() {
    const savedAnswers = localStorage.getItem(`quiz_answers_${currentQuestion}`);
    if (savedAnswers) {
        answers = JSON.parse(savedAnswers);
    }
}

function loadAnswersForQuestion(questionNumber) {
    const currentQuestionElement = document.querySelector(`[data-question="${questionNumber}"]`);
    if (!currentQuestionElement) return;

    const questionId = currentQuestionElement.querySelector('input[type="radio"]').name.match(/\[(\d+)\]/)[1];

    if (answers[questionId]) {
        const radioButton = currentQuestionElement.querySelector(`input[value="${answers[questionId]}"]`);
        if (radioButton) {
            radioButton.checked = true;
        }
    }
}

function markQuestionAsAnswered(questionNumber) {
    const dot = document.querySelector(`.question-dot[data-question="${questionNumber}"]`);
    if (dot) {
        dot.classList.add('answered');
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');

    if (nextBtn) {
        nextBtn.addEventListener('click', nextQuestion);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', prevQuestion);
    }

    // Question dots navigation
    document.querySelectorAll('.question-dot').forEach(dot => {
        dot.addEventListener('click', function() {
            const questionNumber = parseInt(this.getAttribute('data-question'));
            if (questionNumber !== currentQuestion) {
                saveCurrentAnswer();
                currentQuestion = questionNumber;
                showQuestion(currentQuestion);
            }
        });
    });

    // Auto-save answers when radio buttons change
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            saveCurrentAnswer();
        });
    });
});

// Protection contre la navigation arrière
function enableNavigationGuards() {
    if (!beforeUnloadHandlerRef) {
        beforeUnloadHandlerRef = function(e) {
            e.preventDefault();
            e.returnValue = '{{ __("messages.quiz_in_progress_warning") }}';
            return e.returnValue;
        };
        window.addEventListener('beforeunload', beforeUnloadHandlerRef);
    }
    if (!popstateHandlerRef) {
        popstateHandlerRef = function(e) {
            if (confirm('{{ __("messages.quiz_in_progress_warning") }}')) {
                window.history.pushState(null, null, window.location.href);
            } else {
                window.history.pushState(null, null, window.location.href);
            }
        };
        window.addEventListener('popstate', popstateHandlerRef);
    }
    window.history.pushState(null, null, window.location.href);
}

function disableNavigationGuards() {
    if (beforeUnloadHandlerRef) {
        window.removeEventListener('beforeunload', beforeUnloadHandlerRef);
        beforeUnloadHandlerRef = null;
    }
    if (popstateHandlerRef) {
        window.removeEventListener('popstate', popstateHandlerRef);
        popstateHandlerRef = null;
    }
}

// Form validation and submission
function finishQuiz(skipConfirm = false) {
    console.log('Quiz finished, skipConfirm:', skipConfirm);
    saveCurrentAnswer();

    const answeredCount = Object.keys(answers).length;
    if (!skipConfirm) {
        if (answeredCount < totalQuestions) {
            if (!confirm('{{ __("messages.some_questions_unanswered") }}')) {
                return;
            }
        }
    }

    // Stop timer permanently
    stopTimer();
    // Désactiver l'avertissement de navigation avant de quitter
    disableNavigationGuards();
    console.log('Timer stopped permanently');

    // Supprimer la protection contre la navigation arrière
    window.removeEventListener('beforeunload', preventBackNavigation);
    window.removeEventListener('popstate', preventBackNavigation);

    // Si skipConfirm est true (expiration du temps), soumettre directement
    if (skipConfirm) {
        const quizForm = document.getElementById('quiz-form');
        // Marquer comme soumission automatique
        isAutoSubmitting = true;
        markQuizSubmitted();
        // Soumettre directement le formulaire
        quizForm.submit();
    } else {
        // Pour les soumissions manuelles, déclencher le bouton submit qui passera par le gestionnaire d'événement
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.click();
        } else {
            document.getElementById('quiz-form').submit();
        }
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const quizForm = document.getElementById('quiz-form');
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            // Vérifier si c'est une soumission automatique (expiration du temps)
            if (isAutoSubmitting) {
                // Soumission automatique, laisser passer sans confirmation
                console.log('Auto-submitting quiz, skipping confirmations');
                syncAnswersToForm();
                markQuizSubmitted();
                return true;
            }

            // S'assurer de sauvegarder la réponse courante avant le comptage
            saveCurrentAnswer();
            const answeredCount = Object.keys(answers).length;

            // Si incomplet, demander confirmation avec SweetAlert2
            if (answeredCount < totalQuestions) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ __("messages.confirm_submit_incomplete") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("messages.submit_quiz") }}',
                    cancelButtonText: '{{ __("messages.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Stop timer only if proceeding
                        stopTimer();
                        disableNavigationGuards();
                        // Save answers to localStorage before submit
                        localStorage.setItem(`quiz_answers_${currentQuestion}`, JSON.stringify(answers));
                        markQuizSubmitted();
                        quizForm.submit();
                    } else {
                        // Assurer que le timer continue si l'utilisateur annule
                        if (!timerRunning) {
                            startTimer();
                        }
                    }
                });
                return false;
            }

            // Toutes les questions répondues: demander aussi confirmation via SweetAlert2
            e.preventDefault();
            Swal.fire({
                title: '{{ __("messages.confirm_submit") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __("messages.submit_quiz") }}',
                cancelButtonText: '{{ __("messages.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Stop timer only if proceeding
                    stopTimer();
                    disableNavigationGuards();
                    // Save answers to localStorage before submit
                    localStorage.setItem(`quiz_answers_${currentQuestion}`, JSON.stringify(answers));
                    quizForm.submit();
                } else {
                    // Assurer que le timer continue si l'utilisateur annule
                    if (!timerRunning) {
                        startTimer();
                    }
                }
            });
            return false;
        });
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    if (event.key === 'ArrowRight' || event.key === ' ') {
        event.preventDefault();
        if (currentQuestion < totalQuestions) {
            nextQuestion();
        }
    } else if (event.key === 'ArrowLeft') {
        event.preventDefault();
        if (currentQuestion > 1) {
            prevQuestion();
        }
    }
});
</script>
@endsection
