@extends('emails.base-template')

@section('title', $isResubmission ? 'Video Exam Re-Submission' : 'New Video Exam Submission')

@section('header-type', 'primary')

@section('email-title', $isResubmission ? 'Video Exam Re-Submitted' : 'New Video Exam Submission')

@section('content')
    <p class="greeting">Hello Administrator,</p>

    <p class="content-text">
        @if($isResubmission)
            A student has re-submitted their video exam for review after a previous rejection. Please evaluate the new submission.
        @else
            A student has submitted their video exam for review. Please evaluate the submission and provide feedback.
        @endif
    </p>

    @if($isResubmission)
    <div class="info-card" style="background-color: #fef3c7; border-left: 4px solid #f59e0b;">
        <h3 class="info-card-title" style="color: #92400e;">⚠️ Re-Submission Notice</h3>
        <p class="content-text" style="color: #92400e; margin: 0;">
            This is a re-submission. The student has updated their video exam after previous feedback.
        </p>
    </div>
    @endif

    <div class="info-card primary">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Student:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $studentEmail }}</li>
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Submitted:</strong> {{ $submittedAt }}</li>
        </ul>
    </div>

    <div class="info-card" style="background-color: #eff6ff; border-left: 4px solid #3b82f6;">
        <h3 class="info-card-title">Video Information</h3>
        <ul class="info-list">
            <li class="info-list-item">
                <strong>Video Link:</strong><br>
                <a href="{{ $videoLink }}" target="_blank" style="color: #3b82f6; word-break: break-all;">{{ $videoLink }}</a>
            </li>
        </ul>
    </div>

    @if($description && $description !== 'No description provided')
    <div class="info-card" style="background-color: #f9fafb; border-left: 4px solid #6b7280;">
        <h3 class="info-card-title">Student's Description</h3>
        <p class="content-text" style="font-style: italic; color: #374151;">
            {{ $description }}
        </p>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ $reviewUrl }}" class="button button-primary">Review Video Exam</a>
    </div>

    <p class="content-text" style="margin-top: 20px; color: #6b7280; font-size: 14px;">
        <strong>Note:</strong> This submission requires your validation. Please review the video and either approve or reject the submission with appropriate feedback.
    </p>
@endsection
