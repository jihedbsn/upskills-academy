@extends('emails.base-template')

@section('title', 'Zoom Meeting Follow-Up')

@section('header-type', 'success')

@section('email-title', 'Zoom Meeting Follow-Up')

@section('content')
    <p class="greeting">Hello {{ $student->name }},</p>

    <p class="content-text">
        Thank you for attending the Zoom meeting for your course <strong>{{ $meeting->product->titre }}</strong>.
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
        <h3 class="info-card-title">Next Steps</h3>
        <p class="content-text">
            We hope you found the session valuable. Here are some suggestions for what to do next:
        </p>
        <ul class="info-list">
            <li class="info-list-item">Review any materials or resources shared during the meeting</li>
            <li class="info-list-item">Complete any assignments or exercises discussed</li>
            <li class="info-list-item">Continue with your course content</li>
            <li class="info-list-item">Reach out if you have any questions about the session</li>
        </ul>
    </div>

    @if($meeting->recording_url)
    <div class="info-card" style="background: linear-gradient(135deg, #093162 0%, #297a7b 100%); color: white; border: none;">
        <h3 class="info-card-title" style="color: white;">📹 Session Recording Available</h3>
        <p class="content-text" style="color: white;">
            The recording of this session is now available. You can watch it anytime to review the content.
        </p>
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ $meeting->recording_url }}" class="button" style="background-color: white; color: #093162; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                🎬 Watch Recording
            </a>
        </div>
    </div>
    @endif

    @if($meeting->agenda)
    <div class="info-card">
        <h3 class="info-card-title">Topics Covered</h3>
        <p class="content-text">
            {{ $meeting->agenda }}
        </p>
    </div>
    @endif

    <p class="content-text">
        If you have any questions or need clarification on any topics discussed during the meeting, please don't hesitate to reach out.
    </p>

    <hr class="divider">

    <p class="content-text">
        For any questions, you can contact the moderator at: <a href="mailto:{{ $meeting->moderator_email }}">{{ $meeting->moderator_email }}</a>
    </p>

    <p class="content-text">
        We look forward to seeing you in the next session!
    </p>
@endsection
