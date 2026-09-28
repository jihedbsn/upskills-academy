@extends('emails.base-template')

@section('title', 'Additional Requirements Needed')

@section('header-type', 'primary')

@section('email-title', 'Additional Requirements Needed')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Your final course evaluation has been reviewed. Additional work is required before your completion can be approved.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Course Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Review Date:</strong> {{ $review_date }}</li>
            <li class="info-list-item"><strong>Current Status:</strong> Requires Revision</li>
        </ul>
    </div>

    @if(isset($video_url))
    <div class="info-card success">
        <h3 class="info-card-title">Your Video Submission</h3>
        <p class="content-text">
            <a href="{{ $video_url }}" style="color: #1e40af;">View your submitted video</a>
        </p>
    </div>
    @endif

    <div class="info-card warning">
        <h3 class="info-card-title">Requirements</h3>
        <p class="content-text">
            {{ $admin_notes }}
        </p>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Next Steps</h3>
        <ul class="info-list">
            <li class="info-list-item">Review the feedback provided above carefully</li>
            <li class="info-list-item">Complete the additional requirements</li>
            <li class="info-list-item">Resubmit your work when ready</li>
            <li class="info-list-item">Contact your instructor if you need clarification</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $resubmit_url ?? '#' }}" class="button button-primary">Resubmit Work</a>
    </div>

    <p class="content-text">
        We're committed to your success. Please reach out if you have any questions about the feedback.
    </p>
@endsection
