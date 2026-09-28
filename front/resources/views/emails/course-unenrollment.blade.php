@extends('emails.base-template')

@section('title', 'Course Unenrollment Notice')

@section('header-type', 'primary')

@section('email-title', 'Course Unenrollment Notice')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <p class="content-text">
        We are writing to inform you that you have been unenrolled from the following course due to your access having expired more than <strong>{{ $gracePeriodDays }} days ago</strong> without a renewal payment.
    </p>

    <div class="info-card error">
        <h3 class="info-card-title">Unenrolled Course</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            @if($enrollmentDate)
            <li class="info-list-item"><strong>Enrollment Date:</strong> {{ $enrollmentDate }}</li>
            @endif
            <li class="info-list-item"><strong>Access Expired On:</strong> {{ $expirationDate }}</li>
            <li class="info-list-item"><strong>Unenrolled On:</strong> {{ now()->format('Y-m-d') }}</li>
        </ul>
    </div>

    <div class="info-card">
        <h3 class="info-card-title">What Does This Mean?</h3>
        <ul class="info-list">
            <li class="info-list-item">Your enrollment in this course has been removed.</li>
            <li class="info-list-item">You no longer have access to the course materials.</li>
            <li class="info-list-item">Any progress made in the course has been preserved in our records.</li>
        </ul>
    </div>

    <div class="info-card success">
        <h3 class="info-card-title">Want to Continue Learning?</h3>
        <p class="content-text">
            If you would like to regain access to this course, you can re-enroll at any time by visiting our course catalog and purchasing the course again.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ url('/en') }}" class="button button-primary">Browse Courses</a>
    </div>

    <hr class="divider">

    <p class="content-text text-center">
        If you believe this was done in error, or if you have any questions, please contact our support team at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.
    </p>
@endsection
