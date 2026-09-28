@extends('emails.base-template')

@section('title', 'Practical Exam Started')

@section('header-type', 'info')

@section('email-title', 'Your Practical Exam Has Started')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">🎬</div>
    </div>

    <p class="content-text">
        Your practical exam for <strong>{{ $courseName }}</strong> has officially started. You can now begin working on your exam.
    </p>

    <div class="info-card info">
        <h3 class="info-card-title">📋 Exam Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attemptNumber }}</li>
            @if(isset($trainingCaseName) && $trainingCaseName !== 'N/A')
                <li class="info-list-item"><strong>Training Case:</strong> {{ $trainingCaseName }}</li>
            @endif
            <li class="info-list-item"><strong>Started At:</strong> {{ $startedAt }}</li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">📝 Instructions</h3>
        <ul class="info-list">
            <li class="info-list-item">Download and review the training case materials</li>
            <li class="info-list-item">Complete the practical exam requirements</li>
            <li class="info-list-item">Record your video demonstration</li>
            <li class="info-list-item">Upload your video through the exam page</li>
            <li class="info-list-item">Submit your exam for review</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $examUrl }}" class="button button-primary">Go to Exam Page</a>
    </div>

    <hr class="divider">

    <div class="info-card warning">
        <h3 class="info-card-title">⚠️ Important Reminders</h3>
        <ul class="info-list">
            <li class="info-list-item">Make sure your video clearly demonstrates all required tasks</li>
            <li class="info-list-item">Your video will be reviewed by our instructors</li>
            <li class="info-list-item">You will receive feedback via email once graded</li>
        </ul>
    </div>

    <p class="content-text">
        Good luck with your practical exam!
    </p>
@endsection
        </ul>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ $exam_url }}" class="button button-primary">Go to Exam Page</a>
    </div>

    <p class="content-text" style="font-size: 14px; color: #6b7280; margin-top: 30px;">
        Good luck with your practical exam! If you have any questions, please contact our support team.
    </p>
@endsection
