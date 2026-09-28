@extends('emails.base-template')

@section('title', 'Student Ticket Response')

@section('header-type', 'info')

@section('email-title', 'New Student Reply to Support Ticket')

@section('content')
    <p class="greeting">Hello Admin,</p>
    
    <p class="content-text">
        A student has replied to their support ticket. Please review and respond as needed.
    </p>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Ticket Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Ticket ID:</strong> #TKT-{{ str_pad($ticketId, 3, '0', STR_PAD_LEFT) }}</li>
            <li class="info-list-item"><strong>Subject:</strong> {{ $subject }}</li>
            <li class="info-list-item"><strong>Status:</strong> {{ $status }}</li>
            <li class="info-list-item"><strong>Created:</strong> {{ $createdAt }}</li>
        </ul>
    </div>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Student Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Name:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $studentEmail }}</li>
        </ul>
    </div>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Student's Reply</h3>
        <p class="content-text" style="color: #374151; white-space: pre-wrap;">{{ $studentMessage }}</p>
    </div>
    
    <p class="content-text">
        Click the button below to view the full conversation and respond to the student.
    </p>
    
    <div class="button-container">
        <a href="{{ $ticketUrl }}" class="button button-primary">View Ticket & Respond</a>
    </div>
    
    <p class="content-text" style="margin-top: 30px; font-size: 14px; color: #6b7280;">
        This is an automated notification from the support ticket system.
    </p>
@endsection
