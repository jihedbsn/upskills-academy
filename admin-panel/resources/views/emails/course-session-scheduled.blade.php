@extends('emails.base-template')

@section('title', 'Course Session Scheduled')

@section('header-type', 'primary')

@section('email-title', 'New Course Session Scheduled')

@section('content')
    <p class="greeting">Hello {{ $student->name }},</p>

    <p class="content-text">
        A new session has been scheduled for your course <strong>{{ $session->product->titre }}</strong>. Please find the details below.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Session Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Title:</strong> {{ $session->title }}</li>
            <li class="info-list-item"><strong>Date:</strong> {{ $session->formatted_date }}</li>
            <li class="info-list-item"><strong>Time:</strong> {{ $session->formatted_time }}</li>
            <li class="info-list-item"><strong>Type:</strong> {{ $session->getTypeLabel() }}</li>
            @if($session->instructor_name)
            <li class="info-list-item"><strong>Instructor:</strong> {{ $session->instructor_name }}</li>
            @endif
        </ul>
    </div>

    @if($session->session_type === 'classroom' && $session->location)
    <div class="info-card success">
        <h3 class="info-card-title">Location</h3>
        <p class="content-text">
            <strong>{{ $session->location }}</strong>
        </p>
    </div>
    @endif

    @if($session->session_type === 'online' && $session->zoom_join_url)
    <div class="info-card success">
        <h3 class="info-card-title">Online Access</h3>
        <p class="content-text" style="margin-bottom: 10px;">
            This is an online session. Please use the link below to join at the scheduled time.
        </p>
        <div class="button-container">
            <a href="{{ $session->zoom_join_url }}" class="button button-primary">Join Online Session</a>
        </div>
    </div>
    @endif

    @if($session->description)
    <div class="info-card">
        <h3 class="info-card-title">Description</h3>
        <p class="content-text">
            {{ $session->description }}
        </p>
    </div>
    @endif

    @if($session->notes)
    <div class="info-card">
        <h3 class="info-card-title">Additional Notes</h3>
        <p class="content-text">
            {{ $session->notes }}
        </p>
    </div>
    @endif

    <p class="content-text">
        <strong>Important:</strong> Please mark your calendar and ensure you're prepared for this session.
        @if($session->session_type === 'online')
        Try to join 5 minutes early to check your connection.
        @endif
    </p>

    <hr class="divider">

    <p class="content-text">
        View your complete course schedule in your student dashboard.
    </p>
@endsection
