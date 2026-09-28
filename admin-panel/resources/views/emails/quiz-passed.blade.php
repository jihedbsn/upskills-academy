@extends('emails.base-template')

@section('title', 'Quiz Passed')

@section('header-type', 'primary')

@section('email-title')
    {{ $quizType }} Passed
@endsection

@section('content')
    <p class="greeting">Hello {{ $student->first_name }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">✓</div>
    </div>

    <p class="content-text">
        Congratulations! You have successfully passed your {{ $quizType }}. Your hard work and dedication have paid off.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">{{ $quizType }} Results</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $product->name_en ?? $product->name_ar ?? 'Course' }}</li>
            <li class="info-list-item"><strong>{{ $quizType }}:</strong> {{ $quiz->name_en ?? $quiz->name_ar ?? 'Quiz' }}</li>
            <li class="info-list-item"><strong>Score:</strong> {{ number_format($score, 1) }}%</li>
            <li class="info-list-item"><strong>Completion Date:</strong> {{ now()->format('F d, Y') }}</li>
        </ul>
    </div>

    @if(isset($quiz->admin_notes) && $quiz->admin_notes)
    <div class="info-card primary">
        <h3 class="info-card-title">Instructor Feedback</h3>
        <p class="content-text" style="font-style: italic;">
            {{ $quiz->admin_notes }}
        </p>
    </div>
    @endif

    <p class="content-text">
        Your performance demonstrates strong understanding of the material. Keep up the excellent work!
    </p>

    <div class="button-container">
        <a href="{{ $dashboard_url ?? '#' }}" class="button button-primary">View Dashboard</a>
    </div>
@endsection
