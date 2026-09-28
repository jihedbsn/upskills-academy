@extends('emails.base-template')

@section('title', 'Internship Validated')

@section('header-type', 'primary')

@section('email-title', 'Internship Validated')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Congratulations! Your internship submission has been reviewed and validated. This is an important milestone in your course completion.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Validation Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Internship Title:</strong> {{ $stage_title }}</li>
            <li class="info-list-item"><strong>Validation Date:</strong> {{ $validation_date }}</li>
        </ul>
    </div>

    @if(isset($approval_message) && $approval_message)
    <div class="info-card primary">
        <h3 class="info-card-title">Message from Instructor</h3>
        <p class="content-text" style="font-style: italic;">
            {{ $approval_message }}
        </p>
    </div>
    @endif

    @if(isset($admin_notes) && $admin_notes)
    <div class="info-card primary">
        <h3 class="info-card-title">Additional Notes</h3>
        <p class="content-text" style="font-style: italic;">
            {{ $admin_notes }}
        </p>
    </div>
    @endif

    <p class="content-text">
        Your internship work demonstrates excellent understanding and application of the course material. Well done!
    </p>

    <div class="button-container">
        <a href="{{ $dashboard_url ?? '#' }}" class="button button-primary">View Dashboard</a>
    </div>
@endsection
