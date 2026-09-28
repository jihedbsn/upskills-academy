@extends('emails.base-template')

@section('title', 'New Practical Exam Submission')

@section('header-type', 'warning')

@section('email-title', 'New Practical Exam Submission - Requires Review')

@section('content')
    <p class="greeting">Hello Admin,</p>

    <p class="content-text">
        A new practical exam has been submitted and is awaiting your review.
    </p>

    <div class="info-card warning">
        <h3 class="info-card-title">Student Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Student Name:</strong> {{ $student_name }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $student_email }}</li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Exam Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Training Case:</strong> {{ $training_case_name }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attempt_number }}</li>
            <li class="info-list-item"><strong>Exam Type:</strong> {{ $exam_type }}</li>
            <li class="info-list-item"><strong>Submission Date:</strong> {{ $submission_date }}</li>
        </ul>
    </div>

    <p class="content-text">
        Please review and grade this submission at your earliest convenience.
    </p>

    <div class="button-container">
        <a href="{{ $grading_url }}" class="button button-primary">Grade Submission</a>
    </div>
@endsection

@section('footer')
    <p class="footer-text">
        This is an automated notification for practical exam submissions.
    </p>
@endsection
