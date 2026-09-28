@extends('emails.base-template')

@section('title', 'Zoom Meeting Cancelled')

@section('header-type', 'danger')

@section('email-title', 'Zoom Meeting Cancelled')

@section('content')
    <p class="greeting">Hello {{ $student->name }},</p>

    <p class="content-text">
        We regret to inform you that the Zoom meeting for your course <strong>{{ $meeting->product->titre }}</strong> has been cancelled.
    </p>

    <div class="info-card danger">
        <h3 class="info-card-title">❌ Cancelled Meeting Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Topic:</strong> {{ $meeting->topic }}</li>
            <li class="info-list-item"><strong>Originally Scheduled Date:</strong> {{ $meeting->formatted_date }}</li>
            <li class="info-list-item"><strong>Originally Scheduled Time:</strong> {{ $meeting->formatted_time }} ({{ $meeting->timezone }})</li>
            <li class="info-list-item"><strong>Status:</strong> <span style="color: #dc2626; font-weight: bold;">CANCELLED</span></li>
        </ul>
    </div>

    @if($meeting->agenda)
    <div class="info-card">
        <h3 class="info-card-title">Original Agenda</h3>
        <p class="content-text">
            {{ $meeting->agenda }}
        </p>
    </div>
    @endif

    <div class="info-card warning">
        <p class="content-text" style="margin: 0;">
            <strong>What's Next?</strong><br>
            We will notify you if a new meeting is scheduled. Please check your email regularly for updates.
        </p>
    </div>

    <hr class="divider">

    <p class="content-text">
        If you have any questions about this cancellation, please contact the moderator at: <a href="mailto:{{ $meeting->moderator_email }}">{{ $meeting->moderator_email }}</a>
    </p>

    <p class="content-text">
        We apologize for any inconvenience this may cause.
    </p>
@endsection
