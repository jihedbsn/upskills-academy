@extends('emails.base-template')

@section('title', 'Welcome to Upskills Academy')

@section('header-type', 'primary')

@section('email-title', 'Account Created Successfully')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Welcome to the Upskills Academy. Your account has been successfully created and you have been enrolled in your course.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Course Enrollment</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Status:</strong> Active</li>
        </ul>
    </div>

    <div class="info-card warning">
        <h3 class="info-card-title">Your Login Credentials</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Email:</strong> {{ $student_email }}</li>
            <li class="info-list-item"><strong>Password:</strong> <code style="background: #fef3c7; padding: 4px 8px; border-radius: 4px; color: #92400e;">{{ $student_password }}</code></li>
        </ul>
    </div>

    <div class="info-card error">
        <h3 class="info-card-title">Important Security Notice</h3>
        <p class="content-text">
            For your security, please change your password after your first login. Keep your login credentials safe and never share them with others.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ $login_url }}" class="button button-primary">Access Your Account</a>
    </div>

    <hr class="divider">

    <p class="content-text">
        <strong>What's Next?</strong>
    </p>
    <ul class="info-list">
        <li class="info-list-item">Log in to your account using the credentials above</li>
        <li class="info-list-item">Complete your profile information</li>
        <li class="info-list-item">Start exploring your course materials</li>
        <li class="info-list-item">Join our community of English learners and teachers</li>
    </ul>

    <p class="content-text">
        If you have any questions or need assistance, please contact our support team.
    </p>
@endsection
