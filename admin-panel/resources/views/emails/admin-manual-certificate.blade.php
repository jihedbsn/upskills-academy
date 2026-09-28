@extends('emails.base-template')

@section('title', 'Manual Certificate Generation Required - ' . $courseName)

@section('header-type', 'warning')

@section('email-title', 'Manual Certificate Generation Required - ' . $courseName)

@section('content')
    <p class="greeting">Hello Admin,</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">⚠️</div>
    </div>

    <p class="content-text">
        A student has successfully completed a course that requires <strong>manual certificate generation</strong>.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Student & Course Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Student Name:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Student Email:</strong> {{ $studentEmail }}</li>
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Completion Date:</strong> {{ $studentSuccess->validated_at ? $studentSuccess->validated_at->format('F d, Y H:i') : 'N/A' }}</li>
        </ul>
    </div>

    <div class="info-card warning">
        <h3 class="info-card-title">⚡ Action Required</h3>
        <p class="content-text">
            Please review the student's work and generate the certificate manually through the admin panel.
        </p>
    </div>

    <hr class="divider">

    <div class="info-card primary">
        <h3 class="info-card-title">Steps to Generate Certificate:</h3>
        <ol class="info-list" style="list-style-type: decimal; padding-left: 20px;">
            <li class="info-list-item">Click the button below to open the certificate management page</li>
            <li class="info-list-item">Review the student's submission</li>
            <li class="info-list-item">Click "Generate Certificate"</li>
            <li class="info-list-item">The certificate will be automatically sent to the student via email</li>
        </ol>
    </div>

    <div class="info-card info">
        <p class="content-text" style="margin-bottom: 0;">
            <strong>Note:</strong> This course is configured for manual certificate generation. If you want to enable automatic generation, you can change the setting in the course configuration.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ $certificateManagementUrl }}" class="button button-warning">Generate Certificate Now</a>
    </div>
@endsection
