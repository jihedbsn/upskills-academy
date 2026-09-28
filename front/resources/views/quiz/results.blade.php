@extends('layouts.app')

@section('content')
<div class="results-container">
    <div class="container">
        <!-- Header -->
        <div class="results-header">
            <div class="results-header-content">
                <div class="quiz-icon-large">
                    <i class="icon-file-text"></i>
                </div>
                <h1 class="results-title">
                    {{ app()->getLocale() == 'ar' ? $quiz->name_ar : $quiz->name_en }}
                </h1>
                <p class="results-subtitle">{{ __('messages.quiz_completed') }}</p>
            </div>
        </div>

        <!-- Score Summary -->
        <div class="score-summary">
            <div class="score-card">
                <div class="score-circle">
                    <div class="score-number">{{ $percentage }}%</div>
                    <div class="score-label">{{ __('messages.score') }}</div>
                </div>
                <div class="score-details">
                    <div class="score-item">
                        <span class="score-label">{{ __('messages.correct_answers') }}</span>
                        <span class="score-value">{{ $session['score'] }}/{{ $totalPossibleScore }}</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">{{ __('messages.time_taken') }}</span>
                        <span class="score-value">{{ gmdate('i:s', $totalTime) }}</span>
                    </div>
                    @if(isset($session['timeout']) && $session['timeout'])
                    <div class="score-item timeout-warning">
                        <span class="score-label">{{ __('messages.timeout_warning') }}</span>
                        <span class="score-value">{{ __('messages.timeout_message') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="performance-chart">
            <div class="chart-container">
                <div class="chart-bar">
                    <div class="chart-fill" style="width: {{ $percentage }}%"></div>
                </div>
                <div class="chart-labels">
                    <span>{{ __('messages.your_score') }}: {{ $percentage }}%</span>
                    <span>{{ __('messages.passing_score') }}: 60%</span>
                </div>
            </div>
        </div>

        <!-- Questions Review -->
        <div class="questions-review">
            <h2 class="section-title">{{ __('messages.questions_review') }}</h2>

            @foreach($quiz->questions as $index => $question)
            <div class="question-review-card">
                <div class="question-header">
                    <div class="question-number">{{ __('messages.question') }} {{ $index + 1 }}</div>
                    <div class="question-status
                        @if(isset($session['answers'][$index]) && $session['answers'][$index])
                            @php
                                $userAnswer = App\Models\ReponseQuestion::find($session['answers'][$index]);
                                $isCorrect = $userAnswer && $userAnswer->is_correcte;
                            @endphp
                            @if($isCorrect)
                                correct
                            @else
                                incorrect
                            @endif
                        @else
                            unanswered
                        @endif">
                        @if(isset($session['answers'][$index]) && $session['answers'][$index])
                            @php
                                $userAnswer = App\Models\ReponseQuestion::find($session['answers'][$index]);
                                $isCorrect = $userAnswer && $userAnswer->is_correcte;
                            @endphp
                            @if($isCorrect)
                                <i class="icon-check"></i> {{ __('messages.correct') }}
                            @else
                                <i class="icon-x"></i> {{ __('messages.incorrect') }}
                            @endif
                        @else
                            <i class="icon-minus"></i> {{ __('messages.not_answered') }}
                        @endif
                    </div>
                </div>

                <div class="question-content">
                    <h3 class="question-text">
                        {{ app()->getLocale() == 'ar' ? $question->name_ar : $question->name_en }}
                    </h3>
                </div>

                <div class="answers-review">
                    @foreach($question->reponses as $answerIndex => $reponse)
                    <div class="answer-review-item
                        @if($reponse->is_correcte) correct-answer @endif
                        @if(isset($session['answers'][$index]) && $session['answers'][$index] == $reponse->id)
                            @if($reponse->is_correcte)
                                user-correct
                            @else
                                user-incorrect
                            @endif
                        @endif">
                        <div class="answer-marker">
                            <span class="answer-letter">{{ chr(65 + $answerIndex) }}</span>
                            @if($reponse->is_correcte)
                                <i class="icon-check correct-icon"></i>
                            @endif
                            @if(isset($session['answers'][$index]) && $session['answers'][$index] == $reponse->id && !$reponse->is_correcte)
                                <i class="icon-x incorrect-icon"></i>
                            @endif
                        </div>
                        <div class="answer-content">
                            <p class="answer-text">
                                {{ app()->getLocale() == 'ar' ? $reponse->titre_ar : $reponse->titre_en }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(isset($session['answers'][$index]) && $session['answers'][$index])
                    @php
                        $userAnswer = App\Models\ReponseQuestion::find($session['answers'][$index]);
                        $isCorrect = $userAnswer && $userAnswer->is_correcte;
                    @endphp
                    @if(!$isCorrect)
                    <div class="explanation">
                        <div class="explanation-header">
                            <i class="icon-info"></i>
                            <span>{{ __('messages.explanation') }}</span>
                        </div>
                        <p>{{ __('messages.correct_answer_explanation') }}</p>
                    </div>
                    @endif
                @endif
            </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('quiz.start', $quiz) }}" class="btn btn-outline">
                <i class="icon-refresh"></i>
                {{ __('messages.retake_quiz') }}
            </a>
            @if($quiz->products->count() > 0)
            <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $quiz->products->first()->id]) }}" class="btn btn-primary">
                <i class="icon-arrow-right"></i>
                {{ __('messages.back_to_course') }}
            </a>
            @else
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="icon-arrow-right"></i>
                {{ __('messages.back_to_dashboard') }}
            </a>
            @endif
        </div>
    </div>
