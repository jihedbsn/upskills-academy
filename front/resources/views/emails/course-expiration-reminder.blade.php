@extends('emails.base-template')

@section('title', 'Course Expiring Soon')

@section('header-type', 'primary')

@section('email-title', 'Course Access Expiring Soon')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>
    
    <p class="content-text">
        This is an important reminder that your access to the course listed below will expire soon. Please complete any remaining coursework before the expiration date.
    </p>
    
    <div class="info-card warning">
        <h3 class="info-card-title" style="text-align: center;">Time Remaining</h3>
        <div style="text-align: center; padding: 20px 0;">
            <div style="font-size: 48px; font-weight: bold; color: #d97706; margin: 10px 0;">
                {{ $daysRemaining }}
            </div>
            <div style="font-size: 18px; color: #92400e; font-weight: 600;">
                Days Remaining
            </div>
        </div>
    </div>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Course Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Enrollment Date:</strong> {{ $enrollmentDate }}</li>
            <li class="info-list-item"><strong>Expiration Date:</strong> {{ $expirationDate }}</li>
            @if($progress)
            <li class="info-list-item"><strong>Progress:</strong> {{ $progress }}% completed</li>
            @endif
        </ul>
    </div>
    
    <div class="button-container">
        <a href="{{ $courseUrl }}" class="button button-primary">Continue Course</a>
    </div>
    
    <hr class="divider">
    
    <p class="content-text">
        <strong>Need More Time?</strong>
    </p>
    <p class="content-text">
        Contact our support team to discuss extension options for your course access.
    </p>
@endsection
