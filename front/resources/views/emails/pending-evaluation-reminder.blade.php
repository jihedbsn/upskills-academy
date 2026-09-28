@extends('emails.base-template')

@section('title', 'Course Evaluation Reminder')

@section('header-type', 'primary')

@section('email-title', 'Your Feedback Matters!')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">⭐</div>
    </div>

    <p class="content-text">
        Congratulations on completing {{ $pendingCount > 1 ? 'your courses' : 'your course' }}! We noticed you haven't rated {{ $pendingCount > 1 ? 'them' : 'it' }} yet.
    </p>

    <p class="content-text">
        Your feedback is incredibly valuable to us and helps other students make informed decisions. It only takes a minute!
    </p>

    <div class="info-card warning">
        <h3 class="info-card-title">{{ $pendingCount }} Course{{ $pendingCount > 1 ? 's' : '' }} Awaiting Your Review</h3>
        <ul class="info-list">
            @foreach($pendingCourses as $course)
                <li class="info-list-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                    <div>
                        <strong style="display: block; margin-bottom: 4px;">{{ $course->current_variation ? $course->current_variation->name : $course->titre }}</strong>
                        <small style="color: #6b7280;">Click to rate this course</small>
                    </div>
                    <a href="{{ url('/en/courses/' . $course->id . '#rate-course') }}"
                       class="button button-warning"
                       style="display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white !important; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; white-space: nowrap; margin-left: 12px;">
                        Rate Now
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Why Your Review Matters</h3>
        <ul class="info-list">
            <li class="info-list-item">Helps us improve course quality</li>
            <li class="info-list-item">Guides future students in their learning journey</li>
            <li class="info-list-item">Recognizes excellent instructors</li>
            <li class="info-list-item">Shapes the future of our academy</li>
        </ul>
    </div>

    <div class="info-card success">
        <h3 class="info-card-title">What to Include in Your Review</h3>
        <ul class="info-list">
            <li class="info-list-item">Overall rating (1-5 stars)</li>
            <li class="info-list-item">What you liked most about the course</li>
            <li class="info-list-item">Areas for improvement</li>
            <li class="info-list-item">Would you recommend it to others?</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ url('/en/student-dashboard') }}" class="button button-warning" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white !important; text-decoration: none;">
            Go to Dashboard
        </a>
    </div>

    <hr class="divider">

    <p class="content-text text-center" style="font-size: 12px; color: #6b7280;">
        You're receiving this email because you have completed courses that haven't been rated yet.
        Once you submit your reviews, you won't receive this reminder again.
    </p>
@endsection
