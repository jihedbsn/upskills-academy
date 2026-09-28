@extends('emails.base-template')

@section('title', 'Internship Requires Revision')

@section('header-type', 'primary')

@section('email-title', 'Internship Requires Revision')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Your internship submission has been reviewed. Some aspects require revision before it can be validated.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Internship Title:</strong> {{ $stage_title }}</li>
            <li class="info-list-item"><strong>Review Date:</strong> {{ $review_date }}</li>
        </ul>
    </div>

    <div class="info-card warning">
        <h3 class="info-card-title">Revision Notes</h3>
        <p class="content-text">
            {{ $admin_notes }}
        </p>
    </div>

    <div class="info-card success">
        <h3 class="info-card-title">Next Steps</h3>
        <ul class="info-list">
            <li class="info-list-item">Review the feedback provided above</li>
            <li class="info-list-item">Make the necessary revisions to your submission</li>
            <li class="info-list-item">Resubmit your work for review</li>
            <li class="info-list-item">Contact your instructor if you have questions</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ $resubmit_url ?? '#' }}" class="button button-primary">Resubmit Internship</a>
    </div>

    <p class="content-text">
        We're here to support your learning. Don't hesitate to reach out if you need clarification on the feedback.
    </p>
@endsection
