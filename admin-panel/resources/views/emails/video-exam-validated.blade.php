@extends('emails.base-template')

@section('title', 'Video Exam Validated')

@section('header-type', 'primary')

@section('email-title', 'Video Exam Validated')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Excellent work! Your video exam submission has been reviewed and validated by your instructor.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Validation Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Video Exam:</strong> {{ $video_exam_title }}</li>
            <li class="info-list-item"><strong>Validation Date:</strong> {{ $validation_date }}</li>
            @if(isset($score))
            <li class="info-list-item"><strong>Score:</strong> {{ $score }}%</li>
            @endif
        </ul>
    </div>

    @if(isset($video_url))
    <div class="info-card primary">
        <h3 class="info-card-title">Your Submission</h3>
        <p class="content-text">
            <a href="{{ $video_url }}" style="color: #1e40af;">View your video submission</a>
        </p>
    </div>
    @endif

    @if(isset($admin_notes) && $admin_notes)
    <div class="info-card success">
        <h3 class="info-card-title">Instructor Feedback</h3>
        <p class="content-text" style="font-style: italic;">
            {{ $admin_notes }}
        </p>
    </div>
    @endif

    <p class="content-text">
        Your video demonstrates strong understanding of the material. Keep up the excellent work!
    </p>

    <div class="button-container">
        <a href="{{ $dashboard_url ?? '#' }}" class="button button-primary">View Dashboard</a>
    </div>
@endsection
