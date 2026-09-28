@extends('emails.base-template')

@section('title', 'Final Success Approved')

@section('header-type', 'primary')

@section('email-title', 'Final Success Approved')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">🏆</div>
    </div>

    <p class="content-text">
        Congratulations! Your final course evaluation has been approved. You have successfully completed all requirements for this course.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Achievement Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Completion Date:</strong> {{ $completion_date }}</li>
            @if(isset($final_score))
            <li class="info-list-item"><strong>Final Score:</strong> {{ $final_score }}%</li>
            @endif
            <li class="info-list-item"><strong>Status:</strong> Approved</li>
        </ul>
    </div>

    @if(isset($admin_notes) && $admin_notes)
    <div class="info-card primary">
        <h3 class="info-card-title">Instructor Comments</h3>
        <p class="content-text" style="font-style: italic;">
            {{ $admin_notes }}
        </p>
    </div>
    @endif

    <div class="info-card primary">
        <h3 class="info-card-title">Certificate Information</h3>
        <p class="content-text">
            Your course completion certificate will be generated and available for download within 24 hours.
        </p>
    </div>

    @if(isset($video_url))
    <div class="info-card success">
        <h3 class="info-card-title">Your Video Submission</h3>
        <p class="content-text">
            <a href="{{ $video_url }}" style="color: #1e40af;">View your submitted video</a>
        </p>
    </div>
    @endif

    <hr class="divider">

    <div class="info-card warning">
        <h3 class="info-card-title">⭐ Share Your Experience</h3>
        <p class="content-text">
            Your feedback is valuable! Please take a moment to rate and review this course. Your insights help us improve and assist future students in making informed decisions.
        </p>
        <p class="content-text" style="margin-bottom: 0;">
            <strong>What we'd love to know:</strong>
        </p>
        <ul class="info-list">
            <li class="info-list-item">How would you rate the course content?</li>
            <li class="info-list-item">Was the instructor helpful and knowledgeable?</li>
            <li class="info-list-item">Would you recommend this course to others?</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ config('app.user_url') }}/courses/{{ $product->id }}#rate-course" class="button button-warning" style="background: linear-gradient(135deg, #f59e0b, #d97706); margin-bottom: 10px;">
            Rate This Course Now
        </a>
        <a href="{{ $dashboard_url ?? '#' }}" class="button button-outline">View Dashboard</a>
    </div>
@endsection
