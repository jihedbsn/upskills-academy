@extends('emails.base-template')

@section('title', 'New Practical Exam Submission')

@section('header-type', 'primary')

@section('email-title', 'New Practical Exam Awaiting Review')

@section('content')
    <p class="greeting">Hello Admin,</p>

    <p class="content-text">
        A student has submitted their practical exam and it is now ready for your review.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Student Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Student:</strong> {{ $student_name }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $student_email }}</li>
        </ul>
    </div>

    <div class="info-card success">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Training Case:</strong> {{ $training_case_name }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attempt_number }}</li>
            <li class="info-list-item"><strong>Exam Type:</strong>
                @if($exam_type === 'online')
                    <span style="color: #0ea5e9;">Online Exam</span>
                @else
                    <span style="color: #10b981;">Classroom Exam</span>
                @endif
            </li>
            <li class="info-list-item"><strong>Submitted On:</strong> {{ $submitted_at->format('F d, Y') }} at {{ $submitted_at->format('H:i') }}</li>
            @if($video_url)
            <li class="info-list-item"><strong>Video Link:</strong> <a href="{{ $video_url }}" target="_blank" style="color: #0ea5e9;">View Video</a></li>
            @endif
        </ul>
    </div>

    <div class="info-card neutral">
        <h3 class="info-card-title">Action Required</h3>
        <ul class="info-list">
            <li class="info-list-item">Review the student's submission carefully</li>
            <li class="info-list-item">Watch the video (if online exam) or evaluate classroom work</li>
            <li class="info-list-item">Provide detailed feedback to the student</li>
            <li class="info-list-item">Grade the exam as Pass or Fail</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $grade_url }}" class="button button-primary">Review & Grade Now</a>
    </div>

    <p class="content-text" style="font-size: 14px; color: #6b7280; margin-top: 30px;">
        Please review this submission at your earliest convenience.
    </p>
@endsection
