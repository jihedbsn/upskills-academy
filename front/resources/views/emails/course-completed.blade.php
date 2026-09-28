@extends('emails.base-template')

@section('title', 'Course Completed Successfully')

@section('header-type', 'primary')

@section('email-title', 'Course Completed')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <p class="content-text">
        Congratulations on successfully completing your course! 🎉 Your dedication and commitment to learning have paid off, and we are pleased to acknowledge your achievement.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Course Completion Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Completion Date:</strong> {{ $completionDate }}</li>
            @if($finalScore)
            <li class="info-list-item"><strong>Final Score:</strong> {{ $finalScore }}%</li>
            @endif
        </ul>
    </div>

    <div class="info-card info">
        <h3 class="info-card-title">📜 Certificate Being Prepared</h3>
        <p class="content-text">
            Your certificate of completion is being prepared and will be available in your dashboard soon. You will be notified once it's ready for download.
        </p>
    </div>

    <hr class="divider">

    <p class="content-text">
        <strong>Continue Your Learning Journey</strong>
    </p>
    <p class="content-text">
        Explore our catalog for more advanced courses to further enhance your skills and knowledge.
    </p>

    <div class="button-container">
        <a href="{{ $catalogUrl }}" class="button button-outline">Browse Courses</a>
    </div>
@endsection
