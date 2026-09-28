@extends('emails.base-template')

@section('title', 'Video Exam Requires Revision')

@section('header-type', 'primary')

@section('email-title', 'Video Exam Requires Revision')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Your video exam submission has been reviewed. Some aspects require revision before it can be validated.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Video Exam:</strong> {{ $video_exam_title }}</li>
            <li class="info-list-item"><strong>Review Date:</strong> {{ $review_date }}</li>
        </ul>
    </div>

    @if(isset($video_url))
    <div class="info-card success">
        <h3 class="info-card-title">Your Submission</h3>
        <p class="content-text">
            <a href="{{ $video_url }}" style="color: #1e40af;">View your video submission</a>
        </p>
    </div>
    @endif

    <div class="info-card warning">
        <h3 class="info-card-title">Revision Guidelines</h3>
        <p class="content-text">
            {{ $admin_notes }}
        </p>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Next Steps</h3>
        <ul class="info-list">
            <li class="info-list-item">Review the feedback provided above</li>
            <li class="info-list-item">Address the issues mentioned in the guidelines</li>
            <li class="info-list-item">Re-record and submit your video exam</li>
            <li class="info-list-item">Contact your instructor if you need help</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $resubmit_url ?? '#' }}" class="button button-primary">Resubmit Video</a>
    </div>

    <p class="content-text">
        Don't be discouraged. This is an opportunity to improve and demonstrate your knowledge more effectively.
    </p>
@endsection
