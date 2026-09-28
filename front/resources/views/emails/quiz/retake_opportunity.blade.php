@extends('emails.base-template')

@section('title', 'Quiz Retake Opportunity')

@section('email-title', 'Quiz Retake Opportunity')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <p class="content-text">
        You have used all your attempts for the quiz <strong>{{ $quiz->name_en ?? $quiz->name_ar }}</strong> in the course <strong>{{ $product->name_en ?? $product->name_ar }}</strong>.
    </p>

    <div class="info-card warning">
        <h3 class="info-card-title">Retake Option Available</h3>
        <p class="content-text" style="margin-bottom: 0;">
            Don't worry! You can retake the quiz for a fee of <strong>$50</strong>.
        </p>
    </div>

    <p class="content-text">
        If you would like to retake the quiz, please contact the administration or click the button below for more details.
    </p>

    <div class="button-container">
        <a href="{{ route('courses.show', $product->id) }}" class="button button-primary">Go to Course</a>
    </div>
@endsection
