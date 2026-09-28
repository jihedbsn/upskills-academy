@extends('emails.base-template')

@section('title', 'Stage Submission Received')

@section('header-type', 'primary')

@section('email-title', 'Submission Received')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Thank you for submitting your internship work! We have successfully received your submission and it is now under review by our instructors. We will evaluate your work and provide feedback as soon as possible.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Submission Type:</strong> Internship Work</li>
            <li class="info-list-item"><strong>Submitted On:</strong> {{ $submission_date }}</li>
            <li class="info-list-item"><strong>Status:</strong> <span style="color: #f59e0b; font-weight: 600;">Under Review</span></li>
        </ul>
    </div>

    <div class="info-card neutral">
        <h3 class="info-card-title">What Happens Next?</h3>
        <ul class="info-list">
            <li class="info-list-item">Our instructors will review your submission carefully</li>
            <li class="info-list-item">You will receive feedback and evaluation results via email</li>
            <li class="info-list-item">If revisions are needed, you can resubmit your work</li>
            <li class="info-list-item">Once approved, your work will be marked as validated</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $dashboard_url ?? '#' }}" class="button button-primary">View Your Dashboard</a>
    </div>

    <p class="content-text" style="font-size: 14px; color: #6b7280; margin-top: 30px;">
        If you have any questions about your submission, please don't hesitate to contact our support team.
    </p>
@endsection