</div>

<style>
.results-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
    padding: 40px 0;
}

/* Header */
.results-header {
    text-align: center;
    margin-bottom: 40px;
}

.results-header-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.quiz-icon-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #0071ae, #005a8a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 32px;
}

.results-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin: 0 0 10px 0;
}

.results-subtitle {
    font-size: 16px;
    color: #666;
    margin: 0;
}

/* Score Summary */
.score-summary {
    margin-bottom: 40px;
}

.score-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 40px;
}

.score-circle {
    text-align: center;
    flex-shrink: 0;
}

.score-circle::before {
    content: '';
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(
        from 0deg,
        #10b981 {{ $percentage * 3.6 }}deg,
        #f3f4f6 {{ $percentage * 3.6 }}deg 360deg
    );
    display: block;
    margin: 0 auto 15px;
    position: relative;
}

.score-circle::after {
    content: '';
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: white;
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
}

.score-number {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.score-label {
    font-size: 14px;
    color: #666;
}

.score-details {
    flex: 1;
}

.score-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.score-item:last-child {
    border-bottom: none;
}

.score-item .score-label {
    font-size: 16px;
    color: #333;
    font-weight: 500;
}

.score-item .score-value {
    font-size: 16px;
    color: #0071ae;
    font-weight: 600;
}

.timeout-warning .score-value {
    color: #ef4444;
}

/* Performance Chart */
.performance-chart {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
}

.chart-container {
    text-align: center;
}

.chart-bar {
    height: 20px;
    background: #f3f4f6;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
    position: relative;
}

.chart-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #059669);
    border-radius: 10px;
    transition: width 1s ease;
}

.chart-labels {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #666;
}

/* Questions Review */
.questions-review {
    margin-bottom: 40px;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    color: white;
    margin-bottom: 30px;
    text-align: center;
}

.question-review-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.question-number {
    font-size: 18px;
    font-weight: 600;
    color: #0071ae;
}

.question-status {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.question-status.correct {
    background: #d1fae5;
    color: #059669;
}

.question-status.incorrect {
    background: #fee2e2;
    color: #dc2626;
}

.question-status.unanswered {
    background: #f3f4f6;
    color: #6b7280;
}

.question-text {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 20px 0;
    line-height: 1.5;
}

.answers-review {
    margin-bottom: 20px;
}

.answer-review-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.answer-review-item.correct-answer {
    border-color: #10b981;
    background: #f0fdf4;
}

.answer-review-item.user-correct {
    border-color: #10b981;
    background: #d1fae5;
}

.answer-review-item.user-incorrect {
    border-color: #ef4444;
    background: #fee2e2;
}

.answer-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    position: relative;
    flex-shrink: 0;
}

.answer-review-item.correct-answer .answer-marker {
    background: #10b981;
    color: white;
}

.answer-review-item.user-correct .answer-marker {
    background: #10b981;
    color: white;
}

.answer-review-item.user-incorrect .answer-marker {
    background: #ef4444;
    color: white;
}

.answer-letter {
    font-weight: 600;
    font-size: 16px;
}

.correct-icon, .incorrect-icon {
    position: absolute;
    top: -5px;
    right: -5px;
    background: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.correct-icon {
    color: #10b981;
}

.incorrect-icon {
    color: #ef4444;
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

.explanation {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 10px;
    padding: 15px;
    margin-top: 15px;
}

.explanation-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 600;
    color: #92400e;
}

.explanation p {
    margin: 0;
    color: #92400e;
    font-size: 14px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.btn {
    padding: 15px 30px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 16px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-outline {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-outline:hover {
    background: white;
    color: #0071ae;
}

.btn-primary {
    background: white;
    color: #0071ae;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .results-container {
        padding: 20px 0;
    }

    .score-card {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }

    .question-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }

    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .results-header-content,
    .score-card,
    .performance-chart,
    .question-review-card {
        padding: 20px;
        border-radius: 15px;
    }

    .results-title {
        font-size: 24px;
    }

    .question-text {
        font-size: 16px;
    }

    .answer-review-item {
        padding: 12px;
    }

    .answer-marker {
        width: 35px;
        height: 35px;
        margin-right: 12px;
    }

    .answer-text {
        font-size: 14px;
    }
}
</style>
@endsection
