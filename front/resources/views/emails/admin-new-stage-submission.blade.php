@extends('emails.base-template')

@section('title', 'New Stage Submission')

@section('header-type', 'primary')

@section('email-title', 'New Stage Submission Received')

@section('content')
    <p class="greeting">Hello Administrator,</p>

    <p class="content-text">
        A student has submitted their stage/internship work for review. Please evaluate the submission and provide feedback.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Student:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $studentEmail }}</li>
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Submitted:</strong> {{ $submittedAt }}</li>
        </ul>
    </div>

    <div class="info-card" style="background-color: #f0fdf4; border-left: 4px solid #10b981;">
        <h3 class="info-card-title">Files Attached</h3>
        <ul class="info-list">
            <li class="info-list-item">
                <strong>File 1:</strong>
                @if($hasFile1)
                    <span style="color: #10b981;">✓ Uploaded</span>
                @else
                    <span style="color: #ef4444;">✗ Not uploaded</span>
                @endif
            </li>
            <li class="info-list-item">
                <strong>File 2:</strong>
                @if($hasFile2)
                    <span style="color: #10b981;">✓ Uploaded</span>
                @else
                    <span style="color: #6b7280;">— Optional (not uploaded)</span>
                @endif
            </li>
        </ul>
    </div>

    @if($description && $description !== 'No description provided')
    <div class="info-card" style="background-color: #f9fafb; border-left: 4px solid #6b7280;">
        <h3 class="info-card-title">Student's Description</h3>
        <p class="content-text" style="font-style: italic; color: #374151;">
            {{ $description }}
        </p>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ $reviewUrl }}" class="button button-primary">Review Submission</a>
    </div>

    <p class="content-text" style="margin-top: 20px; color: #6b7280; font-size: 14px;">
        <strong>Note:</strong> This submission requires your validation. Please review the attached files and either approve or reject the submission with appropriate feedback.
    </p>
@endsection
