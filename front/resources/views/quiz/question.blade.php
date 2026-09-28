@extends('layouts.app')

@section('content')
<div class="quiz-container">
    <!-- Header Mobile -->
    <div class="quiz-header-mobile d-lg-none">
        <div class="container">
            <div class="d-flex items-center justify-between py-15">
                <div class="d-flex items-center">
                    <div class="quiz-icon-mobile mr-15">
                        <i class="icon-file-text"></i>
                    </div>
                    <div>
                        <h4 class="text-16 fw-600 text-dark-1 mb-5">
                            {{ app()->getLocale() == 'ar' ? $quiz->name_ar : $quiz->name_en }}
                        </h4>
                        <p class="text-12 text-light-1">
                            {{ __('messages.question') }} <span id="currentQuestionNumber">{{ $session['current_question'] + 1 }}</span>/{{ $session['total_questions'] }}
                        </p>
                    </div>
                </div>
                <div class="timer-mobile">
                    <div class="timer-circle" id="timerMobile">
                        {{ sprintf('%02d:%02d', (int)($remainingTime / 60), (int)($remainingTime % 60)) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar Mobile -->
    <div class="progress-mobile d-lg-none">
        <div class="progress-bar-mobile">
            <div class="progress-fill" id="progressFillMobile" style="width: {{ (($session['current_question'] + 1) / $session['total_questions']) * 100 }}%"></div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Sidebar Desktop -->
            <div class="col-lg-4 d-none d-lg-block">
                <div class="quiz-sidebar">
                    <!-- Quiz Info -->
                    <div class="quiz-info-card">
                        <div class="quiz-header">
                            <div class="quiz-icon">
                                <i class="icon-file-text"></i>
                            </div>
                            <h3 class="quiz-title">
                                {{ app()->getLocale() == 'ar' ? $quiz->name_ar : $quiz->name_en }}
                            </h3>
                        </div>

                        <!-- Timer Desktop -->
                        <div class="timer-section">
                            <div class="timer-display" id="timerDesktop">
                                <div class="timer-circle-large">
                                    <span class="timer-text">{{ sprintf('%02d:%02d', (int)($remainingTime / 60), (int)($remainingTime % 60)) }}</span>
                                </div>
                                <p class="timer-label">{{ __('messages.time_remaining') }}</p>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="progress-section">
                            <div class="progress-info">
                                <span>{{ __('messages.progress') }}</span>
                                <span class="progress-percentage" id="progressPercentage">{{ round((($session['current_question'] + 1) / $session['total_questions']) * 100) }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFillDesktop" style="width: {{ (($session['current_question'] + 1) / $session['total_questions']) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Question Navigation -->
                        <div class="question-nav-section">
                            <h4 class="section-title">{{ __('messages.question_navigation') }}</h4>
                            <div class="question-grid" id="questionGrid">
                                @for($i = 0; $i < $session['total_questions']; $i++)
                                <div class="question-item
                                    @if($i < $session['current_question']) completed
                                    @elseif($i == $session['current_question']) current
                                    @else pending @endif"
                                    data-question="{{ $i }}" onclick="navigateToQuestion({{ $i }})">
                                    <span class="question-number">{{ $i + 1 }}</span>
                                    @if($i < $session['current_question'])
                                        <i class="icon-check"></i>
                                    @endif
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8 col-12">
                <div class="quiz-main-content d-flex justify-content-center">
                    <div class="quiz-content-wrapper">
                        <!-- Timer Centered -->
                        <div class="timer-centered">
                            <div class="timer-centered-circle" id="timerCentered">
                                <div class="timer-centered-content">
                                    <i class="icon-clock"></i>
                                    <span class="timer-centered-text">{{ sprintf('%02d:%02d', (int)($remainingTime / 60), (int)($remainingTime % 60)) }}</span>
                                    <span class="timer-centered-label">{{ __('messages.time_remaining') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Question Card -->
                        <div class="question-card" id="questionCard">
                            <div class="question-header">
                                <div class="question-meta">
                                    <span class="question-counter">{{ __('messages.question') }} <span id="questionCounter">{{ $session['current_question'] + 1 }}</span></span>
                                    <span class="question-total">/ {{ $session['total_questions'] }}</span>
                                </div>
                                <div class="question-timer d-none d-lg-block">
                                    <div class="timer-mini" id="timerMini">
                                        <i class="icon-clock"></i>
                                        <span>{{ sprintf('%02d:%02d', (int)($remainingTime / 60), (int)($remainingTime % 60)) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="question-content">
                                <h2 class="question-text" id="questionText">
                                    {{ app()->getLocale() == 'ar' ? $currentQuestion->name_ar : $currentQuestion->name_en }}
                                </h2>
                            </div>

                            <!-- Answer Options -->
                            <form id="answerForm" class="answer-form">
                                @csrf
                                <div class="answer-options" id="answerOptions">
                                    @foreach($currentQuestion->reponses as $index => $reponse)
                                    <div class="answer-option-wrapper">
                                        <input type="radio" name="answer_id" value="{{ $reponse->id }}" id="answer_{{ $reponse->id }}" class="answer-input" data-question="{{ $session['current_question'] }}">
                                        <label for="answer_{{ $reponse->id }}" class="answer-option">
                                            <div class="answer-marker">
                                                <span class="answer-letter">{{ chr(65 + $index) }}</span>
                                            </div>
                                            <div class="answer-content">
                                                <p class="answer-text">
                                                    {{ app()->getLocale() == 'ar' ? $reponse->titre_ar : $reponse->titre_en }}
                                                </p>
                                            </div>
                                            <div class="answer-check">
                                                <i class="icon-check"></i>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="quiz-navigation">
                                    <div class="nav-info">
                                        <i class="icon-info"></i>
                                        <span>{{ __('messages.select_answers') }}</span>
                                    </div>

                                    <div class="nav-buttons">
                                        <button type="button" onclick="goToPreviousQuestion()" class="btn btn-outline" id="prevBtn" {{ $session['current_question'] == 0 ? 'style=display:none' : '' }}>
                                            <i class="icon-arrow-left"></i>
                                            {{ __('messages.previous') }}
                                        </button>

                                        <button type="button" onclick="goToNextQuestion()" class="btn btn-primary" id="nextBtn" disabled>
                                            <span id="btnText">
                                                @if($session['current_question'] == $session['total_questions'] - 1)
                                                    {{ __('messages.finish_quiz') }}
                                                @else
                                                    {{ __('messages.next_question') }}
                                                @endif
                                            </span>
                                            <i class="icon-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Instructions Card -->
                        <div class="instructions-card">
                            <h4 class="instructions-title">
                                <i class="icon-info"></i>
                                {{ __('messages.quiz_instructions') }}
                            </h4>
                            <div class="instructions-list">
                                <div class="instruction-item">
                                    <div class="instruction-icon">
                                        <i class="icon-clock"></i>
                                    </div>
                                    <div class="instruction-content">
                                        <h5>{{ __('messages.instruction_time_limit') }}</h5>
                                        <p>{{ __('messages.instruction_time_limit_desc') }}</p>
                                    </div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-icon">
                                        <i class="icon-check-circle"></i>
                                    </div>
                                    <div class="instruction-content">
                                        <h5>{{ __('messages.instruction_one_answer') }}</h5>
                                        <p>{{ __('messages.instruction_one_answer_desc') }}</p>
                                    </div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-icon">
                                        <i class="icon-arrow-right"></i>
                                    </div>
                                    <div class="instruction-content">
                                        <h5>{{ __('messages.instruction_no_back') }}</h5>
                                        <p>{{ __('messages.instruction_no_back_desc') }}</p>
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
.quiz-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
    padding: 20px 0;
}

/* Mobile Header */
.quiz-header-mobile {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.quiz-icon-mobile {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #0071ae, #005a8a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.timer-mobile .timer-circle {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #0071ae, #005a8a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 15px rgba(0, 113, 174, 0.3);
}

.progress-mobile {
    background: rgba(255, 255, 255, 0.9);
    padding: 10px 0;
}

.progress-bar-mobile {
    height: 6px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #0071ae, #005a8a);
    border-radius: 3px;
    transition: width 0.3s ease;
}

/* Sidebar */
.quiz-sidebar {
    position: sticky;
    top: 20px;
}

.quiz-info-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.quiz-header {
    text-align: center;
    margin-bottom: 30px;
}

.quiz-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #0071ae, #005a8a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 24px;
}

.quiz-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

/* Timer Section */
.timer-section {
    text-align: center;
    margin-bottom: 30px;
}

.timer-circle-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #0071ae, #005a8a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    box-shadow: 0 8px 25px rgba(0, 113, 174, 0.3);
}

.timer-text {
    color: white;
    font-size: 20px;
    font-weight: 700;
}

.timer-label {
    color: #666;
    font-size: 14px;
    margin: 0;
}

/* Progress Section */
.progress-section {
    margin-bottom: 30px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
    color: #666;
}

.progress-percentage {
    font-weight: 600;
    color: #0071ae;
}

.progress-bar {
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

/* Question Navigation */
.question-nav-section {
    margin-bottom: 20px;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.question-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
}

.question-item {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.question-item.completed {
    background: #10b981;
    color: white;
}

.question-item.current {
    background: #0071ae;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 113, 174, 0.3);
}

.question-item.pending {
    background: #f3f4f6;
    color: #9ca3af;
    border: 2px solid #e5e7eb;
}

.question-item.completed .icon-check {
    position: absolute;
    top: -2px;
    right: -2px;
    background: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #10b981;
}

/* Main Content */
.quiz-main-content {
    padding: 20px 0;
}

.quiz-content-wrapper {
    width: 100%;
    max-width: 800px;
}

/* Timer Centered */
.timer-centered {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.timer-centered-circle {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #0071ae, #005a8a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(0, 113, 174, 0.3);
    position: relative;
    transition: all 0.3s ease;
}

.timer-centered-content {
    text-align: center;
    color: white;
}

.timer-centered-content i {
    font-size: 20px;
    margin-bottom: 5px;
    display: block;
}

.timer-centered-text {
    font-size: 24px;
    font-weight: 700;
    display: block;
    margin-bottom: 2px;
}

.timer-centered-label {
    font-size: 12px;
    opacity: 0.9;
    display: block;
}

.question-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.question-meta {
    display: flex;
    align-items: center;
}

.question-counter {
    font-size: 18px;
    font-weight: 600;
    color: #0071ae;
}

.question-total {
    font-size: 18px;
    color: #9ca3af;
    margin-left: 5px;
}

.timer-mini {
    display: flex;
    align-items: center;
    background: #f8fafc;
    padding: 8px 16px;
    border-radius: 20px;
    color: #0071ae;
    font-weight: 600;
}

.timer-mini i {
    margin-right: 8px;
}

.question-content {
    margin-bottom: 40px;
}

.question-text {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    line-height: 1.5;
    margin: 0;
}

/* Answer Options */
.answer-options {
    margin-bottom: 40px;
}

.answer-option-wrapper {
    margin-bottom: 15px;
}

.answer-input {
    display: none;
}

.answer-option {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.answer-option:hover {
    border-color: #0071ae;
    background: #f8fafc;
}

.answer-input:checked + .answer-option {
    border-color: #0071ae;
    background: linear-gradient(135deg, rgba(0, 113, 174, 0.1), rgba(0, 90, 138, 0.1));
    box-shadow: 0 4px 15px rgba(0, 113, 174, 0.2);
}

.answer-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    transition: all 0.3s ease;
}

.answer-input:checked + .answer-option .answer-marker {
    background: #0071ae;
    color: white;
}

.answer-letter {
    font-weight: 600;
    font-size: 16px;
}

.answer-content {
    flex: 1;
}

.answer-text {
    font-size: 16px;
    color: #333;
    margin: 0;
    line-height: 1.5;
}

.answer-check {
    opacity: 0;
    color: #0071ae;
    font-size: 20px;
    transition: opacity 0.3s ease;
}

.answer-input:checked + .answer-option .answer-check {
    opacity: 1;
}

/* Navigation */
.quiz-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 30px;
    border-top: 1px solid #f0f0f0;
}

.nav-info {
    display: flex;
    align-items: center;
    color: #9ca3af;
    font-size: 14px;
}

.nav-info i {
    margin-right: 8px;
}

.nav-buttons {
    display: flex;
    gap: 15px;
}

.btn {
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-outline {
    background: transparent;
    color: #0071ae;
    border: 2px solid #0071ae;
}

.btn-outline:hover {
    background: #0071ae;
    color: white;
}

.btn-primary {
    background: linear-gradient(135deg, #0071ae, #005a8a);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 113, 174, 0.3);
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 113, 174, 0.4);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.btn i {
    margin-left: 8px;
}

/* Instructions Card */
.instructions-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.instructions-title {
    display: flex;
    align-items: center;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 25px;
}

.instructions-title i {
    margin-right: 10px;
    color: #0071ae;
}

.instructions-list {
    display: grid;
    gap: 20px;
}

.instruction-item {
    display: flex;
    align-items: flex-start;
}

.instruction-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, rgba(0, 113, 174, 0.1), rgba(0, 90, 138, 0.1));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: #0071ae;
    flex-shrink: 0;
}

.instruction-content h5 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px 0;
}

.instruction-content p {
    font-size: 14px;
    color: #666;
    margin: 0;
    line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 991px) {
    .quiz-container {
        padding: 0;
    }

    .quiz-main-content {
        padding: 15px;
    }

    .timer-centered-circle {
        width: 100px;
        height: 100px;
    }

    .timer-centered-text {
        font-size: 20px;
    }

    .timer-centered-content i {
        font-size: 16px;
    }

    .question-card {
        padding: 25px;
        border-radius: 15px;
    }

    .question-text {
        font-size: 20px;
    }

    .answer-option {
        padding: 15px;
    }

    .quiz-navigation {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }

    .nav-buttons {
        justify-content: space-between;
    }

    .instructions-card {
        padding: 20px;
        border-radius: 15px;
    }
}

@media (max-width: 576px) {
    .timer-centered-circle {
        width: 80px;
        height: 80px;
    }

    .timer-centered-text {
        font-size: 18px;
    }

    .timer-centered-content i {
        font-size: 14px;
    }

    .question-card {
        padding: 20px;
    }

    .question-text {
        font-size: 18px;
    }

    .answer-option {
        padding: 12px;
    }

    .answer-marker {
        width: 35px;
        height: 35px;
        margin-right: 15px;
    }

    .answer-text {
        font-size: 14px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 13px;
    }

    .question-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    .question-item {
        width: 35px;
        height: 35px;
        font-size: 12px;
    }
}

/* Timer Animation */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.timer-warning {
    animation: pulse 1s infinite;
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
}

.timer-urgent {
    animation: pulse 0.5s infinite;
    background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
}
</style>

<!-- Inclure le fichier JavaScript de navigation -->
<script src="{{ asset('assets/js/quiz-navigation.js') }}"></script>

<script>
// Initialiser le système de navigation du quiz
let quizNavigation;

document.addEventListener('DOMContentLoaded', function() {
    // Configuration du système de navigation
    const config = {
        currentQuestionIndex: {{ $session['current_question'] }},
        totalQuestions: {{ $session['total_questions'] }},
        timeRemaining: {{ (int)$remainingTime }},
        questionsData: @json($quiz->questions),
        userAnswers: {},
        locale: '{{ app()->getLocale() }}'
    };

    // Initialiser les réponses sauvegardées
    @if(isset($session['answers']))
        @foreach($session['answers'] as $questionIndex => $answers)
            config.userAnswers[{{ $questionIndex }}] = @json($answers);
        @endforeach
    @endif

    // Créer l'instance de navigation
    quizNavigation = new QuizNavigation(config);

    // Exposer les fonctions globalement pour les boutons
    window.navigateToQuestion = function(questionIndex) {
        quizNavigation.navigateToQuestion(questionIndex);
    };

    window.goToPreviousQuestion = function() {
        quizNavigation.goToPreviousQuestion();
    };

    window.goToNextQuestion = function() {
        quizNavigation.goToNextQuestion();
    };

    // Warning before leaving
    window.addEventListener('beforeunload', function(e) {
        if (config.timeRemaining > 0) {
            e.preventDefault();
            e.returnValue = '{{ __("messages.quiz_warning") }}';
        }
    });
});

// Add spinner styles
const style = document.createElement('style');
style.textContent = `
.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
`;
document.head.appendChild(style);
</script>
@endsection
