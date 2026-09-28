@extends('emails.base-template')

@section('title', 'Course Enrollment Confirmation')

@section('header-type', 'primary')

@section('email-title', 'Enrollment Confirmed')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Congratulations! You have been successfully enrolled in the course. You now have full access to all course materials and resources.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Enrollment Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Enrollment Date:</strong> {{ $enrollment_date }}</li>
            <li class="info-list-item"><strong>Status:</strong> Active</li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Getting Started</h3>
        <ul class="info-list">
            <li class="info-list-item">Access your course dashboard</li>
            <li class="info-list-item">Review the course syllabus and schedule</li>
            <li class="info-list-item">Complete your first lesson</li>
            <li class="info-list-item">Join the course discussion forum</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $course_url }}" class="button button-primary">Start Learning</a>
    </div>

    <p class="content-text">
        We're excited to have you with us. If you have any questions, our support team is here to help.
    </p>
@endsection
