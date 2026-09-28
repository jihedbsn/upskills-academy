@extends('emails.base-template')

@section('title', 'New Comment Notification')

@section('header-type', 'primary')

@section('email-title', 'New Comment Received')

@section('content')
    <p class="greeting">Hello Administrator,</p>
    
    <p class="content-text">
        A student has posted a new comment on a course discussion. Please review the comment details below.
    </p>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Comment Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course }}</li>
            <li class="info-list-item"><strong>Student:</strong> {{ $student }}</li>
            <li class="info-list-item"><strong>Posted:</strong> {{ $date }}</li>
            <li class="info-list-item"><strong>Status:</strong> {{ $status }}</li>
        </ul>
    </div>
    
    <div class="info-card" style="background-color: #f9fafb; border-left: 4px solid #6b7280;">
        <h3 class="info-card-title">Comment Content</h3>
        <p class="content-text" style="font-style: italic; color: #374151;">
            {{ $comment }}
        </p>
    </div>
    
    <div class="button-container">
        <a href="{{ $reviewUrl }}" class="button button-primary">Review Comment</a>
    </div>
@endsection
