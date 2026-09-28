@extends('emails.base-template')

@section('title', 'Zoom Meeting Scheduled')

@section('header-type', 'primary')

@section('email-title', 'New Zoom Meeting Scheduled')

@section('content')
    <p class="greeting">Hello {{ $student->name }},</p>

    <p class="content-text">
        A new Zoom meeting has been scheduled for your course <strong>{{ $meeting->product->titre }}</strong>. Please find the details below.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Meeting Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Topic:</strong> {{ $meeting->topic }}</li>
            <li class="info-list-item"><strong>Date:</strong> {{ $meeting->formatted_date }}</li>
            <li class="info-list-item"><strong>Time:</strong> {{ $meeting->formatted_time }} ({{ $meeting->timezone }})</li>
            <li class="info-list-item"><strong>Duration:</strong> {{ $meeting->duration }} minutes</li>
        </ul>
    </div>

    <div class="info-card success">
        <h3 class="info-card-title">Access Details</h3>
        <p class="content-text" style="margin-bottom: 10px;">
            Click the button below to join the meeting. The meeting password is embedded in the link for your convenience.
        </p>
        <div class="button-container">
            <a href="{{ $meeting->join_url }}" class="button button-primary">Join Zoom Meeting</a>
        </div>
    </div>

    @if($meeting->agenda)
    <div class="info-card">
        <h3 class="info-card-title">Agenda</h3>
        <p class="content-text">
            {{ $meeting->agenda }}
        </p>
    </div>
    @endif

    <p class="content-text">
        <strong>Important:</strong> Please try to join 5 minutes before the start time to ensure your audio and video are working correctly.
    </p>

    <hr class="divider">

    <p class="content-text">
        If you have any questions, you can contact the moderator at: <a href="mailto:{{ $meeting->moderator_email }}">{{ $meeting->moderator_email }}</a>
    </p>
@endsection
