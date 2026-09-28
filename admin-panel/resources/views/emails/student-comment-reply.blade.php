@extends('emails.base-template')

@section('title', 'Reply to Your Comment')

@section('header-type', 'primary')

@section('email-title', 'Reply to Your Comment')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>
    
    <p class="content-text">
        An administrator has replied to your comment on a course discussion. Here are the details:
    </p>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Reply Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course }}</li>
            <li class="info-list-item"><strong>Replied by:</strong> {{ $admin }}</li>
            <li class="info-list-item"><strong>Reply Date:</strong> {{ $date }}</li>
        </ul>
    </div>
    
    <div class="info-card" style="background-color: #f9fafb; border-left: 4px solid #9ca3af;">
        <h3 class="info-card-title">Your Original Comment</h3>
        <p class="content-text" style="font-style: italic; color: #6b7280;">
            {{ $originalComment }}
        </p>
    </div>
    
    <div class="info-card" style="background-color: #eff6ff; border-left: 4px solid #3b82f6;">
        <h3 class="info-card-title">Admin's Reply</h3>
        <p class="content-text" style="color: #1e40af;">
            {{ $reply }}
        </p>
    </div>
    
    <div class="button-container">
        <a href="{{ $discussionUrl }}" class="button button-primary">View Discussion</a>
    </div>
    
    <hr class="divider">
    
    <p class="content-text text-center">
        You can continue the discussion by replying to this comment in your course dashboard.
    </p>
@endsection
