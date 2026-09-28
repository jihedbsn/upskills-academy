@extends('emails.base-template')

@section('title', 'Course Session Cancelled')

@section('header-type', 'danger')

@section('email-title', 'Course Session Cancelled')

@section('content')
    <p class="greeting">Hello {{ $student->name }},</p>

    <p class="content-text">
        We regret to inform you that a session for your course <strong>{{ $session->product->titre }}</strong> has been cancelled. Please find the details of the cancelled session below.
    </p>

    <div class="info-card danger">
        <h3 class="info-card-title">❌ Session Cancelled</h3>
        <p class="content-text">
            This session has been removed from the course schedule.
        </p>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Cancelled Session Information</h3>
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

    @if($session->notes)
    <div class="info-card">
        <h3 class="info-card-title">Cancellation Notes</h3>
        <p class="content-text">
            {{ $session->notes }}
        </p>
    </div>
    @endif

    <p class="content-text">
        We apologize for any inconvenience this may cause. If this session is rescheduled, you will receive a new notification with the updated details.
    </p>

    <hr class="divider">

    <p class="content-text">
        You can view your remaining course schedule in your student dashboard. If you have any questions or concerns, please don't hesitate to contact us.
    </p>
@endsection
