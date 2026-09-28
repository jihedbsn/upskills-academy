@extends('emails.base-template')

@section('title', 'New Rating Notification')

@section('header-type', 'primary')

@section('email-title', 'New Course Rating')

@section('content')
    <p class="greeting">Hello Administrator,</p>
    
    <p class="content-text">
        A student has submitted a new rating for a course. Please review the feedback provided below.
    </p>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Rating Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Student:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Rating:</strong> {{ $rating }} / 5</li>
            <li class="info-list-item"><strong>Date:</strong> {{ $date }}</li>
            <li class="info-list-item"><strong>Status:</strong> {{ $status }}</li>
        </ul>
    </div>
    
    @if($comment)
    <div class="info-card" style="background-color: #f9fafb; border-left: 4px solid #6b7280;">
        <h3 class="info-card-title">Student Feedback</h3>
        <p class="content-text" style="font-style: italic; color: #374151;">
            {{ $comment }}
        </p>
    </div>
    @endif
    
    <div class="button-container">
        <a href="{{ $reviewUrl }}" class="button button-primary">Review Rating</a>
    </div>
@endsection
