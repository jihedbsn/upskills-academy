@extends('emails.base-template')

@section('title', 'Practical Exam Submission - Review Required')

@section('header-type', $isRetake ? 'warning' : 'info')

@section('email-title', $isRetake ? 'Practical Exam Retake Submitted' : 'New Practical Exam Submission')

@section('content')
    <p class="greeting">Hello Admin,</p>

    @if($isRetake)
        <div style="text-align: center; padding: 20px 0;">
            <div style="font-size: 60px;">🔄</div>
        </div>

        <p class="content-text">
            A student has resubmitted their practical exam after a previous failed attempt. This requires your immediate review.
        </p>

        <div class="info-card warning">
            <h3 class="info-card-title">⚠️ Retake Submission</h3>
            <p class="content-text">
                This is <strong>Attempt #{{ $attemptNumber }}</strong> for this course. The student has reviewed the feedback and is trying again.
            </p>
        </div>
    @else
        <div style="text-align: center; padding: 20px 0;">
            <div style="font-size: 60px;">📹</div>
        </div>

        <p class="content-text">
            A student has submitted a practical exam video for review. Please evaluate their submission at your earliest convenience.
        </p>
    @endif

    <div class="info-card primary">
        <h3 class="info-card-title">Student Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Name:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $studentEmail }}</li>
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attemptNumber }}</li>
            <li class="info-list-item"><strong>Submitted:</strong> {{ $submittedAt }}</li>
        </ul>
    </div>

    @if($videoUrl)
        <div class="info-card success">
            <h3 class="info-card-title">📹 Video Submission</h3>
            <p class="content-text">
                <a href="{{ $videoUrl }}" style="color: #093162; text-decoration: underline;">View Student's Video</a>
            </p>
        </div>
    @endif

    <div class="button-container">
        <a href="{{ $reviewUrl }}" class="button button-primary">Review & Grade Submission</a>
    </div>

    <hr class="divider">

    <p class="content-text">
        Please review this submission promptly. The student is waiting for your feedback.
    </p>
@endsection
