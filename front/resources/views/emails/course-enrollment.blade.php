@extends('emails.base-template')

@section('title', 'Course Enrollment Confirmed')

@section('header-type', 'primary')

@section('email-title', 'Enrollment Confirmed')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <p class="content-text">
        We are pleased to confirm your enrollment in the course below. You now have full access to all course materials and can begin your learning journey immediately.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Course Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course Title:</strong> {{ $course->titre }}</li>
            @if(isset($course->teacher) && $course->teacher)
            <li class="info-list-item"><strong>Instructor:</strong> {{ $course->teacher->prenom }} {{ $course->teacher->nom }}</li>
            @endif
            @if(isset($course->duration) && $course->duration)
            <li class="info-list-item"><strong>Duration:</strong> {{ $course->duration }}</li>
            @endif
            @if(isset($course->niveau) && $course->niveau)
            <li class="info-list-item"><strong>Level:</strong> {{ ucfirst($course->niveau) }}</li>
            @endif
            <li class="info-list-item"><strong>Enrollment Date:</strong> {{ now()->format('F d, Y') }}</li>
        </ul>
    </div>

    @if(isset($course->description) && $course->description)
    <p class="content-text">
        <strong>Course Overview:</strong><br>
        {{ Str::limit(strip_tags($course->description), 250) }}
    </p>
    @endif

    <div class="info-card success">
        <h3 class="info-card-title">Getting Started</h3>
        <ul class="info-list">
            <li class="info-list-item">Access all course content and materials from your dashboard</li>
            <li class="info-list-item">Complete lessons and modules at your own pace</li>
            <li class="info-list-item">Participate in course discussions and community forums</li>
            <li class="info-list-item">Take assessments to validate your understanding</li>
            <li class="info-list-item">Earn your certificate upon successful completion</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ config('app.url') }}/en/courses/{{ $course->id }}" class="button button-primary">Access Course Content</a>
    </div>

    <hr class="divider">

    <p class="content-text text-center">
        We wish you success in your learning journey. Our support team is available to assist you with any questions.
    </p>
@endsection

