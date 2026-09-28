@extends('emails.base-template')

@section('title', 'Practical Exam Submission Confirmed')

@section('header-type', 'success')

@section('email-title', 'Submission Successful!')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Thank you for submitting your practical exam! We have successfully received your video submission and it is now under review by our instructors.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attempt_number }}</li>
            <li class="info-list-item"><strong>Training Case:</strong> {{ $training_case_name }}</li>
            <li class="info-list-item"><strong>Submitted On:</strong> {{ $submitted_at->format('F d, Y') }} at {{ $submitted_at->format('H:i') }}</li>
            <li class="info-list-item"><strong>Video Link:</strong> <a href="{{ $video_url }}" target="_blank" style="color: #0ea5e9;">View Video</a></li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">What Happens Next?</h3>
        <ul class="info-list">
            <li class="info-list-item">Our instructors will review your video submission carefully</li>
            <li class="info-list-item">They will evaluate your practical work and performance</li>
            <li class="info-list-item">You will receive feedback and results via email</li>
            <li class="info-list-item">Check your dashboard regularly for updates</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $exam_url }}" class="button button-primary">View Submission Status</a>
    </div>

    <p class="content-text" style="font-size: 14px; color: #6b7280; margin-top: 30px;">
        Thank you for your hard work! If you have any questions about your submission, please contact our support team.
    </p>
@endsection
