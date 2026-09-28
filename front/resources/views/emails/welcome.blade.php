@extends('emails.base-template')

@section('title', 'Welcome to Upskills Academy')

@section('header-type', 'primary')

@section('email-title', 'Welcome to Upskills Academy')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <p class="content-text">
        Welcome to Upskills Academy. We are pleased to confirm that your account has been successfully created. You are now part of our professional learning community dedicated to excellence in English teaching.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Your Account Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Full Name:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Email Address:</strong> {{ $student->email }}</li>
            <li class="info-list-item"><strong>Registration Date:</strong> {{ now()->format('F d, Y') }}</li>
            @if($student->country)
            <li class="info-list-item"><strong>Country:</strong> {{ $student->country }}</li>
            @endif
        </ul>
    </div>

    <p class="content-text">
        Your account provides you with access to our comprehensive catalog of courses, educational resources, and professional development materials. You can now begin exploring our offerings and start your learning journey.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Available Features</h3>
        <ul class="info-list">
            <li class="info-list-item">Browse our extensive course catalog and educational materials</li>
            <li class="info-list-item">Enroll in courses and access learning content</li>
            <li class="info-list-item">Track your progress through your personal dashboard</li>
            <li class="info-list-item">Participate in course discussions and assessments</li>
            <li class="info-list-item">Earn certificates upon successful course completion</li>
            <li class="info-list-item">Access support resources and documentation</li>
        </ul>
    </div>

    <div class="button-container" style="text-align: center; margin: 32px 0;">
        <a href="{{ config('app.url') }}/en/student-dashboard/"
           style="display: inline-block; padding: 14px 32px; margin: 6px 8px; background-color: #093162; border: 2px solid #093162; border-radius: 10px; color: #ffffff !important; font-size: 15px; font-weight: 600; text-decoration: none; letter-spacing: 0.3px;">
            My Dashboard
        </a>
        <a href="{{ config('app.url') }}/en/courses"
           style="display: inline-block; padding: 14px 32px; margin: 6px 8px; background-color: #ffffff; border: 2px solid #093162; border-radius: 10px; color: #093162 !important; font-size: 15px; font-weight: 600; text-decoration: none; letter-spacing: 0.3px;">
            Browse Courses
        </a>
    </div>

    <hr class="divider">

    <p class="content-text">
        <strong>Getting Started</strong>
    </p>

    <p class="content-text">
        Start your learning journey by exploring our comprehensive course catalog and book collection. Browse through our offerings to find programs that align with your professional development goals. Our support team is available to assist you with any questions you may have.
    </p>
@endsection

