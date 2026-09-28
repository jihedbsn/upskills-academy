@extends('emails.base-template')

@section('title', 'Practical Exam Submitted')

@section('header-type', 'success')

@section('email-title', 'Practical Exam Successfully Submitted')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">✅</div>
    </div>

    <p class="content-text">
        Your practical exam video has been successfully uploaded and submitted for review. Our instructors will evaluate your work and provide feedback soon.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">📋 Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attemptNumber }}</li>
            <li class="info-list-item"><strong>Submitted At:</strong> {{ $submittedAt }}</li>
            <li class="info-list-item"><strong>Status:</strong> <span style="color: #f59e0b;">Under Review</span></li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">🎥 Submitted Video</h3>
        <p style="margin: 10px 0; color: #64748b;">
            Your video link: <a href="{{ $videoUrl }}" style="color: #3b82f6; text-decoration: underline;">{{ $videoUrl }}</a>
        </p>
    </div>

    <div class="button-container">
        <a href="{{ $dashboardUrl }}" class="button button-primary">View Dashboard</a>
    </div>

    <hr class="divider">

    <div class="info-card info">
        <h3 class="info-card-title">⏳ What Happens Next?</h3>
        <ul class="info-list">
            <li class="info-list-item">Your instructor will review your video submission</li>
            <li class="info-list-item">You will receive feedback and your grade via email</li>
            <li class="info-list-item">Check your dashboard for real-time status updates</li>
            <li class="info-list-item">If you pass, you'll receive your course completion certificate</li>
        </ul>
    </div>

    <p class="content-text">
        Thank you for your submission! We'll notify you as soon as your exam has been graded.
    </p>
@endsection
